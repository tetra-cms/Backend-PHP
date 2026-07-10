<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'fcs' => [
                'required',
                'string',
                'max:255',
            ],

            'city' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:32',
            ],
        ];
    }

    /**
     * Названия полей.
     */
    public function attributes(): array
    {
        return [
            'fcs' => 'ФИО',
            'city' => 'город',
            'address' => 'адрес',
            'phone' => 'телефон',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'fcs.required' => 'Введите ФИО клиента.',
            'city.required' => 'Введите город.',
            'address.required' => 'Введите адрес.',
            'phone.required' => 'Введите номер телефона.',

            'fcs.max' => 'ФИО не должно превышать 255 символов.',
            'city.max' => 'Название города слишком длинное.',
            'address.max' => 'Адрес слишком длинный.',
            'phone.max' => 'Телефон слишком длинный.',
        ];
    }
}
