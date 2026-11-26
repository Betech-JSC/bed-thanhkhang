<?php

use App\Models\Product\ProductCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable('product_category_translations')) {
            return;
        }

        $updates = [
            'escalator-spare-parts' => [
                'legacy_vi' => 'Tổng kho xích bậc thang cuốn, xích băng tải hành khách, xích dẫn động và xích tay vịn chính hãng Universal Chains.',
                'en' => 'Complete inventory of escalator step chains, passenger conveyor chains, main drive chains, and handrail drive chains from Universal Chains.',
            ],
            'car-parking-lift-components' => [
                'legacy_vi' => 'Xích bãi đỗ xe lập thể và tuần hoàn đứng chịu tải trọng lớn cho hệ thống đỗ xe thông minh.',
                'en' => 'Heavy-duty chains for multi-level puzzle parking and vertical rotary car parking lift systems.',
            ],
            'baggage-conveyor-systems' => [
                'legacy_vi' => 'Xích con lăn cạnh và xích con lăn trên chuyên dụng cho băng tải hành lý sân bay và kho vận logistics.',
                'en' => 'Side roller and top roller conveyor chains engineered for airport baggage handling and logistics systems.',
            ],
            'packaging-line-components' => [
                'legacy_vi' => 'Xích chốt rỗng, xích con lăn trên, xích cong/chống cong và xích tải M/FV series cho dây chuyền đóng gói và băng tải nhẹ.',
                'en' => 'Hollow pin chains, top roller chains, side-bow/anti-bow chains, and M/FV series conveyor chains for packaging machinery and light conveyors.',
            ],
        ];

        $updatedAny = false;

        foreach ($updates as $slug => $texts) {
            $rows = DB::table('product_category_translations')
                ->where('locale', 'en')
                ->where('slug', $slug)
                ->get(['id', 'description']);

            foreach ($rows as $row) {
                $current = trim((string) ($row->description ?? ''));
                if ($current === '' || $current === $texts['legacy_vi']) {
                    DB::table('product_category_translations')
                        ->where('id', $row->id)
                        ->update(['description' => $texts['en']]);
                    $updatedAny = true;
                }
            }
        }

        if ($updatedAny && class_exists(ProductCategory::class)) {
            ProductCategory::clearCategoryCaches();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No destructive action on rollback to protect CMS-edited product category descriptions
    }
};
