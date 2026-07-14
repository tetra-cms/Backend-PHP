<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
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
                Rule::unique('contents')->ignore(
                    $this->route('content')
                ),
            ],

            'content' => [
                'required',
                'string',
            ],
        ];
    }
}
