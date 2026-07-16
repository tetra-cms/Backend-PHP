<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderPositionResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service
    ) {
    }

    /**
     * Получить список заказов.
     */
    public function index(Request $request)
    {
        $orders = $this->service->all(
            $request->user(),
            $request->only([
                'search',
                'status',
                'client_id',
                'perPage',
            ])
        );

        if ($orders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            return response()->json([
                'data' => OrderResource::collection($orders->items()),
                'pagination' => [
                    'page' => $orders->currentPage(),
                    'perPage' => $orders->perPage(),
                    'total' => $orders->total(),
                    'lastPage' => $orders->lastPage(),
                ],
            ]);
        }

        return OrderResource::collection($orders);
    }

    /**
     * Получить заказ.
     */
    public function show(Request $request, Order $order): OrderResource
    {
        return new OrderResource(
            $this->service->find(
                $request->user(),
                $order
            )
        );
    }

    /**
     * Получить позиции заказа.
     */
    public function positions(
        Request $request,
        Order $order
    ) {
        $positions = $this->service->positions(
            $request->user(),
            $order,
            (int) $request->input('perPage', 15)
        );

        return response()->json([
            'data' => OrderPositionResource::collection(
                $positions->items()
            ),
            'pagination' => [
                'page' => $positions->currentPage(),
                'perPage' => $positions->perPage(),
                'total' => $positions->total(),
                'lastPage' => $positions->lastPage(),
            ],
        ]);
    }

    /**
     * Создать заказ.
     */
    public function store(
        CreateOrderRequest $request
    ): OrderResource {

        return new OrderResource(
            $this->service->create(
                $request->user(),
                $request->validated()
            )
        );
    }

    /**
     * Обновить заказ.
     */
    public function update(
        UpdateOrderRequest $request,
        Order $order
    ): OrderResource {

        return new OrderResource(
            $this->service->update(
                $request->user(),
                $order,
                $request->validated()
            )
        );
    }

    /**
     * Удалить заказ.
     */
    public function destroy(Order $order)
    {
        $this->service->delete(
            request()->user(),
            $order
        );

        return response()->noContent();
    }
}
