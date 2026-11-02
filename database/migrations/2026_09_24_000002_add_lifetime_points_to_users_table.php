<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('lifetime_points')->default(0)->index()->after('topup_points');
        });

        // Khởi tạo điểm tích lũy trọn đời cho user hiện có
        DB::statement("UPDATE users SET lifetime_points = points WHERE lifetime_points = 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['lifetime_points']);
            $table->dropColumn('lifetime_points');
        });
    }
};
