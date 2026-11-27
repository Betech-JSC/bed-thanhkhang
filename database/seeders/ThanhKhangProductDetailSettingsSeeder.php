<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThanhKhangProductDetailSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe, 100% idempotent seeder for PDP 6 default specs (general group).
     * Never overwrites non-empty CMS-edited values.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        if (!Schema::hasTable('settings')) {
            return;
        }

        $now = now();

        $defaults = [
            'product_detail_brand' => 'Universal Chains',
            'product_detail_brand_en' => 'Universal Chains',
            'product_detail_origin' => 'Trung Quốc',
            'product_detail_origin_en' => 'China',
            'product_detail_warranty' => '12 tháng',
            'product_detail_warranty_en' => '12 months',
            'product_detail_condition' => 'Full box',
            'product_detail_condition_en' => 'Full box',
            'product_detail_stock_status' => 'Còn hàng',
            'product_detail_stock_status_en' => 'In stock',
            'product_detail_support' => '24/7',
            'product_detail_support_en' => '24/7',
        ];

        foreach ($defaults as $name => $val) {
            $existing = DB::table('settings')
                ->where('group', 'general')
                ->where('name', $name)
                ->first();

            if (!$existing || trim((string) ($existing->val ?? '')) === '') {
                DB::table('settings')->updateOrInsert(
                    ['group' => 'general', 'name' => $name],
                    [
                        'val' => $val,
                        'updated_at' => $now,
                        'created_at' => $existing->created_at ?? $now,
                    ]
                );
            }
        }

        Config::clearBrandCaches();
        Product::clearProductCaches();
    }
}
