<?php

namespace App\Http\Controllers;

use App\Helpers\PaymentHelper;
use App\Model\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SubscriptionsController extends Controller
{
    protected $paymentHelper;

    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Method used for canceling an active subscription.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $subscriptionId = $request->subscriptionId;
            if ($subscriptionId != null) {
                $subscription = Subscription::query()->where('id', intval($subscriptionId))
                    ->where(function ($query) {
                        $query->where('user_id', '=', Auth::user()->id);
                    })
                    ->first();

                if ($subscription != null) {
                    if ($subscription->status === Subscription::CANCELED_STATUS) {
                        return Redirect::route('my.jobs.get')
                            ->with('error', __('This subscription is already canceled.'));
                    }

                    $cancelSubscription = $this->paymentHelper->cancelSubscription($subscription);
                    if (!$cancelSubscription) {
                        return Redirect::route('my.jobs.get')
                            ->with('error', __('Something went wrong when cancelling this subscription'));
                    }
                } else {
                    return Redirect::route('my.jobs.get')
                        ->with('error', __('Subscription not found'));
                }
            }
        } catch (\Exception $exception) {
            // show proper error message
            return Redirect::route('my.jobs.get')
                ->with('error', $exception->getMessage());
        }

        return Redirect::route('my.jobs.get')
            ->with('success', __('Successfully canceled subscription'));
    }
}
