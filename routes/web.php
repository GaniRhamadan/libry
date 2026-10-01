<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use RentalHub\StarterKit\Http\Controllers\AuthController;
use RentalHub\StarterKit\Http\Controllers\BookingController;
use RentalHub\StarterKit\Http\Controllers\CategoryController;
use RentalHub\StarterKit\Http\Controllers\DashboardController;
use RentalHub\StarterKit\Http\Controllers\ProfileController;
use RentalHub\StarterKit\Http\Controllers\UnitController;

$routePrefix = (string) config('rental-hub.route_prefix', 'rental');
$namePrefix = (string) config('rental-hub.route_name_prefix', 'rental.');
$middleware = (array) config('rental-hub.middleware', ['web']);

Route::prefix($routePrefix)
    ->as($namePrefix)
    ->middleware($middleware)
    ->group(function (): void {
        // Guest Authentication Routes
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

        // Authenticated Routes
        Route::middleware(['rental.auth'])->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // Shared Dashboard (Determined by Role)
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Bookings (Customer & Admin)
            Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
            Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
            Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
            Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

            // Admin Only Routes
            Route::middleware(['rental.role:admin'])->group(function (): void {
                // Units Management
                Route::get('/units', [UnitController::class, 'index'])->name('units.index');
                Route::get('/units/create', [UnitController::class, 'create'])->name('units.create');
                Route::post('/units', [UnitController::class, 'store'])->name('units.store');
                Route::get('/units/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit');
                Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update');
                Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

                // Categories Management
                Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

                // Booking Lifecycle Status Transitions
                Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
                Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
                Route::post('/bookings/{booking}/activate', [BookingController::class, 'activate'])->name('bookings.activate');
                Route::post('/bookings/{booking}/return', [BookingController::class, 'confirmReturn'])->name('bookings.return');
            });
        });
    });

if (!Route::has('login')) {
    Route::get('/rental-login', function () use ($namePrefix) {
        return redirect()->route($namePrefix . 'login');
    })->name('login');
}
