<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseTransactionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'product' => [
        'id' => $this->product->id,
        'name' => $this->product->name,
        'sku' => $this->product->sku,
      ],
      'user' => $this->when($this->user, function () {
        return [
          'id' => $this->user->id,
          'name' => $this->user->name,
          'role' => $this->user->role->value,
        ];
      }),
      'order_id' => $this->order_id,
      'type' => $this->type->value,
      'type_label' => $this->type->label(),
      'quantity' => $this->quantity,
      'stock_before' => $this->stock_before,
      'stock_after' => $this->stock_after,
      'notes' => $this->notes,
      'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
    ];
  }
}