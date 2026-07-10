<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
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
                'min:2',
                'max:100',
                Rule::unique('categories', 'name'),
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
     * Названия полей.
     */
    public function attributes(): array
    {
        return [
            'name' => 'название',
            'title' => 'заголовок',
            'icon_url' => 'иконка',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Введите название категории.',
            'name.unique' => 'Категория с таким названием уже существует.',
            'name.min' => 'Название должно содержать минимум 2 символа.',

            'title.required' => 'Введите отображаемое название категории.',

            'icon_url.string' => 'Некорректный путь к иконке.',
        ];
    }
}
