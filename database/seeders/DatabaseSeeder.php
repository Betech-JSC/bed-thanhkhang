<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Safe, idempotent seeder calling official B2B Thanh Khang Tecsat seeders.
     *
     * @return void
     */
    public function run()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        // Call official seeders
        $this->call([
            RoleSeeder::class,
            ThanhKhangSettingSeeder::class,
            ThanhKhangPolicySeeder::class,
            ThanhKhangProductCatalogSeeder::class,
        ]);

        // Ensure default admin user exists safely without wiping existing users
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
