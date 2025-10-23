<?php

namespace App\Enums;
enum UserRole: string
{
  case ADMIN = 'admin';
  case SELLER = 'seller';
  case CUSTOMER = 'customer';

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }
  public function label(): string
  {
    return match ($this) {
      self::ADMIN => 'Administrator',
      self::SELLER => 'Seller',
      self::CUSTOMER => 'Customer',
    };
  }

  public function isAdmin(): bool
  {
    return $this === self::ADMIN;
  }

  public function canManageProducts(): bool
  {
    return in_array($this, [self::ADMIN, self::SELLER]);
  }

}
