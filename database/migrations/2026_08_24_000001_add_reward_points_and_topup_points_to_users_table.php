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
            $table->unsignedInteger('reward_points')->default(0)->after('points');
            $table->unsignedInteger('topup_points')->default(0)->after('reward_points');
        });

        // Bảo toàn số dư hiện tại của khách hàng: chuyển points hiện có sang topup_points
        DB::table('users')->update([
            'topup_points' => DB::raw('points'),
            'reward_points' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reward_points', 'topup_points']);
        });
    }
};
