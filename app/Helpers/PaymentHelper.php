<?php
/**
 * Created by PhpStorm.
 * User: Lab #2
 * Date: 6/6/2021
 * Time: 4:10 PM.
 */

namespace App\Helpers;

use App\Model\JobListing;
use App\Model\Subscription;
use App\Model\Tax;
use App\Model\Transaction;
use App\Providers\InvoiceServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Model\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Amount;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Plan;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction as PaypalTransaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Common\PayPalModel;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Ramsey\Uuid\Uuid;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Yabacon\Paystack;
use Yabacon\Paystack\Exception\ApiException;

class PaymentHelper
{
    /**
     * Holds up the credentials for paypal API.
     *
     * @var
     */
    private $paypalApiContext;

    private $experienceId;

    public function initiatePaypalContext()
    {
        if (!$this->paypalApiContext instanceof ApiContext) {
            // PP API Context
            $this->paypalApiContext = new ApiContext(new OAuthTokenCredential(config('paypal.client_id'), config('paypal.secret')));
            $this->paypalApiContext->setConfig(config('paypal.settings'));

            // PP Payment Experience
            $this->experienceId = $this->generateWebProfile();
        }
    }

    public function getPaypalApiContext()
    {
        return $this->paypalApiContext;
    }

    public function generatePaypalSubscriptionByTransaction(Transaction $transaction)
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('UTC'));
        //initiate the recurring payment, send back the link for the user to approve it.
        if ($transaction['payment_provider'] === Transaction::PAYPAL_PROVIDER) {
            $plan = $this->createPayPalSubscriptionPlan($transaction);
            $agreement = $this->createPayPalSubscriptionAgreement($transaction, $this->getActiveAgreementPlan($plan->getId()));

            $existingSubscription = $this->getSubscriptionBySenderAndProviderAndJob(
                $transaction['user_id'],
                Transaction::PAYPAL_PROVIDER,
                $transaction['job_id']
            );

            if ($existingSubscription != null) {
                $subscription = $existingSubscription;
                $subscription['paypal_agreement_id'] = $agreement->getId();
                $subscription['paypal_plan_id'] = $plan->getId();
            } else {
                $subscription = $this->createSubscriptionFromTransaction($transaction, $plan);
                $subscription['paypal_agreement_id'] = $agreement->getId();
            }
            $subscription->save();
            $transaction['paypal_transaction_token'] = $this->getPayPalTransactionTokenFromApprovalLink($agreement);
            $transaction['subscription_id'] = $subscription['id'];

            return $agreement->getApprovalLink();
        }
    }

    private function createPayPalSubscriptionPlan(Transaction $transaction)
    {
        $plan = new Plan();
        $plan->setName($this->getPaymentDescriptionByTransaction($transaction))
            ->setDescription($this->getPaymentDescriptionByTransaction($transaction))
            ->setState('ACTIVE')
            ->setType('INFINITE');

        $paymentDefinition = $this->createPayPalSubscriptionPaymentDefinition($transaction);
        $merchantPreferences = $this->createPayPalSubscriptionMerchantPreferences($transaction);
        $plan->setMerchantPreferences($merchantPreferences);
        $plan->setPaymentDefinitions([$paymentDefinition]);

        try {
            $plan = $plan->create($this->paypalApiContext);
        } catch (\Exception $exception) {
            return $this->redirectByTransaction($transaction, "Could not create subscription plan: {$exception->getMessage()}");
        }

        return $plan;
    }

    private function createPayPalSubscriptionPaymentDefinition(Transaction $transaction)
    {
        $paymentDefinitionName = $this->getPaymentDescriptionByTransaction($transaction);

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName($paymentDefinitionName)
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval(strval(PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type)))
            ->setCycles(0)
            ->setAmount(new Currency(['value' => $transaction['amount'], 'currency' => $transaction['currency']]));
        $chargeModel = new ChargeModel();
        $chargeModel->setType('SHIPPING')
            ->setAmount(new Currency(['value' => 0, 'currency' => $transaction['currency']]));

        $paymentDefinition->setChargeModels([$chargeModel]);

        return $paymentDefinition;
    }

    private function createPayPalSubscriptionMerchantPreferences(Transaction $transaction)
    {
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(route('payment.executePaypalPayment'))
            ->setCancelUrl(route('payment.executePaypalPayment'))
            ->setAutoBillAmount('yes')
            ->setInitialFailAmountAction('CONTINUE')
            ->setMaxFailAttempts('0')
            ->setSetupFee(new Currency(['value' => $transaction['amount'], 'currency' => $transaction['currency']]));

        return $merchantPreferences;
    }

    public function createPayPalSubscriptionAgreement(Transaction $transaction, Plan $plan)
    {
        try {
            $agreementDate = new DateTime('+'.PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type).' month', new DateTimeZone('UTC'));
            $agreement = new Agreement();

            $agreement->setName($this->getPaymentDescriptionByTransaction($transaction))
                ->setDescription($this->getPaymentDescriptionByTransaction($transaction))
                ->setStartDate($agreementDate->format('Y-m-d\TH:i:s\Z'));
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            $agreement->setPayer($payer);
            $agreement->setPlan($plan);

            $agreement = $agreement->create($this->paypalApiContext);
        } catch (\Exception $ex) {
            return $this->redirectByTransaction($transaction, "Could not verify PayPal agreement: {$ex->getMessage()}\"");
        }

        return $agreement;
    }

    public function getPayPalTransactionTokenFromApprovalLink(Agreement $agreement)
    {
        $token = explode('token=', $agreement->getApprovalLink());
        if (array_key_exists(1, $token)) {
            return $token[1];
        } else {
            throw new BadRequestHttpException('Failed to fetch PayPal transaction token');
        }
    }

    private function getActiveAgreementPlan($planId)
    {
        $plan = new Plan();
        $plan->setId($planId);
        $patch = new Patch();
        $value = new PayPalModel('{
	       "state":"ACTIVE"
	     }');
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue($value);
        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);

        try {
            $plan->update($patchRequest, $this->paypalApiContext);
        } catch (\Exception $ex) {
            throw new BadRequestHttpException("Could not update PayPal plan: {$ex->getMessage()}");
        }

        return $plan;
    }

    private function createSubscriptionFromTransaction(Transaction $transaction, Plan $plan = null)
    {
        $subscription = new Subscription();
        if ($transaction['user_id'] != null) {
            $subscription['user_id'] = $transaction['user_id'];
            $subscription['provider'] = $transaction['payment_provider'];
            $subscription['type'] = $transaction['type'];
            $subscription['job_id'] = $transaction['job_id'];
            if ($transaction['plan_id']) {
                $subscription['plan_id'] = $transaction['plan_id'];
            }
            if ($plan != null) {
                $subscription['paypal_plan_id'] = $plan->getId();
            }
            if ($transaction['type'] === Transaction::MONTHLY_SUBSCRIPTION_UPDATE) {
                $subscription['type'] = Transaction::ONE_MONTH_SUBSCRIPTION;
            }
            if ($transaction['type'] === Transaction::YEARLY_SUBSCRIPTION_UPDATE) {
                $subscription['type'] = Transaction::YEARLY_SUBSCRIPTION;
            }
            $subscription['status'] = Transaction::PENDING_STATUS;
        }

        return $subscription;
    }

    public function verifyPayPalAgreement($agreementId, $transaction = null, $paypalPaymentId = null)
    {
        try {
            $this->initiatePaypalContext();
            $agreement = Agreement::get($agreementId, $this->paypalApiContext);
            $nowUtc = new DateTime('now', new DateTimeZone('UTC'));
            $now = new DateTime();

            $agreementLastPaymentDate = new DateTime($agreement->getAgreementDetails()->getLastPaymentDate());
            $agreementNextPaymentDate = new DateTime($agreement->getAgreementDetails()->getNextBillingDate());
            $subscription = Subscription::query()->where(['paypal_agreement_id' => $agreementId])->first();
            if ($nowUtc > $agreementLastPaymentDate
                && $nowUtc < $agreementNextPaymentDate
                && strtolower($agreement->getState()) === 'active'
                && $subscription != null) {
                // if it's already active it means we only need to renew this subscription
                if ($subscription->status == Subscription::ACTIVE_STATUS
                    || $subscription->status == Subscription::SUSPENDED_STATUS
                    || $subscription->status == Subscription::EXPIRED_STATUS) {
                    $this->createSubscriptionRenewalTransaction($subscription, $paymentSucceeded = true, $paypalPaymentId);

                // else this webhook comes for first payment of this subscription
                } else {
                    // find last initiated transaction by subscription and update it's status
                    $existingTransaction = Transaction::query()->where([
                        'subscription_id' => $subscription->id,
                        'provider' => Transaction::PAYPAL_PROVIDER,
                        'status' => Transaction::INITIATED_STATUS,
                    ])->latest();

                    if ($existingTransaction instanceof Transaction) {
                        $existingTransaction->status = Transaction::APPROVED_STATUS;

                        $existingTransaction->save();
                    }
                }

                $agreementNextPaymentDate->setTimezone($now->getTimezone());
                $subscriptionBody = [
                    'status' => Subscription::ACTIVE_STATUS,
                    'amount' => $agreement->getPlan()->getPaymentDefinitions()[0]->getAmount()->getValue(),
                    'expires_at' => $agreementNextPaymentDate,
                ];

                Subscription::query()->where('id', $subscription->id)->update($subscriptionBody);

                if ($transaction != null) {
                    $transaction->status = Transaction::APPROVED_STATUS;
                }

                return $agreement;
            }
        } catch (\Exception $exception) {
            if ($exception instanceof PayPalConnectionException) {
                return $this->redirectByTransaction($transaction, "Could not verify PayPal agreement: {$exception->getData()}\"");
            }

            return $this->redirectByTransaction($transaction, "Could not verify PayPal agreement: {$exception->getMessage()}\"");
        }
    }

    public function initiateOneTimePaypalTransaction(Transaction $transaction)
    {
        // Item info
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();

        $item_1->setName($this->getPaymentDescriptionByTransaction($transaction))// item name
        ->setCurrency(SettingsServiceProvider::getAppCurrencyCode())
            ->setQuantity(1)
            ->setPrice($transaction['amount']); // unit price

        // Add item to list
        $item_list = new ItemList();
        $item_list->setItems([$item_1]);

        $amount = new Amount();
        $amount->setCurrency(SettingsServiceProvider::getAppCurrencyCode())
            ->setTotal($transaction['amount']);

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($this->getPaymentDescriptionByTransaction($transaction));

        // Cancel URLs
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('payment.executePaypalPayment'))
            ->setCancelUrl(route('payment.executePaypalPayment'));

        // Generating new Payment
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions([$paypalTransaction])
            ->setExperienceProfileId($this->experienceId);

        $payment->create($this->paypalApiContext);
        $transaction['paypal_transaction_token'] = $payment->getToken();
        $transaction['paypal_transaction_id'] = $payment->getId();

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        return $redirect_url;
    }

    /**
     * Generate a paypal web experience profile.
     *
     * @return string
     */
    private function generateWebProfile()
    {
        $flowConfig = new \PayPal\Api\FlowConfig();
        $flowConfig->setLandingPageType('Billing');
        $flowConfig->setUserAction('commit');
        $flowConfig->setReturnUriHttpMethod('GET');

        $presentation = new \PayPal\Api\Presentation();
        $presentation->setBrandName(getSetting('site.name'))
            ->setLocaleCode('US')
            ->setReturnUrlLabel('Return')
            ->setNoteToSellerLabel('Thanks!');

        $inputFields = new \PayPal\Api\InputFields();
        $inputFields->setAllowNote(true)
            ->setNoShipping(1)
            ->setAddressOverride(0);

        $webProfile = new \PayPal\Api\WebProfile();
        $webProfile->setName(getSetting('site.name').uniqid())
            ->setFlowConfig($flowConfig)
            ->setPresentation($presentation)
            ->setInputFields($inputFields)
            ->setTemporary(true);

        try {
            // Use this call to create a profile.
            $createProfileResponse = $webProfile->create($this->paypalApiContext);

            return $createProfileResponse->id;
        } catch (\Exception $ex) {
            Log::error('Stripe webprofile failure: '.$ex->getMessage());
        }
    }

    public function executePaypalAgreementPayment($transaction)
    {
        $subscription = Subscription::query()->where('id', $transaction->subscription_id)->first();
        if ($subscription != null) {
            if ($subscription->paypal_agreement_id != null) {
                $agreement = $this->verifyPayPalAgreement($subscription->paypal_agreement_id, $transaction);
            } else {
                try {
                    $this->initiatePaypalContext();
                    $agreement = new Agreement();

                    $agreement->execute($transaction->paypal_transaction_token, $this->paypalApiContext);

                    $now = new DateTime();
                    $nowUtc = new DateTime('now', new DateTimeZone('UTC'));
                    $nextBillingDateUtc = new DateTime($agreement->getAgreementDetails()->getNextBillingDate());
                    $nextBillingDate = new DateTime($agreement->getAgreementDetails()->getNextBillingDate(), $now->getTimezone());

                    if ($agreement->getAgreementDetails()->getNextBillingDate() !== null) {
                        $subscription->expires_at = $nextBillingDate;
                    }

                    $subscription->paypal_agreement_id = $agreement->getId();

                    if ($nowUtc < $nextBillingDateUtc) {
                        $subscription->status = Subscription::ACTIVE_STATUS;
                        $subscription->amount = $agreement->getPlan()->getPaymentDefinitions()[0]->getAmount()->getValue();
                        $transaction->status = Transaction::APPROVED_STATUS;
                    } else {
                        $subscription->status = Subscription::EXPIRED_STATUS;
                    }
                } catch (\Exception $ex) {
                    if ($ex instanceof PayPalConnectionException) {
                        return $this->redirectByTransaction($transaction, "Could not verify PayPal agreement: {$ex->getData()}\"");
                    }

                    return $this->redirectByTransaction($transaction, "Could not verify PayPal agreement: {$ex->getMessage()}\"");
                }

                $subscription->paypal_agreement_id = $agreement->getId();

                $subscription->save();
            }

            if ($agreement instanceof Agreement) {
                if ($agreement->getPayer() != null && $agreement->getPayer()->getPayerInfo() != null) {
                    $transaction['paypal_payer_id'] = $agreement->getPayer()->getPayerInfo()->getPayerId();
                }
            }
        } else {
            return $this->redirectByTransaction($transaction, "Couldn't find a subscription for this payment");
        }
    }

    public function executeOneTimePaypalPayment(Request $request, $transaction, $paymentId)
    {
        //Executing the payment
        try {
            // Building up the API Context
            $this->initiatePaypalContext();
            $payment = Payment::get($paymentId, $this->paypalApiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->get('PayerID'));

            $result = $payment->execute($execution, $this->paypalApiContext);

            if ($result->getState() == 'approved') {
                $saleStatus = Transaction::APPROVED_STATUS;
            } elseif ($result->getState() == 'failed') {
                $saleStatus = Transaction::CANCELED_STATUS;
            } else {
                $saleStatus = Transaction::PENDING_STATUS;
            }

            $transaction->status = $saleStatus;
            $transaction->paypal_transaction_id = $result->id;
            $transaction->paypal_payer_id = $request->get('PayerID');
        } catch (\Exception $ex) {
            Log::error('Failed executing one time paypal payment: '.$ex->getMessage());
        }
    }

    public function updateTransactionByStripeSessionId($sessionId)
    {
        $transaction = Transaction::query()->where(['stripe_session_id' => $sessionId])->first();
        if ($transaction != null) {
            try {
                $stripeClient = new StripeClient(getSetting('payments.stripe_secret_key'));
                $stripeSession = $stripeClient->checkout->sessions->retrieve($sessionId);
                if ($stripeSession != null) {
                    if (isset($stripeSession->payment_status)) {
                        $transaction->stripe_transaction_id = $stripeSession->payment_intent;
                        if ($stripeSession->payment_status == 'paid') {
                            if ($transaction->status != Transaction::APPROVED_STATUS) {
                                $transaction->status = Transaction::APPROVED_STATUS;
                                $subscription = Subscription::query()->where('id', $transaction->subscription_id)->first();
                                if ($subscription != null && $this->isSubscriptionPayment($transaction->type)) {
                                    if ($stripeSession->subscription != null) {
                                        $subscription->stripe_subscription_id = $stripeSession->subscription;
                                        $stripeSubscription = $stripeClient->subscriptions->retrieve($stripeSession->subscription);
                                        if ($stripeSubscription != null) {
                                            $latestInvoiceForSubscription = $stripeClient->invoices->retrieve($stripeSubscription->latest_invoice);
                                            if ($latestInvoiceForSubscription != null) {
                                                $transaction->stripe_transaction_id = $latestInvoiceForSubscription->payment_intent;
                                            }
                                        }
                                    }

                                    $expiresDate = new DateTime('+'.PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type).' month', new DateTimeZone('UTC'));
                                    if ($subscription->status != Subscription::ACTIVE_STATUS) {
                                        $subscription->status = Subscription::ACTIVE_STATUS;
                                        $subscription->expires_at = $expiresDate;
                                    } else {
                                        $subscription->expires_at = $expiresDate;
                                    }

                                    $subscription->update();
                                }
                            }
                        } else {
                            $transaction->status = Transaction::CANCELED_STATUS;

                            $subscription = Subscription::query()->where('id', $transaction->subscription_id)->first();

                            if ($subscription != null && $subscription->status == Subscription::ACTIVE_STATUS && $subscription->expires_at <= new DateTime()) {
                                $subscription->status = Subscription::CANCELED_STATUS;

                                $subscription->update();
                            }
                        }
                    }

                    $transaction->update();
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        return $transaction;
    }

    public function generateSubscriptionByTransaction($transaction)
    {
        $existingSubscription = $this->getSubscriptionBySenderAndProviderAndJob(
            $transaction['user_id'],
            $transaction['payment_provider'],
            $transaction['job_id']
        );

        if ($existingSubscription != null) {
            $subscription = $existingSubscription;
        } else {
            $subscription = $this->createSubscriptionFromTransaction($transaction);
            $subscription['amount'] = $transaction['amount'];

            $subscription->save();
        }
        $transaction['subscription_id'] = $subscription['id'];

        return $subscription;
    }

    public function createSubscriptionRenewalTransaction($subscription, $paymentSucceeded, $paymentId = null)
    {
        $transaction = new Transaction();
        $transaction['user_id'] = $subscription->user_id;
        $transaction['type'] = Transaction::SUBSCRIPTION_RENEWAL;
        $transaction['status'] = $paymentSucceeded ? Transaction::APPROVED_STATUS : Transaction::DECLINED_STATUS;
        $transaction['amount'] = $subscription->amount;
        $transaction['currency'] = SettingsServiceProvider::getAppCurrencyCode();
        $transaction['payment_provider'] = $subscription->provider;
        $transaction['subscription_id'] = $subscription->id;
        $transaction['job_id'] = $subscription->job_id;

        // find latest transaction for subscription to get taxes
        $lastTransactionForSubscription = Transaction::query()
            ->where('subscription_id', $subscription->id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($lastTransactionForSubscription != null) {
            $transaction['taxes'] = $lastTransactionForSubscription->taxes;
        }

        if ($paymentId != null) {
            if ($transaction['payment_provider'] === Transaction::PAYPAL_PROVIDER) {
                $transaction['paypal_transaction_id'] = $paymentId;
            } elseif ($transaction['payment_provider'] === Transaction::STRIPE_PROVIDER) {
                $transaction['stripe_transaction_id'] = $paymentId;
            } elseif ($transaction['payment_provider'] === Transaction::CCBILL_PROVIDER) {
                $transaction['ccbill_subscription_id'] = $paymentId;
            }
        }

        $transaction->save();

        try {
            $invoice = InvoiceServiceProvider::createInvoiceByTransaction($transaction);
            if ($invoice != null) {
                $transaction->invoice_id = $invoice->id;
                $transaction->save();
            }
        } catch (\Exception $exception) {
            Log::error('Failed generating invoice for transaction: '.$transaction->id.' error: '.$exception->getMessage());
        }

        return $transaction;
    }

    public function cancelPaypalAgreement($agreementId)
    {
        $this->initiatePaypalContext();
        $agreement = Agreement::get($agreementId, $this->getPaypalApiContext());
        if ($agreement != null) {
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote('Cancel by the client.');

            $agreement->cancel($agreementStateDescriptor, $this->getPaypalApiContext());
        }
    }

    public function cancelStripeSubscription($stripeSubscriptionId)
    {
        $stripe = new StripeClient(getSetting('payments.stripe_secret_key'));

        $stripe->subscriptions->cancel($stripeSubscriptionId);
    }

    /**
     * @param $senderId
     * @param $provider
     * @param $jobId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function getSubscriptionBySenderAndProviderAndJob($senderId, $provider, $jobId)
    {
        $queryCriteria = [
            'user_id' => $senderId,
            'provider' => $provider,
            'job_id' => $jobId,
        ];

        return Subscription::query()
            ->where($queryCriteria)
            ->whereNotIn('status', [Subscription::CANCELED_STATUS])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function generateStripeSessionByTransaction(Transaction $transaction)
    {
        $redirectLink = null;
        $transactionType = $transaction->type;
        if ($transactionType == null || empty($transactionType)) {
            return null;
        }

        try {
            \Stripe\Stripe::setApiKey(getSetting('payments.stripe_secret_key'));
            if ($this->isSubscriptionPayment($transactionType)) {
                // generate stripe product
                $product = \Stripe\Product::create([
                    'name' => getSetting('site.name').' '.__('to post your job listing'),
                ]);

                // generate stripe price
                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => $transaction->amount * 100,
                    'currency' => SettingsServiceProvider::getAppCurrencyCode(),
                    'recurring' => [
                        'interval' => 'month',
                        'interval_count' => PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transactionType),
                    ],
                ]);

                $stripeLineItems = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$stripeLineItems],
                'locale' => 'auto',
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'transactionType' => $transaction->type,
                    'user_id' => Auth::user()->id,
                ],
                'mode' => 'subscription',
                'success_url' => route('payment.checkStripePaymentStatus').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.checkStripePaymentStatus').'?session_id={CHECKOUT_SESSION_ID}',
            ]);

            $transaction['stripe_session_id'] = $session->id;
            $redirectLink = $session->url;
        } catch (\Exception $e) {
            Log::error('Failed generating stripe session for transaction: '.$transaction->id.' error: '.$e->getMessage());
        }

        return $redirectLink;
    }

    /**
     * Verify if payment is made for a subscription.
     *
     * @param $transactionType
     * @return bool
     */
    public function isSubscriptionPayment($transactionType)
    {
        return $transactionType != null
            && (
                $transactionType === Transaction::ONE_MONTH_SUBSCRIPTION
                || $transactionType === Transaction::YEARLY_SUBSCRIPTION
                || $transactionType === Transaction::MONTHLY_SUBSCRIPTION_UPDATE
                || $transactionType === Transaction::YEARLY_SUBSCRIPTION_UPDATE
            );
    }

    /**
     * Get payment description by transaction type.
     *
     * @param $transaction
     * @return string
     */
    public function getPaymentDescriptionByTransaction($transaction)
    {
        $description = '';
        if ($transaction != null) {
            if ($this->isSubscriptionPayment($transaction->type)) {
                switch($transaction->type) {
                    case Transaction::MONTHLY_SUBSCRIPTION_UPDATE:
                    case Transaction::YEARLY_SUBSCRIPTION_UPDATE:
                        $description = getSetting('site.name').' '.__('plan update');
                        break;
                    case Transaction::YEARLY_SUBSCRIPTION:
                        $description = getSetting('site.name').' '.__('yearly recurring payment to post your job listing');
                        break;
                    case Transaction::ONE_MONTH_SUBSCRIPTION:
                        $description = getSetting('site.name').' '.__('monthly recurring payment to post your job listing');
                        break;
                }
            }
        }

        return $description;
    }

    /**
     * Redirect user to proper page after payment process.
     *
     * @param $transaction
     * @param null $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectByTransaction($transaction, $message = null)
    {
        $errorMessage = __('Payment failed.');
        if ($message != null) {
            $errorMessage = $message;
        }
        if ($transaction != null) {
            // handles approved status
            if ($transaction->status === Transaction::APPROVED_STATUS) {
                $successMessage = __('Payment succeeded');
                if ($transaction->type === Transaction::TRIAL || $transaction->amount == 0) {
                    $successMessage = __('Your job listing was successfully posted.');
                } elseif ($this->isSubscriptionPayment($transaction->type)) {
                    $successMessage = __('Your recurring payment for this job was successful created.');
                    if ($this->isPlanUpdatePayment($transaction)) {
                        $successMessage = __('Your recurring payment for this job was successful updated.');
                    }
                }

                return $this->handleRedirectByTransaction($transaction, $successMessage, $success = true);
            // handles any other status
            } else {
                return $this->handleRedirectByTransaction($transaction, $errorMessage, $success = false);
            }
        } else {
            return Redirect::route('jobs.purchase')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Handles redirect by transaction type.
     *
     * @param $transaction
     * @param $recipient
     * @param $message
     * @param bool $success
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleRedirectByTransaction($transaction, $message, $success = false)
    {
        $labelType = $success ? 'success' : 'error';
        if (in_array($transaction->payment_provider, Transaction::PENDING_PAYMENT_PROCESSORS)
            && $transaction->status === Transaction::INITIATED_STATUS) {
            $labelType = 'warning';
            $message = __('Your payment have been successfully initiated but needs to await for approval');
        }

        if ($success) {
            $job = JobListing::query()->where('id', $transaction['job_id'])->first();
            if ($job) {
                return Redirect::route('jobs.get', ['slug' => $job->slug])
                    ->with($labelType, $message);
            }
        }

        return Redirect::route('jobs.purchase', ['jobID' => $transaction->job_id])
            ->with($labelType, $message);
    }

    /**
     * Generate CoinBase transaction by an api call.
     * @param $transaction
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateCoinBaseTransaction($transaction)
    {
        self::generateSubscriptionByTransaction($transaction);

        $redirectUrl = null;
        $httpClient = new Client();
        self::generateCoinbaseTransactionToken($transaction);
        $coinBaseCheckoutRequest = $httpClient->request(
            'POST',
            Transaction::COINBASE_API_BASE_PATH.'/charges',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-CC-Api-Key' => getSetting('payments.coinbase_api_key'),
                    'X-CC-Version' => '2018-03-22',
                ],
                'body' => json_encode(array_merge_recursive([
                    'name' => self::getPaymentDescriptionByTransaction($transaction),
                    'description' => self::getPaymentDescriptionByTransaction($transaction),
                    'local_price' => [
                        'amount' => $transaction->amount,
                        'currency' => $transaction->currency,
                    ],
                    'pricing_type' => 'fixed_price',
                    'metadata' => [],
                    'redirect_url' => route('payment.checkCoinBasePaymentStatus').'?token='.$transaction->coinbase_transaction_token,
                    'cancel_url' => route('payment.checkCoinBasePaymentStatus').'?token='.$transaction->coinbase_transaction_token,
                ])),
            ]
        );

        $response = json_decode($coinBaseCheckoutRequest->getBody(), true);
        if (isset($response['data'])) {
            if (isset($response['data']['id'])) {
                $transaction->coinbase_charge_id = $response['data']['id'];
            }

            if (isset($response['data']['hosted_url'])) {
                $redirectUrl = $response['data']['hosted_url'];
            }
        }

        return $redirectUrl;
    }

    /**
     * Generate unique coinbase transaction token used later as identifier.
     * @param $transaction
     * @throws \Exception
     */
    private function generateCoinbaseTransactionToken($transaction)
    {
        // generate unique token for transaction
        do {
            $id = Uuid::uuid4()->getHex();
        } while (Transaction::query()->where('coinbase_transaction_token', $id)->first() != null);
        $transaction->coinbase_transaction_token = $id;
    }

    /**
     * Update transaction by coinbase charge details.
     * @param $transaction
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAndUpdateCoinbaseTransaction($transaction)
    {
        if ($transaction != null && $transaction->status != Transaction::APPROVED_STATUS
            && $transaction->payment_provider === Transaction::COINBASE_PROVIDER && $transaction->coinbase_charge_id != null) {
            $transactionSucceeded = false;
            $coinbaseChargeStatus = self::getCoinbaseChargeStatus($transaction);
            if ($coinbaseChargeStatus === 'CANCELED') {
                $transaction->status = Transaction::CANCELED_STATUS;
            } elseif ($coinbaseChargeStatus === 'COMPLETED') {
                $transaction->status = Transaction::APPROVED_STATUS;
                $transactionSucceeded = true;
            }
            $this->updateSubscriptionByTransaction($transaction, $transactionSucceeded);
        }
    }

    /**
     * Get coinbase charge latest status.
     * @param $transaction
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getCoinbaseChargeStatus($transaction)
    {
        $httpClient = new Client();
        $coinBaseCheckoutRequest = $httpClient->request(
            'GET',
            Transaction::COINBASE_API_BASE_PATH.'/charges/'.$transaction->coinbase_charge_id,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-CC-Api-Key' => getSetting('payments.coinbase_api_key'),
                    'X-CC-Version' => '2018-03-22',
                ],
            ]
        );
        $coinbaseChargeLastStatus = 'NEW';
        $response = json_decode($coinBaseCheckoutRequest->getBody(), true);
        if (isset($response['data']) && isset($response['data']['timeline'])) {
            $coinbaseChargeLastStatus = $response['data']['timeline'][count($response['data']['timeline']) - 1]['status'];
        }

        return $coinbaseChargeLastStatus;
    }

    /**
     * Generate now payments transaction.
     * @param $transaction
     * @return |null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateNowPaymentsTransaction($transaction)
    {
        self::generateSubscriptionByTransaction($transaction);

        $redirectUrl = null;
        $httpClient = new Client();
        $orderId = self::generateNowPaymentsOrderId($transaction);
        $coinBaseCheckoutRequest = $httpClient->request(
            'POST',
            Transaction::NOWPAYMENTS_API_BASE_PATH.'invoice',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => getSetting('payments.nowpayments_api_key'),
                ],
                'body' => json_encode(array_merge_recursive([
                    'price_amount' => $transaction->amount,
                    'price_currency' => $transaction->currency,
                    'ipn_callback_url' => route('nowPayments.payment.update'),
                    'order_id' => $orderId,
                    'success_url' => route('payment.checkNowPaymentStatus').'?orderId='.$orderId,
                    'cancel_url' => route('payment.checkNowPaymentStatus').'?orderId='.$orderId,
                ])),
            ]
        );

        $response = json_decode($coinBaseCheckoutRequest->getBody(), true);
        if (isset($response['payment_id'])) {
            $transaction->nowpayments_payment_id = $response['payment_id'];
        }
        if (isset($response['order_id'])) {
            $transaction->nowpayments_order_id = $response['order_id'];
        }
        if (isset($response['invoice_url'])) {
            $redirectUrl = $response['invoice_url'];
        }

        return $redirectUrl;
    }

    /**
     * @param $transaction
     * @return string
     * @throws \Exception
     */
    private function generateNowPaymentsOrderId($transaction)
    {
        // generate unique token for transaction
        do {
            $id = Uuid::uuid4()->getHex();
        } while (Transaction::query()->where('nowpayments_order_id', $id)->first() != null);
        $transaction->nowpayments_order_id = $id;

        return $id;
    }

    /**
     * Generates a unique identifier for ccbill transaction.
     * @param $transaction
     * @return string
     * @throws \Exception
     */
    private function generateCCBillUniqueTransactionToken($transaction)
    {
        // generate unique token for transaction
        do {
            $id = Uuid::uuid4()->getHex();
        } while (Transaction::query()->where('ccbill_payment_token', $id)->first() != null);
        $transaction->ccbill_payment_token = $id;

        return $id;
    }

    /**
     * @param $transaction
     * @return int|null
     * @throws \Exception
     */
    public function generateCCBillOneTimePaymentTransaction($transaction)
    {
        $redirectUrl = null;
        if (PaymentsServiceProvider::ccbillCredentialsProvided()) {
            // generate a unique token for transaction and prepare dynamic pricing for the flex form
            $this->generateCCBillUniqueTransactionToken($transaction);

            $redirectUrl = $this->generateCCBillRedirectUrlByTransaction($transaction);
        }

        return $redirectUrl;
    }

    /**
     * Generates redirect url for ccbill payment.
     * @param $transaction
     * @return int|string
     */
    private function generateCCBillRedirectUrlByTransaction($transaction)
    {
        $user = Auth::user();
        $amount = $transaction->amount;
        $ccBillInitialPeriod = $this->getCCBillRecurringPeriodInDaysByTransaction($transaction);
        $ccBillNumRebills = 99;
        $isSubscriptionPayment = $this->isSubscriptionPayment($transaction->type);
        $ccBillClientAcc = getSetting('payments.ccbill_account_number');
        $ccBillClientSubAccRecurring = getSetting('payments.ccbill_subaccount_number_recurring');
        $ccBillClientSubAccOneTime = getSetting('payments.ccbill_subaccount_number_one_time');
        $ccBillSalt = getSetting('payments.ccbill_salt_key');
        $ccBillFlexFormId = getSetting('payments.ccbill_flex_form_id');
        $ccBillCurrencyCode = $this->getCCBillCurrencyCodeByCurrency(SettingsServiceProvider::getAppCurrencyCode());
        $ccBillRecurringPeriod = $this->getCCBillRecurringPeriodInDaysByTransaction($transaction);
        $billingAddress = urlencode($user->billing_address);
        $billingFirstName = $user->first_name;
        $billingLastName = $user->last_name;
        $billingEmail = $user->email;
        $billingCity = $user->city;
        $billingState = $user->state;
        $billingPostcode = $user->postcode;
        $billingCountry = $user->country;
        $ccBillFormDigest = $isSubscriptionPayment
            ? md5(number_format(floatval($amount), 2).$ccBillInitialPeriod.$amount.$ccBillRecurringPeriod.$ccBillNumRebills.$ccBillCurrencyCode.$ccBillSalt)
            : md5(number_format(floatval($amount), 2).$ccBillInitialPeriod.$ccBillCurrencyCode.$ccBillSalt);

        // common form metadata for both one time & recurring payments
        $redirectUrl = Transaction::CCBILL_FLEX_FORM_BASE_PATH.$ccBillFlexFormId.
            '?clientAccnum='.$ccBillClientAcc.'&initialPrice='.$amount.
            '&initialPeriod='.$ccBillInitialPeriod.'&currencyCode='.$ccBillCurrencyCode.'&formDigest='.$ccBillFormDigest.
            '&customer_fname='.$billingFirstName.'&customer_lname='.$billingLastName.'&address1='.$billingAddress.
            '&email='.$billingEmail.'&city='.$billingCity.'&state='.$billingState.'&zipcode='.$billingPostcode.
            '&country='.$billingCountry.'&token='.$transaction->ccbill_payment_token;

        // set client sub account for recurring payments & add extra params
        if ($isSubscriptionPayment) {
            $redirectUrl .= '&clientSubacc='.$ccBillClientSubAccRecurring.'&recurringPrice='.$amount.'&recurringPeriod='.$ccBillRecurringPeriod.'&numRebills='.$ccBillNumRebills;
        // set client sub account for one time payments & add extra params
        } else {
            $redirectUrl .= '&clientSubacc='.$ccBillClientSubAccOneTime;
        }

        return $redirectUrl;
    }

    /**
     * Get ccbill subscription recurring billing period in days.
     * @param $transaction
     * @return float|int
     */
    public function getCCBillRecurringPeriodInDaysByTransaction($transaction)
    {
        return PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type) * 30;
    }

    /**
     * @param $currency
     * @return mixed
     */
    public function getCCBillCurrencyCodeByCurrency($currency)
    {
        $availableCurrencies = [
            'EUR' => '978',
            'AUD' => '036',
            'CAD' => '124',
            'GBP' => '826',
            'JPY' => '392',
            'USD' => '840',
        ];

        return $availableCurrencies[$currency];
    }

    /**
     * @param $transaction
     * @return int|string|null
     * @throws \Exception
     */
    public function generateCCBillSubscriptionPayment($transaction)
    {
        $redirectUrl = null;
        if (PaymentsServiceProvider::ccbillCredentialsProvided()) {
            // generate a unique token for transaction and prepare dynamic pricing for the flex form
            $this->generateCCBillUniqueTransactionToken($transaction);
            $this->generateCCBillSubscriptionByTransaction($transaction);
            $redirectUrl = $this->generateCCBillRedirectUrlByTransaction($transaction);
        }

        return $redirectUrl;
    }

    /**
     * @param $transaction
     * @return Subscription|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function generateCCBillSubscriptionByTransaction($transaction)
    {
        $existingSubscription = $this->getSubscriptionBySenderAndProviderAndJob(
            $transaction['user_id'],
            Transaction::CCBILL_PROVIDER,
            $transaction['job_id']
        );

        if ($existingSubscription != null) {
            $subscription = $existingSubscription;
        } else {
            $subscription = $this->createSubscriptionFromTransaction($transaction);
            $subscription['amount'] = $transaction['amount'];
            $subscription['ccbill_subscription_id'] = $transaction['ccbill_subscription_id'];

            $subscription->save();
        }
        $transaction['subscription_id'] = $subscription['id'];

        return $subscription;
    }

    /**
     * Makes the call to CCBill API to cancel a subscription.
     * @param $stripeSubscriptionId
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancelCCBillSubscription($stripeSubscriptionId)
    {
        $client = new Client(['debug' => fopen('php://stderr', 'w')]);
        $res = $client->request('GET', 'https://datalink.ccbill.com/utils/subscriptionManagement.cgi', [
            'query' => [
                'clientAccnum' => getSetting('payments.ccbill_account_number'),
                'clientSubacc' => getSetting('payments.ccbill_subaccount_number_recurring'),
                'username' => getSetting('payments.ccbill_datalink_username'),
                'password' => getSetting('payments.ccbill_datalink_password'),
                'subscriptionId' => $stripeSubscriptionId,
                'action' => 'cancelSubscription',
            ],
        ]);
        $response = $res->getBody()->getContents();
        if ($response) {
            $responseAsArray = str_getcsv($response, "\n");
            if ($responseAsArray && isset($responseAsArray[0]) && isset($responseAsArray[1])) {
                if ($responseAsArray[0] === 'results' && $responseAsArray[1] === '1') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $transaction
     * @return string
     * @throws \Exception
     */
    private function generatePaystackUniqueTransactionToken($transaction)
    {
        // generate unique token for transaction
        do {
            $id = Uuid::uuid4()->getHex();
        } while (Transaction::query()->where('paystack_payment_token', $id)->first() != null);
        $transaction->paystack_payment_token = $id;

        return $id;
    }

    /**
     * @param $transaction
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public function generatePaystackTransaction($transaction, $email)
    {
        self::generateSubscriptionByTransaction($transaction);

        $paystack = new Paystack(getSetting('payments.paystack_secret_key'));
        $reference = self::generatePaystackUniqueTransactionToken($transaction);
        $paystackTransaction = $paystack->transaction->initialize([
            'amount'=>$transaction->amount * 100,
            'email'=>$email,
            'reference'=>$reference,
        ]);

        return $paystackTransaction->data->authorization_url;
    }

    /**
     * Calls PayStack API to verify payment status and updates transaction in our side accordingly.
     *
     * @param $reference
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function verifyPaystackTransaction($reference)
    {
        $transaction = null;
        if ($reference) {
            $transaction = Transaction::query()
                ->where('paystack_payment_token', $reference)
                ->with('subscription')
                ->first();
            if ($transaction && $transaction->status !== Transaction::APPROVED_STATUS) {
                $paystack = new Paystack(getSetting('payments.paystack_secret_key'));
                try {
                    $paystackTransaction = $paystack->transaction->verify([
                        'reference'=>$reference,
                    ]);

                    if ('success' === $paystackTransaction->data->status) {
                        $transaction->status = Transaction::APPROVED_STATUS;
                        $transaction->save();

                        $this->updateSubscriptionByTransaction($transaction, true);
                    }
                } catch(ApiException $e) {
                    Log::error('Failed verifying paystack transaction: '.$e->getMessage());
                }
            }
        }

        return $transaction;
    }

    /**
     * Validate transaction data.
     * @param $transaction
     * @return bool
     */
    public function validateTransaction($transaction)
    {
        $valid = false;
        if ($transaction) {
            if (!$this->validateTransactionType($transaction)) {
                return false;
            }

            // Validate plan exists
            if (!$transaction['plan_id']) {
                return false;
            }
            $plan = \App\Model\Plan::query()->where('id', $transaction['plan_id'])->first();
            if (!$plan) {
                return false;
            }

            if (!$this->validateTransactionPaymentProvider($transaction, $plan)) {
                return false;
            }

            // Validate job exists
            if (!$transaction['job_id']) {
                return false;
            }
            $job = JobListing::query()->where('id', $transaction['job_id'])->first();
            if (!$job) {
                return false;
            }
            // Validate amount
            $exclusiveTaxesAmount = 0;
            $fixedTaxesAmount = 0;
            $taxes = PaymentsServiceProvider::calculateTaxesForTransaction($transaction);
            if (isset($taxes['exclusiveTaxesAmount'])) {
                $exclusiveTaxesAmount = $taxes['exclusiveTaxesAmount'];
            }
            if (isset($taxes['fixedTaxesAmount'])) {
                $fixedTaxesAmount = $taxes['fixedTaxesAmount'];
            }
            $transactionAmountWithoutTaxes = (string) ($transaction['amount'] - $exclusiveTaxesAmount - $fixedTaxesAmount);

            // Note*: Doing (string) comparison due to PHP float inaccuracy
            // Note* Doing (string)($number + 0) comparison because some mysql drivers doesn't truncate .00 decimals for floats
            switch ($transaction->type) {
                case Transaction::ONE_MONTH_SUBSCRIPTION:
                case Transaction::MONTHLY_SUBSCRIPTION_UPDATE:
                    if ($transactionAmountWithoutTaxes === (string) ($plan->price + 0)) {
                        $valid = true;
                    }
                    break;
                case Transaction::YEARLY_SUBSCRIPTION:
                case Transaction::YEARLY_SUBSCRIPTION_UPDATE:
                    if ($transactionAmountWithoutTaxes === (string) ($plan->yearly_price + 0)) {
                        $valid = true;
                    }
                    break;
            }
        }

        return $valid;
    }

    /**
     * Update subscription for transaction.
     *
     * @param $transaction
     * @param $succeeded
     * @return void
     * @throws \Exception
     */
    public function updateSubscriptionByTransaction($transaction, $succeeded)
    {
        if ($transaction && $transaction->subscription) {
            $subscription = $transaction->subscription;
            if ($succeeded) {
                $expiresDate = new DateTime('+'.PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type).' month', new DateTimeZone('UTC'));
                $subscription->status = Subscription::ACTIVE_STATUS;
                $subscription->expires_at = $expiresDate;
            } else {
                $subscription->status = Subscription::CANCELED_STATUS;
            }
            $subscription->save();
        }
    }

    /**
     * Suspend subscription by transaction.
     *
     * @param $transaction
     * @return void
     * @throws \Exception
     */
    public function suspendSubscriptionByTransaction($transaction)
    {
        if ($transaction->subscription != null) {
            $transaction->subscription->status = Subscription::SUSPENDED_STATUS;
            $transaction->subscription->expires_at = new DateTime('now', new DateTimeZone('UTC'));
            $transaction->subscription->save();
        }
    }

    /**
     * @param $userId
     * @param $type
     * @param $status
     * @param $amount
     * @param $provider
     * @param $taxes
     * @param $jobId
     * @param $planId
     * @return Transaction
     */
    public function createBaseTransaction($userId, $type, $status, $amount, $provider, $taxes, $jobId, $planId)
    {
        $transaction = new Transaction();
        $transaction['user_id'] = $userId;
        $transaction['type'] = $type;
        $transaction['status'] = $status;
        $transaction['amount'] = $amount;
        $transaction['currency'] = SettingsServiceProvider::getAppCurrencyCode();
        $transaction['payment_provider'] = $provider;
        $transaction['taxes'] = $taxes;
        $transaction['job_id'] = $jobId;
        $transaction['plan_id'] = !empty($planId) && $planId != 'null' ? $planId : null;

        return $transaction;
    }

    /**
     * Handles cancel of stripe, paypal and ccbill recurring payments.
     *
     * @param $subscription
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancelRecurringPaymentSubscription($subscription)
    {
        $subscriptionCancelled = false;
        try {
            if ($subscription->provider === Transaction::PAYPAL_PROVIDER && $subscription->paypal_agreement_id != null) {
                $this->cancelPaypalAgreement($subscription->paypal_agreement_id);
                $subscriptionCancelled = true;
            } elseif ($subscription->provider === Transaction::STRIPE_PROVIDER && $subscription->stripe_subscription_id != null) {
                $this->cancelStripeSubscription($subscription->stripe_subscription_id);
                $subscriptionCancelled = true;
            } elseif ($subscription->provider === Transaction::CCBILL_PROVIDER && $subscription->ccbill_subscription_id != null) {
                if ($this->cancelCCBillSubscription($subscription->ccbill_subscription_id)) {
                    $subscriptionCancelled = true;
                }
            }
        } catch (\Exception $exception) {
            Log::error('Failed cancelling subscriptionID '.$subscription->id.' error: '.$exception->getMessage());
        }

        return $subscriptionCancelled;
    }

    /**
     * Cancels logged user most recent subscription for job.
     *
     * @param $job
     * @param $updatedPlan
     * @param $transaction
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancelLoggedUserSubscriptionForJob($job)
    {
        $user = Auth::user();
        $userSubscriptionForJob = Subscription::query()->where(
            [
                'user_id' => $user->id,
                'job_id' => $job->id,
            ]
        )->orderBy('id', 'DESC')->first();
        $subscriptionUpdated = false;
        if ($userSubscriptionForJob) {
            // check if this is a ccbill, stripe or paypal subscription so we need to cancel the automatic payments
            $subscriptionCancelled = $this->cancelSubscription($userSubscriptionForJob);
            if (!$subscriptionCancelled) {
                return false;
            }

            // returns true if we successfully canceled the current subscription
            $subscriptionUpdated = true;
        }

        return $subscriptionUpdated;
    }

    /**
     * Create a new transaction and subscription for updated plan.
     *
     * @param $plan
     * @param $job
     * @param $transaction
     * @return void
     */
    private function createNewPlanSubscriptionForJob($transaction)
    {
        $subscription = $this->createSubscriptionFromTransaction($transaction);
        $plan = \App\Model\Plan::query()->where('id', $transaction->plan_id)->first();
        // handle trial subscriptions
        if ($plan && $plan->trial_days > 0 && !$plan->hasPaymentForPlan) {
            $subscription->expires_at = new DateTime('+'.$plan->trial_days.' days');
            $subscription->type = Transaction::TRIAL;
        } else {
            $this->setSubscriptionExpiryDate($transaction, $subscription);
        }
        $this->updateSubscriptionTypeBasedOnTransactionType($transaction, $subscription);
        if ($transaction->amount == 0) {
            $subscription->status = Subscription::ACTIVE_STATUS;
            $subscription->amount = 0;
        }
        $subscription->save();
        $transaction['subscription_id'] = $subscription['id'];
    }

    /**
     * @param $transaction
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processCurrentPlanUpdate($transaction)
    {
        $selectedPlanId = $transaction->plan_id;
        $jobId = $transaction->job_id;
        $currentPlanUpdated = false;
        if ($selectedPlanId && $jobId) {
            $selectedPlan = \App\Model\Plan::query()->where('id', $selectedPlanId)->first();
            $job = JobListing::query()->where('id', $jobId)->first();
            if ($selectedPlan && $job) {
                // set the payment provider to free for free plans
                if ($selectedPlan->monthly_price == 0 && !$transaction->payment_provider) {
                    $transaction->payment_provider = Transaction::FREE_PLAN;
                }
                $currentPlanUpdated = $this->cancelLoggedUserSubscriptionForJob($job);
            }
        }

        return $currentPlanUpdated;
    }

    /**
     * Handles free plan payments.
     *
     * @param $transaction
     * @return void
     */
    public function handleFreePlanPayment($transaction)
    {
        if ($transaction->amount == 0) {
            $transaction['payment_provider'] = Transaction::FREE_PLAN;
            $transaction['status'] = Transaction::APPROVED_STATUS;

            $this->createNewPlanSubscriptionForJob($transaction);
        }
    }

    /**
     * @param $transaction
     * @param $subscription
     * @return void
     * @throws \Exception
     */
    private function setSubscriptionExpiryDate($transaction, $subscription)
    {
        $expiresDate = new DateTime('+'.PaymentsServiceProvider::getSubscriptionMonthlyIntervalByTransactionType($transaction->type).' month', new DateTimeZone('UTC'));
        $subscription->expires_at = $expiresDate;
    }

    /**
     * @param $transaction
     * @param $subscription
     * @return void
     */
    private function updateSubscriptionTypeBasedOnTransactionType($transaction, $subscription)
    {
        if ($transaction->type === Transaction::MONTHLY_SUBSCRIPTION_UPDATE) {
            $subscription->type = Transaction::ONE_MONTH_SUBSCRIPTION;
        }
        if ($transaction->type === Transaction::YEARLY_SUBSCRIPTION_UPDATE) {
            $subscription->type = Transaction::YEARLY_SUBSCRIPTION;
        }
    }

    /**
     * Returns true if this is a plan update payment.
     *
     * @param $transaction
     * @return bool
     */
    public function isPlanUpdatePayment($transaction)
    {
        return $transaction['type'] === Transaction::MONTHLY_SUBSCRIPTION_UPDATE
            || $transaction['type'] === Transaction::YEARLY_SUBSCRIPTION_UPDATE;
    }

    /**
     * Get user most recent active subscription for job.
     *
     * @param $userId
     * @param $jobId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getUserActiveSubscriptionForJob($userId, $jobId)
    {
        return Subscription::query()
            ->where([
                'user_id' => $userId,
                'status' => Subscription::ACTIVE_STATUS,
                'job_id' => $jobId,
            ])
            ->whereDate('expires_at', '>', Carbon::now())
            ->orderBy('id', 'DESC')->first();
    }

    /**
     * Cancels a subscription.
     * @param $subscription
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancelSubscription($subscription)
    {
        // check if this is a ccbill, stripe or paypal subscription so we need to cancel the automatic payments
        $automaticPaymentsProviders = [
            Transaction::STRIPE_PROVIDER,
            Transaction::PAYPAL_PROVIDER,
            Transaction::CCBILL_PROVIDER,
        ];

        if (in_array($subscription->provider, $automaticPaymentsProviders)
            && $subscription->status === Subscription::ACTIVE_STATUS) {
            $cancelSubscription = $this->cancelRecurringPaymentSubscription($subscription);

            // return false if we cannot cancel the current subscription
            if (!$cancelSubscription) {
                return false;
            }
        }
        // cancel current user subscription
        $subscription->status = Subscription::CANCELED_STATUS;
        $subscription->canceled_at = new DateTime();
        $subscription->save();

        return true;
    }

    /**
     * Validates if we allow this payment provider.
     * @param $transaction
     * @return bool
     */
    private function validateTransactionPaymentProvider($transaction, $plan)
    {
        return in_array($transaction->payment_provider, Transaction::ALLOWED_PAYMENT_PROVIDERS)
            || (!$transaction->payment_provider && ($plan->price == 0 || $this->allowTrialForPlan($plan->id)));
    }

    /**
     * Validates if we allow this payment type.
     * @param $transaction
     * @return bool
     */
    private function validateTransactionType($transaction)
    {
        return in_array($transaction->type, Transaction::ALLOWED_TRANSACTION_TYPES);
    }

    /**
     * Free trial check.
     * @param $planId
     * @return bool
     */
    public function allowTrialForPlan($planId)
    {
        $plan = \App\Model\Plan::query()->where('id', $planId)->first();

        return $plan != null && $plan->trial_days > 0 && !$plan->hasPaymentForPlan;
    }

    /**
     * Handles free trial payments.
     * @param $transaction
     * @return void
     */
    public function handleFreeTrialPayment($transaction)
    {
        $transaction->amount = 0;
        $transaction->type = Transaction::TRIAL;
        $transaction->status = Transaction::APPROVED_STATUS;

        $this->createNewPlanSubscriptionForJob($transaction);
    }
}
