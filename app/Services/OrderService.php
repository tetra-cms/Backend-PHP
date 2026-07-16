<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderPosition;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use DomainException;

class OrderService
{
    /**
     * Получить все заказы пользователя.
     */
    public function all(User $user, array $filters = []): Collection|LengthAwarePaginator
    {
        $query = Order::query()
            ->with([
                'client',
                'user',
                'positions.product',
            ]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($client) use ($search) {
                        $client->where('fcs', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['perPage'])) {
            return $query
                ->latest()
                ->paginate((int) $filters['perPage']);
        }

        if (
            $user->role !== Role::ADMIN &&
            $user->role !== Role::EMPLOYEE
        ) {
            $query->where('user_id', $user->id);
        }

        return $query
            ->latest()
            ->get();
    }

    /**
     * Получить заказ.
     */
    public function find(User $user, Order $order): Order
    {
        $this->ensureOwner($user, $order);

        return $order->load([
            'client',
            'user',
            'positions.product',
        ]);
    }

    public function positions(
        User $user,
        Order $order,
        int $perPage = 15
    ): LengthAwarePaginator {
        $this->ensureOwner($user, $order);

        return $order->positions()
            ->with('product')
            ->paginate($perPage);
    }

    /**
     * Создать заказ.
     */
    public function create(User $user, array $data): Order
    {
        $this->ensureClientOwner($user, $data['client_id']);

        return DB::transaction(function () use ($user, $data) {

            $order = Order::create([
                'user_id' => $user->id,
                'client_id' => $data['client_id'],
                'comment' => $data['comment'] ?? null,
                'payment_type' => $data['payment_type'],
                'delivery_type' => $data['delivery_type'],
                'status' => OrderStatus::InProgress,
            ]);

            $products = Product::query()
                ->whereIn(
                    'id',
                    collect($data['positions'])->pluck('product_id')
                )
                ->get()
                ->keyBy('id');

            foreach ($data['positions'] as $position) {

                $product = $products->get($position['product_id']);

                if (!$product) {
                    throw new DomainException(
                        'Товар не найден.'
                    );
                }

                OrderPosition::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $position['quantity'],
                ]);
            }

            return $order->fresh([
                'client',
                'user',
                'positions.product',
            ]);
        });
    }

    /**
     * Обновить заказ.
     */
    public function update(
        User $user,
        Order $order,
        array $data
    ): Order {

        $this->ensureOwner($user, $order);

        $this->ensureClientOwner($user, $data['client_id']);

        return DB::transaction(function () use (
            $order,
            $data
        ) {

            $order->update([
                'client_id' => $data['client_id'],
                'comment' => $data['comment'] ?? null,
                'payment_type' => $data['payment_type'] ?? null,
                'delivery_type' => $data['delivery_type'] ?? null,
                'status' => $data['status'],
            ]);

            $order->positions()->delete();

            $products = Product::query()
                ->whereIn(
                    'id',
                    collect($data['positions'])->pluck('product_id')
                )
                ->get()
                ->keyBy('id');

            foreach ($data['positions'] as $position) {

                $product = $products->get($position['product_id']);

                if (!$product) {
                    throw new DomainException(
                        'Товар не найден.'
                    );
                }

                OrderPosition::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $position['quantity'],
                ]);
            }

            return $order->fresh([
                'client',
                'user',
                'positions.product',
            ]);
        });
    }

    /**
     * Удалить заказ.
     */
    public function delete(User $user, Order $order): void
    {
        $this->ensureOwner($user, $order);

        DB::transaction(function () use ($order) {

            $order->positions()->delete();
            $order->delete();

        });
    }

    /**
     * Проверяет, принадлежит ли заказ пользователю.
     *
     * @throws AuthorizationException
     */
    private function ensureOwner(
        User $user,
        Order $order
    ): void {
        if (
            $user->role === Role::EMPLOYEE ||
            $user->role === Role::ADMIN
        ) {
            return;
        }


        if ($order->user_id !== $user->id) {
            throw new AuthorizationException(
                'Вы не можете управлять этим заказом.'
            );
        }
    }

    /**
     * Проверяет, принадлежит ли клиент пользователю.
     *
     * @throws AuthorizationException
     */
    private function ensureClientOwner(
        User $user,
        int $clientId
    ): void {

        $exists = Client::query()
            ->whereKey($clientId)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            throw new AuthorizationException(
                'Вы не можете использовать этого клиента.'
            );
        }
    }
}
