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
        // Register the different Mail Services used by AggregateMailer
        $mailers = [
            'MailjetMailer',
            'SendgridMailer',
        ];
        $this->app->tag($mailers, 'mailers');
        $this->app->bind('App\Services\Mailer', function ($app) {
            return new AggregateMailer($app->tagged('mailers'));
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
