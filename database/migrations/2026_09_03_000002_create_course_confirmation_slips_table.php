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
        Schema::create('course_confirmation_slips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id')->index();
            $table->unsignedInteger('version')->default(1);
            $table->string('slip_code', 50)->unique();
            $table->string('reference_code', 50)->index(); // FKC01024 (SePay syntax)

            $table->string('student_name');
            $table->string('student_email')->nullable();
            $table->string('student_phone')->nullable();

            $table->string('course_title');
            $table->string('schedule_info')->nullable();

            $table->decimal('original_price', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('final_price', 14, 2)->default(0);

            $table->text('discounts_summary')->nullable(); // JSON hoặc text mô tả ưu đãi
            $table->text('qr_url'); // URL VietQR động

            $table->string('file_path')->nullable(); // Đường dẫn file PDF lưu trong storage
            $table->string('download_token', 64)->unique()->index(); // Token bảo mật ngẫu nhiên chống IDOR

            $table->string('email_status', 20)->default('PENDING'); // PENDING, SENT, FAILED
            $table->unsignedTinyInteger('email_attempts')->default(0);
            $table->text('email_error')->nullable();
            $table->timestamp('email_sent_at')->nullable();

            $table->unsignedBigInteger('generated_by')->nullable(); // ID admin tạo thủ công hoặc null nếu tự động
            $table->timestamps();

            $table->index(['contact_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_confirmation_slips');
    }
};
