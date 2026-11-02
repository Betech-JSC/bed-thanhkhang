<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // Tên đầy đủ VD: ĐH Kiến trúc TP.HCM
            $table->string('short_name')->nullable();       // Tên viết tắt VD: UAH
            $table->string('type')->nullable();             // Loại: ĐH, CĐ, Nước ngoài
            $table->string('status')->default('ACTIVE');    // ACTIVE | INACTIVE
            $table->unsignedInteger('position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
