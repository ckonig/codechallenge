<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\HelloWorldService;

class HelloWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helloworld';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A basic HelloWorld command';

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
    public function handle(HelloWorldService $service)
    {
        $this->info($service->hello()['title']);
    }
}
