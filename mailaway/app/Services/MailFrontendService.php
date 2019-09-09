<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\MailModel;
use Log;

class MailFrontendService
{
    public function processMailRequest(string $fromName, string $fromEmail, string $title, array $recipients, string $txt, string $html)
    {
        $mail = new MailModel([
            'title' => $title,
            'fromName' => $fromName,
            'fromEmail' => $fromEmail,
            'to' => json_encode($recipients),
            'body_txt' => json_encode($txt),
            'body_html' => json_encode($html),
        ]);

        $mail->save();
        SendMailJob::dispatch($mail);

        Log::info('Dispatched mail with ID ' . $mail->id);

        return $mail;
    }
}
