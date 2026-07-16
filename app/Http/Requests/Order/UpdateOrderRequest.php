<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use App\Enums\DeliveryTypes;
use App\Enums\PaymentTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderRequest extends FormRequest
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
                'required',
                new Enum(OrderStatus::class),
            ],

            'payment_type' => [
                'required',
                new Enum(PaymentTypes::class),
            ],

            'delivery_type' => [
                'required',
                new Enum(DeliveryTypes::class),
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
}
