<?php

namespace App\Console\Commands;

use App\Jobs\SendMailJob;
use ContactModel;
use Illuminate\Console\Command;
use MailModel;

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
    public function handle()
    {
        //@todo how to test the completeness of the mapping of the arguments?

        $mail = new MailModel();
        $mail->title = $this->argument('title');
        $mail->from = new ContactModel();
        $mail->from->name = $this->argument('fromName');
        $mail->from->email = $this->argument('fromEmail');
        $mail->to = [];
        $toEmails = $this->argument('toEmail');
        foreach ($toEmails as $i => $email) {
            $recipient = new ContactModel();
            $recipient->email = $email;
            $mail->to[] = $recipient;
        }

        $mail->body_txt = $this->argument('txt');
        $mail->body_html = $this->argument('html');
        SendMailJob::dispatch($mail);
        $success = true; //@todo generate & return ID
        $this->info($success ? "mail sent" : "mail not sent!");
        return $success ? 0 : 1;
    }
}
