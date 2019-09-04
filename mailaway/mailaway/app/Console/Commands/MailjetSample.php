<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\MailjetMailer;

class MailjetSample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailjetsample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a sample email through mailjet';

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
    public function handle(MailJetMailer $mailer)
    {
        $this->info($mailer->sample());
    }
}
