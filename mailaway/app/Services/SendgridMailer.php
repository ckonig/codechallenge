<?php

namespace App\Services;

use App\Models\MailModel;

class SendgridMailer
{
    public $name = 'Sendgrid';

    public function __construct(SendgridMessageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function sendMail(MailModel $mail)
    {
        $email = $this->builder->getMessage($mail);
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $response = $sendgrid->send($email);
        return $response && $response->statusCode() == 202;
    }
}
