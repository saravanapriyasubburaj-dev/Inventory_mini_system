<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->validated('customer'),
                $request->validated('items')
            );

            return response()->json([
                'message' => 'Order created successfully.',
                'data'    => $order,
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(string $email): JsonResponse
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.',
                'data'    => [],
            ], 404);
        }

        $orders = Order::where('customer_id', $customer->id)
            ->with(['items.product'])
            ->latest()
            ->get();

        return response()->json([
            'customer' => $customer->only(['id', 'name', 'email']),
            'data'     => $orders,
        ]);
    }
}