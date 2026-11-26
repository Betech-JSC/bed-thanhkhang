<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (
            Schema::hasTable('post_categories') &&
            Schema::hasTable('post_category_translations') &&
            Schema::hasTable('posts') &&
            Schema::hasTable('post_translations') &&
            Schema::hasTable('post_ref_categories') &&
            \App\Models\Post\PostCategory::where('type', \App\Models\Post\PostCategory::TYPE_DOCUMENT)->count() === 0 &&
            \App\Models\Post\Post::where('type', \App\Models\Post\Post::TYPE_DOCUMENT)->count() === 0 &&
            class_exists(\Database\Seeders\ThanhKhangDocumentLibrarySeeder::class)
        ) {
            (new \Database\Seeders\ThanhKhangDocumentLibrarySeeder())->run();
        }

        try {
            if (
                Schema::hasTable('permissions') &&
                Schema::hasTable('roles') &&
                Schema::hasTable('role_has_permissions') &&
                class_exists(\Database\Seeders\RoleSeeder::class)
            ) {
                \Database\Seeders\RoleSeeder::createPermissions();
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync permissions in document library migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Non-destructive: preserve CMS-managed document categories and library documents.
    }
};
