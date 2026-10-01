<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Models\RentalCategory;
use RentalHub\StarterKit\Models\RentalUnit;
use RentalHub\StarterKit\Tests\Fixtures\User;
use RentalHub\StarterKit\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_install_command_executes_successfully(): void
    {
        $this->artisan('rental:install', [
            '--no-seed' => true,
            '--admin-email' => 'admin@rental.test',
            '--admin-password' => 'secret123456',
        ])->assertSuccessful();

        $admin = User::where('email', 'admin@rental.test')->first();
        $this->assertNotNull($admin);
        $this->assertEquals(UserRole::ADMIN->value, $admin->rental_role);
    }

    public function test_install_command_is_idempotent_when_run_twice(): void
    {
        // First run with demo seed
        $this->artisan('rental:install', [
            '--admin-email' => 'admin@rental.test',
            '--admin-password' => 'secret123456',
        ])->assertSuccessful();

        $unitCountFirst = RentalUnit::count();
        $categoryCountFirst = RentalCategory::count();

        $this->assertEquals(3, $unitCountFirst);
        $this->assertEquals(1, $categoryCountFirst);

        // Second run must succeed with 0 duplicates
        $this->artisan('rental:install', [
            '--admin-email' => 'admin@rental.test',
            '--admin-password' => 'secret123456',
        ])->assertSuccessful();

        $this->assertEquals($unitCountFirst, RentalUnit::count());
        $this->assertEquals($categoryCountFirst, RentalCategory::count());
        $this->assertEquals(1, User::where('email', 'admin@rental.test')->count());
    }

    public function test_install_command_interactive_prompts(): void
    {
        $this->artisan('rental:install')
            ->expectsQuestion('Masukkan email Administrator', 'superadmin@rental.test')
            ->expectsQuestion('Masukkan nama Administrator', 'Super Admin Rental')
            ->expectsQuestion('Masukkan password Administrator (kosongkan untuk generate acak)', 'supersecret123')
            ->expectsConfirmation('Apakah Anda ingin membuat data demo (1 kategori & 3 unit armada)?', 'yes')
            ->assertSuccessful();

        $admin = User::where('email', 'superadmin@rental.test')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('Super Admin Rental', $admin->name);
        $this->assertEquals(UserRole::ADMIN->value, $admin->rental_role);
    }
}
