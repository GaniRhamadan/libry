<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('rental-hub::rental.status_pending'),
            self::APPROVED => __('rental-hub::rental.status_approved'),
            self::REJECTED => __('rental-hub::rental.status_rejected'),
            self::ACTIVE => __('rental-hub::rental.status_active'),
            self::RETURNED => __('rental-hub::rental.status_returned'),
            self::CANCELLED => __('rental-hub::rental.status_cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
            self::APPROVED => 'bg-blue-50 text-blue-700 ring-1 ring-blue-700/20',
            self::REJECTED => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
            self::ACTIVE => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
            self::RETURNED => 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20',
            self::CANCELLED => 'bg-zinc-100 text-zinc-600 ring-1 ring-zinc-500/20',
        };
    }

    public function isCancelable(): bool
    {
        return $this === self::PENDING;
    }
}
