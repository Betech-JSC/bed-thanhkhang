<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_confirmation_slips', function (Blueprint $table) {
            if (!Schema::hasColumn('course_confirmation_slips', 'paid_amount')) {
                $table->decimal('paid_amount', 14, 2)->default(0)->after('final_price');
            }
            if (!Schema::hasColumn('course_confirmation_slips', 'debt_amount')) {
                $table->decimal('debt_amount', 14, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('course_confirmation_slips', 'payment_status')) {
                $table->string('payment_status', 20)->default('PAID')->after('debt_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_confirmation_slips', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'debt_amount', 'payment_status']);
        });
    }
};
