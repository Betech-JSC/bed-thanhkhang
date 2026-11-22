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
            \JamstackVietnam\Slider\Models\Slider::where('position_display', 'BANNER_HOME_CATEGORIES_CTA')->count() === 0
            && class_exists(\Database\Seeders\ThanhKhangHomeCategoriesCtaSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangHomeCategoriesCtaSeeder())->run();
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
