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
            (
                \JamstackVietnam\Slider\Models\Slider::where('position_display', 'BANNER_DOCUMENT_HERO')->count() === 0
                || \JamstackVietnam\Slider\Models\Slider::where('position_display', 'BANNER_DOCUMENT_CTA')->count() === 0
            )
            && class_exists(\Database\Seeders\ThanhKhangDocumentBannerSeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangDocumentBannerSeeder())->run();
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
