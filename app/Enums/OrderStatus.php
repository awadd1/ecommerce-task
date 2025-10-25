<?php

namespace App\Enums;

enum OrderStatus: string
{
  case PENDING = 'pending';
  case PROCESSING = 'processing';
  case SHIPPED = 'shipped';
  case COMPLETED = 'completed';
  case CANCELLED = 'cancelled';

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public function label(): string
  {
    return match ($this) {
      self::PENDING => 'Pending',
      self::PROCESSING => 'Processing',
      self::SHIPPED => 'Shipped',
      self::COMPLETED => 'Completed',
      self::CANCELLED => 'Cancelled',
    };
  }

  public function canBeCancelled(): bool
  {
    return in_array($this, [self::PENDING, self::PROCESSING]);
  }

  public function nextStatuses(): array
  {
    return match ($this) {
      self::PENDING => [self::PROCESSING, self::CANCELLED],
      self::PROCESSING => [self::SHIPPED, self::CANCELLED],
      self::SHIPPED => [self::COMPLETED],
      self::COMPLETED => [],
      self::CANCELLED => [],
    };
  }

}

