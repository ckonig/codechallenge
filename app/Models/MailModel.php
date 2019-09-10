<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailModel extends Model
{

    protected $table = 'mails';

    protected $fillable = [
        'title',
        'fromName',
        'fromEmail',
        'to',
        'body_txt',
        'body_html',
    ];
}
