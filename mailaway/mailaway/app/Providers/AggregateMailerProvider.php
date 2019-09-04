<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;

class AggregateMailerService
{
    public function __construct(MailjetMailer $mailjet, SendgridMailer $sendgrid)
    {
        $this->mailers = [$mailjet, $sendgrid];
    }

    public function sample()
    {
        //@todo retry strategy
        //@todo backoff strategy
        $success = false;
        foreach ($this->mailers as $mailer) {
            if (!$success) {
                Log::info('attempt sending mail using ' . $mailer->name);
                $success = $mailer->sample();
                if ($success) {
                    Log::info('sent email successfully');
                } else {
                    Log::error('NOT successful using ' . $mailer->name);
                }
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
