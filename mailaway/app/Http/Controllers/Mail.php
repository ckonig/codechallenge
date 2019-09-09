<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMailRequest;
use App\Services\MailFrontendService;

class Mail extends Controller
{
    public function __construct(MailFrontendService $service)
    {
        $this->service = $service;
    }

    public function sendMail(SendMailRequest $request)
    {
        $mail = $this->service->processMailRequest(
            $request->input('from.name'),
            $request->input('from.email'),
            $request->input('title'),
            $request->input('to'),
            $request->input('body_txt'),
            $request->input('body_html')
        );

        // @todo use proper HTTP status code
        return response()->json(['status' => $mail->status, 'id' => $mail->id]);
    }

    public function getMailStatus(int $id)
    {
        $entity = $this->service->retrieveMail($id);
        return response()->json([
            'id' => $id,
            'status' => $entity->status,
        ]);
    }
}
