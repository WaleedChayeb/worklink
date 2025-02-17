<?php

namespace App\Providers;

use App\Model\Invoice;
use App\User;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
    }

    public static function createInvoiceByTransaction($transaction)
    {
        if (!getSetting('payments.invoices_enabled')) {
            return null;
        }

        $data = [];
        $invoice = null;
        if ($transaction != null) {
            if ($transaction->invoice_id != null) {
                return null;
            }

            $senderUser = User::query()->where(['id' => $transaction->user_id])->first();

            if ($senderUser != null) {
                $billingDetails = [];
                $billingDetails['senderName'] = setting('payments.invoices_sender_name');
                $billingDetails['senderAddress'] = setting('payments.invoices_sender_country_name');
                $billingDetails['senderCountry'] = setting('payments.invoices_sender_street_address');
                $billingDetails['senderState'] = setting('payments.invoices_sender_state_name');
                $billingDetails['senderPostcode'] = setting('payments.invoices_sender_city_name');
                $billingDetails['senderCity'] = setting('payments.invoices_sender_postcode');
                $billingDetails['senderCompanyNumber'] = setting('payments.invoices_sender_company_number');
                $billingDetails['receiverFirstName'] = $senderUser->first_name;
                $billingDetails['receiverLastName'] = $senderUser->last_name;
                $billingDetails['receiverCountryName'] = $senderUser->country;
                $billingDetails['receiverState'] = $senderUser->state;
                $billingDetails['receiverCity'] = $senderUser->city;
                $billingDetails['receiverPostcode'] = $senderUser->postcode;
                $billingDetails['receiverBillingAddress'] = $senderUser->billing_address;
                $data['subtotal'] = $transaction->amount;
                $data['taxesTotalAmount'] = 0.00;
                if ($transaction->taxes != null) {
                    $taxes = json_decode($transaction->taxes, true);
                    $data['taxes'] = $taxes;
                    $data['subtotal'] = $taxes['subtotal'];
                    $data['taxesTotalAmount'] = $taxes['taxesTotalAmount'];
                }
                $data['billingDetails'] = $billingDetails;
                $data['totalAmount'] = $transaction->amount;
                $data['dueDate'] = $transaction->created_at;
                $data['invoicePrefix'] = setting('payments.invoices_prefix');

                $latestInvoice = Invoice::orderBy('id', 'DESC')->first();
                if ($latestInvoice != null) {
                    $invoiceId = intval($latestInvoice->invoice_id) + 1;
                } else {
                    $invoiceId = 1;
                }

                $invoice = Invoice::create([
                    'invoice_id' => $invoiceId,
                    'data' => json_encode($data),
                ]);
            }
        }

        return $invoice;
    }
}
