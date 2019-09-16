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
        //@todo do not accept array with one empty string as recipients
        $mail = $this->service->processMailRequest(
            $request->input('fromName'),
            $request->input('fromEmail'),
            $request->input('title'),
            $request->input('to'),
            $request->input('body_txt'),
            $request->input('body_html')
        );

        // @todo use proper HTTP status code
        return response()->json(['status' => $mail->status, 'id' => $mail->id]);
    }

    public function getMailStatus(string $id)
    {
        $entity = $this->service->retrieveMail($id);
        if (!$entity) {
            return abort(404);
        }

        return response()->json([
            'id' => $id,
            'status' => $entity->status,
            'attempt' => $entity->attempt,
        ]);
    }

    //@todo secure this endpoint or remove it
    public function getMail(string $id)
    {
        $entity = $this->service->retrieveMail($id);
        if (!$entity) {
            return abort(404);
        }

        return response()->json($entity);
    }
}
