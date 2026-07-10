<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => [
                'required',
                'string',
                'min:3',
                'max:32',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
        ];
    }

    /**
     * Названия полей для сообщений об ошибках.
     */
    public function attributes(): array
    {
        return [
            'username' => 'имя пользователя',
            'password' => 'пароль',
        ];
    }

    /**
     * Кастомные сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Введите имя пользователя.',
            'username.min' => 'Имя пользователя должно содержать минимум :min символа.',
            'username.max' => 'Имя пользователя не должно превышать :max символов.',

            'password.required' => 'Введите пароль.',
            'password.min' => 'Пароль должен содержать минимум :min символов.',
        ];
    }
}
