<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AggregateMailer;

class AggregateMailerProvider extends ServiceProvider
{
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
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('AggregateMailer', function ($app) {
            return new AggregateMailer();
        });
    }
}
