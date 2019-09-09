<?php

namespace App\Console\Commands;

use App\Services\MailFrontendService;
use Illuminate\Console\Command;

class GetMail extends Command
{
    protected $signature = 'mail:get {id}';

    protected $description = 'Check the status of an email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(MailFrontendService $service)
    {
        $id = $this->argument('id');
        $mail = $service->retrieveMail($id);
        if ($mail) {
            $this->info('status for email #' . $id . ': ' . $mail->status);
            return true;
        } else {
            $this->error('email #' . $id . ' not found');
            return false;
        }
    }
}
