<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (\App\Models\Policy\Policy::count() === 0 && class_exists(\Database\Seeders\ThanhKhangPolicySeeder::class)) {
            (new \Database\Seeders\ThanhKhangPolicySeeder())->run();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No action needed for down migration
    }
};
