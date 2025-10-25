<?php

namespace App\Models;

use App\Enums\WarehouseTransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'type' => WarehouseTransactionType::class,
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType($query, WarehouseTransactionType $type)
    {
        return $query->where('type', $type);
    }
}