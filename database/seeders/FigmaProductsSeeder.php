<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class FigmaProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed 21 Figma products (1 Model Cáo ID 1 + 16 catalog + 4 related products) safely.
     * ZERO data loss: Never drops tables or truncates existing data.
     * ZERO pricing: price and compare_at_price are strictly NULL for all products.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            // 1. Seed / Update 4 Figma Categories
            $categoriesConfig = [
                'space_decor' => [
                    'vi_slug' => 'trang-tri-khong-gian',
                    'en_slug' => 'space-decor',
                    'vi_title' => 'Trang trí không gian',
                    'en_title' => 'Space Decor',
                    'vi_desc' => 'Bộ sưu tập mô hình và đồ decor không gian sống & làm việc hiện đại.',
                    'en_desc' => 'Modern 3D printed model and decor collection for home and workspace.',
                    'position' => 1,
                ],
                'keychains' => [
                    'vi_slug' => 'moc-khoa-phu-kien',
                    'en_slug' => 'keychains-accessories',
                    'vi_title' => 'Móc khoá & Phụ kiện',
                    'en_title' => 'Keychains & Accessories',
                    'vi_desc' => 'Móc khoá cơ khí độc bản, phụ kiện cá nhân in 3D tinh xảo.',
                    'en_desc' => 'Unique mechanical keychains and precision 3D printed personal accessories.',
                    'position' => 2,
                ],
                'stationery' => [
                    'vi_slug' => 'trang-tri-do-dung',
                    'en_slug' => 'decor-lifestyle',
                    'vi_title' => 'Trang trí & Đồ dùng',
                    'en_title' => 'Decor & Lifestyle',
                    'vi_desc' => 'Đồ dùng bàn làm việc thông minh, đèn LED tổ ong và đồ decor đa giác.',
                    'en_desc' => 'Smart desktop essentials, honeycomb LED lamps, and geometric decor.',
                    'position' => 3,
                ],
                'custom' => [
                    'vi_slug' => 'gia-cong-theo-yeu-cau',
                    'en_slug' => 'custom-manufacturing',
                    'vi_title' => 'Gia công theo yêu cầu',
                    'en_title' => 'Custom Manufacturing',
                    'vi_desc' => 'Dịch vụ in 3D công nghiệp kỹ thuật cao, sợi carbon và resin 8K sắc nét.',
                    'en_desc' => 'High-spec industrial 3D printing, carbon fiber composites, and 8K resin.',
                    'position' => 4,
                ],
            ];

            $categoryMap = [];

            foreach ($categoriesConfig as $key => $config) {
                $catTrans = DB::table('product_category_translations')
                    ->where('slug', $config['vi_slug'])
                    ->orWhere('slug', $config['en_slug'])
                    ->first();

                if ($catTrans) {
                    $catId = $catTrans->product_category_id;
                    DB::table('product_categories')->where('id', $catId)->update([
                        'status' => 'ACTIVE',
                        'position' => $config['position'],
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = DB::table('product_categories')->insertGetId([
                        'parent_id' => 0,
                        'status' => 'ACTIVE',
                        'position' => $config['position'],
                        'view_count' => rand(50, 150),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Upsert VI translation
                DB::table('product_category_translations')->updateOrInsert(
                    ['product_category_id' => $catId, 'locale' => 'vi'],
                    [
                        'title' => $config['vi_title'],
                        'slug' => $config['vi_slug'],
                        'seo_slug' => $config['vi_slug'],
                        'description' => $config['vi_desc'],
                        'seo_meta_title' => $config['vi_title'] . ' - HH Maker',
                        'seo_meta_description' => $config['vi_desc'],
                    ]
                );

                // Upsert EN translation
                DB::table('product_category_translations')->updateOrInsert(
                    ['product_category_id' => $catId, 'locale' => 'en'],
                    [
                        'title' => $config['en_title'],
                        'slug' => $config['en_slug'],
                        'seo_slug' => $config['en_slug'],
                        'description' => $config['en_desc'],
                        'seo_meta_title' => $config['en_title'] . ' - HH Maker',
                        'seo_meta_description' => $config['en_desc'],
                    ]
                );

                $categoryMap[$key] = $catId;
            }

            // 2. Define Products List (21 items: Model Cáo ID 1 + 16 catalog items + 4 related items)
            // Featured items are strictly 4 per category:
            // - space_decor: Mô hình Cáo trang trí, Mô hình Phi hành gia, Tượng Cyber Wolf, Đèn LED Hexagon
            // - keychains: Móc khoá trái tim cơ khí, Móc khoá turbine phản lực, Móc khoá động cơ V8, Móc khoá vô lăng F1
            // - stationery: Giá đỡ tai nghe Voronoi, Hộp đựng danh thiếp Cyberpunk, Đèn ngủ mặt trăng, Mô hình tàu vũ trụ Discovery
            // - custom: Mô hình Iron Man Mark 85, Khung vỏ robot Carbon Fiber, Cụm bánh răng hành tinh, Khớp nối cầu Bionic SLS
            $productsList = [
                // Product ID 1: Mô hình Cáo trang trí (space_decor, featured = 1)
                [
                    'target_id' => 1,
                    'slug' => 'mo-hinh-cao-trang-tri',
                    'category_key' => 'space_decor',
                    'is_featured' => 1,
                    'image' => '/images/pdp/product_main.png',
                    'material' => 'Nhựa PLA Matte Cao Cấp',
                    'print_technology' => 'FDM High-Precision',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Mô hình Cáo trang trí',
                    'title_en' => 'Decorative Fox Model',
                    'desc_vi' => 'Thêm một chút linh hoạt và thông minh vào góc nhỏ của bạn. Chú cáo nhỏ với chiếc đuôi xù đặc trưng và những lớp in tỉ mỉ không chỉ là một món đồ trang trí, mà còn là người bạn đồng hành truyền cảm hứng mỗi ngày.',
                    'desc_en' => 'Add a touch of agility and smart elegance to your space. Featuring an iconic bushy tail and meticulous 3D printed layers, this companion inspires creativity every day.',
                    'content_vi' => '<h3>Quy cách in 3D & Thông số kỹ thuật</h3><p>Mô hình chú cáo phong cách Low-Poly tối giản là điểm nhấn hiện đại cho bàn làm việc hoặc kệ sách. Được in bằng công nghệ FDM độ chính xác cao với các mặt cắt đa giác chuẩn xác, phản chiếu ánh sáng độc đáo ở từng góc nhìn.</p><ul><li><strong>Vật liệu:</strong> Nhựa PLA Matte cao cấp, chống bám vân tay, thân thiện môi trường.</li><li><strong>Độ dày lớp in (Layer Height):</strong> 0.16mm siêu mịn.</li><li><strong>Độ cứng (Infill):</strong> 20% cấu trúc Gyroid chịu lực vững chắc.</li><li><strong>Kích thước tiêu chuẩn:</strong> 120 x 85 x 140 mm.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày làm việc.</li></ul>',
                    'content_en' => '<h3>3D Printing Specifications & Details</h3><p>The minimal Low-Poly fox model brings modern charm to any workspace or bookshelf. Fabricated via high-precision FDM technology with crisp polygonal facets that catch light beautifully from all angles.</p><ul><li><strong>Material:</strong> Premium Matte PLA, anti-fingerprint, eco-friendly.</li><li><strong>Layer Height:</strong> Ultra-fine 0.16mm.</li><li><strong>Infill:</strong> 20% structural Gyroid infill.</li><li><strong>Dimensions:</strong> 120 x 85 x 140 mm.</li><li><strong>Lead Time:</strong> 1-2 business days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM High-Precision'],
                        ['label' => 'Vật liệu', 'value' => 'Nhựa PLA Matte Cao Cấp'],
                        ['label' => 'Kích thước mẫu', 'value' => '120 x 85 x 140 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                        ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Zero-support)'],
                    ],
                ],

                // 1. Iron Man Mark 85 (custom, featured = 1)
                [
                    'slug' => 'iron-man-mark-85-resin-8k',
                    'category_key' => 'custom',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-1.png',
                    'material' => '8K High-Detail Tough Resin',
                    'print_technology' => 'SLA 8K Photochemical',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                    'title_en' => 'Iron Man Mark 85 Model - High Detail 8K Resin',
                    'desc_vi' => 'Mô hình giáp Iron Man tỉ lệ 1:10, độ chi tiết sắc nét từng đường rãnh cơ khí với công nghệ SLA quang hóa.',
                    'desc_en' => '1:10 scale Iron Man armor model with crisp mechanical grooves produced using photochemical SLA technology.',
                    'content_vi' => '<h3>Mô tả & Quy cách gia công</h3><p>Mô hình giáp Iron Man Mark 85 tái hiện sống động bộ giáp công nghệ nano đỉnh cao trong vũ trụ Marvel. Từng tấm giáp ghép nối, đường rãnh tản nhiệt và chi tiết lò phản ứng Arc Reactor đều đạt độ phân giải quang học 8K cực đại.</p><ul><li><strong>Công nghệ in:</strong> SLA (Stereolithography) quang hóa độ phân giải 8K.</li><li><strong>Độ phân giải lớp (Layer):</strong> 0.025mm (25 micron) hầu như triệt tiêu hoàn toàn đường kẻ lớp in.</li><li><strong>Vật liệu:</strong> Resin cao cấp chịu lực (Tough Resin), kháng giòn gãy và chống cong vênh.</li><li><strong>Hậu kỳ:</strong> Rửa siêu âm bằng cồn IPA công nghiệp và sấy lưu hóa trong buồng chiếu UV đa hướng.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Description & Fabrication Specs</h3><p>The Iron Man Mark 85 model sharply recreates the iconic nano-tech superhero armor. Every armor plate joint, heat dispersion line, and Arc Reactor detail is brought to life with 8K photochemical optical precision.</p><ul><li><strong>Technology:</strong> High-resolution 8K SLA (Stereolithography).</li><li><strong>Layer Height:</strong> 0.025mm (25 microns) virtually eliminating visible layer lines.</li><li><strong>Material:</strong> Tough photopolymer resin, shatter-resistant and warp-free.</li><li><strong>Post-processing:</strong> Ultrasonic IPA bath cleaning and omnidirectional UV curing.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'SLA 8K Photochemical'],
                        ['label' => 'Vật liệu', 'value' => '8K High-Detail Tough Resin'],
                        ['label' => 'Kích thước mẫu', 'value' => '180 x 110 x 280 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                        ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV 405nm)'],
                        ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% (Solid Resin)'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support dễ tách (Light Touch)'],
                    ],
                ],

                // 2. Khung Vỏ Robot Mini - Carbon Fiber (custom, featured = 1)
                [
                    'slug' => 'khung-vo-robot-carbon-fiber',
                    'category_key' => 'custom',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-2.png',
                    'material' => 'PETG Gia Cường 20% Carbon Fiber (PETG-CF)',
                    'print_technology' => 'FDM Industrial Hardened Nozzle',
                    'lead_time' => '2-3 ngày',
                    'title_vi' => 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                    'title_en' => 'Mini Robot Mechanical Chassis - Impact Resistant Carbon Fiber',
                    'desc_vi' => 'Khung robot gia cường sợi carbon chịu lực va đập vượt trội, kháng uốn và ổn định kích thước tuyệt đối.',
                    'desc_en' => 'Carbon fiber reinforced robot chassis offering outstanding impact resistance, flexural strength, and dimensional stability.',
                    'content_vi' => '<h3>Khung Vỏ Kỹ Thuật Chịu Lực Cao</h3><p>Sản phẩm chuyên dụng cho robot tự hành, drone FPV và các cơ cấu tự động hóa đòi hỏi tỉ số độ cứng trên trọng lượng (stiffness-to-weight) tối đa. Sợi carbon cắt ngắn được phân bổ đồng đều trong nền nhựa kỹ thuật PETG.</p><ul><li><strong>Vật liệu:</strong> PETG-CF chứa 20% sợi carbon ngắn, độ cứng uốn tăng 300% so với nhựa ABS thông thường.</li><li><strong>Đầu in:</strong> Đầu phun thép tôi cứng Hardened Steel 0.4mm, nhiệt độ đùn 270°C.</li><li><strong>Dung sai kích thước:</strong> ±0.05mm, đảm bảo ăn khớp chính xác với động cơ bước và vòng bi trục.</li><li><strong>Thời gian gia công:</strong> 2-3 ngày.</li></ul>',
                    'content_en' => '<h3>High-Strength Engineering Chassis</h3><p>Tailored for autonomous mobile robots, FPV drones, and automated mechanisms demanding superior stiffness-to-weight ratio. Chopped carbon fibers are uniformly dispersed within a technical PETG matrix.</p><ul><li><strong>Material:</strong> PETG-CF with 20% chopped carbon fibers, 300% stiffer than standard ABS.</li><li><strong>Extruder:</strong> 0.4mm Hardened Steel Nozzle running at 270°C.</li><li><strong>Tolerance:</strong> ±0.05mm ensuring exact motor mount and bearing fitment.</li><li><strong>Lead Time:</strong> 2-3 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Industrial Hardened Nozzle'],
                        ['label' => 'Vật liệu', 'value' => 'PETG Gia Cường 20% Carbon Fiber (PETG-CF)'],
                        ['label' => 'Kích thước mẫu', 'value' => '220 x 160 x 85 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Thép Tôi Cứng (Hardened Steel)'],
                        ['label' => 'Mật độ ruột', 'value' => '40% Honeycomb Chịu Lực'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support Dễ Tách'],
                    ],
                ],

                // 3. Hộp Đựng Danh Thiếp Cyberpunk (stationery, featured = 1)
                [
                    'slug' => 'hop-dung-danh-thiep-cyberpunk',
                    'category_key' => 'stationery',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-3.png',
                    'material' => 'PLA+ Matte Futuristic Finish',
                    'print_technology' => 'FDM Precision',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                    'title_en' => 'Cyberpunk Hexagon Business Card & Pen Holder',
                    'desc_vi' => 'Khay bút tổ ong đa giác phong cách Futuristic, tối ưu không gian làm việc công nghệ cao.',
                    'desc_en' => 'Futuristic honeycomb polygon organizer tray, optimizing high-tech desktop workspaces.',
                    'content_vi' => '<h3>Phong cách Futuristic Hiện Đại Cho Bàn Làm Việc</h3><p>Hộp đựng danh thiếp Cyberpunk Hexagon lấy cảm hứng từ kiến trúc công nghệ viễn tưởng. Cấu trúc tổ ong đối xứng giúp lưu trữ gọn gàng danh thiếp chuẩn quốc tế (90x54mm), thẻ nhân viên và 6 vị trí cắm bút công thái học.</p><ul><li><strong>Vật liệu:</strong> PLA+ Matte mờ chống bám vân tay, độ bền cơ học cao.</li><li><strong>Kích thước:</strong> 160 x 95 x 75 mm.</li><li><strong>Màu sắc:</strong> Đen nhám phối viền xanh Cyan công nghệ.</li><li><strong>Thời gian hoàn thành:</strong> Trong vòng 24 giờ.</li></ul>',
                    'content_en' => '<h3>Futuristic High-Tech Desktop Organizer</h3><p>Inspired by sci-fi aesthetics, the Cyberpunk Hexagon organizer features symmetrical honeycomb cells designed to hold international standard business cards (90x54mm), ID badges, and 6 ergonomic pen slots.</p><ul><li><strong>Material:</strong> Matte PLA+ with anti-fingerprint coating.</li><li><strong>Dimensions:</strong> 160 x 95 x 75 mm.</li><li><strong>Colorway:</strong> Matte Black with Cyber Cyan accent trim.</li><li><strong>Turnaround Time:</strong> 24 hours.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Precision'],
                        ['label' => 'Vật liệu', 'value' => 'PLA+ Matte Futuristic Finish'],
                        ['label' => 'Kích thước mẫu', 'value' => '160 x 95 x 75 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '15% Grid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place)'],
                    ],
                ],

                // 4. Móc Khoá Turbine Phản Lực (keychains, featured = 1)
                [
                    'slug' => 'moc-khoa-turbine-phan-luc',
                    'category_key' => 'keychains',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-4.png',
                    'material' => 'Engineering Tough Resin / Titanium Coating',
                    'print_technology' => 'SLA Precision Micro-Detail',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                    'title_en' => 'Jet Turbine Keychain with Spinning Fan Blades',
                    'desc_vi' => 'Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt.',
                    'desc_en' => 'Aerodynamic turbine blades that spin smoothly when blown, finished with an eye-catching metallic titanium coating.',
                    'content_vi' => '<h3>Cơ Khí Hàng Không Thu Nhỏ</h3><p>Móc khoá mô phỏng động cơ phản lực cánh quạt Turbofan của máy bay chở khách hiện đại. Cụm 12 cánh quạt bên trong được gắn trên trục bi xoay bằng thép không gỉ, cho phép cánh quạt quay tít êm ái khi thổi hơi nhẹ.</p><ul><li><strong>Độ chính xác:</strong> In SLA quang hóa với khe hở trục chỉ 0.15mm.</li><li><strong>Bề mặt:</strong> Sơn phủ titan ánh kim metallic cao cấp, phủ clear-coat chống trầy xước.</li><li><strong>Kích thước:</strong> Đường kính 30mm, chiều dài 48mm.</li><li><strong>Thời gian gia công:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>Miniature Aviation Engineering</h3><p>Scale replica of a commercial airliner turbofan jet engine. Features a 12-blade rotor mounted on miniature stainless steel bearings, spinning freely with a gentle blow.</p><ul><li><strong>Precision:</strong> SLA printed with 0.15mm mechanical clearance.</li><li><strong>Finish:</strong> Metallic titanium coating with scratch-resistant clear coat.</li><li><strong>Dimensions:</strong> 30mm diameter, 48mm length.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'SLA Precision Micro-Detail'],
                        ['label' => 'Vật liệu', 'value' => 'Engineering Tough Resin / Titanium Coating'],
                        ['label' => 'Kích thước mẫu', 'value' => 'Ø30 x 48 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                        ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV)'],
                        ['label' => 'Mật độ ruột', 'value' => 'Đặc 100%'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Micro-support tách siêu mịn'],
                    ],
                ],

                // 5. Giá Đỡ Tai Nghe Voronoi (stationery, featured = 1)
                [
                    'slug' => 'gia-do-tai-nghe-voronoi',
                    'category_key' => 'stationery',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-5.png',
                    'material' => 'PETG Gradient Bionic Structure',
                    'print_technology' => 'FDM Advanced Parametric',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                    'title_en' => 'Voronoi Geometrical Headphone Stand',
                    'desc_vi' => 'Giá đỡ tai nghe dạng lưới cấu trúc bionic Voronoi siêu bền, tôn lên vẻ thẩm mỹ cho góc làm việc.',
                    'desc_en' => 'Ultra-durable bionic Voronoi lattice headphone holder, elevating desktop aesthetics.',
                    'content_vi' => '<h3>Kiến Trúc Sinh Học Voronoi Đẳng Cấp</h3><p>Giá đỡ tai nghe sử dụng thuật toán phân chia không gian Voronoi tự nhiên, giảm 40% trọng lượng vật liệu nhưng vẫn duy trì khả năng chịu tải trên 3kg cho mọi dòng tai nghe phòng thu (Sennheiser, Sony, Beyerdynamic).</p><ul><li><strong>Vật liệu:</strong> PETG dẻo dai kháng va đập, chân đế cao su chống trượt.</li><li><strong>Chiều cao:</strong> 250mm, mặt cung đỡ cong công thái học chống hằn đệm tai nghe.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Parametric Bionic Voronoi Architecture</h3><p>Engineered using natural Voronoi cell partitioning algorithms, shedding 40% material weight while supporting over 3kg loads for studio headphones (Sennheiser, Sony, Beyerdynamic).</p><ul><li><strong>Material:</strong> Tough PETG with rubberized non-slip feet.</li><li><strong>Height:</strong> 250mm with ergonomic curved headband cradle.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Advanced Parametric'],
                        ['label' => 'Vật liệu', 'value' => 'PETG Gradient Bionic Structure'],
                        ['label' => 'Kích thước mẫu', 'value' => '140 x 130 x 250 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                        ['label' => 'Mật độ ruột', 'value' => '25% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Cấu trúc Bionic tự chống đỡ)'],
                    ],
                ],

                // 6. Cụm Bánh Răng Hành Tinh (custom, featured = 1)
                [
                    'slug' => 'cum-banh-rang-hanh-tinh',
                    'category_key' => 'custom',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-6.png',
                    'material' => 'Nylon PA12 Kỹ Thuật Tự Bôi Trơn',
                    'print_technology' => 'FDM High-Tolerance Engineering',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                    'title_en' => 'Planetary Gearbox Assembly Set',
                    'desc_vi' => 'Hộp số hành tinh tỉ số truyền 1:5, khớp răng chuẩn dung sai cơ khí, xoay êm ái linh hoạt.',
                    'desc_en' => '1:5 gear reduction planetary gearbox with precise mechanical tolerances and smooth rotation.',
                    'content_vi' => '<h3>Mô Hình Truyền Động Cơ Khí Chuẩn Xác</h3><p>Cụm hộp số giảm tốc hành tinh gồm 1 bánh răng mặt trời (Sun gear), 3 bánh răng hành tinh (Planet gears) và 1 vành răng bao ngoài (Ring gear). Khớp răng biên dạng thân khai chuẩn mô-đun M1.0 vận hành mượt mà.</p><ul><li><strong>Vật liệu:</strong> Nylon PA12 chịu mài mòn cao, hệ số ma sát thấp.</li><li><strong>Tỉ số truyền:</strong> 1:5.</li><li><strong>Ứng dụng:</strong> Giảng dạy STEM, đồ chơi giảm stress cơ học, mẫu thử R&D.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>High-Precision Mechanical Powertrain</h3><p>Planetary gear reduction set comprising 1 Sun gear, 3 Planet gears, and 1 outer Ring gear. Involute tooth profile running at M1.0 module for zero-jam smooth rotation.</p><ul><li><strong>Material:</strong> Self-lubricating wear-resistant Nylon PA12.</li><li><strong>Gear Ratio:</strong> 1:5 reduction.</li><li><strong>Usage:</strong> STEM training, desk fidget, functional mechanical prototype.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM High-Tolerance Engineering'],
                        ['label' => 'Vật liệu', 'value' => 'Nylon PA12 Kỹ Thuật Tự Bôi Trơn'],
                        ['label' => 'Kích thước mẫu', 'value' => '110 x 110 x 45 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                        ['label' => 'Mật độ ruột', 'value' => '35% Rectilinear'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Dung sai trục 0.2mm)'],
                    ],
                ],

                // 7. Tượng Cyber Wolf Low-Poly (space_decor, featured = 1)
                [
                    'slug' => 'tuong-cyber-wolf-low-poly',
                    'category_key' => 'space_decor',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-7.png',
                    'material' => 'Silk Metallic PLA / Protective Nano Layer',
                    'print_technology' => 'FDM Multi-Axis Contour',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                    'title_en' => 'Low-Poly Cyber Wolf Art Sculpture',
                    'desc_vi' => 'Tượng sói phong cách đa giác góc cạnh, bề mặt được xử lý mịn màng và phủ lớp nano bảo vệ bền bỉ.',
                    'desc_en' => 'Geometric faceted wolf sculpture featuring smooth surfaces and protective nano-coating.',
                    'content_vi' => '<h3>Tác Phẩm Điêu Khắc Đa Giác Nghệ Thuật</h3><p>Tượng Sói Cyber Wolf mang năng lượng dũng mãnh và quyết đoán vào không gian làm việc. Hơn 150 mặt đa giác phẳng tạo nên vũ điệu ánh sáng độc đáo khi thay đổi góc chiếu sáng của phòng.</p><ul><li><strong>Vật liệu:</strong> Silk PLA ánh kim bạch kim sang trọng.</li><li><strong>Kích thước:</strong> Chiều dài 210mm x Cao 165mm x Rộng 95mm.</li><li><strong>Xử lý bề mặt:</strong> Phủ bóng Nano UV chống phai màu dưới ánh nắng.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Artistic Geometric Polygonal Sculpture</h3><p>The Cyber Wolf sculpture conveys boldness and determination to your decor. Over 150 planar polygonal facets produce a striking light play as ambient lighting shifts.</p><ul><li><strong>Material:</strong> Premium Platinum Silk Metallic PLA.</li><li><strong>Dimensions:</strong> 210mm length x 165mm height x 95mm width.</li><li><strong>Surface Finish:</strong> Nano UV coating preventing fading.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Multi-Axis Contour'],
                        ['label' => 'Vật liệu', 'value' => 'Silk Metallic PLA / Nano Coating'],
                        ['label' => 'Kích thước mẫu', 'value' => '210 x 95 x 165 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '15% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support tối giản'],
                    ],
                ],

                // 8. Móc Khoá Động Cơ V8 Piston (keychains, featured = 1)
                [
                    'slug' => 'moc-khoa-dong-co-v8-piston',
                    'category_key' => 'keychains',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-8.png',
                    'material' => 'PLA+ Tough Print-in-Place',
                    'print_technology' => 'FDM Print-in-Place Khớp Liền Khối',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                    'title_en' => 'Moving Piston V8 Engine Keychain',
                    'desc_vi' => 'Mô phỏng trục khuỷu và piston động cơ V8 xe đua cơ bắp chuyển động tịnh tiến khi vặn núm xoay.',
                    'desc_en' => 'Simulates crankshaft and reciprocating pistons of a muscle car V8 engine when rotating the dial.',
                    'content_vi' => '<h3>Trái Tim Động Cơ V8 Trên Tay Bạn</h3><p>Đỉnh cao công nghệ in 3D Print-in-Place: Toàn bộ khối động cơ V8 gồm trục khuỷu, thanh truyền và 8 piston được in liền mạch cùng một lúc mà không cần tháo rời lắp ốc. Xoay núm phía sau để chiêm ngưỡng các piston chuyển động lên xuống nhịp nhàng.</p><ul><li><strong>Công nghệ:</strong> In liền mạch không cần lắp ráp (Print-in-Place).</li><li><strong>Vật liệu:</strong> PLA+ Tough chịu mỏi cơ học tốt.</li><li><strong>Kích thước:</strong> 55 x 35 x 30 mm.</li><li><strong>Thời gian gia công:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>V8 Engine Heart in Your Hands</h3><p>Print-in-Place 3D printing pinnacle: The entire V8 block with crankshaft, connecting rods, and 8 pistons is printed simultaneously without post-assembly. Turn the rear dial to watch all 8 pistons cycle smoothly.</p><ul><li><strong>Technology:</strong> Monolithic Print-in-Place.</li><li><strong>Material:</strong> High-durability Tough PLA+.</li><li><strong>Dimensions:</strong> 55 x 35 x 30 mm.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place Khớp Liền Khối'],
                        ['label' => 'Vật liệu', 'value' => 'PLA+ Tough Print-in-Place'],
                        ['label' => 'Kích thước mẫu', 'value' => '55 x 35 x 30 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '30% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place liền khối)'],
                    ],
                ],

                // 9. Đèn Ngủ Mặt Trăng 3D Lithophane (stationery, featured = 1)
                [
                    'slug' => 'den-ngu-mat-trang-lithophane',
                    'category_key' => 'stationery',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-9.png',
                    'material' => 'Translucent White Optical PLA',
                    'print_technology' => 'FDM Lithophane Độ Phân Giải 0.12mm',
                    'lead_time' => '2 ngày',
                    'title_vi' => 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                    'title_en' => '3D Lithophane Moon Night Lamp with Wooden Base',
                    'desc_vi' => 'Quả cầu địa hình bề mặt mặt trăng với công nghệ thấu quang Lithophane, phát sáng dịu nhẹ ấm áp.',
                    'desc_en' => 'Moon surface topography sphere with Lithophane light-transmitting technology, emitting soft warm ambient glow.',
                    'content_vi' => '<h3>Ánh Trăng Dịu Êm Trong Phòng Ngủ</h3><p>Sản phẩm ứng dụng kỹ thuật thấu quang Lithophane cổ điển kết hợp mô hình số vệ tinh NASA. Độ dày mỏng của lớp in điều tiết lượng ánh sáng đi qua, tạo nên các mảng sáng tối chân thực của miệng núi lửa và bề mặt mặt trăng.</p><ul><li><strong>Vật liệu:</strong> PLA thấu quang màu trắng ngà tự nhiên.</li><li><strong>Hệ thống đèn:</strong> Module LED vàng ấm 3000K, cấp nguồn qua cáp Type-C tiện lợi.</li><li><strong>Phụ kiện:</strong> Đế gỗ sồi tự nhiên gia công CNC tỉ mỉ.</li><li><strong>Đường kính:</strong> 150mm.</li><li><strong>Thời gian gia công:</strong> 2 ngày.</li></ul>',
                    'content_en' => '<h3>Gentle Moonlight for Your Space</h3><p>Combining traditional Lithophane backlighting optics with NASA satellite digital elevation models. Variable shell thickness modulates light transmission, rendering realistic lunar craters and seas.</p><ul><li><strong>Material:</strong> Translucent warm white optical PLA.</li><li><strong>Lighting:</strong> 3000K warm LED powered via standard USB Type-C.</li><li><strong>Stand:</strong> Solid natural oak wood tripod stand.</li><li><strong>Diameter:</strong> 150mm.</li><li><strong>Lead Time:</strong> 2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Lithophane Độ Phân Giải Cao'],
                        ['label' => 'Vật liệu', 'value' => 'Translucent White Optical PLA'],
                        ['label' => 'Kích thước mẫu', 'value' => 'Ø150 mm (Cầu)'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% (Vỏ thấu quang Lithophane)'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support chân đế'],
                    ],
                ],

                // 10. Khớp Nối Cầu Đa Hướng Ball-Joint Bionic (custom, featured = 1)
                [
                    'slug' => 'khop-noi-cau-bionic-sls',
                    'category_key' => 'custom',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-10.png',
                    'material' => 'Polyamide 12 (Nylon PA12) Bột Thiêu Kết',
                    'print_technology' => 'SLS (Selective Laser Sintering) Công Nghiệp',
                    'lead_time' => '2-3 ngày',
                    'title_vi' => 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                    'title_en' => 'Bionic Multi-Directional Ball-Joint',
                    'desc_vi' => 'Khớp nối linh hoạt in nguyên khối không cần support, độ bền cơ học và chịu mài mòn chuẩn công nghiệp.',
                    'desc_en' => 'Monolithic print-in-place flexible joint with zero support material, offering industrial-grade wear resistance.',
                    'content_vi' => '<h3>Khớp Cầu Cơ Khí Tiêu Chuẩn Công Nghiệp</h3><p>In bằng công nghệ thiêu kết laser bột Polymer SLS, khớp cầu được chế tạo lồng khít bên trong ổ bi với khe hở 0.2mm hoàn toàn không cần vật liệu chống đỡ (support-free). Khả năng chịu lực kéo và xoay 360 độ cực đại.</p><ul><li><strong>Công nghệ:</strong> SLS thiêu kết bằng chùm tia laser CO2.</li><li><strong>Vật liệu:</strong> Bột PA12 nguyên sinh, độ bền kéo 48 MPa.</li><li><strong>Nhiệt độ làm việc:</strong> Lên đến 120°C liên tục.</li><li><strong>Thời gian gia công:</strong> 2-3 ngày.</li></ul>',
                    'content_en' => '<h3>Industrial Grade Mechanical Ball-Joint</h3><p>Selective Laser Sintering (SLS) allows printing spherical joints directly trapped inside sockets with 0.2mm clearance and zero supports. Offers 360-degree rotation and high tensile load capacity.</p><ul><li><strong>Technology:</strong> Industrial SLS laser sintering.</li><li><strong>Material:</strong> Pure PA12 powder, 48 MPa tensile strength.</li><li><strong>Operating Temp:</strong> Up to 120°C continuous.</li><li><strong>Lead Time:</strong> 2-3 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'SLS (Selective Laser Sintering) Công Nghiệp'],
                        ['label' => 'Vật liệu', 'value' => 'Polyamide 12 (Nylon PA12) Bột Thiêu Kết'],
                        ['label' => 'Kích thước mẫu', 'value' => '65 x 65 x 75 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.10 mm'],
                        ['label' => 'Đầu phun', 'value' => 'Chùm tia Laser CO2 (Hệ buồng kín)'],
                        ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% Đồng nhất'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Zero-support (Bột tự nâng đỡ)'],
                    ],
                ],

                // 11. Mô Hình Tàu Vũ Trụ Discovery (stationery, featured = 1)
                [
                    'slug' => 'mo-hinh-tau-vu-tru-discovery',
                    'category_key' => 'stationery',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-11.png',
                    'material' => 'PLA Pro Matte & Transparent Optical Resin',
                    'print_technology' => 'Hybrid FDM + SLA Multi-Material',
                    'lead_time' => '2-3 ngày',
                    'title_vi' => 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                    'title_en' => 'Discovery 3D Spacecraft Model with LED Base',
                    'desc_vi' => 'Mô hình phi thuyền không gian viễn tưởng tỉ mỉ tới từng tấm khiên nhiệt, buồng lái hoàn thiện trong suốt.',
                    'desc_en' => 'Sci-fi spaceship model meticulously crafted down to heat shields and transparent cockpit canopy.',
                    'content_vi' => '<h3>Kiệt Tác Trưng Bày Không Gian Viễn Tưởng</h3><p>Mô hình tàu vũ trụ Discovery tái hiện thiết kế khí động học tương lai. Thân tàu được in FDM siêu mịn phủ sơn weathered chân thực, buồng lái in SLA trong suốt nhìn rõ nội thất điều khiển.</p><ul><li><strong>Chiều dài thân tàu:</strong> 260mm.</li><li><strong>Đế trưng bày:</strong> Đế nam châm hắt sáng LED xanh neon tạo cảm giác phi thuyền đang lướt trong vũ trụ.</li><li><strong>Thời gian gia công:</strong> 2-3 ngày.</li></ul>',
                    'content_en' => '<h3>Sci-Fi Collector Spacecraft Masterpiece</h3><p>The Discovery spacecraft features futuristic aerodynamic lines. Fuselage printed via fine FDM with authentic weathering paint, while the transparent SLA canopy reveals cockpit interior controls.</p><ul><li><strong>Fuselage Length:</strong> 260mm.</li><li><strong>Display Base:</strong> Magnetic neon-blue LED edge-lit pedestal.</li><li><strong>Lead Time:</strong> 2-3 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'Hybrid FDM + SLA Multi-Material'],
                        ['label' => 'Vật liệu', 'value' => 'PLA Pro Matte & Transparent Optical Resin'],
                        ['label' => 'Kích thước mẫu', 'value' => '260 x 140 x 95 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass + SLA Optics'],
                        ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support tách bề mặt mịn'],
                    ],
                ],

                // 12. Khay Tổ Chức Dây Cáp Modular (stationery, featured = 0)
                [
                    'slug' => 'khay-to-chuc-day-cap-modular',
                    'category_key' => 'stationery',
                    'is_featured' => 0,
                    'image' => '/assets/images/products/figma-product-12.png',
                    'material' => 'PETG Matte & Neodymium N52 Magnets',
                    'print_technology' => 'FDM High-Strength Modular',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                    'title_en' => 'Modular Magnetic Cable & Accessories Organizer',
                    'desc_vi' => 'Khay cáp bàn làm việc ghép nối nam châm Neodymium linh hoạt, gom gọn mọi loại dây sạc gọn gàng.',
                    'desc_en' => 'Flexible desktop cable tray connected with Neodymium magnets, organizing all charging cables neatly.',
                    'content_vi' => '<h3>Gọn Gàng & Thông Minh Cho Setup Bàn Làm Việc</h3><p>Bộ 4 khay module ghép nối tự do nhờ các khối nam châm vĩnh cửu Neodymium N52 ẩn tinh tế bên trong thân vỏ in 3D. Rãnh giữ cáp đàn hồi hỗ trợ mọi chuẩn dây từ USB Type-C, Lightning, cáp bện dù đến sạc laptop công suất cao.</p><ul><li><strong>Vật liệu:</strong> PETG Matte đen tuyền chống trầy xước.</li><li><strong>Module:</strong> 4 khay ghép tự do theo chiều ngang hoặc dọc.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Smart Modular Cable Organization for Clean Desks</h3><p>Set of 4 interlocking modules powered by embedded Neodymium N52 magnets. Flexible retention slots accommodate braided cables, Type-C, Lightning, and heavy laptop power cords.</p><ul><li><strong>Material:</strong> Scratch-resistant Matte Black PETG.</li><li><strong>Modularity:</strong> 4 snap-together trays in any orientation.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM High-Strength Modular'],
                        ['label' => 'Vật liệu', 'value' => 'PETG Matte & Neodymium N52 Magnets'],
                        ['label' => 'Kích thước mẫu', 'value' => '200 x 80 x 35 mm (Module)'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '20% Grid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần'],
                    ],
                ],

                // 13. Móc Khoá Vô Lăng F1 Quick Release (keychains, featured = 1)
                [
                    'slug' => 'moc-khoa-vo-lang-f1-quick-release',
                    'category_key' => 'keychains',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-13.png',
                    'material' => 'Tough Resin 8K & Steel Ball Bearings',
                    'print_technology' => 'SLA Precision 25 Micron',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                    'title_en' => 'F1 Quick Release Racing Steering Wheel Keychain',
                    'desc_vi' => 'Mô phỏng vô lăng xe đua F1 với cơ chế tháo lắp khớp bi cực kỳ chân thực, kim loại phối nhựa dẻo cao cấp.',
                    'desc_en' => 'Replicates F1 race car steering wheel with authentic ball-lock quick-release mechanism.',
                    'content_vi' => '<h3>Trải Nghiệm Tốc Độ Công Thức 1 Trên Tay</h3><p>Mô phỏng chuẩn xác vô lăng xe đua F1 tỉ lệ thu nhỏ với đầy đủ nút bấm điều khiển, núm vặn xoay vi sai và cụm lẫy chuyển số paddle shifter. Cơ chế ngàm tháo nhanh Quick-Release nhả bi cho cảm giác bấm mở vô cùng thỏa mãn.</p><ul><li><strong>Công nghệ:</strong> SLA quang hóa độ phân giải 25 micron sắc sảo.</li><li><strong>Sơn hoàn thiện:</strong> Sơn phủ acrylic đa sắc phối grip carbon nhám.</li><li><strong>Thời gian hoàn thành:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>Formula 1 Racing Spirit in Your Pocket</h3><p>Scale miniature of an authentic F1 steering wheel complete with rotary dials, paddle shifters, and tactile buttons. Integrated quick-release ball collar delivers a satisfying mechanical lock click.</p><ul><li><strong>Technology:</strong> 25-micron SLA photochemical printing.</li><li><strong>Finish:</strong> Multi-color acrylic details with carbon-textured grips.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'SLA Precision 25 Micron'],
                        ['label' => 'Vật liệu', 'value' => 'Tough Resin 8K & Steel Ball Bearings'],
                        ['label' => 'Kích thước mẫu', 'value' => '60 x 42 x 25 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                        ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV)'],
                        ['label' => 'Mật độ ruột', 'value' => 'Đặc 100%'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support vi điểm bóc tay'],
                    ],
                ],

                // 14. Đèn Bàn LED Hexagon Tổ Ong (space_decor, featured = 1)
                [
                    'slug' => 'den-ban-led-hexagon-to-ong',
                    'category_key' => 'space_decor',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-14.png',
                    'material' => 'Heat-Resistant PETG & Optical Diffusion Shell',
                    'print_technology' => 'FDM High-Precision Optics',
                    'lead_time' => '2 ngày',
                    'title_vi' => 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                    'title_en' => 'Touch-Sensitive Hexagonal Honeycomb LED Lamp',
                    'desc_vi' => 'Các mô-đun đèn lục giác ghép nối tự do theo sở thích, điều khiển ánh sáng bằng cảm ứng bề mặt.',
                    'desc_en' => 'Modular hexagon light modules freely assembled and operated via capacitive surface touch controls.',
                    'content_vi' => '<h3>Hệ Thống Chiếu Sáng Đa Sắc Cảm Ứng Chạm</h3><p>Các tấm đèn lục giác tổ ong liên kết với nhau qua các chốt ngàm nam châm dẫn điện. Người dùng chỉ cần chạm nhẹ tay lên bề mặt từng tấm đèn để bật/tắt hoặc vuốt để điều chỉnh độ sáng và chế độ chuyển màu RGB sống động.</p><ul><li><strong>Mặt kính khuếch tán:</strong> Nhựa Translucent PETG in 3D với vi cấu trúc tán quang chống lóa mắt.</li><li><strong>Bộ nguồn:</strong> DC 5V/2A an toàn, tiết kiệm điện.</li><li><strong>Thời gian gia công:</strong> 2 ngày.</li></ul>',
                    'content_en' => '<h3>Modular Touch-Responsive Ambient Lighting</h3><p>Magnetic hexagonal honeycomb modules snap together into endless geometric patterns. Gently tap individual tiles to turn on/off or swipe to cycle through brightness and dynamic RGB lighting modes.</p><ul><li><strong>Diffusion Face:</strong> Translucent 3D printed PETG with anti-glare microstructures.</li><li><strong>Power:</strong> Safe 5V/2A USB input.</li><li><strong>Lead Time:</strong> 2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM High-Precision Optics'],
                        ['label' => 'Vật liệu', 'value' => 'Heat-Resistant PETG & Optical Diffusion Shell'],
                        ['label' => 'Kích thước mẫu', 'value' => '130 x 115 x 30 mm (1 Module)'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '15% Honeycomb'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần'],
                    ],
                ],

                // 15. Vỏ Case Bàn Phím Cơ Custom (custom, featured = 0)
                [
                    'slug' => 'vo-case-ban-phim-co-custom',
                    'category_key' => 'custom',
                    'is_featured' => 0,
                    'image' => '/assets/images/products/figma-product-15.png',
                    'material' => 'PETG-CF / Gasket Mount Isolation',
                    'print_technology' => 'FDM Multi-Material Carbon Reinforced',
                    'lead_time' => '2-3 ngày',
                    'title_vi' => 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                    'title_en' => 'Custom 3D Printed Multi-Color Mechanical Keyboard Case',
                    'desc_vi' => 'Vỏ phím cơ layout 75% cấu trúc gasket mount tối ưu âm gõ, chịu nhiệt và chống biến dạng xuất sắc.',
                    'desc_en' => '75% layout keyboard case with gasket-mount structure for acoustic optimization and thermal resistance.',
                    'content_vi' => '<h3>Âm Gõ Đầm Chắc Cho Bàn Phím Custom</h3><p>Vỏ case bàn phím cơ layout 75% / TKL được tối ưu hóa âm học với khoang tiêu âm chuẩn xác và ngàm đệm Gasket mount silicon. Sử dụng vật liệu composite PETG gia cường sợi carbon chống biến dạng khi gõ phím tốc độ cao.</p><ul><li><strong>Vật liệu:</strong> PETG-CF kết hợp PLA Pro hai màu viền thể thao.</li><li><strong>Góc nghiêng:</strong> 6.5 độ công thái học cho cổ tay thoải mái gõ phím lâu.</li><li><strong>Thời gian gia công:</strong> 2-3 ngày.</li></ul>',
                    'content_en' => '<h3>Acoustic Optimized Custom Keyboard Chassis</h3><p>75% / TKL custom keyboard case tailored for deep, thocky acoustics with internal sound chambers and silicone gasket mounting. Carbon-fiber reinforced PETG prevents flexing during intense typing sessions.</p><ul><li><strong>Material:</strong> PETG-CF with dual-tone accent highlights.</li><li><strong>Typing Angle:</strong> Ergonomic 6.5 degrees.</li><li><strong>Lead Time:</strong> 2-3 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Multi-Material Carbon Reinforced'],
                        ['label' => 'Vật liệu', 'value' => 'PETG-CF / Gasket Mount Isolation'],
                        ['label' => 'Kích thước mẫu', 'value' => '325 x 140 x 38 mm (Layout 75%)'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                        ['label' => 'Mật độ ruột', 'value' => '35% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support khu vực cổng kết nối'],
                    ],
                ],

                // 16. Móc Khoá Trái Tim Cơ Khí (keychains, featured = 1)
                [
                    'slug' => 'moc-khoa-trai-tim-co-khi',
                    'category_key' => 'keychains',
                    'is_featured' => 1,
                    'image' => '/assets/images/products/figma-product-16.png',
                    'material' => 'Silk Metallic PLA / Print-in-Place Mechanism',
                    'print_technology' => 'FDM Print-in-Place Siêu Mịn 0.12mm',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                    'title_en' => 'Interlocking Gear Mechanical Heart Keychain',
                    'desc_vi' => 'Trái tim cơ khí độc đáo với cụm bánh răng lồng ghép chuyển động ăn khớp nhịp nhàng khi xoay chốt.',
                    'desc_en' => 'Unique mechanical heart with interlocking gears that mesh smoothly as the pin turns.',
                    'content_vi' => '<h3>Nhịp Đập Cơ Khí Phong Cách Steampunk</h3><p>Món quà ý nghĩa kết hợp giữa sự lãng mạn và kỹ thuật cơ khí chính xác. Khi vặn núm điều khiển bên hông, hệ thống 6 bánh răng xoắn ốc liên hoàn bên trong sẽ chuyển động nhịp nhàng như một nhịp tim cơ học.</p><ul><li><strong>Công nghệ:</strong> In 3D nguyên khối không cần lắp ráp (Print-in-Place).</li><li><strong>Vật liệu:</strong> Silk PLA màu đỏ ruby ánh kim phối vàng đồng cổ điển.</li><li><strong>Thời gian gia công:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>Steampunk Romance & Mechanical Art</h3><p>A mesmerizing fusion of romance and kinetic mechanical art. Rotating the side pin activates 6 interlocking spiral bevel gears, moving harmoniously like a mechanical heartbeat.</p><ul><li><strong>Technology:</strong> Monolithic Print-in-Place.</li><li><strong>Material:</strong> Silk Ruby Red and Antique Bronze PLA.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place Siêu Mịn 0.12mm'],
                        ['label' => 'Vật liệu', 'value' => 'Silk Metallic PLA / Print-in-Place Mechanism'],
                        ['label' => 'Kích thước mẫu', 'value' => '50 x 45 x 22 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '25% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place)'],
                    ],
                ],

                // 17. Mô hình Phi hành gia Không gian (space_decor, featured = 1)
                [
                    'slug' => 'mo-hinh-phi-hanh-gia',
                    'category_key' => 'space_decor',
                    'is_featured' => 1,
                    'image' => '/images/pdp/related_1.png',
                    'material' => 'PLA Matte Tuyết Trắng & Visor Mạ Vàng',
                    'print_technology' => 'FDM High-Detail Sculpting',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Mô hình Phi hành gia Không gian',
                    'title_en' => 'Space Astronaut Model',
                    'desc_vi' => 'Tạo hình sắc nét, vật liệu PLA matte chống bám vân tay, độ bền màu cao.',
                    'desc_en' => 'Crisp contours, matte PLA fingerprint-resistant finish, high color retention.',
                    'content_vi' => '<h3>Chinh Phục Vũ Trụ Trên Bàn Làm Việc</h3><p>Mô hình phi hành gia Apollo trong tư thế không trọng lực bay bổng giữa không gian. Chi tiết bộ đồ du hành vũ trụ EVA được khắc họa tỉ mỉ với mặt nạ che visor mạ vàng phản quang tuyệt mỹ.</p><ul><li><strong>Chiều cao:</strong> 160mm.</li><li><strong>Vật liệu:</strong> PLA Matte cao cấp, chân đế gắn liền vững chãi.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Cosmic Explorer for Your Desk</h3><p>Apollo astronaut model floating in zero-gravity. Meticulously sculpted Extravehicular Activity (EVA) spacesuit details finished with a reflective gold visor.</p><ul><li><strong>Height:</strong> 160mm.</li><li><strong>Material:</strong> Premium Matte PLA on an integrated display base.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM High-Detail Sculpting'],
                        ['label' => 'Vật liệu', 'value' => 'PLA Matte Tuyết Trắng & Visor Mạ Vàng'],
                        ['label' => 'Kích thước mẫu', 'value' => '95 x 80 x 160 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support hữu cơ'],
                    ],
                ],

                // 18. Chậu cây đa giác Mini Geometric (stationery, featured = 0)
                [
                    'slug' => 'chau-cay-da-giac-mini',
                    'category_key' => 'stationery',
                    'is_featured' => 0,
                    'image' => '/images/pdp/related_2.png',
                    'material' => 'PETG Waterproof Chống Thấm 100%',
                    'print_technology' => 'FDM Watertight In Chống Rò Rỉ',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Chậu cây đa giác Mini Geometric',
                    'title_en' => 'Mini Geometric Plant Pot',
                    'desc_vi' => 'Thiết kế thoát nước thông minh cho sen đá và cây để bàn văn phòng.',
                    'desc_en' => 'Smart drainage design ideal for desk succulents and workspace greenery.',
                    'content_vi' => '<h3>Sống Xanh Cùng Thiết Kế Đa Giác Kim Cương</h3><p>Chậu cây đa giác Mini Geometric sở hữu đáy thông minh với khay hứng nước ngầm có thể tháo rời. Đảm bảo rễ cây sen đá, xương rồng luôn thoáng khí mà không để rò rỉ một giọt nước nào ra bàn làm việc.</p><ul><li><strong>Vật liệu:</strong> PETG chống thấm nước và chịu nhiệt độ ánh nắng cửa sổ.</li><li><strong>Kích thước:</strong> Đường kính 90mm x Cao 80mm.</li><li><strong>Thời gian gia công:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>Modern Faceted Planter for Desk Flora</h3><p>Faceted geometric planter equipped with a detachable concealed drainage saucer. Ensures optimal aeration for succulents while guaranteeing zero leaks onto your desk.</p><ul><li><strong>Material:</strong> 100% Watertight UV-resistant PETG.</li><li><strong>Dimensions:</strong> 90mm diameter x 80mm height.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Watertight In Chống Rò Rỉ'],
                        ['label' => 'Vật liệu', 'value' => 'PETG Waterproof Chống Thấm 100%'],
                        ['label' => 'Kích thước mẫu', 'value' => 'Ø90 x 80 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '20% Grid (Thành vỏ 4 lớp chống thấm)'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Góc vát tự chống đỡ)'],
                    ],
                ],

                // 19. Đế đỡ điện thoại Mecha Cyberpunk (stationery, featured = 0)
                [
                    'slug' => 'de-do-dien-thoai-mecha',
                    'category_key' => 'stationery',
                    'is_featured' => 0,
                    'image' => '/images/pdp/related_3.png',
                    'material' => 'PLA+ Tough Gia Cường Chịu Lực',
                    'print_technology' => 'FDM Ergonomic Engineering',
                    'lead_time' => '1 ngày',
                    'title_vi' => 'Đế đỡ điện thoại Mecha Cyberpunk',
                    'title_en' => 'Mecha Cyberpunk Phone Stand',
                    'desc_vi' => 'Góc nghiêng công thái học 60 độ, tương thích tất cả các dòng smartphone.',
                    'desc_en' => 'Ergonomic 60-degree viewing angle, compatible with all smartphone models.',
                    'content_vi' => '<h3>Chiến Giáp Mecha Giữ Vững Thiết Bị</h3><p>Đế đỡ điện thoại thiết kế góc nghiêng chuẩn công thái học 60 độ, tối ưu góc nhìn cho các cuộc gọi video FaceTime/Zoom, xem video hoặc theo dõi thông báo. Khe cắm cáp sạc dọc tiện lợi hỗ trợ sạc nhanh liên tục.</p><ul><li><strong>Tương thích:</strong> Tất cả các dòng iPhone, Samsung Galaxy, iPad Mini từ 4.7 đến 11 inch.</li><li><strong>Đệm lót:</strong> Chân đế cao su chống trượt trên mặt kính và mặt gỗ.</li><li><strong>Thời gian gia công:</strong> 1 ngày.</li></ul>',
                    'content_en' => '<h3>Armored Mecha Stand for Modern Devices</h3><p>Engineered with an ergonomic 60-degree viewing angle ideal for video conferences, media streaming, and heads-up notifications. Open pass-through cable channel enables uninterrupted charging.</p><ul><li><strong>Compatibility:</strong> All smartphones and small tablets (4.7" to 11").</li><li><strong>Stability:</strong> Weighted geometry with non-slip pads.</li><li><strong>Lead Time:</strong> 1 day.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Ergonomic Engineering'],
                        ['label' => 'Vật liệu', 'value' => 'PLA+ Tough Gia Cường Chịu Lực'],
                        ['label' => 'Kích thước mẫu', 'value' => '85 x 75 x 110 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '25% Gyroid Chịu Lực'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Thiết kế góc nghiêng 60°)'],
                    ],
                ],

                // 20. Robot Khớp cử động (custom, featured = 0)
                [
                    'slug' => 'robot-khop-cu-dong',
                    'category_key' => 'custom',
                    'is_featured' => 0,
                    'image' => '/images/pdp/related_4.png',
                    'material' => 'PETG Tough Kháng Gãy Khớp',
                    'print_technology' => 'FDM Print-in-Place 14 Khớp Linh Hoạt',
                    'lead_time' => '1-2 ngày',
                    'title_vi' => 'Bộ lắp ghép Robot Khớp cử động',
                    'title_en' => 'Articulated Robot Assembly Kit',
                    'desc_vi' => '14 khớp linh hoạt, in liền mạch không cần lắp ráp vít kim loại.',
                    'desc_en' => '14 flexible articulated joints, seamless print-in-place without metal screws.',
                    'content_vi' => '<h3>Robot Mô Hình Khớp Động Toàn Thân</h3><p>Mô hình robot chiến binh sở hữu 14 điểm khớp cử động độc lập từ đầu, vai, khuỷu tay, cổ tay, khớp háng đến mắt cá chân. Tạo được hàng chục tư thế chiến đấu ngoạn mục mà không lo tuột khớp hay gãy vỡ.</p><ul><li><strong>Vật liệu:</strong> PETG dẻo dai với độ bám dính lớp in siêu việt.</li><li><strong>Chiều cao:</strong> 175mm.</li><li><strong>Thời gian gia công:</strong> 1-2 ngày.</li></ul>',
                    'content_en' => '<h3>Fully Articulated Action Figure Robot</h3><p>Warrior robot featuring 14 independent articulation joints across head, shoulders, elbows, wrists, hips, and ankles. Holds dynamic combat poses securely without loose screws.</p><ul><li><strong>Material:</strong> Tough PETG with superior interlayer bonding.</li><li><strong>Height:</strong> 175mm.</li><li><strong>Lead Time:</strong> 1-2 days.</li></ul>',
                    'specifications' => [
                        ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place 14 Khớp Linh Hoạt'],
                        ['label' => 'Vật liệu', 'value' => 'PETG Tough Kháng Gãy Khớp'],
                        ['label' => 'Kích thước mẫu', 'value' => '90 x 50 x 175 mm'],
                        ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                        ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                        ['label' => 'Mật độ ruột', 'value' => '30% Gyroid'],
                        ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Khớp cầu in liền khối)'],
                    ],
                ],
            ];

            // 3. Insert or Update Products and Translations
            $position = 1;
            $allProductIds = [];
            $productIdBySlug = [];

            foreach ($productsList as $pData) {
                $catId = $categoryMap[$pData['category_key']] ?? $categoryMap['custom'];

                // Check if product exists by target_id or translation slug
                $existingProduct = null;
                if (isset($pData['target_id'])) {
                    $existingProduct = DB::table('products')->where('id', $pData['target_id'])->first();
                }

                if (!$existingProduct) {
                    $existingTrans = DB::table('product_translations')
                        ->where('slug', $pData['slug'])
                        ->orWhere('seo_slug', $pData['slug'])
                        ->first();

                    if ($existingTrans) {
                        $existingProduct = DB::table('products')->where('id', $existingTrans->product_id)->first();
                    }
                }

                $imagesJson = json_encode([
                    ['path' => $pData['image'], 'alt' => $pData['title_vi']],
                    ['path' => '/images/pdp/thumb_2.png', 'alt' => 'Chi tiết bề mặt in 3D'],
                    ['path' => '/images/pdp/thumb_3.png', 'alt' => 'Tỉ lệ kích thước thực tế'],
                ]);

                $imageJson = json_encode([
                    'path' => $pData['image'],
                    'alt' => $pData['title_vi'],
                ]);

                $productPayload = [
                    'status' => 'ACTIVE',
                    'is_featured' => $pData['is_featured'],
                    'is_new' => 1,
                    'is_stock' => 1,
                    'price' => null,
                    'compare_at_price' => null,
                    'position' => $position,
                    'material' => $pData['material'],
                    'print_technology' => $pData['print_technology'],
                    'lead_time' => $pData['lead_time'],
                    'image' => $imageJson,
                    'images' => $imagesJson,
                    'updated_at' => now(),
                    'deleted_at' => null,
                ];

                if (Schema::hasColumn('products', 'specifications') && isset($pData['specifications'])) {
                    $productPayload['specifications'] = json_encode($pData['specifications']);
                }

                if ($existingProduct) {
                    $productId = $existingProduct->id;
                    DB::table('products')->where('id', $productId)->update($productPayload);
                } else {
                    $productPayload['created_at'] = now();
                    $productPayload['view_count'] = rand(120, 350);
                    $productId = DB::table('products')->insertGetId($productPayload);
                }

                $allProductIds[] = $productId;
                $productIdBySlug[$pData['slug']] = $productId;

                // Upsert VI product translation
                DB::table('product_translations')->updateOrInsert(
                    ['product_id' => $productId, 'locale' => 'vi'],
                    [
                        'title' => $pData['title_vi'],
                        'slug' => $pData['slug'],
                        'seo_slug' => $pData['slug'],
                        'description' => $pData['desc_vi'],
                        'content' => $pData['content_vi'],
                        'seo_meta_title' => $pData['title_vi'] . ' - HH Maker | Dịch Vụ In 3D Chuyên Nghiệp',
                        'seo_meta_description' => $pData['desc_vi'],
                    ]
                );

                // Upsert EN product translation
                DB::table('product_translations')->updateOrInsert(
                    ['product_id' => $productId, 'locale' => 'en'],
                    [
                        'title' => $pData['title_en'],
                        'slug' => $pData['slug'],
                        'seo_slug' => $pData['slug'],
                        'description' => $pData['desc_en'],
                        'content' => $pData['content_en'],
                        'seo_meta_title' => $pData['title_en'] . ' - HH Maker | 3D Printing Service',
                        'seo_meta_description' => $pData['desc_en'],
                    ]
                );

                // Attach exact category in product_ref_categories
                DB::table('product_ref_categories')->where('product_id', $productId)->delete();
                DB::table('product_ref_categories')->updateOrInsert(
                    ['product_id' => $productId, 'product_category_id' => $catId],
                    []
                );

                $position++;
            }

            // 4. Build Related Products: Dedicated Figma links for Model Cáo ID 1
            $foxId = $productIdBySlug['mo-hinh-cao-trang-tri'] ?? 1;
            $foxRelatedSlugs = [
                'mo-hinh-phi-hanh-gia',
                'chau-cay-da-giac-mini',
                'de-do-dien-thoai-mecha',
                'robot-khop-cu-dong',
            ];

            $foxRelatedIds = [];
            foreach ($foxRelatedSlugs as $fSlug) {
                if (isset($productIdBySlug[$fSlug])) {
                    $foxRelatedIds[] = $productIdBySlug[$fSlug];
                } else {
                    $trans = DB::table('product_translations')->where('slug', $fSlug)->first();
                    if ($trans) {
                        $foxRelatedIds[] = $trans->product_id;
                    }
                }
            }

            // Sync related products for Model Cáo ID 1
            DB::table('related_products')->where('product_id', $foxId)->delete();
            foreach ($foxRelatedIds as $relId) {
                DB::table('related_products')->updateOrInsert(
                    ['product_id' => $foxId, 'related_product_id' => $relId],
                    []
                );
            }

            // Sync related products for other products
            foreach ($allProductIds as $idx => $pId) {
                if ($pId === $foxId) {
                    continue;
                }
                $otherIds = array_values(array_filter($allProductIds, fn($id) => $id !== $pId));
                $selectedRelated = array_slice($otherIds, $idx % max(1, count($otherIds) - 4), 4);

                DB::table('related_products')->where('product_id', $pId)->delete();
                foreach ($selectedRelated as $relId) {
                    DB::table('related_products')->updateOrInsert(
                        ['product_id' => $pId, 'related_product_id' => $relId],
                        []
                    );
                }
            }

            // 5. Enforce Core Constraint: price = null and compare_at_price = null for ALL products
            DB::table('products')->update([
                'price' => null,
                'compare_at_price' => null,
            ]);

            // Deactivate and soft-delete any test/legacy products not in the 21 Figma products list
            DB::table('products')->whereNotIn('id', $allProductIds)->update([
                'status' => 'INACTIVE',
                'deleted_at' => now(),
            ]);
        });

        // 6. Automatic Cache Eviction
        Cache::flush();
        foreach (['vi', 'en'] as $locale) {
            Cache::forget('products_index_' . $locale);
            Cache::forget('galleries_index_' . $locale);
            Cache::forget('home_page_data_' . $locale);
            Cache::forget('inertia_layout_shared_data_' . $locale);
            Cache::forget('search_page_data_' . $locale . '_');
        }
    }
}
