<?php

namespace App\Jobs;

use App\Models\MailRequest;
use App\Services\AggregateMailer;
use App\Services\SendMailCacheService;
use App\Services\SendMailQueueService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mailRequest;

    public function __construct(MailRequest $mail)
    {
        $this->mailRequest = $mail;
    }

    public function handle(AggregateMailer $mailer, SendMailCacheService $cache, SendMailQueueService $queue)
    {
        $retries = env('QUEUE_RETRIES', 3);
        $mail = $cache->retrieve($this->mailRequest->id);
        if (empty($mail) || is_null($mail) || !is_object($mail)) {
            throw new Exception('Could not retrieve mail from cache');
        }

        $mail->attempt++;
        $prefix = 'Mail #' . $this->mailRequest->id . ' - Attempt ' . $mail->attempt . '/' . $retries;

        $result = $mailer->sendMail($mail);

        if ($result) {
            $mail->status = 'sent';
            Log::info($prefix . ' successfully sent using AggregateMailer.');
            $cache->insertOrUpdate($mail);
        } else if ($mail->attempt > ($retries - 1)) {
            $mail->status = 'cancelled';
            Log::error($prefix . ' failed to send using AggregateMailer.');
            $cache->insertOrUpdate($mail);
            throw new Exception('no more retries');
        } else {
            $mail->status = 'retry';
            $delay = pow(2, ($mail->attempt + 1));
            Log::warning($prefix . ' failed to send using AggregateMailer. requeueing with ' . $delay . 's delay');
            $cache->insertOrUpdate($mail, $delay);
            $queue->dispatchMailRequest($mail, $delay);
        }
    }
}
