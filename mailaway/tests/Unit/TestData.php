<?php

namespace Tests\Unit;

use App\Models\MailModel;

class TestData
{
    public static function getMail()
    {
        $mail = new MailModel();
        $mail->title = 'foo';
        $mail->body_txt = 'txt bar';
        $mail->body_html = 'html bar';
        $mail->fromName = 'Peter';
        $mail->fromEmail = 'peter@foo.bar';
        $to1 = 'fritz@foo.bar';
        $to2 = 'klaus@foo.bar';
        $mail->to = json_encode([$to1, $to2]);
        return $mail;
    }
}
