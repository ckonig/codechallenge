<?php

namespace App\Providers;

use App\Services\AggregateMailer;
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
        $this->app->bind('App\Services\Mailer', function ($app) {
            return new AggregateMailer(
                [
                    $app->make('App\Services\Mailjet\MailjetMailer'),
                    $app->make('App\Services\Sendgrid\SendgridMailer')
                ]
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
