<?php

namespace App\Services;

use Mailjet\LaravelMailjet\Facades\Mailjet;
use Mailjet\Resources;
use MailModel;

class MailjetMailer
{
    public $name = 'Mailjet';

    public function __construct(MailjetMessageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function sendMail(MailModel $mail)
    {
        $email = $this->builder->getMessage($mail);
        $response = Mailjet::post(Resources::$Email, $email);
        return $response && $response->success();
    }
}
