<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMailRequest;
use App\Jobs\SendMailJob;
use App\Models\MailModel;
use App\Models\ContactModel;
use Log;

class Mail extends Controller
{
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

        SendMailJob::dispatch($mail);
        $result = true; //@todo generate & return ID

        Log::info($result);

        // @todo use proper HTTP status code
        return response()->json(['status'=>'OK']);
    }
}
