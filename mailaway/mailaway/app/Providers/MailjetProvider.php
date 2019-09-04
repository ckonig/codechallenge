<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mailjet\LaravelMailjet\Facades\Mailjet;
use \Mailjet\Resources;

//@todo how to move service class & interface away from this file?
class MailjetMailer
{

    public $name = 'Mailjet';

    public function sample()
    {
        $body = [
            'Messages' => [
                [
                    'FromEmail' => "itckoenig@gmail.com",
                    'FromName' => "Mailaway Service",
                    'Recipients' => [
                        [
                            'Email' => "itckoenig@mail.com",
                        ],
                    ],
                    'Subject' => "Greetings from Mailjet.",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                    'CustomID' => "AppGettingStartedTest",
                ],
            ],
        ];
        $response = Mailjet::post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}

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
