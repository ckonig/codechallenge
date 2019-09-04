<?php

namespace App\Services;

use MailModel;
use Log;

class AggregateMailer
{
    public function __construct(MailjetMailer $mailjet, SendgridMailer $sendgrid)
    {
        $this->mailers = [$sendgrid, $mailjet];
    }

    public function sample(MailModel $mail)
    {
        //@todo retry strategy
        //@todo backoff strategy
        $success = false;
        foreach ($this->mailers as $mailer) {
            if (!$success) {
                Log::info('attempt sending mail using ' . $mailer->name);
                $success = $mailer->sample($mail);
                if ($success) {
                    Log::info('sent email successfully');
                } else {
                    Log::error('NOT successful using ' . $mailer->name);
                }
            }
        }

        return $success;
    }
}
