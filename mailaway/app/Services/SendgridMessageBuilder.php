<?php

namespace App\Services;

use MailModel;

class SendgridMessageBuilder
{
    public function getMessage(MailModel $mail)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($mail->from->email, $mail->from->name);
        $email->setSubject($mail->title);
        foreach ($mail->to as $to) {
            $email->addTo($to->email, $to->name);
        }

        $email->addContent("text/plain", $mail->body_txt);
        $email->addContent("text/html", $mail->body_html);
        return $email;
    }
}
