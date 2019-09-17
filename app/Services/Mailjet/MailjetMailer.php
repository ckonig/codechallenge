<?php

namespace App\Services\Mailjet;

use App\Models\MailModel;
use App\Services\Mailer;
use Log;
use Mailjet\LaravelMailjet\Facades\Mailjet;
use Mailjet\Resources;

class MailjetMailer implements Mailer
{
    public function getName()
    {
        return 'Mailjet';
    }

    public function __construct(MailjetMessageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function sendMail(MailModel $mail)
    {
        $email = $this->builder->getMessage($mail);
        $response = Mailjet::post(Resources::$Email, $email);
        $data = $response->getData();
        Log::debug('Received response from mailjet: ' . json_encode($data));
        return $response && $response->success();
    }
}
