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
            'body_txt' => json_encode($txt), //@todo why do we need to encode a string?
            'body_html' => json_encode($html), //@todo why do we need to encode a string?
        ]);

        $mail->save();
        $mail->refresh();
        SendMailJob::dispatch($mail)->onConnection('redis')->delay(now()->addSeconds(1));

        Log::info('Dispatched mail with ID ' . $mail->id . ' to queue');

        return $mail;
    }

    public function retrieveMail($id)
    {
        return MailModel::find($id);
    }
}
