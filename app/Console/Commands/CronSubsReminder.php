<?php

namespace App\Console\Commands;

use App\Model\Subscription;
use App\Providers\EmailsServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CronSubsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:subs_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process upcoming expiring subscription email notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Saves admin settings to laravel seeds.
     *
     * @return mixed
     */
    public function handle()
    {
        echo '[*]['.date('H:i:s')."] Processing subs reminder.\r\n";

        $expiringSubs = Subscription::query()
            ->whereRaw('HOUR(TIMEDIFF(expires_at,now() )) <= 24')
            ->whereRaw('expires_at < now() + INTERVAL 24 HOUR')
            ->whereRaw("(provider NOT IN ('stripe','paypal','ccbill') or type = 'trial' or amount = 0)")
            ->get();

        foreach ($expiringSubs as $sub) {
            echo '[*]['.date('H:i:s').'] Processing email notification for sub id '.$sub->id.".\r\n";

            App::setLocale($sub->user->settings['locale']);
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $sub->user,
                    'subject' => __('Your subscription is about to expire'),
                    'title' => __('Hello, :name,', ['name'=>$sub->user->name]),
                    'content' => __('Your subscription for your job listing :jobName will expire soon and will be hidden from search or other people until you renew it', ['jobName' => $sub->job->title]),
                    'button' => [
                        'text' => __('Go to your jobs'),
                        'url' => route('my.jobs.get'),
                    ],
                ]
            );
        }

        return 0;
    }
}
