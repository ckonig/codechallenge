<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AggregateMailer;
use MailModel;
use ContactModel;

class AggregateMailSample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aggregatemailsample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a sample email using aggregate mailer';

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
    public function handle(AggregateMailer $service)
    {
        //@todo rewrite to use console input instead of static content!

        $mail = new MailModel();
        $mail->title = 'Sample Mail';
        $mail->from = new ContactModel();
        $mail->from->name = 'Mailaway Service';
        $mail->from->email = 'itckoenig@gmail.com';
        $mail->to = [];
        $mail->to[] = new ContactModel();
        $mail->to[0]->name = 'Christian';
        $mail->to[0]->email = 'itckoenig@gmail.com';
        $mail->body_txt = 'Sample Email Content';
        $mail->body_html = '<h1>Yay</h1> It works';
        $success = $service->sendMail($mail);
        $message = $success ? "mail sent": "mail not sent!";
        $this->info($message);
    }
}
