<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sample_size')) {
                $table->string('sample_size')->nullable()->after('lead_time');
            }
            if (!Schema::hasColumn('products', 'layer_height')) {
                $table->string('layer_height')->nullable()->after('sample_size');
            }
            if (!Schema::hasColumn('products', 'nozzle_size')) {
                $table->string('nozzle_size')->nullable()->after('layer_height');
            }
            if (!Schema::hasColumn('products', 'infill')) {
                $table->string('infill')->nullable()->after('nozzle_size');
            }
            if (!Schema::hasColumn('products', 'support_material')) {
                $table->string('support_material')->nullable()->after('infill');
            }
            if (!Schema::hasColumn('products', 'image_color_label')) {
                $table->string('image_color_label')->nullable()->after('support_material');
            }
            if (!Schema::hasColumn('products', 'customization_label')) {
                $table->string('customization_label')->nullable()->after('image_color_label');
            }
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('customization_label');
            }
            if (!Schema::hasColumn('products', 'pdp_metadata')) {
                $table->json('pdp_metadata')->nullable()->after('specifications');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'sample_size',
                'layer_height',
                'nozzle_size',
                'infill',
                'support_material',
                'image_color_label',
                'customization_label',
                'specifications',
                'pdp_metadata',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
