<?php

namespace App\Jobs;

use App\Models\MailRequest;
use App\Services\AggregateMailer;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mailRequest;

    public function __construct(MailRequest $mail)
    {
        $this->mailRequest = $mail;
    }

    public function handle(AggregateMailer $mailer)
    {
        $ttl = env('CACHE_TTL', 600);
        $retries = env('QUEUE_RETRIES', 3);

        $mail = Cache::store('redis')->get($this->mailRequest->id);
        if (empty($mail) || is_null($mail) || !is_object($mail)) {
            throw new Exception('Could not retrieve mail from cache');
        }

        $mail->attempt++;
        $prefix = 'Mail #' . $mail->id . ' - Attempt ' . $mail->attempt . '/' . $retries;

        $result = $mailer->sendMail($mail);

        //@todo unit test retry & backoff behavior

        if ($result) {
            $mail->status = 'sent';
            Cache::store('redis')->put($mail->id, $mail, $ttl);
            Log::info($prefix . ' successfully sent using AggregateMailer.');
        } else if ($mail->attempt > ($retries - 1)) {
            $mail->status = 'cancelled';
            Cache::store('redis')->put($mail->id, $mail, $ttl);
            Log::error($prefix . ' failed to send using AggregateMailer.');
            throw new Exception('no more retries');
        } else {
            $mail->status = 'retry';
            $delay = pow(2, ($mail->attempt + 1));
            Cache::store('redis')->put($mail->id, $mail, $ttl + $delay);
            Log::warning($prefix . ' failed to send using AggregateMailer. requeueing with ' . $delay . 's delay');
            SendMailJob::dispatch($mailRequest)->onConnection('redis')->delay(now()->addSeconds($delay));
        }
    }
}
