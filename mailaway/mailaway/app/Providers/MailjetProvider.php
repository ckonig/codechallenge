<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MailjetMailer;

class MailjetProvider extends ServiceProvider
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
        $this->app->singleton('MailjetMailer', function ($app) {
            return new MailjetMailer();
        });
    }
}
