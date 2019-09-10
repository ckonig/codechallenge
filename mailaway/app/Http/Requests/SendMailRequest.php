<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'body_txt' => 'required',
            'body_html' => 'required',
            'fromName' => 'required',
            'fromEmail' => 'required|email',
            'to' => 'required|array|min:1',
            'to*' => 'required|email',
        ];
    }
}
