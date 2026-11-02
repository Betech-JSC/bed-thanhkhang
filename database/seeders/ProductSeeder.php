<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Truncate tables to ensure clean seeding
        \Schema::disableForeignKeyConstraints();
        DB::table('brands')->truncate();
        DB::table('brand_translations')->truncate();
        DB::table('product_categories')->truncate();
        DB::table('product_category_translations')->truncate();
        DB::table('products')->truncate();
        DB::table('product_translations')->truncate();
        DB::table('product_ref_categories')->truncate();
        DB::table('related_products')->truncate();
        \Schema::enableForeignKeyConstraints();

        // 2. Insert Brands
        $brands = [
            [
                'id' => 1,
                'status' => 'ACTIVE',
                'position' => 1,
                'image' => json_encode(['path' => 'uploads/brands/brand_1.png', 'alt' => 'Konoike Logistics']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'status' => 'ACTIVE',
                'position' => 2,
                'image' => json_encode(['path' => 'uploads/brands/brand_2.png', 'alt' => 'Alpha Trans']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'status' => 'ACTIVE',
                'position' => 3,
                'image' => json_encode(['path' => 'uploads/brands/brand_3.png', 'alt' => 'Funky Brand']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        DB::table('brands')->insert($brands);

        $brandTranslations = [
            [
                'brand_id' => 1,
                'locale' => 'vi',
                'title' => 'Konoike Logistics',
                'slug' => 'konoike-logistics',
                'summary' => 'Tập đoàn logistics hàng đầu Nhật Bản và Việt Nam',
                'description' => 'Cung cấp các giải pháp logistics toàn diện từ vận tải, kho bãi đến quản lý chuỗi cung ứng.',
            ],
            [
                'brand_id' => 2,
                'locale' => 'vi',
                'title' => 'Alpha Trans',
                'slug' => 'alpha-trans',
                'summary' => 'Chuyên gia vận tải nội địa và quốc tế',
                'description' => 'Dịch vụ vận tải đa phương thức với mạng lưới toàn cầu chuyên nghiệp.',
            ],
            [
                'brand_id' => 3,
                'locale' => 'vi',
                'title' => 'Funky Brand',
                'slug' => 'funky-brand',
                'summary' => 'Giải pháp vận chuyển sáng tạo và nhanh chóng',
                'description' => 'Ứng dụng công nghệ hiện đại tối ưu hóa quy trình giao nhận hàng hóa chặng cuối.',
            ]
        ];
        DB::table('brand_translations')->insert($brandTranslations);

        // 3. Insert Product Categories
        $categories = [
            [
                'id' => 1,
                'parent_id' => 0,
                'status' => 'ACTIVE',
                'position' => 1,
                'view_count' => 120,
                'image' => json_encode(['path' => 'uploads/categories/cat_1.png', 'alt' => 'Vận tải đường bộ']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'status' => 'ACTIVE',
                'position' => 2,
                'view_count' => 95,
                'image' => json_encode(['path' => 'uploads/categories/cat_2.png', 'alt' => 'Vận tải đường biển']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'parent_id' => 0,
                'status' => 'ACTIVE',
                'position' => 3,
                'view_count' => 150,
                'image' => json_encode(['path' => 'uploads/categories/cat_3.png', 'alt' => 'Dịch vụ kho bãi']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'parent_id' => 0,
                'status' => 'ACTIVE',
                'position' => 4,
                'view_count' => 200,
                'image' => json_encode(['path' => 'uploads/categories/cat_4.png', 'alt' => 'Chuỗi cung ứng lạnh']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        DB::table('product_categories')->insert($categories);

        $categoryTranslations = [
            [
                'product_category_id' => 1,
                'locale' => 'vi',
                'title' => 'Vận tải đường bộ',
                'slug' => 'van-tai-duong-bo',
                'description' => 'Dịch vụ vận chuyển hàng hóa bằng xe tải, xe container nội địa chất lượng cao.',
            ],
            [
                'product_category_id' => 2,
                'locale' => 'vi',
                'title' => 'Vận tải đường biển',
                'slug' => 'van-tai-duong-bien',
                'description' => 'Vận chuyển hàng hóa quốc tế bằng đường biển, xuất nhập khẩu chính ngạch.',
            ],
            [
                'product_category_id' => 3,
                'locale' => 'vi',
                'title' => 'Dịch vụ kho bãi',
                'slug' => 'dich-vu-kho-bai',
                'description' => 'Hệ thống kho thường, kho mát và kho lạnh đạt chuẩn quốc tế.',
            ],
            [
                'product_category_id' => 4,
                'locale' => 'vi',
                'title' => 'Chuỗi cung ứng lạnh',
                'slug' => 'chuoi-cung-ung-lanh',
                'description' => 'Kiểm soát nhiệt độ khép kín từ kho mát đến vận tải lạnh chuyên nghiệp.',
            ]
        ];
        DB::table('product_category_translations')->insert($categoryTranslations);

        // 4. Insert Products
        $products = [
            [
                'id' => 1,
                'brand_id' => 1,
                'status' => 'ACTIVE',
                'is_featured' => 1,
                'is_new' => 1,
                'is_stock' => 1,
                'position' => 1,
                'view_count' => 100,
                'image' => json_encode(['path' => 'uploads/products/product_1.jpg', 'alt' => 'Vận chuyển container lạnh']),
                'images' => json_encode([['path' => 'uploads/products/product_1.jpg', 'alt' => 'Vận chuyển container lạnh']]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'brand_id' => 1,
                'status' => 'ACTIVE',
                'is_featured' => 1,
                'is_new' => 0,
                'is_stock' => 1,
                'position' => 2,
                'view_count' => 150,
                'image' => json_encode(['path' => 'uploads/products/product_2.jpg', 'alt' => 'Cho thuê kho lạnh tiêu chuẩn Nhật Bản']),
                'images' => json_encode([['path' => 'uploads/products/product_2.jpg', 'alt' => 'Cho thuê kho lạnh tiêu chuẩn Nhật Bản']]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'brand_id' => 2,
                'status' => 'ACTIVE',
                'is_featured' => 0,
                'is_new' => 1,
                'is_stock' => 1,
                'position' => 3,
                'view_count' => 80,
                'image' => json_encode(['path' => 'uploads/products/product_3.jpg', 'alt' => 'Vận tải hàng siêu trường siêu trọng']),
                'images' => json_encode([['path' => 'uploads/products/product_3.jpg', 'alt' => 'Vận tải hàng siêu trường siêu trọng']]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'brand_id' => 2,
                'status' => 'ACTIVE',
                'is_featured' => 0,
                'is_new' => 0,
                'is_stock' => 1,
                'position' => 4,
                'view_count' => 210,
                'image' => json_encode(['path' => 'uploads/products/product_4.jpg', 'alt' => 'Dịch vụ khai báo hải quan trọn gói']),
                'images' => json_encode([['path' => 'uploads/products/product_4.jpg', 'alt' => 'Dịch vụ khai báo hải quan trọn gói']]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'brand_id' => 3,
                'status' => 'ACTIVE',
                'is_featured' => 0,
                'is_new' => 0,
                'is_stock' => 1,
                'position' => 5,
                'view_count' => 130,
                'image' => json_encode(['path' => 'uploads/products/product_5.jpg', 'alt' => 'Lưu kho hàng bách hóa tổng hợp']),
                'images' => json_encode([['path' => 'uploads/products/product_5.jpg', 'alt' => 'Lưu kho hàng bách hóa tổng hợp']]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'brand_id' => 3,
                'status' => 'ACTIVE',
                'is_featured' => 1,
                'is_new' => 1,
                'is_stock' => 1,
                'position' => 6,
                'view_count' => 170,
                'image' => json_encode(['path' => 'uploads/products/product_6.jpg', 'alt' => 'Giao hàng chặng cuối nhanh chóng']),
                'images' => json_encode([['path' => 'uploads/products/product_6.jpg', 'alt' => 'Giao hàng chặng cuối nhanh chóng']]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        DB::table('products')->insert($products);

        $productTranslations = [
            [
                'product_id' => 1,
                'locale' => 'vi',
                'title' => 'Vận chuyển container lạnh',
                'slug' => 'van-chuyen-container-lanh',
                'description' => 'Vận chuyển hàng hóa đông lạnh và tươi sống bằng xe container lạnh chuyên dụng.',
                'content' => '<p>Vận chuyển hàng hóa đông lạnh và tươi sống bằng xe container lạnh chuyên dụng. Đây là dữ liệu mẫu của hệ thống.</p>',
            ],
            [
                'product_id' => 2,
                'locale' => 'vi',
                'title' => 'Cho thuê kho lạnh tiêu chuẩn Nhật Bản',
                'slug' => 'cho-thue-kho-lanh-tieu-chuan-nhat-ban',
                'description' => 'Hệ thống kho bảo quản nhiệt độ từ -25°C đến +15°C với công nghệ hiện đại.',
                'content' => '<p>Hệ thống kho bảo quản nhiệt độ từ -25°C đến +15°C với công nghệ hiện đại. Đây là dữ liệu mẫu của hệ thống.</p>',
            ],
            [
                'product_id' => 3,
                'locale' => 'vi',
                'title' => 'Vận tải hàng siêu trường siêu trọng',
                'slug' => 'van-tai-hang-sieu-truong-sieu-trong',
                'description' => 'Giải pháp vận chuyển thiết bị công nghiệp nặng và hàng quá khổ an toàn.',
                'content' => '<p>Giải pháp vận chuyển thiết bị công nghiệp nặng và hàng quá khổ an toàn. Đây là dữ liệu mẫu của hệ thống.</p>',
            ],
            [
                'product_id' => 4,
                'locale' => 'vi',
                'title' => 'Dịch vụ khai báo hải quan trọn gói',
                'slug' => 'dich-vu-khai-bao-hai-quan-tron-goi',
                'description' => 'Thông quan nhanh chóng hàng hóa xuất nhập khẩu tại các cảng biển và sân bay.',
                'content' => '<p>Thông quan nhanh chóng hàng hóa xuất nhập khẩu tại các cảng biển và sân bay. Đây là dữ liệu mẫu của hệ thống.</p>',
            ],
            [
                'product_id' => 5,
                'locale' => 'vi',
                'title' => 'Lưu kho hàng bách hóa tổng hợp',
                'slug' => 'luu-kho-hang-bach-hoa-tong-hop',
                'description' => 'Dịch vụ lưu kho an toàn với hệ thống camera giám sát 24/7 và PCCC tiêu chuẩn.',
                'content' => '<p>Dịch vụ lưu kho an toàn với hệ thống camera giám sát 24/7 và PCCC tiêu chuẩn. Đây là dữ liệu mẫu của hệ thống.</p>',
            ],
            [
                'product_id' => 6,
                'locale' => 'vi',
                'title' => 'Giao hàng chặng cuối nhanh chóng',
                'slug' => 'giao-hang-chang-cuoi-nhanh-chong',
                'description' => 'Giải pháp phân phối hàng hóa đô thị hiệu quả tối ưu thời gian giao hàng.',
                'content' => '<p>Giải pháp phân phối hàng hóa đô thị hiệu quả tối ưu thời gian giao hàng. Đây là dữ liệu mẫu của hệ thống.</p>',
            ]
        ];
        DB::table('product_translations')->insert($productTranslations);

        // 5. Insert Pivot Table links
        $productRefCategories = [
            ['product_id' => 1, 'product_category_id' => 4], // Reefer Container Transport -> Cold Chain
            ['product_id' => 2, 'product_category_id' => 3], // Japanese Standard Cold Storage -> Warehouse
            ['product_id' => 2, 'product_category_id' => 4], // Japanese Standard Cold Storage -> Cold Chain
            ['product_id' => 3, 'product_category_id' => 1], // Oversized Cargo -> Road Transport
            ['product_id' => 4, 'product_category_id' => 2], // Customs Clearance -> Sea Transport
            ['product_id' => 5, 'product_category_id' => 3], // General Cargo -> Warehouse
            ['product_id' => 6, 'product_category_id' => 1]  // Last-Mile Delivery -> Road Transport
        ];
        DB::table('product_ref_categories')->insert($productRefCategories);

        // 6. Link products to courses (type = COURSE)
        $courses = DB::table('posts')->where('type', 'COURSE')->pluck('id')->toArray();
        if (!empty($courses)) {
            DB::table('product_ref_posts')->truncate();
            $productRefPosts = [];
            $products = DB::table('products')->pluck('id')->toArray();
            foreach ($products as $productId) {
                // Link each product to 1-2 random courses
                $randomCourses = (array) array_rand(array_combine($courses, $courses), min(count($courses), rand(1, 2)));
                foreach ($randomCourses as $courseId) {
                    $productRefPosts[] = [
                        'product_id' => $productId,
                        'post_id' => $courseId,
                    ];
                }
            }
            DB::table('product_ref_posts')->insert($productRefPosts);
        }
    }
}
