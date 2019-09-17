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
        //@todo Unit Test to ensure the completeness of the mapping of the arguments

        $mail = $service->processMailRequest(
            $this->argument('fromName'),
            $this->argument('fromEmail'),
            $this->argument('title'),
            $this->argument('toEmail'),
            $this->argument('txt'),
            $this->argument('html')
        );

        $this->info("Created Mail.");
        $this->info("ID: $mail->id");
        $this->info("Status: $mail->status");
        return 0;
    }
}
