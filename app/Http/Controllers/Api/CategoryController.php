<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::withCount('products');

            // Filter by active status
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            $categories = $query->latest()->paginate($request->input('per_page', 15));

            return $this->successResponse(
                CategoryResource::collection($categories)->response()->getData(),
                'Categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve categories: ' . $e->getMessage(), 500);
        }
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());

            return $this->successResponse(
                new CategoryResource($category),
                'Category created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    public function show(Category $category): JsonResponse
    {
        try {
            $category->loadCount('products');

            return $this->successResponse(
                new CategoryResource($category),
                'Category retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve category: ' . $e->getMessage(), 500);
        }
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category->update($request->validated());

            return $this->successResponse(
                new CategoryResource($category),
                'Category updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            if ($category->products()->count() > 0) {
                return $this->errorResponse(
                    'Cannot delete category with existing products',
                    400
                );
            }

            $category->delete();

            return $this->successResponse(
                null,
                'Category deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete category: ' . $e->getMessage(), 500);
        }
    }
}
