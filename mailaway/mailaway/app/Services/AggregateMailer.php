<?php

namespace App\Services;

use Log;
use MailModel;

class AggregateMailer
{
    public function __construct(MailjetMailer $mailjet, SendgridMailer $sendgrid)
    {
        $this->mailers = [$mailjet, $sendgrid];
    }

    public function sendMail(MailModel $mail)
    {
        //@todo retry strategy
        //@todo backoff strategy
        $success = false;
        foreach ($this->mailers as $mailer) {
            if (!$success) {
                Log::info('attempt sending mail using ' . $mailer->name);
                try {
                    $success = $mailer->sendMail($mail);
                    if ($success) {
                        Log::info('sent email successfully using ' . $mailer->name);
                    } else {
                        Log::error('sending email NOT successful using ' . $mailer->name);
                    }
                } catch (\Exception $ex) {
                    Log::error('Sending email not successful using ' . $mailer->name . ', caught exception: ' . $ex->getMessage() . "\n");
                }
            }
        }

        return $success;
    }
}
