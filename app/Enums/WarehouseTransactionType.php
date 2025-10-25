<?php

namespace App\Enums;

enum WarehouseTransactionType: string
{
  case INITIAL_STOCK = 'initial_stock';
  case STOCK_IN = 'stock_in';
  case STOCK_OUT = 'stock_out';
  case ORDER_DEDUCTION = 'order_deduction';
  case ORDER_RETURN = 'order_return';
  case MANUAL_ADJUSTMENT = 'manual_adjustment';
  case DAMAGED = 'damaged';
  case LOST = 'lost';

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public function label(): string
  {
    return match ($this) {
      self::INITIAL_STOCK => 'Initial Stock',
      self::STOCK_IN => 'Stock In',
      self::STOCK_OUT => 'Stock Out',
      self::ORDER_DEDUCTION => 'Order Deduction',
      self::ORDER_RETURN => 'Order Return',
      self::MANUAL_ADJUSTMENT => 'Manual Adjustment',
      self::DAMAGED => 'Damaged',
      self::LOST => 'Lost',
    };
  }

  public function isDeduction(): bool
  {
    return in_array($this, [
      self::STOCK_OUT,
      self::ORDER_DEDUCTION,
      self::DAMAGED,
      self::LOST,
    ]);
  }

  public function isAddition(): bool
  {
    return in_array($this, [
      self::INITIAL_STOCK,
      self::STOCK_IN,
      self::ORDER_RETURN,
    ]);
  }
}