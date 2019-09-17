<?php

namespace App\Services\Mailjet;

use App\Models\MailModel;

class MailjetMessageBuilder
{
    public function getMessage(MailModel $mail)
    {
        $message = [];
        $message['Subject'] = $mail->title;
        $message['FromEmail'] = $mail->fromEmail;
        $message['FromName'] = $mail->fromName;
        $message['Recipients'] = [];
        foreach (json_decode($mail->to) as $to) {
            $recipient = [];
            $recipient['Email'] = $to;
            $message['Recipients'][] = $recipient;
        }

        $message['Text-Part'] = $mail->body_txt;
        $message['HTML-Part'] = $mail->body_html;
        $body = [
            'Messages' => [
                $message,
            ],
        ];
        $email = ['body' => $body];
        return $email;
    }
}
