<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Safe and idempotent entry point: loads roles/admin, settings/FAQs, policies,
     * product catalog & hero banner, Home sections, About sections, Document banners,
     * and default user accounts in a single `php artisan db:seed` run without
     * truncating or duplicating existing records.
     *
     * @return void
     */
    public function run()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        // Call all official Thanh Khang Tecsat seeders in deterministic order
        $this->call([
            RoleSeeder::class,
            ThanhKhangSettingSeeder::class,
            ThanhKhangPolicySeeder::class,
            ThanhKhangProductCatalogSeeder::class,
            ThanhKhangProductHeroSeeder::class,
            ThanhKhangHomeSliderSeeder::class,
            ThanhKhangHomeAboutSeeder::class,
            ThanhKhangHomeCategoriesCtaSeeder::class,
            ThanhKhangHomeTrustSeeder::class,
            ThanhKhangHomeProcessSeeder::class,
            ThanhKhangHomeSamplePostsSeeder::class,
            ThanhKhangHomeContactSeeder::class,
            ThanhKhangAboutHeroSeeder::class,
            ThanhKhangAboutStorySeeder::class,
            ThanhKhangAboutMissionVisionSeeder::class,
            ThanhKhangAboutStatsSeeder::class,
            ThanhKhangAboutCompanionSeeder::class,
            ThanhKhangDocumentBannerSeeder::class,
            ThanhKhangDocumentLibrarySeeder::class,
        ]);

        // Ensure default users exist safely without wiping existing users
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin@gmail.com'),
                'role' => 'SUPER_ADMIN',
                'status' => User::STATUS_ACTIVE,
            ]
        );

        User::firstOrCreate(
            ['email' => 'testuser@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('12345678'),
                'role' => 'USER',
                'status' => User::STATUS_ACTIVE,
            ]
        );
    }
}
