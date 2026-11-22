<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhKhangHomeContactSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 7 — Contact & Quote Form (Figma 595:15496).
     * Seeds ONLY the 4 home_contact_* keys into settings (group='general') IF missing or empty,
     * preserving existing admin edits and all other brand settings.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $now = now();

        $contactSettings = [
            'home_contact_title' => 'KẾT NỐI & NHẬN BÁO GIÁ CÁC SẢN PHẨM TẠI THÀNH KHANG',
            'home_contact_title_en' => 'CONNECT & REQUEST A QUOTE FOR PRODUCTS AT THANH KHANG',

            'home_contact_description' => 'Xích công nghiệp chính hãng, đa dạng kích thước, đáp ứng nhiều điều kiện làm việc khác nhau. Đội ngũ kỹ thuật tư vấn lựa chọn đúng loại xích theo tải trọng và ứng dụng thực tế, giúp quý khách tối ưu chi phí và hạn chế gián đoạn sản xuất',
            'home_contact_description_en' => 'Genuine industrial chains in a wide range of sizes to suit diverse operating conditions. Our engineering team advises on selecting the right chain for your actual load and application, helping optimize costs and minimize production downtime.',
        ];

        DB::transaction(function () use ($contactSettings, $now) {
            foreach ($contactSettings as $name => $val) {
                $existing = DB::table('settings')
                    ->where('group', 'general')
                    ->where('name', $name)
                    ->first();

                if (!$existing) {
                    DB::table('settings')->insert([
                        'group' => 'general',
                        'name' => $name,
                        'val' => $val,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } elseif (trim((string) $existing->val) === '') {
                    DB::table('settings')
                        ->where('id', $existing->id)
                        ->update([
                            'val' => $val,
                            'updated_at' => $now,
                        ]);
                }
            }
        });

        Config::clearBrandCaches();
    }
}
