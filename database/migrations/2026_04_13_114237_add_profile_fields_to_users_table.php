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
        Schema::table('users', function (Blueprint $table) {
            // Tự nhập tên trường (trường hợp không có trong danh sách)
            $table->string('school')->nullable()->after('note');
            // FK đến bảng schools (chọn từ danh sách)
            $table->unsignedBigInteger('school_id')->nullable()->after('school');
            // Chuyên ngành
            $table->string('major')->nullable()->after('school_id');
            // Hiện tại đang là: sv_year_1..5, graduated, unknown
            $table->string('current_status')->nullable()->after('major');
            // Năm học VD: 2024-2025
            $table->string('school_year')->nullable()->after('current_status');
            // Ghi chú riêng cho Funky
            $table->text('funky_note')->nullable()->after('school_year');

            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['school', 'school_id', 'major', 'current_status', 'school_year', 'funky_note']);
        });
    }
};
