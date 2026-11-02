<?php

use App\Models\Combo;
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
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tên combo
            $table->text('description')->nullable();
            $table->string('discount_type')->default(Combo::PERCENT); // Kiểu giảm giá
            $table->unsignedInteger('discount_value')->default(0); // Giá trị giảm (phần trăm hoặc số tiền)
            $table->unsignedTinyInteger('priority')->default(0); // Để sắp xếp hiển thị
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_until')->nullable(); // Hạn dùng combo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combos');
    }
};
