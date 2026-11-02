<?php

namespace Database\Seeders;

use App\Models\PointPackage;
use Illuminate\Database\Seeder;

class PointPackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name'         => 'Gói Cơ Bản',
                'points'       => 50,
                'bonus_points' => 0,
                'price'        => 50000,
                'is_active'    => true,
                'position'     => 1,
            ],
            [
                'name'         => 'Gói Chuyên Nghiệp',
                'points'       => 100,
                'bonus_points' => 20,
                'price'        => 100000,
                'is_active'    => true,
                'position'     => 2,
            ],
            [
                'name'         => 'Gói Premium',
                'points'       => 250,
                'bonus_points' => 50,
                'price'        => 250000,
                'is_active'    => true,
                'position'     => 3,
            ],
        ];

        foreach ($packages as $package) {
            PointPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        $this->command->info('✅ Seeded ' . count($packages) . ' point packages!');
    }
}
