<?php

namespace App\Services;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderPosition;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Получить список заказов.
     */
    public function all(Request $request)
    {
        $query = Order::with([
            'client',
            'user',
            'positions.product',
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($client) use ($search) {
                        $client->where('fcs', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')
            );
        }

        if ($request->filled('client_id')) {
            $query->where(
                'client_id',
                $request->integer('client_id')
            );
        }

        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->integer('user_id')
            );
        }

        if (
            !$request->has('page') &&
            !$request->has('perPage')
        ) {
            return $query
                ->latest()
                ->get();
        }

        $orders = $query
            ->latest()
            ->paginate(
                $request->integer('perPage', 15)
            );

        return response()->json([
            'data' => $orders->items(),
            'pagination' => [
                'page' => $orders->currentPage(),
                'perPage' => $orders->perPage(),
                'total' => $orders->total(),
                'lastPage' => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * Получить заказ.
     */
    public function getById(int $id): ?Order
    {
        return Order::with([
            'client',
            'user',
            'positions.product',
        ])->find($id);
    }

    /**
     * Получить список заказов пользователя
     */
    public function my(Request $request, int $userId)
    {
        $query = Order::with([
            'client',
            'user',
            'positions.product',
        ])->where('user_id', $userId);

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')
            );
        }

        if (
            !$request->has('page') &&
            !$request->has('perPage')
        ) {
            return $query
                ->latest()
                ->get();
        }

        $orders = $query
            ->latest()
            ->paginate(
                $request->integer('perPage', 15)
            );

        return response()->json([
            'data' => $orders->items(),
            'pagination' => [
                'page' => $orders->currentPage(),
                'perPage' => $orders->perPage(),
                'total' => $orders->total(),
                'lastPage' => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * Создать заказ.
     */
    public function create(
        CreateOrderRequest $request,
        int $userId
    ): Order {

        return DB::transaction(function () use (
            $request,
            $userId
        ) {

            $order = Order::create([
                'user_id' => $userId,
                'client_id' => $request->client_id,
                'comment' => $request->comment,
                'status' => $request->status,
            ]);

            foreach ($request->positions as $position) {

                $product = Product::findOrFail(
                    $position['product_id']
                );

                OrderPosition::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $position['quantity'],
                ]);

            }

            return $order->load([
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
        Order $order,
        UpdateOrderRequest $request
    ): Order {

        return DB::transaction(function () use (
            $order,
            $request
        ) {

            $order->update([
                'client_id' => $request->client_id,
                'comment' => $request->comment,
                'status' => $request->status,
            ]);

            $order->positions()->delete();

            foreach ($request->positions as $position) {

                $product = Product::findOrFail(
                    $position['product_id']
                );

                OrderPosition::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $position['quantity'],
                ]);
            }

            return $order->load([
                'client',
                'user',
                'positions.product',
            ]);
        });
    }

    /**
     * Удалить заказ.
     */
    public function delete(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->positions()->delete();
            $order->delete();
        });
    }
}
