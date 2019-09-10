<?php

namespace App\Services;

use App\Models\MailModel;

class SendgridMessageBuilder
{
    public function getMessage(MailModel $mail)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($mail->fromEmail, $mail->fromName);
        $email->setSubject($mail->title);
        foreach (json_decode($mail->to) as $to) {
            $email->addTo($to, '');
        }

        $email->addContent("text/plain", $mail->body_txt);
        $email->addContent("text/html", $mail->body_html);
        return $email;
    }
}
