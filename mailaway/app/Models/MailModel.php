<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailModel extends Model {

    protected $table = 'mails';

    protected $fillable = [
        'title',
        'from',
        'to',
        'body_txt',
        'body_html'
    ];

    public $title;
    public $from;
    public $to;
    public $body_txt;
    public $body_html;
}
