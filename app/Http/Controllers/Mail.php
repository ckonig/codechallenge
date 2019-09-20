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
            $request->input('fromName'),
            $request->input('fromEmail'),
            $request->input('title'),
            $request->input('to'),
            $request->input('body_txt'),
            $request->input('body_html')
        );

        return response()->json(['status' => $mail->status, 'id' => $mail->id], 201);
    }

    public function getMail(string $id)
    {
        $entity = $this->service->retrieveMail($id);
        if (!$entity) {
            return abort(404);
        }

        return response()->json($entity);
    }
}
