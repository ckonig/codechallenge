<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Models\MailModel;
use App\Models\MailRequest;
use Illuminate\Support\Facades\Cache;
use Log;

class MailFrontendService
{
    public function processMailRequest(string $fromName, string $fromEmail, string $title, array $recipients, string $txt, string $html)
    {
        $mail = new MailModel();
        $mail->title = $title;
        $mail->fromName = $fromName;
        $mail->fromEmail = $fromEmail;
        $mail->to = json_encode($recipients);
        $mail->body_txt = $txt;
        $mail->body_html = $html;
        $mail->status = 'queued';
        $guid = $this->GUID();
        $mail->id = $guid;

        Cache::store('redis')->put($mail->id, $mail, 600);

        SendMailJob::dispatch(new MailRequest($mail->id))
            ->onConnection('redis')
            ->delay(now()->addSeconds(1));

        Log::info('Dispatched mail with ID ' . $mail->id . ' to queue');

        return $mail;
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function retrieveMail($id)
    {
        return Cache::store('redis')->get($id);
    }
}
