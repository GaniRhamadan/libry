<?php

declare(strict_types=1);

namespace RentalHub\StarterKit;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use RentalHub\StarterKit\Console\Commands\InstallRentalPackage;
use RentalHub\StarterKit\Http\Middleware\AuthenticateRental;
use RentalHub\StarterKit\Http\Middleware\EnsureRentalRole;
use RentalHub\StarterKit\Models\RentalBooking;
use RentalHub\StarterKit\Policies\RentalBookingPolicy;
use RentalHub\StarterKit\Services\BookingService;

class RentalHubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rental-hub.php', 'rental-hub');

        $this->app->singleton(BookingService::class, function () {
            return new BookingService();
        });

        $userModel = config('rental-hub.user_model', 'App\\Models\\User');
        if (!class_exists($userModel) && class_exists(\RentalHub\StarterKit\Tests\Fixtures\User::class)) {
            class_alias(\RentalHub\StarterKit\Tests\Fixtures\User::class, $userModel);
        }

        $this->app['router']->aliasMiddleware('rental.auth', AuthenticateRental::class);
        $this->app['router']->aliasMiddleware('rental.role', EnsureRentalRole::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rental-hub');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'rental-hub');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Gate::policy(RentalBooking::class, RentalBookingPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallRentalPackage::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/rental-hub.php' => config_path('rental-hub.php'),
            ], 'rental-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/rental-hub'),
            ], 'rental-views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/rental-hub'),
            ], 'rental-lang');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'rental-migrations');

            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/rental-hub'),
            ], 'rental-assets');
        }
    }
}
