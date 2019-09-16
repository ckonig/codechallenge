<?php

namespace App\Models;

class MailRequest
{
    public $id;

    public function __construct($id){
        $this->id = $id;
    }
}
