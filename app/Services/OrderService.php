<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{

  public function createOrder(int $userId, array $data): Order
  {
    return DB::transaction(function () use ($userId, $data) {

      $order = Order::create([
        'user_id' => $userId,
        'status' => OrderStatus::PENDING,
        'total_amount' => 0,
      ]);

      $totalAmount = 0;

      foreach ($data['items'] as $item) {
        $product = Product::findOrFail($item['product_id']);

        if (!$product->isInStock($item['quantity'])) {
          throw new \Exception("Product '{$product->name}' is out of stock ");
        }

        OrderItem::create([
          'order_id' => $order->id,
          'product_id' => $product->id,
          'quantity' => $item['quantity'],
          'price' => $product->price,
        ]);

        $product->decreaseStock($item['quantity']);
        $totalAmount += $product->price * $item['quantity'];
      }
      $order->update(['total_amount' => $totalAmount]);
      return $order->load(['items.product', 'user']);
    });
  }

  public function updateOrderStatus(Order $order, OrderStatus $newStatus, ?string $notes = null): Order
  {
    if (!$order->canUpdateToStatus($newStatus)) {
      throw new \Exception("Cannot update order from {$order->status->label()} to {$newStatus->label()}");
    }

    $order->update([
      'status' => $newStatus,
      'notes' => $notes ?? $order->notes,
    ]);

    return $order->fresh(['items.product', 'user']);
  }

  public function cancelOrder(Order $order, ?string $notes = null): Order
  {
    if (!$order->status->canBeCancelled()) {
      throw new \Exception("Order cannot be cancelled in {$order->status->label()} status");
    }

    return DB::transaction(function () use ($order, $notes) {

      foreach ($order->items as $item) {
        $item->product->increaseStock($item->quantity);
      }

      $order->update([
        'status' => OrderStatus::CANCELLED,
        'notes' => $notes ?? $order->notes,
      ]);      

      return $order->fresh(['items.product', 'user']);
    });
  }


  public function getUserOrders(User $user, array $filters = [])
  {
    $query = Order::with(['items.product'])->forUser($user->id);

    if (isset($filters['status'])) {
      $query->where('status', $filters['status']);
    }

    return $query->latest()->paginate($filters['per_page'] ?? 10);
  }

  public function getAllOrders(array $filters = [])
  {
    $query = Order::with(['items.product', 'user']);

    if (isset($filters['status'])) {
      $query->where('status', $filters['status']);
    }

    if (isset($filters['user_id'])) {
      $query->forUser($filters['user_id']);
    }

    return $query->latest()->paginate($filters['per_page'] ?? 10);
  }
}