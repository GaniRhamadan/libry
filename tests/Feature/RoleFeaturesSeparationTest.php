<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RentalHub\StarterKit\Enums\BookingStatus;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Models\RentalCategory;
use RentalHub\StarterKit\Models\RentalUnit;
use RentalHub\StarterKit\Tests\Fixtures\User;
use RentalHub\StarterKit\Tests\TestCase;

class RoleFeaturesSeparationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private User $otherCustomer;
    private RentalCategory $category;
    private RentalUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Rental',
            'email' => 'admin@rental.test',
            'password' => bcrypt('password'),
            'rental_role' => UserRole::ADMIN->value,
            'phone' => '081200000001',
        ]);

        $this->customer = User::create([
            'name' => 'Budi Penyewa',
            'email' => 'budi@gmail.com',
            'password' => bcrypt('password'),
            'rental_role' => UserRole::CUSTOMER->value,
            'phone' => '081200000002',
        ]);

        $this->otherCustomer = User::create([
            'name' => 'Siti Penyewa Lain',
            'email' => 'siti@gmail.com',
            'password' => bcrypt('password'),
            'rental_role' => UserRole::CUSTOMER->value,
            'phone' => '081200000003',
        ]);

        $this->category = RentalCategory::create([
            'name' => 'City Car',
            'slug' => 'city-car',
        ]);

        $this->unit = RentalUnit::create([
            'category_id' => $this->category->id,
            'name' => 'Honda Brio Satya',
            'code' => 'B 1234 ABC',
            'price_per_hour' => 25000,
            'price_per_day' => 250000,
            'status' => UnitStatus::AVAILABLE->value,
            'late_fee_per_hour' => 40000,
        ]);
    }

    public function test_customer_can_access_catalog_and_view_available_vehicles(): void
    {
        $response = $this->actingAs($this->customer)->get(route('rental.catalog'));

        $response->assertStatus(200);
        $response->assertSee('Honda Brio Satya');
        $response->assertSee('City Car');
        $response->assertSee(route('rental.bookings.create', ['unit_id' => $this->unit->id]));
    }

    public function test_customer_cannot_access_admin_units_crud(): void
    {
        // View units index
        $resIndex = $this->actingAs($this->customer)->get(route('rental.units.index'));
        $resIndex->assertStatus(403);

        // View unit create form
        $resCreate = $this->actingAs($this->customer)->get(route('rental.units.create'));
        $resCreate->assertStatus(403);

        // Submit new unit
        $resStore = $this->actingAs($this->customer)->post(route('rental.units.store'), [
            'category_id' => $this->category->id,
            'name' => 'Unit Ilegal',
            'code' => 'B 9999 HCK',
            'price_per_hour' => 50000,
            'price_per_day' => 500000,
            'status' => UnitStatus::AVAILABLE->value,
        ]);
        $resStore->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_categories_crud(): void
    {
        // View categories index
        $resIndex = $this->actingAs($this->customer)->get(route('rental.categories.index'));
        $resIndex->assertStatus(403);

        // Store category
        $resStore = $this->actingAs($this->customer)->post(route('rental.categories.store'), [
            'name' => 'Kategori Palsu',
        ]);
        $resStore->assertStatus(403);
    }

    public function test_customer_only_sees_own_bookings(): void
    {
        $start = Carbon::now()->addDay();
        $end = Carbon::now()->addDays(2);

        // Customer's booking
        $ownBooking = RentalBooking::create([
            'booking_code' => 'RNT-OWN-001',
            'user_id' => $this->customer->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 24,
            'base_price' => 250000,
            'total_price' => 250000,
            'status' => BookingStatus::PENDING->value,
        ]);

        // Other customer's booking
        $otherBooking = RentalBooking::create([
            'booking_code' => 'RNT-OTHER-002',
            'user_id' => $this->otherCustomer->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start->copy()->addDays(3),
            'end_time' => $end->copy()->addDays(3),
            'total_hours' => 24,
            'base_price' => 250000,
            'total_price' => 250000,
            'status' => BookingStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->customer)->get(route('rental.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('RNT-OWN-001');
        $response->assertDontSee('RNT-OTHER-002');
    }

    public function test_admin_can_access_units_and_categories_crud(): void
    {
        // Admin accesses units
        $resUnits = $this->actingAs($this->admin)->get(route('rental.units.index'));
        $resUnits->assertStatus(200);
        $resUnits->assertSee('Honda Brio Satya');

        // Admin accesses categories
        $resCats = $this->actingAs($this->admin)->get(route('rental.categories.index'));
        $resCats->assertStatus(200);
        $resCats->assertSee('City Car');
    }

    public function test_admin_is_redirected_away_from_customer_self_booking_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('rental.bookings.create'));

        $response->assertRedirect(route('rental.bookings.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_submit_self_rental_booking(): void
    {
        $start = Carbon::now()->addDays(2)->format('Y-m-d\TH:00');
        $end = Carbon::now()->addDays(3)->format('Y-m-d\TH:00');

        $response = $this->actingAs($this->admin)->post(route('rental.bookings.store'), [
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
        ]);

        $response->assertRedirect(route('rental.bookings.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_sees_all_bookings_and_can_perform_approval_workflow(): void
    {
        $start = Carbon::now()->addDay();
        $end = Carbon::now()->addDays(2);

        $booking = RentalBooking::create([
            'booking_code' => 'RNT-APP-001',
            'user_id' => $this->customer->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 24,
            'base_price' => 250000,
            'total_price' => 250000,
            'status' => BookingStatus::PENDING->value,
        ]);

        // Admin sees the booking in the table
        $response = $this->actingAs($this->admin)->get(route('rental.bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('RNT-APP-001');
        $response->assertSee('Budi Penyewa');

        // Admin approves the booking
        $approveRes = $this->actingAs($this->admin)->post(route('rental.bookings.approve', $booking));
        $approveRes->assertRedirect();
        $this->assertEquals(BookingStatus::APPROVED, $booking->fresh()->status);
    }

    public function test_customer_cannot_perform_admin_booking_status_actions(): void
    {
        $start = Carbon::now()->addDay();
        $end = Carbon::now()->addDays(2);

        $booking = RentalBooking::create([
            'booking_code' => 'RNT-DENY-001',
            'user_id' => $this->customer->id,
            'unit_id' => $this->unit->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_hours' => 24,
            'base_price' => 250000,
            'total_price' => 250000,
            'status' => BookingStatus::PENDING->value,
        ]);

        // Customer attempts to approve
        $appRes = $this->actingAs($this->customer)->post(route('rental.bookings.approve', $booking));
        $appRes->assertStatus(403);

        // Customer attempts to reject
        $rejRes = $this->actingAs($this->customer)->post(route('rental.bookings.reject', $booking), [
            'cancellation_reason' => 'Tolak sendiri',
        ]);
        $rejRes->assertStatus(403);
    }
}
