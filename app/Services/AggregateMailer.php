<?php

namespace App\Services;

use App\Models\MailModel;
use Log;

class AggregateMailer implements Mailer
{
    public function __construct(array $mailers)
    {
        $this->mailers = $mailers;
    }

    public function getName()
    {
        return 'Aggregate';
    }

    public function sendMail(MailModel $mail)
    {
        $success = false;
        foreach ($this->mailers as $mailer) {
            if (!$success) {
                try {
                    $success = $mailer->sendMail($mail);
                    if ($success) {
                        Log::debug('sent email successfully using ' . $mailer->getName());
                    } else {
                        Log::warning('sending email NOT successful using ' . $mailer->getName());
                    }
                } catch (\Exception $ex) {
                    Log::warning('Sending email not successful using ' . $mailer->getName() . ', caught exception: ' . $ex->getMessage());
                }
            }
        }

        return $success;
    }
}
