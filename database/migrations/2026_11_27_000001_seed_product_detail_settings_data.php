<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (
            Schema::hasTable('settings')
            && class_exists(\Database\Seeders\ThanhKhangProductDetailSettingsSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangProductDetailSettingsSeeder())->run();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Non-destructive: preserve CMS-managed PDP default settings.
    }
};
