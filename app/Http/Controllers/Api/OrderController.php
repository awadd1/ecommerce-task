<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->isCustomer()) {
                $orders = $this->orderService->getUserOrders($user, [
                    'status' => $request->input('status'),
                    'per_page' => $request->input('per_page', 10),
                ]);
            } else {
                $orders = $this->orderService->getAllOrders([
                    'status' => $request->input('status'),
                    'user_id' => $request->input('user_id'),
                    'per_page' => $request->input('per_page', 10),
                ]);
            }

            return $this->successResponse(
                OrderResource::collection($orders)->response()->getData(),
                'Orders retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve orders: ' . $e->getMessage(), 500);
        }
    }

    public function store(OrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->user()->id,
                $request->validated()
            );

            return $this->successResponse(
                new OrderResource($order),
                'Order created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        try {
            if ($request->user()->isCustomer() && $order->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('You do not have permission to view this order');
            }

            $order->load(['items.product', 'user']);

            return $this->successResponse(
                new OrderResource($order),
                'Order retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve order: ' . $e->getMessage(), 500);
        }
    }

    public function updateStatus(OrderRequest $request, Order $order): JsonResponse
    {
        try {
            if (!$request->user()->canManageProducts()) {
                return $this->forbiddenResponse('You do not have permission to update order status');
            }

            $newStatus = OrderStatus::from($request->input('status'));

            $order = $this->orderService->updateOrderStatus(
                $order,
                $newStatus,
                $request->input('notes')
            );

            return $this->successResponse(
                new OrderResource($order),
                'Order status updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update order status: ' . $e->getMessage(), 500);
        }
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        try {

            if ($request->user()->isCustomer() && $order->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('You do not have permission to cancel this order');
            }

            $order = $this->orderService->cancelOrder(
                $order,
                $request->input('notes')
            );

            return $this->successResponse(
                new OrderResource($order),
                'Order cancelled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to cancel order: ' . $e->getMessage(), 500);
        }
    }
}
