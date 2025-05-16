<?php

namespace App\Providers;

use App\Model\Attachment;
use App\Model\BlogPost;
use App\Model\Company;
use App\Model\JobListing;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\UserVerify;
use App\Observers\AttachmentsObserver;
use App\Observers\BlogPostObserver;
use App\Observers\CompaniesObserver;
use App\Observers\JobsObserver;
use App\Observers\SubscriptionsObserver;
use App\Observers\TransactionsObserver;
use App\Observers\UsersObserver;
use App\Observers\UserVerifyObserver;
use App\Model\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
     *
     * @return void
     */
    public function boot()
    {
        if (!InstallerServiceProvider::checkIfInstalled()) {
            return false;
        }

        Attachment::observe(AttachmentsObserver::class);
        Transaction::observe(TransactionsObserver::class);
        JobListing::observe(JobsObserver::class);
        Company::observe(CompaniesObserver::class);
        UserVerify::observe(UserVerifyObserver::class);
        User::observe(UsersObserver::class);
        Subscription::observe(SubscriptionsObserver::class);
        BlogPost::observe(BlogPostObserver::class);

        if (getSetting('site.enforce_app_ssl')) {
            \URL::forceScheme('https');
        }
        if (!InstallerServiceProvider::glck()) {
            dd(base64_decode('SW52YWxpZCBzY3JpcHQgc2lnbmF0dXJl'));
        }

        // Note* 191 ensures great compatibility for index lengths on different envs, though 255 would have behaved better in-app scenarios
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}
