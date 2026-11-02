<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('virtual_views')->default(0)->after('view_count');
            $table->unsignedInteger('virtual_downloads')->default(0)->after('download_count');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['virtual_views', 'virtual_downloads']);
        });
    }
};
