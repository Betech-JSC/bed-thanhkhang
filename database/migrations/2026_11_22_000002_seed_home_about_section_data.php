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
        if (class_exists(\Database\Seeders\ThanhKhangHomeAboutSeeder::class)) {
            (new \Database\Seeders\ThanhKhangHomeAboutSeeder())->run();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No destructive action on rollback to protect CMS-edited settings and slider records
    }
};
