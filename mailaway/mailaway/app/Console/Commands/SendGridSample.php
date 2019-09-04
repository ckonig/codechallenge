<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\SendgridMailer;

class SendGridSample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendgridsample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a sample email through sendgrid';

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
    public function handle(SendgridMailer $service)
    {
        $this->info($service->sample());
    }
}
