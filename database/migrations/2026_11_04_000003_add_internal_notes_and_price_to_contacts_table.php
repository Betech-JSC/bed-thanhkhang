<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'estimated_price')) {
                $table->decimal('estimated_price', 15, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('contacts', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('estimated_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_price',
                'internal_notes',
            ]);
        });
    }
};
