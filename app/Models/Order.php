<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'comment',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    /**
     * Пользователь, оформивший заказ.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Клиент заказа.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Позиции заказа.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(OrderPosition::class);
    }

    /**
     * Общая стоимость заказа.
     */
    public function getTotalPriceAttribute(): int
    {
        return $this->positions->sum(function (OrderPosition $position) {
            return $position->price * $position->quantity;
        });
    }

    /**
     * Общее количество товаров.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->positions->sum('quantity');
    }
}
