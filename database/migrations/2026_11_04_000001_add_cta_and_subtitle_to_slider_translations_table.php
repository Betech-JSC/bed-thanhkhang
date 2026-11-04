<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slider_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('slider_translations', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (!Schema::hasColumn('slider_translations', 'primary_cta_text')) {
                $table->string('primary_cta_text')->nullable()->after('description');
            }
            if (!Schema::hasColumn('slider_translations', 'primary_cta_link')) {
                $table->string('primary_cta_link')->nullable()->after('primary_cta_text');
            }
            if (!Schema::hasColumn('slider_translations', 'secondary_cta_text')) {
                $table->string('secondary_cta_text')->nullable()->after('primary_cta_link');
            }
            if (!Schema::hasColumn('slider_translations', 'secondary_cta_link')) {
                $table->string('secondary_cta_link')->nullable()->after('secondary_cta_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('slider_translations', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'primary_cta_text',
                'primary_cta_link',
                'secondary_cta_text',
                'secondary_cta_link',
            ]);
        });
    }
};
