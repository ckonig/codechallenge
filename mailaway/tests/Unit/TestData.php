<?php

namespace Tests\Unit;
use App\Models\MailModel;
use App\Models\ContactModel;

class TestData {
    public static function getMail() {
        $mail = new MailModel();
        $mail->title = 'foo';
        $mail->body_txt = 'txt bar';
        $mail->body_html = 'html bar';
        $mail->from = new ContactModel();
        $mail->from->name = 'Peter';
        $mail->from->email = 'peter@foo.bar';
        $mail->to = [];
        $mail->to[0] = new ContactModel();
        $mail->to[0]->name = 'Fritz';
        $mail->to[0]->email = 'fritz@foo.bar';
        $mail->to[1] = new ContactModel();
        $mail->to[1]->name = 'Klaus';
        $mail->to[1]->email = 'klaus@foo.bar';
        return $mail;
    }
}
