<?php

namespace App\Services;

use Log;
use App\Models\ContactModel;
use App\Models\MailModel;
use App\Jobs\SendMailJob;

class MailFrontendService {
    public function processMailRequest($fromName, $fromEmail, $title, $recipients, $txt, $html) {
        $from = new ContactModel();
        $from->name = $fromName;
        $from->email = $fromEmail;

        $to = [];
        foreach($recipients as $to) {
            $recipient = new ContactModel();
            $recipient->name = $to['name'];
            $recipient->email = $to['email'];
            $to[] = $recipient;
        }

        $mail = new MailModel([
            'title' => $title,
            'from' => $from->toJson(),
            'to' => json_encode($to),
            'body_txt'=> json_encode($txt),
            'body_html' => json_encode($html)
        ]);

        $mail->save();
        SendMailJob::dispatch($mail);

        Log::info('Dispatched mail with ID ' . $mail->id);

        return $mail;
    }
}
