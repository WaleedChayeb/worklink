<?php

namespace App\Providers;

use App\Model\Plan;
use Illuminate\Support\ServiceProvider;

class PlansServiceProvider extends ServiceProvider
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
    }

    /**
     * Fetch plan data by id.
     * @param $planId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getPlanById($planId)
    {
        return Plan::query()->where('id', $planId)->first();
    }
}
