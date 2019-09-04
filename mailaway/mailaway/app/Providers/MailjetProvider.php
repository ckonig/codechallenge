<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mailjet\LaravelMailjet\Facades\Mailjet;
use MailModel;
use \Mailjet\Resources;

//@todo how to move service class & interface away from this file?
class MailjetMailer
{

    public $name = 'Mailjet';

    public function sample(MailModel $mail)
    {
        $message = [];
        $message['Subject'] = $mail->title;
        $message['FromEmail'] = $mail->from->email;
        $message['FromName'] = $mail->from->name;
        $message['Recipients'] = [];
        foreach ($mail->to as $to) {
            $recipient = [];
            $recipient['Email'] = $to->email;
            $recipient['Name'] = $to->name;
            $message['Recipients'][] = $recipient;
        }
        $message['Text-Part'] = $mail->body_txt;
        $message['HTML-Part'] = $mail->body_html;
        $body = [
            'Messages' => [
                $message,
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
