<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('status')->index();
            $table->string('class_allocation_status')->default('NOT_ALLOCATED')->after('class_id')->index();
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['class_id']);
            $table->dropIndex(['class_allocation_status']);
            $table->dropColumn(['class_id', 'class_allocation_status']);
        });
    }
};
