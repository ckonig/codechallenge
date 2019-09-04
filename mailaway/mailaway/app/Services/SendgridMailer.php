<?php
namespace App\Services;
use MailModel;

class SendgridMailer
{

    public $name = 'Sendgrid';

    public function sample(MailModel $mail)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($mail->from->email, $mail->from->name);
        $email->setSubject($mail->title);
        foreach ($mail->to as $to) {
            $email->addTo($to->email, $to->name);
        }
        $email->addContent("text/plain", $mail->body_txt);
        $email->addContent("text/html", $mail->body_html);
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            return $response->statusCode() == 202;
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
            return false;
        }
    }
}
