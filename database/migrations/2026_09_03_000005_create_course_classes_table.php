<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // trỏ tới posts.id (type = 'COURSE')
            $table->string('class_code')->unique(); // VD: SKP-K26-T35
            $table->string('name'); // Tên hiển thị của lớp
            $table->date('start_date')->nullable(); // Ngày khai giảng
            $table->string('schedule_time')->nullable(); // Khung giờ: 19h30 - 21h30
            $table->string('duration')->nullable(); // Thời lượng: 10 buổi
            $table->string('mentor')->nullable(); // Giảng viên phụ trách
            $table->string('class_type')->default('ONLINE'); // ONLINE / OFFLINE
            $table->decimal('tuition_fee', 15, 2)->default(0); // Học phí niêm yết của lớp
            $table->integer('max_students')->default(15); // Sĩ số tối đa
            $table->integer('enrolled_students')->default(0); // Sĩ số học viên đã xác nhận
            $table->integer('display_order')->default(1); // STT hiển thị
            $table->string('status')->default('OPEN'); // OPEN, FULL, STARTED, COMPLETED, CANCELLED
            $table->text('note')->nullable(); // Ghi chú lớp học
            $table->integer('version')->default(1); // Optimistic Lock version
            $table->timestamps();

            $table->index(['course_id', 'display_order']);
            $table->index(['status', 'start_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_classes');
    }
};
