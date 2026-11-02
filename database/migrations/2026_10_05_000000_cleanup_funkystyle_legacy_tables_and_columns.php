<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Dọn dẹp an toàn toàn bộ các bảng và cột thừa của FunkyStyle.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Xóa các bảng dữ liệu FunkyStyle (theo thứ tự phụ thuộc khóa ngoại)
        $tablesToDrop = [
            'combo_course',
            'course_confirmation_slips',
            'course_classes',
            'combos',
            'schools',
            'point_transactions',
            'point_packages',
            'user_ranks',
            'google_sheet_sync_logs',
        ];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }

        // 2. Xóa các cột mồ côi trên bảng `users`
        if (Schema::hasTable('users')) {
            $userColumns = [
                'points',
                'reward_points',
                'topup_points',
                'lifetime_points',
                'school',
                'school_id',
                'major',
                'current_status',
                'school_year',
                'funky_note',
                'is_reward_locked',
                'reward_lock_reason',
                'reward_locked_until',
            ];

            Schema::table('users', function (Blueprint $table) use ($userColumns) {
                foreach ($userColumns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // 3. Xóa các cột mồ côi trên bảng `contacts`
        if (Schema::hasTable('contacts')) {
            $contactColumns = [
                'class_id',
                'class_allocation_status',
            ];

            Schema::table('contacts', function (Blueprint $table) use ($contactColumns) {
                foreach ($contactColumns as $column) {
                    if (Schema::hasColumn('contacts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // 4. Xóa các cột mồ côi trên bảng `posts`
        if (Schema::hasTable('posts')) {
            $postColumns = [
                'points_cost',
                'download_count',
                'virtual_downloads',
                'resource_type',
                'is_countdown_active',
                'starts_at',
            ];

            Schema::table('posts', function (Blueprint $table) use ($postColumns) {
                foreach ($postColumns as $column) {
                    if (Schema::hasColumn('posts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // 5. Xóa các cột mồ côi trên bảng `post_translations`
        if (Schema::hasTable('post_translations')) {
            $translationColumns = [
                'number_of_course',
                'timetable',
                'quantity',
                'schedule',
                'groups',
                'course_for',
                'knowledge_content',
                'course_target',
            ];

            Schema::table('post_translations', function (Blueprint $table) use ($translationColumns) {
                foreach ($translationColumns as $column) {
                    if (Schema::hasColumn('post_translations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Quá trình loại bỏ module FunkyStyle là một chiều (irreversible migration)
        // để chuyển đổi dứt điểm sang Saigonchem Base CMS.
    }
};
