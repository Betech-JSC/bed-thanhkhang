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
        Schema::create('google_sheet_sync_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id')->index();
            $table->string('sheet_id')->nullable();
            $table->string('sheet_tab')->nullable();
            $table->string('action', 20)->default('INSERT'); // INSERT, UPDATE
            $table->string('status', 20)->default('PENDING'); // PENDING, SUCCESS, FAILED
            $table->unsignedTinyInteger('attempts')->default(1);
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_sheet_sync_logs');
    }
};
