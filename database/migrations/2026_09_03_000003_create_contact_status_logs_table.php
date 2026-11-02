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
        Schema::create('contact_status_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id')->index();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->index();
            
            $table->unsignedBigInteger('changed_by')->nullable()->index(); // ID Admin
            $table->string('admin_name')->nullable();
            
            $table->string('reason')->nullable(); // Bắt buộc khi hủy đơn (CLOSE / CANCELLED)
            $table->text('note')->nullable(); // Ghi chú bổ sung
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_status_logs');
    }
};
