<?php

namespace App\Services;

use App\Models\MailModel;
use App\Models\MailRequest;
use App\Jobs\SendMailJob;

class SendMailQueueService
{
    public function dispatchMailRequest(MailModel $mail, int $extraTtl = 0)
    {
        SendMailJob::dispatch(new MailRequest($mail->id))
            ->onConnection('redis')
            ->delay(now()->addSeconds($extraTtl));
    }
}
