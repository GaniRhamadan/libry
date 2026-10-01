<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Http\Requests\BookingRequest;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Models\RentalUnit;
use RentalHub\StarterKit\Services\BookingService;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = ($user->rental_role ?? 'customer') === UserRole::ADMIN->value;
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = RentalBooking::with(['user', 'unit', 'unit.category'])->latest();

        if (!$isAdmin) {
            // Pelanggan hanya boleh melihat booking miliknya sendiri
            $query->forUser((int) $user->getAuthIdentifier());
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $isAdmin): void {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('unit', function ($unitQuery) use ($search): void {
                        $unitQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });

                if ($isAdmin) {
                    $q->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            });
        }

        $bookings = $query->paginate(12)->withQueryString();

        return view('rental-hub::bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'currentStatus' => $statusFilter,
            'search' => $search,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedUnitId = $request->query('unit_id') ? (int) $request->query('unit_id') : null;
        $availableUnits = RentalUnit::available()->with('category')->orderBy('name')->get();

        return view('rental-hub::bookings.create', [
            'units' => $availableUnits,
            'selectedUnitId' => $selectedUnitId,
        ]);
    }

    public function store(BookingRequest $request, BookingService $bookingService): RedirectResponse
    {
        $userId = (int) $request->user()->getAuthIdentifier();
        $booking = $bookingService->createBooking($request->validated(), $userId);

        return redirect()->route('rental.bookings.show', $booking)->with(
            'success',
            (string) __('rental-hub::rental.booking_created')
        );
    }

    public function show(RentalBooking $booking, BookingService $bookingService): View
    {
        Gate::authorize('view', $booking);

        $booking->load(['user', 'unit', 'unit.category']);

        $projectedLateFee = 0;
        if ($booking->status === BookingStatus::ACTIVE && $booking->end_time->isPast()) {
            $projectedLateFee = $bookingService->calculateLateFee($booking, Carbon::now());
        }

        return view('rental-hub::bookings.show', [
            'booking' => $booking,
            'projectedLateFee' => $projectedLateFee,
        ]);
    }

    public function cancel(Request $request, RentalBooking $booking, BookingService $bookingService): RedirectResponse
    {
        Gate::authorize('cancel', $booking);

        $reason = $request->input('cancellation_reason');
        $bookingService->cancelBooking($booking, $reason ? (string) $reason : null);

        return back()->with('success', (string) __('rental-hub::rental.booking_cancelled'));
    }

    public function approve(RentalBooking $booking, BookingService $bookingService): RedirectResponse
    {
        Gate::authorize('approve', $booking);

        $bookingService->approveBooking($booking);

        return back()->with('success', (string) __('rental-hub::rental.booking_approved'));
    }

    public function reject(Request $request, RentalBooking $booking, BookingService $bookingService): RedirectResponse
    {
        Gate::authorize('reject', $booking);

        $reason = $request->input('cancellation_reason');
        $bookingService->rejectBooking($booking, $reason ? (string) $reason : null);

        return back()->with('success', (string) __('rental-hub::rental.booking_rejected'));
    }

    public function activate(RentalBooking $booking, BookingService $bookingService): RedirectResponse
    {
        Gate::authorize('activate', $booking);

        $bookingService->markAsActive($booking);

        return back()->with('success', (string) __('rental-hub::rental.booking_activated'));
    }

    public function confirmReturn(
        Request $request,
        RentalBooking $booking,
        BookingService $bookingService
    ): RedirectResponse {
        Gate::authorize('return', $booking);

        $manualLateFee = $request->filled('manual_late_fee')
            ? (int) $request->input('manual_late_fee')
            : null;

        $bookingService->confirmReturn($booking, $manualLateFee);

        return back()->with('success', (string) __('rental-hub::rental.booking_returned'));
    }
}
