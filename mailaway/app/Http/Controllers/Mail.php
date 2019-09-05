<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMailRequest;
use App\Services\AggregateMailer;
use MailModel;
use ContactModel;
use Log;

class Mail extends Controller
{
    public function __construct(AggregateMailer $mailer) {
        $this->mailer = $mailer;
    }

    public function sendMail(SendMailRequest $request) {
        $mail = new MailModel();
        $mail->title = $request->input('title');
        $mail->body_txt = $request->input('body_txt');
        $mail->body_html = $request->input('body_html');
        $mail->from = new ContactModel();
        $mail->from->name = $request->input('from.name');
        $mail->from->email = $request->input('from.email');
        $mail->to = [];
        foreach($request->input('to') as $to) {
            $recipient = new ContactModel();
            $recipient->name = $to['name'];
            $recipient->email = $to['email'];
            $mail->to[] = $recipient;
        }

        $result = $this->mailer->sendMail($mail);

        // @todo handle result of mailing... which later represents "adding to queue"
        // the API user needs to know if this worked or not.
        Log::info($result);

        // @todo use proper HTTP status code
        return response()->json(['status'=>'OK']);
    }
}
