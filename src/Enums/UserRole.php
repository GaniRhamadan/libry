<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('rental-hub::rental.role_admin'),
            self::CUSTOMER => __('rental-hub::rental.role_customer'),
        };
    }
}
