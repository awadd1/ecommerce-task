<?php

namespace App\Traits;

use App\Enums\UserRole;

trait HasRoles
{

  public function hasRole(UserRole|string $role): bool
  {
    if ($role instanceof UserRole) {
      return $this->role->value === $role->value;
    }

    return $this->role->value === $role;
  }

  public function hasAnyRole(array $roles): bool
  {
    foreach ($roles as $role) {
      if ($this->hasRole($role)) {
        return true;
      }
    }

    return false;
  }

  public function isAdmin(): bool
  {
    return $this->hasRole(UserRole::ADMIN);
  }

  public function isSeller(): bool
  {
    return $this->hasRole(UserRole::SELLER);
  }

  public function isCustomer(): bool
  {
    return $this->hasRole(UserRole::CUSTOMER);
  }

  public function canManageProducts(): bool
  {
    return in_array($this->role->value, [
      UserRole::ADMIN->value,
      UserRole::SELLER->value
    ]);
  }
}