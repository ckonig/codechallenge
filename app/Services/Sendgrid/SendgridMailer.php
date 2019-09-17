<?php

namespace App\Services\Sendgrid;

use App\Models\MailModel;
use App\Services\Mailer;
use Log;

class SendgridMailer implements Mailer
{
    public function getName()
    {
        return 'Sendgrid';
    }

    public function __construct(SendgridMessageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function sendMail(MailModel $mail)
    {
        $email = $this->builder->getMessage($mail);
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $response = $sendgrid->send($email);
        Log::debug('Received response from sendgrid: ' . json_encode($response));
        return $response && $response->statusCode() == 202;
    }
}
