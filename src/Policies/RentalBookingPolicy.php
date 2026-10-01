<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Models\RentalBooking;

class RentalBookingPolicy
{
    private function isAdmin(Authenticatable $user): bool
    {
        return ($user->rental_role ?? null) === UserRole::ADMIN->value;
    }

    public function view(Authenticatable $user, RentalBooking $booking): bool
    {
        return $this->isAdmin($user) || (int) $booking->user_id === (int) $user->getAuthIdentifier();
    }

    public function cancel(Authenticatable $user, RentalBooking $booking): bool
    {
        $canAccess = $this->isAdmin($user) || (int) $booking->user_id === (int) $user->getAuthIdentifier();
        return $canAccess && $booking->status === BookingStatus::PENDING;
    }

    public function approve(Authenticatable $user, RentalBooking $booking): bool
    {
        return $this->isAdmin($user) && $booking->status === BookingStatus::PENDING;
    }

    public function reject(Authenticatable $user, RentalBooking $booking): bool
    {
        return $this->isAdmin($user) && $booking->status === BookingStatus::PENDING;
    }

    public function activate(Authenticatable $user, RentalBooking $booking): bool
    {
        return $this->isAdmin($user) && $booking->status === BookingStatus::APPROVED;
    }

    public function return(Authenticatable $user, RentalBooking $booking): bool
    {
        return $this->isAdmin($user) && $booking->status === BookingStatus::ACTIVE;
    }
}
