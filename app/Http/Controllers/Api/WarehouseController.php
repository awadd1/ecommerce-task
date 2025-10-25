<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\WarehouseTransactionResource;
use App\Models\Product;
use App\Services\WarehouseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\WarehouseTransactionType;

class WarehouseController extends Controller
{
    use ApiResponse;

    protected WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function adjustStock(StockAdjustmentRequest $request): JsonResponse
    {
        try {
            $transaction = $this->warehouseService->adjustStock(
                productId: $request->input('product_id'),
                type: WarehouseTransactionType::from($request->input('type')),
                quantity: $request->input('quantity'),
                userId: $request->user()->id,
                notes: $request->input('notes')
            );

            return $this->successResponse(
                new WarehouseTransactionResource($transaction->load(['product', 'user'])),
                'Stock adjusted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to adjust stock: ' . $e->getMessage(), 500);
        }
    }

    public function getProductHistory(Request $request, Product $product): JsonResponse
    {
        try {
            $transactions = $this->warehouseService->getProductStockHistory(
                $product->id,
                [
                    'type' => $request->input('type'),
                    'from_date' => $request->input('from_date'),
                    'to_date' => $request->input('to_date'),
                    'per_page' => $request->input('per_page', 10),
                ]
            );

            return $this->successResponse(
                WarehouseTransactionResource::collection($transactions)->response()->getData(),
                'Product stock history retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve history: ' . $e->getMessage(), 500);
        }
    }

    public function getAllTransactions(Request $request): JsonResponse
    {
        try {
            if (!$request->user()->canManageProducts()) {
                return $this->forbiddenResponse('You do not have permission to view transactions');
            }

            $transactions = $this->warehouseService->getAllTransactions([
                'product_id' => $request->input('product_id'),
                'type' => $request->input('type'),
                'user_id' => $request->input('user_id'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
                'per_page' => $request->input('per_page', 15),
            ]);

            return $this->successResponse(
                WarehouseTransactionResource::collection($transactions)->response()->getData(),
                'Warehouse transactions retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve transactions: ' . $e->getMessage(), 500);
        }
    }

    public function getLowStockProducts(Request $request): JsonResponse
    {
        try {
            if (!$request->user()->canManageProducts()) {
                return $this->forbiddenResponse('You do not have permission to view low stock products');
            }

            $threshold = $request->input('threshold', 10);
            $products = $this->warehouseService->getLowStockProducts($threshold);

            return $this->successResponse(
                ProductResource::collection($products),
                'Low stock products retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve low stock products: ' . $e->getMessage(), 500);
        }
    }

    public function getStockSummary(Request $request): JsonResponse
    {
        try {
            if (!$request->user()->canManageProducts()) {
                return $this->forbiddenResponse('You do not have permission to view stock summary');
            }

            $summary = $this->warehouseService->getStockSummary();

            return $this->successResponse(
                $summary,
                'Stock summary retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve stock summary: ' . $e->getMessage(), 500);
        }
    }
}