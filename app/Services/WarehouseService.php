<?php

namespace App\Services;

use App\Enums\WarehouseTransactionType;
use App\Models\Product;
use App\Models\WarehouseTransaction;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    public function adjustStock(
        int $productId,
        WarehouseTransactionType $type,
        int $quantity,
        int $userId,
        ?string $notes = null
    ): WarehouseTransaction {
        return DB::transaction(function () use ($productId, $type, $quantity, $userId, $notes) {
            $product = Product::findOrFail($productId);

            if ($type->isDeduction() && !$product->isInStock($quantity)) {
                throw new \Exception("Insufficient stock. Available: {$product->stock}, Requested: {$quantity}");
            }

            return $product->recordTransaction(
                type: $type,
                quantity: $quantity,
                userId: $userId,
                notes: $notes
            );
        });
    }

    public function getProductStockHistory(int $productId, array $filters = [])
    {
        $query = WarehouseTransaction::with(['user', 'order'])
            ->forProduct($productId);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getAllTransactions(array $filters = [])
    {
        $query = WarehouseTransaction::with(['product', 'user', 'order']);

        if (isset($filters['product_id'])) {
            $query->forProduct($filters['product_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::with('category')
            ->where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->get();
    }

    public function getStockSummary()
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'total_stock_value' => Product::sum(DB::raw('stock * price')),
        ];
    }
}