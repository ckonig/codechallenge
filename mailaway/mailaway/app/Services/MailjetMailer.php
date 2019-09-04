<?php

namespace App\Services;

use MailModel;

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
