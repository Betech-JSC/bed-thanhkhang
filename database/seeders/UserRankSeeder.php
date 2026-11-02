<?php

namespace Database\Seeders;

use App\Models\UserRank;
use Illuminate\Database\Seeder;

class UserRankSeeder extends Seeder
{
    public function run()
    {
        $ranks = [
            [
                'name'         => 'Người làm vườn',
                'min_points'   => 0,
                'description'  => 'Bắt đầu gieo những hạt giống đầu tiên.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/01_Nguoi_lam_vuon.png',
                    'url'  => '/assets/images/ranks/01_Nguoi_lam_vuon.png',
                    'alt'  => 'Người làm vườn',
                ],
                'banner_image' => null,
                'position'     => 1,
                'is_active'    => true,
            ],
            [
                'name'         => 'Tiểu nông',
                'min_points'   => 100,
                'description'  => 'Chăm chỉ vun trồng, thu hoạch mỗi ngày.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/02_Tieu_nong.png',
                    'url'  => '/assets/images/ranks/02_Tieu_nong.png',
                    'alt'  => 'Tiểu nông',
                ],
                'banner_image' => null,
                'position'     => 2,
                'is_active'    => true,
            ],
            [
                'name'         => 'Điền chủ',
                'min_points'   => 200,
                'description'  => 'Mở rộng ruộng đồng, xây dựng cơ ngơi.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/03_Dien_chu.png',
                    'url'  => '/assets/images/ranks/03_Dien_chu.png',
                    'alt'  => 'Điền chủ',
                ],
                'banner_image' => null,
                'position'     => 3,
                'is_active'    => true,
            ],
            [
                'name'         => 'Thương gia',
                'min_points'   => 500,
                'description'  => 'Buôn bán khắp nơi, kết nối muôn phương.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/04_Thuong_gia.png',
                    'url'  => '/assets/images/ranks/04_Thuong_gia.png',
                    'alt'  => 'Thương gia',
                ],
                'banner_image' => null,
                'position'     => 4,
                'is_active'    => true,
            ],
            [
                'name'         => 'Phú hộ',
                'min_points'   => 1000,
                'description'  => 'Sở hữu vườn tược trù phú, cuộc sống sung túc.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/05_Phu_ho.png',
                    'url'  => '/assets/images/ranks/05_Phu_ho.png',
                    'alt'  => 'Phú hộ',
                ],
                'banner_image' => null,
                'position'     => 5,
                'is_active'    => true,
            ],
            [
                'name'         => 'Bá hộ',
                'min_points'   => 2000,
                'description'  => 'Danh tiếng lan xa, đất đai trù mật.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/06_Ba_ho.png',
                    'url'  => '/assets/images/ranks/06_Ba_ho.png',
                    'alt'  => 'Bá hộ',
                ],
                'banner_image' => null,
                'position'     => 6,
                'is_active'    => true,
            ],
            [
                'name'         => 'Quan lớn',
                'min_points'   => 5000,
                'description'  => 'Nắm quyền cai quản, phù trợ muôn dân.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/07_Quan_lon.png',
                    'url'  => '/assets/images/ranks/07_Quan_lon.png',
                    'alt'  => 'Quan lớn',
                ],
                'banner_image' => null,
                'position'     => 7,
                'is_active'    => true,
            ],
            [
                'name'         => 'Vua mùa vàng',
                'min_points'   => 10000,
                'description'  => 'Biểu tượng thịnh vượng tối thượng.',
                'avatar_icon'  => [
                    'path' => 'assets/images/ranks/08_Vua_mua_vang.png',
                    'url'  => '/assets/images/ranks/08_Vua_mua_vang.png',
                    'alt'  => 'Vua mùa vàng',
                ],
                'banner_image' => null,
                'position'     => 8,
                'is_active'    => true,
            ],
        ];

        foreach ($ranks as $rank) {
            UserRank::updateOrCreate(
                ['position' => $rank['position']],
                $rank
            );
        }

        $this->command?->info('✅ Seeded ' . count($ranks) . ' user ranks successfully!');
    }
}
