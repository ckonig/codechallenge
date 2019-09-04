<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//@todo how to move service class & interface away from this file?
class SendgridMailer {
    public function sample() {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("itckoenig@gmail.com", "Example User");
        $email->setSubject("Sending with Twilio SendGrid is Fun");
        $email->addTo("itckoenig@gmail.com", "Example User");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            return $response->statusCode() == 202;
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
            return false;
        }
    }
}

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
