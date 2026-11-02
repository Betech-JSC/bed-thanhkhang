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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('ACTIVE')->after('email_verified_at');
            $table->text('address')->nullable()->after('phone');
            $table->text('note')->nullable()->after('address');
            $table->json('banner')->nullable()->after('note');
            $table->json('avatar')->nullable()->after('banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'address', 'note', 'banner', 'avatar']);
        });
    }
};
