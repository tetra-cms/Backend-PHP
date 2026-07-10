<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RefreshRequest extends FormRequest
{
    /**
     * Определяет, имеет ли пользователь право выполнить запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            'refresh_token' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Названия полей.
     */
    public function attributes(): array
    {
        return [
            'refresh_token' => 'refresh token',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'refresh_token.required' => 'Необходимо передать refresh token.',
            'refresh_token.string' => 'Некорректный формат refresh token.',
        ];
    }
}
