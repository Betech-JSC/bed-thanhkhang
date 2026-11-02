<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('point_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                             // Tên gói: "Gói Chuyên Nghiệp"
            $table->unsignedInteger('points');                  // Quả cơ bản nhận được
            $table->unsignedInteger('bonus_points')->default(0); // Quả thưởng thêm
            $table->unsignedBigInteger('price');                // Giá VNĐ
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('point_packages');
    }
};
