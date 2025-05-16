<?php

namespace App\Providers;

use App\Model\Tax;
use App\Model\Transaction;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get subscription monthly interval.
     *
     * @param $transactionType
     * @return int
     */
    public static function getSubscriptionMonthlyIntervalByTransactionType($transactionType)
    {
        return $transactionType === Transaction::YEARLY_SUBSCRIPTION ? 12 : 1;
    }

    /**
     * Checks if CCBill keys are provided in admin panel.
     * @return bool
     */
    public static function ccbillCredentialsProvided()
    {
        return getSetting('payments.ccbill_account_number') && (getSetting('payments.ccbill_subaccount_number_recurring')
                || getSetting('payments.ccbill_subaccount_number_one_time'))
            && getSetting('payments.ccbill_flex_form_id') && getSetting('payments.ccbill_salt_key') && !getSetting('payments.ccbill_checkout_disabled');
    }

    /**
     * Calculate taxes for transaction.
     * @param $transaction
     * @return float[]
     */
    public static function calculateTaxesForTransaction($transaction)
    {
        $taxes = [
            'inclusiveTaxesAmount' => 0.00,
            'exclusiveTaxesAmount' => 0.00,
            'fixedTaxesAmount' => 0.00,
        ];

        $transactionTaxes = json_decode($transaction['taxes'], true);
        if ($transaction != null && $transactionTaxes != null) {
            if (isset($transactionTaxes['data']) && is_array($transactionTaxes['data'])) {
                foreach ($transactionTaxes['data'] as $tax) {
                    if (isset($tax['taxType']) && isset($tax['taxAmount'])) {
                        if ($tax['taxType'] === Tax::INCLUSIVE_TYPE) {
                            $taxes['inclusiveTaxesAmount'] += $tax['taxAmount'];
                        } elseif ($tax['taxType'] === Tax::EXCLUSIVE_TYPE) {
                            $taxes['exclusiveTaxesAmount'] += $tax['taxAmount'];
                        } elseif ($tax['taxType'] === Tax::FIXED_TYPE) {
                            $taxes['fixedTaxesAmount'] += $tax['taxAmount'];
                        }
                    }
                }
            }
        }

        return $taxes;
    }
}
