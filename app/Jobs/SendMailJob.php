<?php

namespace App\Jobs;

use App\Models\MailModel;
use App\Services\AggregateMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mail;

    public function __construct(MailModel $mail)
    {
        $this->mail = $mail;
    }

    public function handle(AggregateMailer $mailer)
    {
        $this->mail->attempt++;
        $prefix = 'Mail #' . $this->mail->id . ' Attempt ' . $this->mail->attempt . '/3';
        $result = $mailer->sendMail($this->mail);

        //@todo unit test retry & backoff behavior

        //@todo review backoff period increment and number of retries

        if ($result) {
            $this->mail->status = 'sent';
            $this->mail->save();
            Log::info($prefix . ' successfully sent using AggregateMailer.');
        } else if ($this->mail->attempt > 2) {
            $this->mail->status = 'cancelled';
            $this->mail->save();
            Log::error($prefix . ' failed to send using AggregateMailer.'); //@todo DLQ
        } else {
            $this->mail->status = 'retry';
            $this->mail->save();
            $delay = pow(2, ($this->mail->attempt + 1));
            Log::warn($prefix . ' failed to send using AggregateMailer. requeueing with ' . $delay . 's delay');
            SendMailJob::dispatch($this->mail)->onConnection('database')->delay(now()->addSeconds($delay));
        }
    }
}
