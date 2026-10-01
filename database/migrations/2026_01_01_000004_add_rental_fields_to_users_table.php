<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
        $userTable = $this->getUserTableName();

        if (Schema::hasTable($userTable)) {
            Schema::table($userTable, function (Blueprint $table) use ($userTable): void {
                if (!Schema::hasColumn($userTable, 'rental_role')) {
                    $table->string('rental_role')->default('customer')->index();
                }

                if (!Schema::hasColumn($userTable, 'phone')) {
                    $table->string('phone')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $userTable = $this->getUserTableName();

        if (Schema::hasTable($userTable)) {
            Schema::table($userTable, function (Blueprint $table) use ($userTable): void {
                if (Schema::hasColumn($userTable, 'rental_role')) {
                    $table->dropColumn('rental_role');
                }

                if (Schema::hasColumn($userTable, 'phone')) {
                    $table->dropColumn('phone');
                }
            });
        }
    }
};
