<?php

namespace App\Jobs;

use App\Models\MailRequest;
use App\Services\AggregateMailer;
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

    private $mail;
    private $mailRequest;

    public function __construct(MailRequest $mail)
    {
        $this->mailRequest = $mail;
    }

    public function handle(AggregateMailer $mailer)
    {
        $this->mail = Cache::store('redis')->get($this->mailRequest->id);
        if (empty($this->mail) || is_null($this->mail) || !is_object($this->mail)) {
            Log::error('Could not retrieve mail from cache');
            return;
        }

        $this->mail->attempt++;
        $prefix = 'Mail #' . $this->mail->id . ' - Attempt ' . $this->mail->attempt . '/3';
        $result = $mailer->sendMail($this->mail);

        //@todo unit test retry & backoff behavior

        //@todo review backoff period increment and number of retries

        if ($result) {
            $this->mail->status = 'sent';
            Cache::store('redis')->put($this->mail->id, $this->mail, 600);
            Log::info($prefix . ' successfully sent using AggregateMailer.');
        } else if ($this->mail->attempt > 2) {
            $this->mail->status = 'cancelled';
            Cache::store('redis')->put($this->mail->id, $this->mail, 600);
            Log::error($prefix . ' failed to send using AggregateMailer.'); //@todo DLQ
        } else {
            $this->mail->status = 'retry';
            Cache::store('redis')->put($this->mail->id, $this->mail, 600);
            $delay = pow(2, ($this->mail->attempt + 1));
            Log::warning($prefix . ' failed to send using AggregateMailer. requeueing with ' . $delay . 's delay');
            SendMailJob::dispatch($this->mail)->onConnection('redis')->delay(now()->addSeconds($delay));
        }
    }
}
