<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function getTableName(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'categories';
    }

    public function up(): void
    {
        $tableName = $this->getTableName();

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->getTableName());
    }
};
