<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Thay đổi cột type từ ENUM sang VARCHAR(50) để hỗ trợ các loại giao dịch mới như share_facebook
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE point_transactions MODIFY COLUMN type VARCHAR(50) NOT NULL");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE point_transactions MODIFY COLUMN type ENUM('topup', 'spend', 'bonus', 'manual_add', 'manual_subtract') NOT NULL");
        }
    }
};
