<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'icon_url' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Пользовательские сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Название категории обязательно.',
            'title.required' => 'Отображаемое название обязательно.',
        ];
    }
}
