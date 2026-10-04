<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Models\RentalUnit;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $userRole = (string) ($user->rental_role ?? 'customer');

        if ($userRole === UserRole::ADMIN->value) {
            $now = Carbon::now();

            $stats = [
                'total_units' => RentalUnit::count(),
                'available_units' => RentalUnit::where('status', UnitStatus::AVAILABLE->value)->count(),
                'rented_units' => RentalUnit::where('status', UnitStatus::RENTED->value)->count(),
                'maintenance_units' => RentalUnit::where('status', UnitStatus::MAINTENANCE->value)->count(),
                'pending_bookings' => RentalBooking::where('status', BookingStatus::PENDING->value)->count(),
                'active_bookings' => RentalBooking::where('status', BookingStatus::ACTIVE->value)->count(),
                'revenue_this_month' => (int) RentalBooking::where('status', BookingStatus::RETURNED->value)
                    ->whereMonth('actual_return_time', $now->month)
                    ->whereYear('actual_return_time', $now->year)
                    ->sum('total_price'),
            ];

            $latestBookings = RentalBooking::with(['user', 'unit'])
                ->latest()
                ->limit(5)
                ->get();

            $overdueBookings = RentalBooking::with(['user', 'unit'])
                ->where('status', BookingStatus::ACTIVE->value)
                ->where('end_time', '<', $now)
                ->get();

            return view('rental-hub::dashboard.admin', [
                'stats' => $stats,
                'latestBookings' => $latestBookings,
                'overdueBookings' => $overdueBookings,
            ]);
        }

        $userId = (int) $user->getAuthIdentifier();

        $activeBookings = RentalBooking::with(['unit', 'unit.category'])
            ->where('user_id', $userId)
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::APPROVED->value,
                BookingStatus::ACTIVE->value,
            ])
            ->latest()
            ->get();

        $recentHistory = RentalBooking::with(['unit', 'unit.category'])
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $availableUnits = RentalUnit::available()
            ->with('category')
            ->latest()
            ->limit(3)
            ->get();

        return view('rental-hub::dashboard.customer', [
            'activeBookings' => $activeBookings,
            'recentHistory' => $recentHistory,
            'availableUnits' => $availableUnits,
        ]);
    }
}
