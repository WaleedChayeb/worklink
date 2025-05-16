<?php

namespace App\Providers;

use App\Model\Notification;
use App\Model\Transaction;
use App\Notifications\SlackNotification;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\Uuid;

class NotificationServiceProvider extends ServiceProvider
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
     * Creates a notification payload and broadcasts it.
     *
     * @param $type
     * @return void
     */
    public static function createAndPublishNotification($type)
    {
        try {
            // generate unique id for notification
            do {
                $id = Uuid::uuid4()->getHex();
            } while (Notification::query()->where('id', $id)->first() != null);

            $notificationData = [];
            $notificationData['id'] = $id;
            $notificationData['user_id'] = Auth::id();
            $notificationData['type'] = $type;
            $notification['message'] = 'notification message';

            $toUser = User::query()->where('id', $notificationData['user_id'])->first();
            if ($toUser != null) {
                $modelData = $notificationData;
                unset($modelData['message']);
                $notification = Notification::create($modelData);
                $notification->setAttribute('message', $notificationData['message']);
            }
        } catch (\Exception $exception) {
            Log::error('Failed sending notification: '.$exception->getMessage());
        }
    }

    /**
     * Dispatches a sub renewal notification.
     * @param $subscription
     * @return |null
     */
    public static function sendSubscriptionRenewalEmailNotification($subscription, $succeeded)
    {
        if ($subscription != null) {
            if ($subscription->user != null) {
                // send email for the user who initiated the subscription
                if (isset($subscription->subscriber->settings['notification_email_renewals'])
                    && $subscription->subscriber->settings['notification_email_renewals'] == 'true') {
                    $message = $succeeded ? __('successfully renewed') : __('failed renewing');
                    $buttonText = __('Go back to the website');
                    $buttonUrl = route('home');

                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $subscription->subscriber->email,
                            'subject' => __('Your subscription renewal'),
                            'title' => __('Hello, :name,', ['name'=>$subscription->user->name]),
                            'content' =>  __('Email subscription updated', ['name'=>getSetting('site.name')]),
                            'button' => [
                                'text' => $buttonText,
                                'url' => $buttonUrl,
                            ],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Get notification filter type.
     * @param $notification
     * @return string|null
     */
    public static function getNotificationFilterType($notification)
    {
        $type = null;
        if ($notification != null) {
            switch ($notification->type) {
                case Notification::NEW_SUBSCRIPTION:
                    $type = Notification::SUBSCRIPTIONS_FILTER;
                    break;
                default:
                    $type = false;
                    break;
            }
        }

        return $type;
    }

    /**
     * Gets the user un-read notifications.
     * @return object
     */
    public static function getUnreadNotifications()
    {
        $unreadNotifications = [
            'total' => 0,
            Notification::SUBSCRIPTIONS_FILTER => 0,
        ];
        if (Auth::user()) {
            $userId = Auth::user()->id;
            $userUnreadNotifications = Notification::where(['user_id' => $userId, 'read' => false])
                ->groupBy('type')->select('type', DB::raw('count(*) as total'))->get();
            if (count($userUnreadNotifications)) {
                foreach ($userUnreadNotifications as $notification) {
                    if (self::getNotificationFilterType($notification)) {
                        $unreadNotifications[self::getNotificationFilterType($notification)] += $notification->total;
                        $unreadNotifications['total'] += $notification->total;
                    }
                }
            }
        }

        return (object) $unreadNotifications;
    }

    /**
     * Send partially paid NowPayments transaction email notification for website admin.
     * @param $transaction
     */
    public static function sendNowPaymentsPartiallyPaidTransactionEmailNotification($transaction)
    {
        if ($transaction && $transaction->status === Transaction::PARTIALLY_PAID_STATUS) {
            $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
            foreach ($adminEmails as $email) {
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $email,
                        'subject' => __('Partially paid payment'),
                        'title' => __('Hello, :name,', ['name'=>'Admin']),
                        'content' =>  __('There is a partially paid payment done with NowPayments that requires your attention. (:paymentId)', ['paymentId' => $transaction->nowpayments_payment_id]),
                        'button' => [
                            'text' => __('Check payment'),
                            'url' => 'https://account.nowpayments.io/payments',
                        ],
                    ]
                );
            }
        }
    }

    /**
     * Sends slack notification.
     * @param $job
     */
    public static function sendSlackNotification($job) {
        $message = "
🎉 *New job post available*

A new job posting is available on *[site_name]*. Details on short:

   * Title: _*[job_title]*_
   * Company: _[company_name]_
   * Type: _[job_type]_
   * Location: _[job_location]_

🔗 Check out more info on the listing page, at this link [job_slug] .
";

        if(getSetting('slack.slack_message_template')){
            $message = getSetting('slack.slack_message_template');
        }

        $replaces = [
            '[job_title]' => $job->title,
            '[company_name]' => $job->company->name,
            '[job_type]' => $job->type->name,
            '[job_location]' => $job->location,
            '[job_slug]' => route('jobs.get', ['slug'=>$job->slug]),
            '[site_name]' => getSetting('site.name'),
        ];

        foreach($replaces as $replace => $value){
            $message = str_replace($replace, $value, $message);
        }

        \Illuminate\Support\Facades\Notification::route('slack', getSetting('slack.slack_webhook_url'))
            ->notify(new SlackNotification($message));
    }
}
