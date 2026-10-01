<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function getTableName(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'units';
    }

    private function getCategoryTableName(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'categories';
    }

    public function up(): void
    {
        $tableName = $this->getTableName();
        $categoryTable = $this->getCategoryTableName();

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($categoryTable): void {
                $table->id();
                $table->foreignId('category_id')->constrained($categoryTable)->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->unique();
                $table->json('specifications')->nullable();
                $table->unsignedBigInteger('price_per_hour');
                $table->unsignedBigInteger('price_per_day');
                $table->string('status')->default('available')->index();
                $table->unsignedBigInteger('late_fee_per_hour')->nullable();
                $table->string('photo_path')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->getTableName());
    }
};
