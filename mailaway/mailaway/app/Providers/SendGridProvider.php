<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SendgridMailer;

class SendGridProvider extends ServiceProvider
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
        $this->app->singleton('SendgridMailer', function ($app) {
            return new SendgridMailer();
        });
    }
}
