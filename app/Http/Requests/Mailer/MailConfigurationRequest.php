<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mailer' => ['required', 'string'],
            'scheme' => ['nullable', 'string'],
            'host' => ['required', 'string'],
            'port' => ['required', 'integer'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'from_address' => ['required', 'email'],
            'from_name' => ['required', 'string'],
        ];
    }
}
