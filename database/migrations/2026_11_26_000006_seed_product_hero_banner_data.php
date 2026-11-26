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
            \JamstackVietnam\Slider\Models\Slider::where('position_display', 'BANNER_PRODUCT_HERO')->count() === 0
            && class_exists(\Database\Seeders\ThanhKhangProductHeroSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangProductHeroSeeder())->run();
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
