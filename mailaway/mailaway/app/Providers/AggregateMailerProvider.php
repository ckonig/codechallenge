<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AggregateMailerService {
    public function __construct(MailjetMailer $mailjet, SendgridMailer $sendgrid) {
        $this->mailers = [$mailjet, $sendgrid];
    }

    public function sample() {
        //@todo retry strategy
        //@todo backoff strategy
        //@todo logging
        $success = false;
        foreach($this->mailers as $mailer) {
            if (!$success) {
                $success = $mailer->sample();
            }
        }
        return $success;
    }
}

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
        $this->app->singleton('AggregateMailerService', function ($app) {
            return new AggregateMailerService();
        });
    }
}
