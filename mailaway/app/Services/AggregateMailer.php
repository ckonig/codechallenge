<?php

namespace App\Services;

use Log;
use App\Models\MailModel;

class AggregateMailer
{
    public function __construct(MailjetMailer $mailjet, SendgridMailer $sendgrid)
    {
        $this->mailers = [$mailjet, $sendgrid];
    }

    public function sendMail(MailModel $mail)
    {
        $success = false;
        foreach ($this->mailers as $mailer) {
            if (!$success) {
                try {
                    $success = $mailer->sendMail($mail);
                    if ($success) {
                        Log::info('sent email successfully using ' . $mailer->name);
                    } else {
                        Log::warn('sending email NOT successful using ' . $mailer->name);
                    }
                } catch (\Exception $ex) {
                    Log::warn('Sending email not successful using ' . $mailer->name . ', caught exception: ' . $ex->getMessage());
                }
            }
        }

        return $success;
    }
}
