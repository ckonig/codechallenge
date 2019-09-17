<?php

namespace App\Models;

class MailModel
{
    public $title;
    public $fromName;
    public $fromEmail;
    public $to;
    public $body_txt;
    public $body_html;
    public $attempt = 0;
    public $status;
}
