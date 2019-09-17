<?php

namespace App\Services;

use App\Models\MailModel;

interface Mailer {
    public function getName();
    public function sendMail(MailModel $mail);
}
