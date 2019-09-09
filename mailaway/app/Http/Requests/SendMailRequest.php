<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'body_txt' => 'required',
            'body_html' => 'required',
            'from.name' => 'required',
            'from.email' => 'required|email',
            'to' => 'required|array|min:1',
            'to*' => 'required|email',
        ];
    }
}
