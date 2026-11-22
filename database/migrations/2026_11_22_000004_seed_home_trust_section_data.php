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
        if (class_exists(\Database\Seeders\ThanhKhangHomeTrustSeeder::class)) {
            (new \Database\Seeders\ThanhKhangHomeTrustSeeder())->run();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No destructive action on rollback to protect CMS-edited slider records
    }
};
