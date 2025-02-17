<?php

namespace App\Providers;

use App\Model\Company;
use App\Model\UserWallet;
use Illuminate\Support\ServiceProvider;

class CompanyServiceProvider extends ServiceProvider
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

    public static function validateCompanySlug($slug)
    {
        if (!$slug) {
            return ['success' => false, 'message' => __('Job url can not be generated. Make sure to use alphanumeric characters for the job and company title.')];
        }
        if (Company::where('slug', $slug)->count()) {
            return ['success' => false, 'message' => __('Job slug could not be generated. Please try a different job name.')];
        }

        return ['success' => true];
    }
}
