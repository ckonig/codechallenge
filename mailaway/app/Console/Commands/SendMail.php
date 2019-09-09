<?php

namespace App\Console\Commands;

use App\Services\MailFrontendService;
use Illuminate\Console\Command;

class SendMail extends Command
{
    protected $signature = 'mail:send
                            {fromName : Name of the sender}
                            {fromEmail : Email address of the sender}
                            {title : Subject line of the email}
                            {txt : Text content for clients that do not support HTML}
                            {html : HTML content for modern mail clients}
                            {toEmail* : Recipient(s) of the email. Multiple values are allowed.}';

    protected $description = 'Send an email.';

    public function handle(MailFrontendService $service)
    {
        //@todo how to test the completeness of the mapping of the arguments?

        $mail = $service->processMailRequest(
            $this->argument('fromName'),
            $this->argument('fromEmail'),
            $this->argument('title'),
            $this->argument('toEmail'),
            $this->argument('txt'),
            $this->argument('html')
        );

        $success = true; //@todo generate & return ID
        $this->info($success ? ('mail #' . $mail->id . ' created, status: ' . $mail->status) : 'mail not sent!');
        return $success ? 0 : 1;
    }
}
