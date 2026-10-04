<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Models\RentalCategory;
use RentalHub\StarterKit\Models\RentalUnit;

class InstallRentalPackage extends Command
{
    /**
     * @var string
     */
    protected $signature = 'rental:install
        {--force : Timpa file konfigurasi yang sudah ada}
        {--no-seed : Jangan buat data demo kategori dan armada}
        {--admin-email= : Email untuk akun administrator}
        {--admin-password= : Password untuk akun administrator}';

    /**
     * @var string
     */
    protected $description = 'Instalasi dan konfigurasi otomatis Starter Kit Rental Hub';

    public function handle(): int
    {
        $this->info('====================================================');
        $this->info(' Memulai Instalasi Starter Kit Rental Hub           ');
        $this->info('====================================================');

        // 1. Publikasikan konfigurasi
        $this->line('<fg=cyan>Langkah 1:</> Mempublikasikan konfigurasi...');
        $this->call('vendor:publish', [
            '--tag' => 'rental-config',
            '--force' => (bool) $this->option('force'),
        ]);

        // 2. Jalankan migrasi database
        $this->line('<fg=cyan>Langkah 2:</> Menjalankan migrasi database...');
        $connection = config('database.default');
        if ($connection === 'sqlite') {
            $database = config('database.connections.sqlite.database');
            if ($database && $database !== ':memory:' && !File::exists($database)) {
                File::ensureDirectoryExists(dirname($database));
                File::put($database, '');
            }
        }
        $this->call('migrate');

        // 3. Storage Symlink
        $this->line('<fg=cyan>Langkah 3:</> Memeriksa symlink penyimpanan (storage:link)...');
        $storagePath = public_path('storage');
        if (!File::exists($storagePath) && !is_link($storagePath)) {
            $this->call('storage:link');
        } else {
            $this->line('Symlink storage sudah tersedia.');
        }

        // 4. Inisialisasi Akun Administrator
        $this->line('<fg=cyan>Langkah 4:</> Menyiapkan akun administrator...');
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');

        if (!class_exists($userModel) && class_exists(\RentalHub\StarterKit\Tests\Fixtures\User::class)) {
            class_alias(\RentalHub\StarterKit\Tests\Fixtures\User::class, $userModel);
        }

        if (!class_exists($userModel)) {
            $this->error("Model user {$userModel} tidak ditemukan. Silakan periksa config/rental-hub.php");
            return self::FAILURE;
        }

        $adminEmailOption = $this->option('admin-email');
        $adminPasswordOption = $this->option('admin-password');

        $isScripted = !empty($adminEmailOption);

        $adminEmail = (string) ($adminEmailOption
            ?: ($this->input->isInteractive() ? $this->ask('Masukkan email Administrator', 'admin@rental.test') : 'admin@rental.test'));

        $adminName = 'Administrator Rental';
        if (!$isScripted && $this->input->isInteractive()) {
            $adminName = (string) $this->ask('Masukkan nama Administrator', 'Administrator Rental');
        }

        $adminPassword = (string) $adminPasswordOption;
        $passwordGenerated = false;

        if ($adminPassword === '') {
            if (!$isScripted && $this->input->isInteractive()) {
                $inputPass = (string) $this->secret('Masukkan password Administrator (kosongkan untuk generate acak)');
                if ($inputPass !== '') {
                    $adminPassword = $inputPass;
                }
            }

            if ($adminPassword === '') {
                $adminPassword = Str::random(12);
                $passwordGenerated = true;
            }
        }

        /** @var \Illuminate\Database\Eloquent\Model|null $existingUser */
        $existingUser = $userModel::where('email', $adminEmail)->first();

        if ($existingUser !== null) {
            $existingUser->setAttribute('rental_role', UserRole::ADMIN->value);
            if ($this->option('admin-password') || !$passwordGenerated) {
                $existingUser->setAttribute('password', Hash::make($adminPassword));
            }
            $existingUser->save();
            $this->info("Akun {$adminEmail} sudah ada dan perannya telah diperbarui menjadi admin.");
        } else {
            $userModel::forceCreate([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'rental_role' => UserRole::ADMIN->value,
                'phone' => '081234567890',
            ]);
            $this->info("Akun administrator baru ({$adminEmail}) berhasil dibuat.");
        }

        if ($passwordGenerated) {
            $this->newLine();
            $this->warn('******************************************************************');
            $this->warn(" PERHATIAN: Password acak berikut HANYA ditampilkan SEKALI ini: ");
            $this->warn(" Password: {$adminPassword}                                      ");
            $this->warn('******************************************************************');
            $this->newLine();
        }

        // 5. Seeding Demo Data (Idempotent)
        $shouldSeed = !$this->option('no-seed');
        if ($shouldSeed && !$isScripted && $this->input->isInteractive()) {
            $shouldSeed = $this->confirm('Apakah Anda ingin membuat data demo (1 kategori & 3 unit armada)?', true);
        }

        if ($shouldSeed) {
            $this->line('<fg=cyan>Langkah 5:</> Menyiapkan data demo armada...');
            $category = RentalCategory::firstOrCreate(
                ['slug' => 'mobil-keluarga'],
                [
                    'name' => 'Mobil Keluarga & MPV',
                    'description' => 'Armada nyaman dan lega untuk perjalanan dinas atau liburan keluarga.',
                ]
            );

            RentalUnit::firstOrCreate(
                ['code' => 'B 1001 RNT'],
                [
                    'category_id' => $category->id,
                    'name' => 'Toyota Avanza 1.3 G',
                    'price_per_hour' => 35000,
                    'price_per_day' => 350000,
                    'status' => UnitStatus::AVAILABLE->value,
                    'late_fee_per_hour' => 50000,
                    'specifications' => [
                        'Transmisi' => 'Manual 5-Speed',
                        'Kapasitas' => '7 Kursi Penumpang',
                        'Bahan Bakar' => 'Bensin (Pertalite/Pertamax)',
                    ],
                ]
            );

            RentalUnit::firstOrCreate(
                ['code' => 'B 2002 RNT'],
                [
                    'category_id' => $category->id,
                    'name' => 'Toyota Innova Zenix Hybrid',
                    'price_per_hour' => 65000,
                    'price_per_day' => 650000,
                    'status' => UnitStatus::AVAILABLE->value,
                    'late_fee_per_hour' => 75000,
                    'specifications' => [
                        'Transmisi' => 'CVT Automatic',
                        'Kapasitas' => '7 Kursi Penumpang',
                        'Bahan Bakar' => 'Bensin Hybrid',
                    ],
                ]
            );

            RentalUnit::firstOrCreate(
                ['code' => 'B 3003 RNT'],
                [
                    'category_id' => $category->id,
                    'name' => 'Mitsubishi Pajero Sport Dakar',
                    'price_per_hour' => 95000,
                    'price_per_day' => 950000,
                    'status' => UnitStatus::AVAILABLE->value,
                    'late_fee_per_hour' => 100000,
                    'specifications' => [
                        'Transmisi' => 'Automatic 8-Speed',
                        'Kapasitas' => '7 Kursi Penumpang',
                        'Bahan Bakar' => 'Solar / Dexlite',
                    ],
                ]
            );

            $this->info('Data demo armada berhasil ditambahkan tanpa duplikasi.');
        }

        // 6. Ringkasan Sukses
        $prefix = (string) config('rental-hub.route_prefix', 'rental');
        $loginUrl = url($prefix . '/login');

        $this->newLine();
        $this->info('====================================================');
        $this->info(' Instalasi Starter Kit Rental Hub Berhasil Selesai! ');
        $this->info('====================================================');
        $this->line("URL Halaman Masuk: <fg=yellow>{$loginUrl}</>");
        $this->line("Email Admin      : <fg=green>{$adminEmail}</>");
        if ($passwordGenerated) {
            $this->line("Password Admin   : <fg=green>{$adminPassword}</>");
        }
        $this->newLine();

        return self::SUCCESS;
    }
}
