<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\MailModel;
use App\Services\AggregateMailer;
use Log;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mail;

    /**
     * Create a new job instance.
     *
     * @param MailModel $mail
     * @return void
     */
    public function __construct(MailModel $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @param AggregateMailer $mailer
     * @return void
     */
    public function handle(AggregateMailer $mailer)
    {
        $mailer->sendMail($this->mail);
    }
}
