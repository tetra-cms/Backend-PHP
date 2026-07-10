<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
                'alpha_dash',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ];
    }

    /**
     * Названия полей.
     */
    public function attributes(): array
    {
        return [
            'username' => 'имя пользователя',
            'email' => 'электронная почта',
            'password' => 'пароль',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Введите имя пользователя.',
            'username.unique' => 'Пользователь с таким именем уже существует.',
            'username.alpha_dash' => 'Имя пользователя может содержать только буквы, цифры, дефисы и подчёркивания.',

            'email.required' => 'Введите адрес электронной почты.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Пользователь с таким email уже существует.',

            'password.required' => 'Введите пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
        ];
    }
}
