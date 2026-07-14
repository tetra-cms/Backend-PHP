<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

class CreateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route' => [
                'required',
                'string',
                'max:255',
                'unique:contents,route',
            ],

            'content' => [
                'required',
                'string',
            ],
        ];
    }
}
