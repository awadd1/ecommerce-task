<?php

namespace App\Models;

use App\Enums\WarehouseTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'stock',
        'is_active',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    public function warehouseTransactions(): HasMany
    {
        return $this->hasMany(WarehouseTransaction::class);
    }

    public function recordTransaction(
        WarehouseTransactionType $type,
        int $quantity,
        ?int $userId = null,
        ?int $orderId = null,
        ?string $notes = null
    ): WarehouseTransaction {
        $stockBefore = $this->stock;

        if ($type->isDeduction()) {
            $this->decrement('stock', $quantity);
        } else {
            $this->increment('stock', $quantity);
        }

        $this->refresh();

        return $this->warehouseTransactions()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock,
            'notes' => $notes,
        ]);
    }
}

