<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'compare_at_price')) {
                $table->decimal('compare_at_price', 15, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'print_technology')) {
                $table->string('print_technology')->nullable()->after('compare_at_price');
            }
            if (!Schema::hasColumn('products', 'material')) {
                $table->string('material')->nullable()->after('print_technology');
            }
            if (!Schema::hasColumn('products', 'lead_time')) {
                $table->string('lead_time')->nullable()->after('material');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'compare_at_price',
                'print_technology',
                'material',
                'lead_time',
            ]);
        });
    }
};
