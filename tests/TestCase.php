<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RentalHub\StarterKit\RentalHubServiceProvider;
use RentalHub\StarterKit\Tests\Fixtures\User;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            RentalHubServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('rental-hub.user_model', User::class);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        // 1. Buat tabel users milik aplikasi host terlebih dahulu
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Jalankan seluruh migrasi bawaan package rental-hub
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
