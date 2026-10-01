<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Enums;

enum UnitStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => __('rental-hub::rental.unit_status_available'),
            self::RENTED => __('rental-hub::rental.unit_status_rented'),
            self::MAINTENANCE => __('rental-hub::rental.unit_status_maintenance'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
            self::RENTED => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/20',
            self::MAINTENANCE => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
        };
    }
}
