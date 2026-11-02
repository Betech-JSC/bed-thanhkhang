<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type', 50);
            $table->integer('amount');                              // Dương = cộng, Âm = trừ
            $table->unsignedInteger('balance_before')->default(0);
            $table->unsignedInteger('balance_after')->default(0);
            $table->string('reference_code')->nullable()->unique(); // FK10023 - nội dung CK
            $table->string('description')->nullable();
            $table->unsignedBigInteger('point_package_id')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();  // Post ID khi tải tài nguyên
            $table->unsignedBigInteger('admin_id')->nullable();     // Admin thực hiện manual adjust
            $table->enum('status', ['pending', 'completed', 'failed', 'expired'])->default('pending');
            $table->string('sepay_transaction_id')->nullable()->unique(); // ID từ SePay - chống cộng điểm trùng
            $table->unsignedBigInteger('amount_paid')->nullable();  // Số tiền thực tế đã chuyển
            $table->timestamp('expires_at')->nullable();            // Thời gian QR hết hạn
            $table->json('meta')->nullable();                       // Raw data từ SePay webhook
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['reference_code', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('point_transactions');
    }
};
