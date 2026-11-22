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
        if (
            \JamstackVietnam\Slider\Models\Slider::where('position_display', 'HOME_SLIDER')->count() === 0
            && class_exists(\Database\Seeders\ThanhKhangHomeSliderSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangHomeSliderSeeder())->run();
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
