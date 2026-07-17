<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
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
            'image_url' => [
                'nullable',
                'string',
                'max:255',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
            ],

            'supply_quantum' => [
                'required',
                'integer',
                'min:1',
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
        ];
    }

    /**
     * Названия полей.
     */
    public function attributes(): array
    {
        return [
            'image_url' => 'изображение',
            'name' => 'название',
            'description' => 'описание',
            'price' => 'цена',
            'stock' => 'остаток',
            'supply_quantum' => 'кратность поставки',
            'category_id' => 'категория',
        ];
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Введите название товара.',
            'description.required' => 'Введите описание.',
            'price.required' => 'Введите цену.',
            'price.integer' => 'Цена должна быть целым числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'stock.required' => 'Введите количество.',
            'stock.integer' => 'Количество должно быть целым числом.',
            'supply_quantum.required' => 'Введите кратность поставки.',
            'supply_quantum.min' => 'Кратность должна быть не меньше 1.',
            'category_id.required' => 'Выберите категорию.',
            'category_id.exists' => 'Указанная категория не существует.',
        ];
    }
}
