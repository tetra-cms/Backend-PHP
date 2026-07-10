<?php

namespace App\Http\Requests\User;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:32',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],

            'role' => [
                'required',
                Rule::enum(Role::class),
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
            'role' => 'роль',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Введите имя пользователя.',
            'username.alpha_dash' => 'Имя пользователя может содержать только буквы, цифры, дефисы и подчёркивания.',
            'username.unique' => 'Пользователь с таким именем уже существует.',

            'email.required' => 'Введите email.',
            'email.email' => 'Некорректный email.',
            'email.unique' => 'Пользователь с таким email уже существует.',

            'role.required' => 'Укажите роль.',
        ];
    }
}
