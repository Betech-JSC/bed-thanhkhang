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
            Schema::hasTable('sliders')
            && Schema::hasTable('slider_translations')
            && \JamstackVietnam\Slider\Models\Slider::where('position_display', 'BANNER_GLOBAL_CTA')->count() === 0
            && class_exists(\Database\Seeders\ThanhKhangGlobalCtaBannerSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangGlobalCtaBannerSeeder())->run();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Non-destructive: preserve CMS-managed BANNER_GLOBAL_CTA slider record.
    }
};
