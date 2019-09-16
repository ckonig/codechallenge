<?php

namespace App\Console\Commands;

use App\Services\MailFrontendService;
use Illuminate\Console\Command;

class GetMail extends Command
{
    protected $signature = 'mail:get {id}';

    protected $description = 'Check the status of an email';

    public function handle(MailFrontendService $service)
    {
        $id = $this->argument('id');
        $mail = $service->retrieveMail($id);
        if ($mail) {
            $this->info("Mail ID:   $mail->id");
            $this->info("Status:    $mail->status");
            $this->info("From:      $mail->fromEmail");
            $this->info("Recipients:");
            $recipients = json_decode($mail->to);
            foreach ($recipients as $to) {
                $this->info("           - $to");
            }

            $this->info("Subject:   $mail->title");
            $this->info("Text Content:");
            $this->info($mail->body_txt);
            $this->info("HTML Content:");
            $this->info($mail->body_html);
            return true;
        } else {
            $this->error('email #' . $id . ' not found');
            return false;
        }
    }
}
