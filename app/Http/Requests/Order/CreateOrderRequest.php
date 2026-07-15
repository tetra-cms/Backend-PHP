<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],

            'comment' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                new Enum(OrderStatus::class),
            ],

            'positions' => [
                'required',
                'array',
                'min:1',
            ],

            'positions.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'positions.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Значения по умолчанию.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('status')) {
            $this->merge([
                'status' => OrderStatus::PendingPayment->value,
            ]);
        }
    }
}
