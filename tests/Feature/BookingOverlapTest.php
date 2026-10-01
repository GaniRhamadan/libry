<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Models\RentalCategory;
use RentalHub\StarterKit\Models\RentalUnit;
use RentalHub\StarterKit\Services\BookingService;
use RentalHub\StarterKit\Tests\Fixtures\User;
use RentalHub\StarterKit\Tests\TestCase;

class BookingOverlapTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;
    private User $user;
    private RentalCategory $category;
    private RentalUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BookingService::class);

        $this->user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'password' => bcrypt('password'),
            'rental_role' => 'customer',
        ]);

        $this->category = RentalCategory::create([
            'name' => 'MPV',
            'slug' => 'mpv',
        ]);

        $this->unit = RentalUnit::create([
            'category_id' => $this->category->id,
            'name' => 'Toyota Avanza',
            'code' => 'B 1111 RNT',
            'price_per_hour' => 30000,
            'price_per_day' => 300000,
            'status' => UnitStatus::AVAILABLE->value,
            'late_fee_per_hour' => 50000,
        ]);
    }

    public function test_it_detects_identical_overlapping_range(): void
    {
        $start = Carbon::parse('2026-10-10 10:00:00');
        $end = Carbon::parse('2026-10-12 10:00:00');

        RentalBooking::create([
            'booking_code' => 'RNT-20261010-001',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 48,
            'base_price' => 600000,
            'late_fee' => 0,
            'total_price' => 600000,
            'status' => BookingStatus::APPROVED,
        ]);

        // Same time range must report overlap
        $this->assertTrue($this->service->hasOverlap($this->unit->id, $start, $end));

        // Attempting to create booking with identical range must throw ValidationException
        $this->expectException(ValidationException::class);
        $this->service->createBooking([
            'unit_id' => $this->unit->id,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
        ], $this->user->id);
    }

    public function test_it_detects_partial_overlapping_ranges(): void
    {
        $existingStart = Carbon::parse('2026-10-10 10:00:00');
        $existingEnd = Carbon::parse('2026-10-12 10:00:00');

        RentalBooking::create([
            'booking_code' => 'RNT-20261010-002',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
            'total_hours' => 48,
            'base_price' => 600000,
            'late_fee' => 0,
            'total_price' => 600000,
            'status' => BookingStatus::PENDING,
        ]);

        // 1. Starts before and ends inside existing booking
        $this->assertTrue($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-09 12:00:00'),
            Carbon::parse('2026-10-11 12:00:00')
        ));

        // 2. Starts inside and ends after existing booking
        $this->assertTrue($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-11 12:00:00'),
            Carbon::parse('2026-10-13 12:00:00')
        ));

        // 3. Completely inside existing booking
        $this->assertTrue($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-11 00:00:00'),
            Carbon::parse('2026-10-11 18:00:00')
        ));

        // 4. Encompasses existing booking entirely
        $this->assertTrue($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-09 00:00:00'),
            Carbon::parse('2026-10-13 00:00:00')
        ));
    }

    public function test_it_permits_non_overlapping_ranges(): void
    {
        $existingStart = Carbon::parse('2026-10-10 10:00:00');
        $existingEnd = Carbon::parse('2026-10-12 10:00:00');

        RentalBooking::create([
            'booking_code' => 'RNT-20261010-003',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => $existingStart,
            'end_time' => $existingEnd,
            'total_hours' => 48,
            'base_price' => 600000,
            'late_fee' => 0,
            'total_price' => 600000,
            'status' => BookingStatus::ACTIVE,
        ]);

        // Completely before existing booking
        $this->assertFalse($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-08 10:00:00'),
            Carbon::parse('2026-10-10 10:00:00')
        ));

        // Completely after existing booking
        $this->assertFalse($this->service->hasOverlap(
            $this->unit->id,
            Carbon::parse('2026-10-12 10:00:00'),
            Carbon::parse('2026-10-14 10:00:00')
        ));
    }

    public function test_cancelled_and_rejected_bookings_do_not_block(): void
    {
        $start = Carbon::parse('2026-10-10 10:00:00');
        $end = Carbon::parse('2026-10-12 10:00:00');

        RentalBooking::create([
            'booking_code' => 'RNT-20261010-004',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 48,
            'base_price' => 600000,
            'late_fee' => 0,
            'total_price' => 600000,
            'status' => BookingStatus::CANCELLED,
        ]);

        $this->assertFalse($this->service->hasOverlap($this->unit->id, $start, $end));
    }

    public function test_different_unit_does_not_conflict(): void
    {
        $start = Carbon::parse('2026-10-10 10:00:00');
        $end = Carbon::parse('2026-10-12 10:00:00');

        RentalBooking::create([
            'booking_code' => 'RNT-20261010-005',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 48,
            'base_price' => 600000,
            'late_fee' => 0,
            'total_price' => 600000,
            'status' => BookingStatus::APPROVED,
        ]);

        $otherUnit = RentalUnit::create([
            'category_id' => $this->category->id,
            'name' => 'Daihatsu Xenia',
            'code' => 'B 2222 RNT',
            'price_per_hour' => 25000,
            'price_per_day' => 250000,
            'status' => UnitStatus::AVAILABLE->value,
        ]);

        $this->assertFalse($this->service->hasOverlap($otherUnit->id, $start, $end));
    }

    public function test_pricing_calculation_under_and_over_24_hours(): void
    {
        // 5 hours: 5 * 30.000 = 150.000
        $shortStart = Carbon::parse('2026-10-10 08:00:00');
        $shortEnd = Carbon::parse('2026-10-10 13:00:00');
        $shortCalc = $this->service->calculateDurationAndPrice($this->unit, $shortStart, $shortEnd);

        $this->assertEquals(5, $shortCalc['total_hours']);
        $this->assertEquals(150000, $shortCalc['base_price']);

        // 27 hours (1 day + 3 hours): (1 * 300.000) + (3 * 30.000) = 390.000
        $longStart = Carbon::parse('2026-10-10 08:00:00');
        $longEnd = Carbon::parse('2026-10-11 11:00:00');
        $longCalc = $this->service->calculateDurationAndPrice($this->unit, $longStart, $longEnd);

        $this->assertEquals(27, $longCalc['total_hours']);
        $this->assertEquals(390000, $longCalc['base_price']);
    }

    public function test_late_fee_calculation(): void
    {
        $booking = RentalBooking::create([
            'booking_code' => 'RNT-20261010-006',
            'user_id' => $this->user->id,
            'unit_id' => $this->unit->id,
            'start_time' => Carbon::parse('2026-10-10 10:00:00'),
            'end_time' => Carbon::parse('2026-10-11 10:00:00'),
            'total_hours' => 24,
            'base_price' => 300000,
            'late_fee' => 0,
            'total_price' => 300000,
            'status' => BookingStatus::ACTIVE,
        ]);

        // On time: fee = 0
        $onTime = Carbon::parse('2026-10-11 10:00:00');
        $this->assertEquals(0, $this->service->calculateLateFee($booking, $onTime));

        // 2 hours 15 minutes late -> ceil to 3 hours late * 50.000 = 150.000
        $lateTime = Carbon::parse('2026-10-11 12:15:00');
        $this->assertEquals(150000, $this->service->calculateLateFee($booking, $lateTime));
    }
}
