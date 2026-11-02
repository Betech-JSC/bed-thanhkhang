<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if (class_exists(\Database\Seeders\RoleSeeder::class)) {
                \Database\Seeders\RoleSeeder::createPermissions();
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync permissions in migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
