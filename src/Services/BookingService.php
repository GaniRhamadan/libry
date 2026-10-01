<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Models\RentalUnit;

class BookingService
{
    /**
     * Hitung total jam dan tarif dasar sewa berdasarkan tarif per jam dan per hari.
     * Aturan: durasi >= 24 jam menggunakan tarif harian + sisa jam, durasi < 24 jam tarif per jam.
     *
     * @return array{total_hours: int, base_price: int}
     */
    public function calculateDurationAndPrice(
        RentalUnit $unit,
        DateTimeInterface $start,
        DateTimeInterface $end
    ): array {
        $startTimestamp = $start->getTimestamp();
        $endTimestamp = $end->getTimestamp();

        $diffSeconds = max(0, $endTimestamp - $startTimestamp);
        $totalHours = (int) ceil($diffSeconds / 3600);

        if ($totalHours <= 0) {
            $totalHours = 1;
        }

        if ($totalHours >= 24) {
            $days = intdiv($totalHours, 24);
            $remainingHours = $totalHours % 24;
            $basePrice = ($days * (int) $unit->price_per_day) + ($remainingHours * (int) $unit->price_per_hour);
        } else {
            $basePrice = $totalHours * (int) $unit->price_per_hour;
        }

        return [
            'total_hours' => $totalHours,
            'base_price' => $basePrice,
        ];
    }

    /**
     * Cek apakah rentang waktu bertabrakan dengan booking berstatus pending/approved/active.
     * Logika overlap: start < existing_end AND end > existing_start.
     */
    public function hasOverlap(
        int $unitId,
        DateTimeInterface $start,
        DateTimeInterface $end,
        ?int $ignoreBookingId = null,
        bool $lock = false
    ): bool {
        $query = RentalBooking::where('unit_id', $unitId)
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::APPROVED->value,
                BookingStatus::ACTIVE->value,
            ])
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);

        if ($ignoreBookingId !== null) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }

    /**
     * Hitung denda keterlambatan pengembalian: jam terlambat dikali tarif denda per jam.
     */
    public function calculateLateFee(RentalBooking $booking, DateTimeInterface $returnTime): int
    {
        if ($returnTime->getTimestamp() <= $booking->end_time->getTimestamp()) {
            return 0;
        }

        $overdueSeconds = $returnTime->getTimestamp() - $booking->end_time->getTimestamp();
        $overdueHours = (int) ceil($overdueSeconds / 3600);

        $hourlyFeeRate = $booking->unit->late_fee_per_hour
            ?? (int) config('rental-hub.late_fee_per_hour', 50000);

        return $overdueHours * $hourlyFeeRate;
    }

    /**
     * Buat data booking baru dengan proteksi race condition melalui lockForUpdate & DB transaction.
     *
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function createBooking(array $data, int $userId): RentalBooking
    {
        return DB::transaction(function () use ($data, $userId): RentalBooking {
            /** @var RentalUnit $unit */
            $unit = RentalUnit::where('id', $data['unit_id'])->lockForUpdate()->firstOrFail();

            if ($unit->status === UnitStatus::MAINTENANCE) {
                throw ValidationException::withMessages([
                    'unit_id' => __('rental-hub::rental.unit_not_available'),
                ]);
            }

            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);

            $maxDays = (int) config('rental-hub.max_booking_days', 30);
            if ($start->diffInDays($end) > $maxDays) {
                throw ValidationException::withMessages([
                    'end_time' => __('rental-hub::rental.booking_duration_exceeded', ['max' => $maxDays]),
                ]);
            }

            // Validasi overlap jadwal dengan penguncian baris untuk mencegah race condition
            if ($this->hasOverlap($unit->id, $start, $end, null, true)) {
                throw ValidationException::withMessages([
                    'unit_id' => __('rental-hub::rental.unit_overlap_error'),
                ]);
            }

            $calc = $this->calculateDurationAndPrice($unit, $start, $end);
            $bookingCode = 'RNT-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            return RentalBooking::create([
                'booking_code' => $bookingCode,
                'user_id' => $userId,
                'unit_id' => $unit->id,
                'start_time' => $start,
                'end_time' => $end,
                'total_hours' => $calc['total_hours'],
                'base_price' => $calc['base_price'],
                'late_fee' => 0,
                'total_price' => $calc['base_price'],
                'status' => BookingStatus::PENDING,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Setujui permohonan booking oleh Admin.
     */
    public function approveBooking(RentalBooking $booking): RentalBooking
    {
        return DB::transaction(function () use ($booking): RentalBooking {
            $locked = RentalBooking::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $locked->status = BookingStatus::APPROVED;
            $locked->save();

            return $locked;
        });
    }

    /**
     * Tolak permohonan booking oleh Admin.
     */
    public function rejectBooking(RentalBooking $booking, ?string $reason = null): RentalBooking
    {
        return DB::transaction(function () use ($booking, $reason): RentalBooking {
            $locked = RentalBooking::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $locked->status = BookingStatus::REJECTED;
            $locked->cancellation_reason = $reason;
            $locked->save();

            return $locked;
        });
    }

    /**
     * Batalkan booking oleh Pelanggan (hanya berlaku jika status masih pending).
     */
    public function cancelBooking(RentalBooking $booking, ?string $reason = null): RentalBooking
    {
        return DB::transaction(function () use ($booking, $reason): RentalBooking {
            $locked = RentalBooking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if (!$locked->status->isCancelable()) {
                throw ValidationException::withMessages([
                    'status' => 'Booking tidak dapat dibatalkan karena sudah diproses atau aktif.',
                ]);
            }

            $locked->status = BookingStatus::CANCELLED;
            $locked->cancellation_reason = $reason;
            $locked->save();

            return $locked;
        });
    }

    /**
     * Tandai armada telah diambil pelanggan: booking aktif dan unit berubah menjadi rented.
     */
    public function markAsActive(RentalBooking $booking): RentalBooking
    {
        return DB::transaction(function () use ($booking): RentalBooking {
            $lockedBooking = RentalBooking::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $unit = RentalUnit::where('id', $lockedBooking->unit_id)->lockForUpdate()->firstOrFail();

            $lockedBooking->status = BookingStatus::ACTIVE;
            $lockedBooking->save();

            $unit->status = UnitStatus::RENTED;
            $unit->save();

            return $lockedBooking;
        });
    }

    /**
     * Konfirmasi pengembalian unit: hitung denda otomatis atau terapkan denda manual dari admin.
     */
    public function confirmReturn(
        RentalBooking $booking,
        ?int $manualLateFee = null,
        ?DateTimeInterface $returnTime = null
    ): RentalBooking {
        return DB::transaction(function () use ($booking, $manualLateFee, $returnTime): RentalBooking {
            $lockedBooking = RentalBooking::where('id', $booking->id)->lockForUpdate()->firstOrFail();
            $unit = RentalUnit::where('id', $lockedBooking->unit_id)->lockForUpdate()->firstOrFail();

            $actualReturn = $returnTime !== null ? Carbon::instance($returnTime) : Carbon::now();

            $lateFee = $manualLateFee !== null
                ? max(0, $manualLateFee)
                : $this->calculateLateFee($lockedBooking, $actualReturn);

            $lockedBooking->actual_return_time = $actualReturn;
            $lockedBooking->late_fee = $lateFee;
            $lockedBooking->total_price = (int) $lockedBooking->base_price + $lateFee;
            $lockedBooking->status = BookingStatus::RETURNED;
            $lockedBooking->save();

            $unit->status = UnitStatus::AVAILABLE;
            $unit->save();

            return $lockedBooking;
        });
    }
}
