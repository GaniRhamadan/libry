<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function getTableName(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'bookings';
    }

    private function getUnitTableName(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'units';
    }

    private function getUserTableName(): string
    {
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');

        if (class_exists($userModel)) {
            return (new $userModel())->getTable();
        }

        return 'users';
    }

    public function up(): void
    {
        $tableName = $this->getTableName();
        $unitTable = $this->getUnitTableName();
        $userTable = $this->getUserTableName();

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($unitTable, $userTable): void {
                $table->id();
                $table->string('booking_code')->unique();
                $table->foreignId('user_id')->constrained($userTable)->cascadeOnDelete();
                $table->foreignId('unit_id')->constrained($unitTable)->cascadeOnDelete();
                $table->dateTime('start_time')->index();
                $table->dateTime('end_time')->index();
                $table->dateTime('actual_return_time')->nullable();
                $table->unsignedInteger('total_hours');
                $table->unsignedBigInteger('base_price');
                $table->unsignedBigInteger('late_fee')->default(0);
                $table->unsignedBigInteger('total_price');
                $table->string('status')->default('pending')->index();
                $table->text('notes')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->getTableName());
    }
};
