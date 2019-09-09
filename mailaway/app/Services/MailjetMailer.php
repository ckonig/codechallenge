<?php

namespace App\Services;

use Mailjet\LaravelMailjet\Facades\Mailjet;
use Mailjet\Resources;
use App\Models\MailModel;
use Log;

class MailjetMailer
{
    public $name = 'Mailjet';

    public function __construct(MailjetMessageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function sendMail(MailModel $mail)
    {
        Log::info('trying to send mail');
        $email = $this->builder->getMessage($mail);
        $response = Mailjet::post(Resources::$Email, $email);
        return $response && $response->success();
    }
}
