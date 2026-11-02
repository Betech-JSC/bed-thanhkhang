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

        // Gỡ khóa ngoại và index trên bảng `users` / `contacts` trước khi xóa bảng/cột
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'school_id')) {
                try {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropForeign(['school_id']);
                    });
                } catch (\Throwable $e) {
                    // Bỏ qua nếu FK không tồn tại hoặc trên SQLite
                }
            }
            if (Schema::hasColumn('users', 'lifetime_points')) {
                try {
                    Schema::table('users', function (Blueprint $table) {
                        $table->dropIndex(['lifetime_points']);
                    });
                } catch (\Throwable $e) {
                    // Bỏ qua nếu index không tồn tại
                }
            }
        }

        if (Schema::hasTable('contacts')) {
            foreach (['class_id', 'class_allocation_status'] as $indexedCol) {
                if (Schema::hasColumn('contacts', $indexedCol)) {
                    try {
                        Schema::table('contacts', function (Blueprint $table) use ($indexedCol) {
                            $table->dropIndex([$indexedCol]);
                        });
                    } catch (\Throwable $e) {
                        // Bỏ qua nếu index không tồn tại
                    }
                }
            }
        }

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

            $existingUserColumns = array_values(array_filter(
                $userColumns,
                fn (string $column) => Schema::hasColumn('users', $column)
            ));

            if (!empty($existingUserColumns)) {
                Schema::table('users', function (Blueprint $table) use ($existingUserColumns) {
                    $table->dropColumn($existingUserColumns);
                });
            }
        }

        // 3. Xóa các cột mồ côi trên bảng `contacts`
        if (Schema::hasTable('contacts')) {
            $contactColumns = [
                'class_id',
                'class_allocation_status',
            ];

            $existingContactColumns = array_values(array_filter(
                $contactColumns,
                fn (string $column) => Schema::hasColumn('contacts', $column)
            ));

            if (!empty($existingContactColumns)) {
                Schema::table('contacts', function (Blueprint $table) use ($existingContactColumns) {
                    $table->dropColumn($existingContactColumns);
                });
            }
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

            $existingPostColumns = array_values(array_filter(
                $postColumns,
                fn (string $column) => Schema::hasColumn('posts', $column)
            ));

            if (!empty($existingPostColumns)) {
                Schema::table('posts', function (Blueprint $table) use ($existingPostColumns) {
                    $table->dropColumn($existingPostColumns);
                });
            }
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

            $existingTranslationColumns = array_values(array_filter(
                $translationColumns,
                fn (string $column) => Schema::hasColumn('post_translations', $column)
            ));

            if (!empty($existingTranslationColumns)) {
                Schema::table('post_translations', function (Blueprint $table) use ($existingTranslationColumns) {
                    $table->dropColumn($existingTranslationColumns);
                });
            }
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
