<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMailRequest;
use App\Models\MailModel;
use App\Models\ContactModel;
use App\Services\MailFrontendService;
use Log;

class Mail extends Controller
{
    public function __construct(MailFrontendService $service) {
        $this->service = $service;
    }

    public function sendMail(SendMailRequest $request) {
        $mail = $this->service->processMailRequest(
            $request->input('from.name'),
            $request->input('from.email'),
            $request->input('title'),
            $request->input('to'),
            $request->input('body_txt'),
            $request->input('body_html')
        );

        // @todo use proper HTTP status code
        return response()->json(['status'=>'queued', 'id' => $mail->id]);
    }

    public function getMailStatus(int $id) {
        $entity = MailModel::find($id);
        return response()->json(['status' => $entity->status]);
    }
}
