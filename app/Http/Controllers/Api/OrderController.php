<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;

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
        $result = $this->service->all($request);

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            $data = $result->getData(true);

            return response()->json([
                'data' => OrderResource::collection(
                    collect($data['data'])
                ),
                'pagination' => $data['pagination'],
            ]);
        }

        return OrderResource::collection($result);
    }

    /**
     * Получить заказ.
     */
    public function show(int $id): OrderResource
    {
        $order = $this->service->getById($id);

        abort_if(
            !$order,
            Response::HTTP_NOT_FOUND,
            'Order not found'
        );

        return new OrderResource($order);
    }

    /**
     * Создать заказ.
     */
    public function store(
        CreateOrderRequest $request
    ): OrderResource {

        $order = $this->service->create(
            $request,
            auth()->id()
        );

        return new OrderResource($order);
    }

    /**
     * Обновить заказ.
     */
    public function update(
        UpdateOrderRequest $request,
        int $id
    ): OrderResource {

        $order = Order::find($id);

        abort_if(
            !$order,
            Response::HTTP_NOT_FOUND,
            'Order not found'
        );

        $order = $this->service->update(
            $order,
            $request
        );

        return new OrderResource($order);
    }

    /**
     * Удалить заказ.
     */
    public function destroy(int $id)
    {
        $order = Order::find($id);

        abort_if(
            !$order,
            Response::HTTP_NOT_FOUND,
            'Order not found'
        );

        $this->service->delete($order);

        return response()->noContent();
    }

    public function my(Request $request)
    {
        $result = $this->service->my(
            $request,
            $request->user()->id
        );

        if ($result instanceof \Illuminate\Http\JsonResponse) {

            $data = $result->getData(true);

            return response()->json([
                'data' => OrderResource::collection(
                    collect($data['data'])
                ),
                'pagination' => $data['pagination'],
            ]);
        }

        return OrderResource::collection($result);
    }
}
