<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'seller']);

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            if ($request->boolean('in_stock')) {
                $query->inStock();
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');
            }

            $products = $query->latest()->paginate($request->input('per_page', default: 10));

            return $this->successResponse(
                ProductResource::collection($products)->response()->getData(),
                'Products retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve products: ' . $e->getMessage(), 500);
        }
    }

    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $product = Product::create($data);
            $product->load(['category', 'seller']);

            return $this->successResponse(
                new ProductResource($product),
                'Product created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            $product->load(['category', 'seller']);

            return $this->successResponse(
                new ProductResource($product),
                'Product retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve product: ' . $e->getMessage(), 500);
        }
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        try {
            if (!$request->user()->isAdmin() && $product->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('You do not have permission to update this product');
            }

            $product->update($request->validated());
            $product->load(['category', 'seller']);

            return $this->successResponse(
                new ProductResource($product),
                'Product updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update this product: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        try {
           
            if (!$request->user()->isAdmin() && $product->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('You do not have permissionn to delete this product');
            }

            if ($product->orderItems()->count() > 0) {
                return $this->errorResponse(
                    'Cannot delete product with existing orders',
                    400
                );
            }
            $product->delete();

            return $this->successResponse(
                null,
                'Product deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete this product: ' . $e->getMessage(), 500);
        }
    }

}