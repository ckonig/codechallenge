<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\AggregateMailerService;

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
    public function handle(AggregateMailerService $service)
    {
        $success = $service->sample();
        $message = $success ? "mail sent": "mail not sent!";
        $this->info($message);
    }
}
