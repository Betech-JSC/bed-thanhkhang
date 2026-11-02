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
        Schema::create('user_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('min_points')->default(0);
            $table->text('description')->nullable();
            $table->json('avatar_icon')->nullable();
            $table->json('banner_image')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tự động nạp sẵn 8 mốc danh hiệu mẫu và avatar icon khi triển khai lên môi trường mới
        if (class_exists(\App\Models\UserRank::class)) {
            \Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\UserRankSeeder',
                '--force' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ranks');
    }
};
