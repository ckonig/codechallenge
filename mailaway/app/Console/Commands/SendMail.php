<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use App\Models\ContactModel;
use Illuminate\Console\Command;
use App\Models\MailModel;
use App\Services\MailFrontendService;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendmail
                            {fromName : Name of the sender}
                            {fromEmail : Email address of the sender}
                            {title : Subject line of the email}
                            {txt : Text content for clients that do not support HTML}
                            {html : HTML content for modern mail clients}
                            {toEmail* : Recipient(s) of the email. Multiple values are allowed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(MailFrontendService $service)
    {
        //@todo how to test the completeness of the mapping of the arguments?

        $mail = $service->processMailRequest(
            $this->argument('fromName'),
            $this->argument('fromEmail'),
            $this->argument('title'),
            array_map(function($item){ return ['name' => '', 'email' => $item]; }, $this->argument('toEmail')),
            $this->argument('txt'),
            $this->argument('html')
        );

        $success = true; //@todo generate & return ID
        $this->info($success ? "mail sent" : "mail not sent!");
        return $success ? 0 : 1;
    }
}
