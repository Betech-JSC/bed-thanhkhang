<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Cache::remember('products_index_' . current_locale(), 3600, function () {
                $products = Product::query()
                    ->active()
                    ->ordered()
                    ->get()
                    ->map(fn($item) => $item->transform());

                $categories = ProductCategory::query()
                    ->active()
                    ->sortByPosition()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'products' => $products,
                    'categories' => $categories,
                    'seo' => [
                        'title' => current_locale() === 'en'
                            ? '3D Printed Products Catalog - HH Maker | FDM, SLA, SLS Technology'
                            : 'Danh mục Sản phẩm in 3D - HH Maker | Công nghệ FDM, SLA, SLS',
                        'description' => current_locale() === 'en'
                            ? 'Explore high-tech 3D printed products by HH Maker. Diverse materials including 8K Resin, PLA+, PETG, Carbon Fiber with micron precision.'
                            : 'Khám phá bộ sưu tập sản phẩm in 3D công nghệ cao từ HH Maker. Đa dạng vật liệu Resin 8K, PLA+, PETG, Carbon Fiber chính xác từng micron.',
                    ],
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Product/Index', $data)
                ->withViewData(['seo' => $data['seo'] ?? null]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('product_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                // 1. Search in Database first
                $product = Product::query()
                    ->active()
                    ->whereSlug($slug)
                    ->first();

                if ($product) {
                    $details = $product->transformDetails();
                    $details['material'] = $product->material ?? null;
                    $details['print_technology'] = $product->print_technology ?? null;
                    $details['lead_time'] = $product->lead_time ?? null;

                    return [
                        'product' => $details,
                        'related_products' => $product->related(),
                        'seo' => $product->transformSeo(),
                    ];
                }

                // 2. Graceful Fallback for Figma Catalog & Related Mock Slugs
                return $this->getFallbackProduct($slug);
            });

            if (!$data) {
                Cache::forget('product_show_' . $slug . '_' . current_locale());
                return abort(404);
            }

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Product/Show', $data)
                ->withViewData(['seo' => $data['seo'] ?? null]);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }

    /**
     * Fallback mock catalog covering all 16 Figma products + 4 Related Products + Fox Model
     */
    private function getFallbackProduct(string $slug): ?array
    {
        $catalog = [
            'mo-hinh-cao-trang-tri' => [
                'slug' => 'mo-hinh-cao-trang-tri',
                'title_vi' => 'Mô hình Cáo trang trí',
                'title_en' => 'Decorative Fox Model',
                'desc_vi' => 'Thêm một chút linh hoạt và thông minh vào góc nhỏ của bạn. Chú cáo nhỏ với chiếc đuôi xù đặc trưng và những lớp in tỉ mỉ không chỉ là một món đồ trang trí, mà còn là người bạn đồng hành truyền cảm hứng mỗi ngày.',
                'desc_en' => 'Add a touch of agility and smart elegance to your space. Featuring an iconic bushy tail and meticulous 3D printed layers, this companion inspires creativity every day.',
                'content_vi' => '<h3>Quy cách in 3D</h3><p>Mô hình chú cáo phong cách Low-Poly tối giản là điểm nhấn hiện đại cho bàn làm việc. In bằng công nghệ FDM độ chính xác cao với các mặt cắt đa giác chuẩn xác.</p>',
                'content_en' => '<h3>3D Printing Specifications</h3><p>Minimal Low-Poly fox model crafted via high-precision FDM technology with crisp polygonal facets.</p>',
                'image' => '/images/pdp/product_main.png',
                'category_vi' => 'Trang trí không gian',
                'category_en' => 'Space Decor',
                'category_slug' => 'trang-tri-khong-gian',
                'material' => 'Nhựa PLA Matte Cao Cấp',
                'print_technology' => 'FDM High-Precision',
                'lead_time' => '1-2 ngày',
            ],
            'iron-man-mark-85-resin-8k' => [
                'slug' => 'iron-man-mark-85-resin-8k',
                'title_vi' => 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                'title_en' => 'Iron Man Mark 85 Model - High Detail 8K Resin',
                'desc_vi' => 'Mô hình giáp Iron Man tỉ lệ 1:10, độ chi tiết sắc nét từng đường rãnh cơ khí với công nghệ SLA quang hóa.',
                'desc_en' => '1:10 scale Iron Man armor model with crisp mechanical grooves produced using photochemical SLA technology.',
                'content_vi' => '<h3>Mô tả & Quy cách gia công</h3><p>Mô hình giáp Iron Man Mark 85 tái hiện sống động bộ giáp công nghệ nano đỉnh cao trong vũ trụ Marvel với độ phân giải quang học 8K cực đại.</p>',
                'content_en' => '<h3>Description & Fabrication Specs</h3><p>The Iron Man Mark 85 model sharply recreates the iconic nano-tech superhero armor with 8K photochemical optical precision.</p>',
                'image' => '/assets/images/products/figma-product-1.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => '8K High-Detail Tough Resin',
                'print_technology' => 'SLA 8K Photochemical',
                'lead_time' => '1-2 ngày',
            ],
            'khung-vo-robot-carbon-fiber' => [
                'slug' => 'khung-vo-robot-carbon-fiber',
                'title_vi' => 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                'title_en' => 'Mini Robot Mechanical Chassis - Impact Resistant Carbon Fiber',
                'desc_vi' => 'Khung robot gia cường sợi carbon chịu lực va đập vượt trội, kháng uốn và ổn định kích thước tuyệt đối.',
                'desc_en' => 'Carbon fiber reinforced robot chassis offering outstanding impact resistance, flexural strength, and dimensional stability.',
                'content_vi' => '<h3>Khung Vỏ Kỹ Thuật Chịu Lực Cao</h3><p>Sản phẩm chuyên dụng cho robot tự hành và drone FPV đòi hỏi độ cứng uốn vượt trội với sợi carbon gia cường.</p>',
                'content_en' => '<h3>High-Strength Engineering Chassis</h3><p>Tailored for autonomous robots and FPV drones demanding superior stiffness-to-weight ratio.</p>',
                'image' => '/assets/images/products/figma-product-2.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => 'PETG Gia Cường 20% Carbon Fiber (PETG-CF)',
                'print_technology' => 'FDM Industrial Hardened Nozzle',
                'lead_time' => '2-3 ngày',
            ],
            'hop-dung-danh-thiep-cyberpunk' => [
                'slug' => 'hop-dung-danh-thiep-cyberpunk',
                'title_vi' => 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                'title_en' => 'Cyberpunk Hexagon Business Card & Pen Holder',
                'desc_vi' => 'Khay bút tổ ong đa giác phong cách Futuristic, tối ưu không gian làm việc công nghệ cao.',
                'desc_en' => 'Futuristic honeycomb polygon organizer tray, optimizing high-tech desktop workspaces.',
                'content_vi' => '<h3>Phong cách Futuristic Cho Bàn Làm Việc</h3><p>Cấu trúc tổ ong đối xứng giúp lưu trữ gọn gàng danh thiếp chuẩn quốc tế và khay cắm bút công thái học.</p>',
                'content_en' => '<h3>Futuristic Desktop Organizer</h3><p>Honeycomb hexagonal layout fitting standard business cards and ergonomic pen slots.</p>',
                'image' => '/assets/images/products/figma-product-3.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PLA+ Matte Futuristic Finish',
                'print_technology' => 'FDM Precision',
                'lead_time' => '1 ngày',
            ],
            'moc-khoa-turbine-phan-luc' => [
                'slug' => 'moc-khoa-turbine-phan-luc',
                'title_vi' => 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                'title_en' => 'Jet Turbine Keychain with Spinning Fan Blades',
                'desc_vi' => 'Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt.',
                'desc_en' => 'Aerodynamic turbine blades that spin smoothly when blown, finished with an eye-catching metallic titanium coating.',
                'content_vi' => '<h3>Cơ Khí Hàng Không Thu Nhỏ</h3><p>Cụm 12 cánh quạt bên trong được gắn trên trục bi xoay bằng thép không gỉ, cho phép cánh quạt quay tít khi thổi hơi nhẹ.</p>',
                'content_en' => '<h3>Miniature Aviation Engineering</h3><p>Scale replica of a turbofan jet engine with 12 spinning blades mounted on miniature stainless steel bearings.</p>',
                'image' => '/assets/images/products/figma-product-4.png',
                'category_vi' => 'Móc khoá & Phụ kiện',
                'category_en' => 'Keychains & Accessories',
                'category_slug' => 'moc-khoa-phu-kien',
                'material' => 'Engineering Tough Resin / Titanium Coating',
                'print_technology' => 'SLA Precision Micro-Detail',
                'lead_time' => '1 ngày',
            ],
            'gia-do-tai-nghe-voronoi' => [
                'slug' => 'gia-do-tai-nghe-voronoi',
                'title_vi' => 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                'title_en' => 'Voronoi Geometrical Headphone Stand',
                'desc_vi' => 'Giá đỡ tai nghe dạng lưới cấu trúc bionic Voronoi siêu bền, tôn lên vẻ thẩm mỹ cho góc làm việc.',
                'desc_en' => 'Ultra-durable bionic Voronoi lattice headphone holder, elevating desktop aesthetics.',
                'content_vi' => '<h3>Kiến Trúc Sinh Học Voronoi Đẳng Cấp</h3><p>Giá đỡ tai nghe sử dụng thuật toán Voronoi tự nhiên, giảm trọng lượng vật liệu nhưng vẫn duy trì tải trọng trên 3kg.</p>',
                'content_en' => '<h3>Parametric Bionic Voronoi Architecture</h3><p>Engineered with Voronoi lattice cell algorithms, supporting over 3kg loads for studio headphones.</p>',
                'image' => '/assets/images/products/figma-product-5.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PETG Gradient Bionic Structure',
                'print_technology' => 'FDM Advanced Parametric',
                'lead_time' => '1-2 ngày',
            ],
            'cum-banh-rang-hanh-tinh' => [
                'slug' => 'cum-banh-rang-hanh-tinh',
                'title_vi' => 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                'title_en' => 'Planetary Gearbox Assembly Set',
                'desc_vi' => 'Hộp số hành tinh tỉ số truyền 1:5, khớp răng chuẩn dung sai cơ khí, xoay êm ái linh hoạt.',
                'desc_en' => '1:5 gear reduction planetary gearbox with precise mechanical tolerances and smooth rotation.',
                'content_vi' => '<h3>Mô Hình Truyền Động Cơ Khí Chuẩn Xác</h3><p>Cụm hộp số giảm tốc hành tinh biên dạng răng thân khai chuẩn mô-đun M1.0 vận hành mượt mà.</p>',
                'content_en' => '<h3>High-Precision Powertrain</h3><p>Planetary gear reduction set with involute tooth profile for zero-jam smooth rotation.</p>',
                'image' => '/assets/images/products/figma-product-6.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => 'Nylon PA12 Kỹ Thuật Tự Bôi Trơn',
                'print_technology' => 'FDM High-Tolerance Engineering',
                'lead_time' => '1-2 ngày',
            ],
            'tuong-cyber-wolf-low-poly' => [
                'slug' => 'tuong-cyber-wolf-low-poly',
                'title_vi' => 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                'title_en' => 'Low-Poly Cyber Wolf Art Sculpture',
                'desc_vi' => 'Tượng sói phong cách đa giác góc cạnh, bề mặt được xử lý mịn màng và phủ lớp nano bảo vệ bền bỉ.',
                'desc_en' => 'Geometric faceted wolf sculpture featuring smooth surfaces and protective nano-coating.',
                'content_vi' => '<h3>Tác Phẩm Điêu Khắc Đa Giác Nghệ Thuật</h3><p>Hơn 150 mặt đa giác phẳng tạo nên vũ điệu ánh sáng độc đáo khi thay đổi góc chiếu sáng của phòng.</p>',
                'content_en' => '<h3>Artistic Geometric Polygonal Sculpture</h3><p>Over 150 planar facets produce a striking light play as ambient lighting shifts.</p>',
                'image' => '/assets/images/products/figma-product-7.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'Silk Metallic PLA / Protective Nano Layer',
                'print_technology' => 'FDM Multi-Axis Contour',
                'lead_time' => '1-2 ngày',
            ],
            'moc-khoa-dong-co-v8-piston' => [
                'slug' => 'moc-khoa-dong-co-v8-piston',
                'title_vi' => 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                'title_en' => 'Moving Piston V8 Engine Keychain',
                'desc_vi' => 'Mô phỏng trục khuỷu và piston động cơ V8 xe đua cơ bắp chuyển động tịnh tiến khi vặn núm xoay.',
                'desc_en' => 'Simulates crankshaft and reciprocating pistons of a muscle car V8 engine when rotating the dial.',
                'content_vi' => '<h3>Trái Tim Động Cơ V8 Trên Tay Bạn</h3><p>In 3D nguyên khối Print-in-Place: Toàn bộ khối động cơ V8 hoạt động nhịp nhàng khi xoay núm phía sau.</p>',
                'content_en' => '<h3>V8 Engine Kinetic Keychain</h3><p>Monolithic Print-in-Place 3D printing cycling 8 pistons smoothly via rear rotary dial.</p>',
                'image' => '/assets/images/products/figma-product-8.png',
                'category_vi' => 'Móc khoá & Phụ kiện',
                'category_en' => 'Keychains & Accessories',
                'category_slug' => 'moc-khoa-phu-kien',
                'material' => 'PLA+ Tough Print-in-Place',
                'print_technology' => 'FDM Print-in-Place',
                'lead_time' => '1 ngày',
            ],
            'den-ngu-mat-trang-lithophane' => [
                'slug' => 'den-ngu-mat-trang-lithophane',
                'title_vi' => 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                'title_en' => '3D Lithophane Moon Night Lamp with Wooden Base',
                'desc_vi' => 'Quả cầu địa hình bề mặt mặt trăng với công nghệ thấu quang Lithophane, phát sáng dịu nhẹ ấm áp.',
                'desc_en' => 'Moon surface topography sphere with Lithophane light-transmitting technology, emitting soft warm ambient glow.',
                'content_vi' => '<h3>Ánh Trăng Dịu Êm Trong Phòng Ngủ</h3><p>Kỹ thuật thấu quang Lithophane cổ điển kết hợp bản đồ độ cao vệ tinh NASA tạo nên ánh sáng dịu mắt.</p>',
                'content_en' => '<h3>Gentle Moonlight for Your Space</h3><p>Lithophane backlighting optics combined with NASA elevation data emitting soft warm glow.</p>',
                'image' => '/assets/images/products/figma-product-9.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'Translucent White Optical PLA',
                'print_technology' => 'FDM Lithophane 0.12mm',
                'lead_time' => '2 ngày',
            ],
            'khop-noi-cau-bionic-sls' => [
                'slug' => 'khop-noi-cau-bionic-sls',
                'title_vi' => 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                'title_en' => 'Bionic Multi-Directional Ball-Joint',
                'desc_vi' => 'Khớp nối linh hoạt in nguyên khối không cần support, độ bền cơ học và chịu mài mòn chuẩn công nghiệp.',
                'desc_en' => 'Monolithic print-in-place flexible joint with zero support material, offering industrial-grade wear resistance.',
                'content_vi' => '<h3>Khớp Cầu Cơ Khí Tiêu Chuẩn Công Nghiệp</h3><p>Công nghệ thiêu kết laser bột Polymer SLS, khớp cầu xoay 360 độ đa hướng không cần chống đỡ.</p>',
                'content_en' => '<h3>Industrial Grade Mechanical Ball-Joint</h3><p>Selective Laser Sintering (SLS) 360-degree joint with zero support structures.</p>',
                'image' => '/assets/images/products/figma-product-10.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => 'Polyamide 12 (Nylon PA12) Bột Thiêu Kết',
                'print_technology' => 'SLS Industrial',
                'lead_time' => '2-3 ngày',
            ],
            'mo-hinh-tau-vu-tru-discovery' => [
                'slug' => 'mo-hinh-tau-vu-tru-discovery',
                'title_vi' => 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                'title_en' => 'Discovery 3D Spacecraft Model with LED Base',
                'desc_vi' => 'Mô hình phi thuyền không gian viễn tưởng tỉ mỉ tới từng tấm khiên nhiệt, buồng lái hoàn thiện trong suốt.',
                'desc_en' => 'Sci-fi spaceship model meticulously crafted down to heat shields and transparent cockpit canopy.',
                'content_vi' => '<h3>Kiệt Tác Trưng Bày Không Gian Viễn Tưởng</h3><p>Mô hình tàu Discovery tái hiện thiết kế khí động học tương lai kèm chân đế tích hợp đèn LED xanh neon.</p>',
                'content_en' => '<h3>Sci-Fi Collector Spacecraft Masterpiece</h3><p>Futuristic spacecraft featuring detailed heat shields and edge-lit LED display base.</p>',
                'image' => '/assets/images/products/figma-product-11.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PLA Pro Matte & Transparent Resin',
                'print_technology' => 'Hybrid FDM + SLA',
                'lead_time' => '2-3 ngày',
            ],
            'khay-to-chuc-day-cap-modular' => [
                'slug' => 'khay-to-chuc-day-cap-modular',
                'title_vi' => 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                'title_en' => 'Modular Magnetic Cable & Accessories Organizer',
                'desc_vi' => 'Khay cáp bàn làm việc ghép nối nam châm Neodymium linh hoạt, gom gọn mọi loại dây sạc gọn gàng.',
                'desc_en' => 'Flexible desktop cable tray connected with Neodymium magnets, organizing all charging cables neatly.',
                'content_vi' => '<h3>Gọn Gàng & Thông Minh Cho Bàn Làm Việc</h3><p>Hệ thống 4 khay module ghép nối qua nam châm đất hiếm Neodymium N52 âm bên trong thân vỏ.</p>',
                'content_en' => '<h3>Smart Modular Cable Organization</h3><p>4 snap-together trays connected via embedded Neodymium magnets keeping desks organized.</p>',
                'image' => '/assets/images/products/figma-product-12.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PETG Matte & Neodymium N52 Magnets',
                'print_technology' => 'FDM High-Strength Modular',
                'lead_time' => '1-2 ngày',
            ],
            'moc-khoa-vo-lang-f1-quick-release' => [
                'slug' => 'moc-khoa-vo-lang-f1-quick-release',
                'title_vi' => 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                'title_en' => 'F1 Quick Release Racing Steering Wheel Keychain',
                'desc_vi' => 'Mô phỏng vô lăng xe đua F1 với cơ chế tháo lắp khớp bi cực kỳ chân thực, kim loại phối nhựa dẻo cao cấp.',
                'desc_en' => 'Replicates F1 race car steering wheel with authentic ball-lock quick-release mechanism.',
                'content_vi' => '<h3>Tốc Độ Công Thức 1 Trên Tay Bạn</h3><p>Mô phỏng chuẩn xác vô lăng F1 tỉ lệ thu nhỏ với cơ chế tháo nhanh Quick-Release nhả bi thép chân thực.</p>',
                'content_en' => '<h3>Formula 1 Racing Spirit Keychain</h3><p>Scale miniature F1 steering wheel featuring a functional ball-lock quick-release mechanism.</p>',
                'image' => '/assets/images/products/figma-product-13.png',
                'category_vi' => 'Móc khoá & Phụ kiện',
                'category_en' => 'Keychains & Accessories',
                'category_slug' => 'moc-khoa-phu-kien',
                'material' => 'Tough Resin 8K & Steel Ball Bearings',
                'print_technology' => 'SLA Precision 25 Micron',
                'lead_time' => '1 ngày',
            ],
            'den-ban-led-hexagon-to-ong' => [
                'slug' => 'den-ban-led-hexagon-to-ong',
                'title_vi' => 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                'title_en' => 'Touch-Sensitive Hexagonal Honeycomb LED Lamp',
                'desc_vi' => 'Các mô-đun đèn lục giác ghép nối tự do theo sở thích, điều khiển ánh sáng bằng cảm ứng bề mặt.',
                'desc_en' => 'Modular hexagon light modules freely assembled and operated via capacitive surface touch controls.',
                'content_vi' => '<h3>Chiếu Sáng Cảm Ứng Chạm Tổ Ong</h3><p>Khung lục giác liên kết nam châm, chạm nhẹ tay lên bề mặt để bật/tắt và đổi màu RGB sống động.</p>',
                'content_en' => '<h3>Modular Touch-Responsive Ambient Lamp</h3><p>Magnetic hexagonal honeycomb modules with capacitive touch lighting control.</p>',
                'image' => '/assets/images/products/figma-product-14.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'Heat-Resistant PETG & Optical Shell',
                'print_technology' => 'FDM High-Precision Optics',
                'lead_time' => '2 ngày',
            ],
            'vo-case-ban-phim-co-custom' => [
                'slug' => 'vo-case-ban-phim-co-custom',
                'title_vi' => 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                'title_en' => 'Custom 3D Printed Multi-Color Mechanical Keyboard Case',
                'desc_vi' => 'Vỏ phím cơ layout 75% cấu trúc gasket mount tối ưu âm gõ, chịu nhiệt và chống biến dạng xuất sắc.',
                'desc_en' => '75% layout keyboard case with gasket-mount structure for acoustic optimization and thermal resistance.',
                'content_vi' => '<h3>Âm Gõ Đầm Chắc Cho Bàn Phím Custom</h3><p>Tối ưu hóa âm học với khoang tiêu âm và ngàm đệm Gasket silicon, in từ composite PETG-CF gia cường sợi carbon.</p>',
                'content_en' => '<h3>Acoustic Optimized Custom Keyboard Chassis</h3><p>Deep acoustic profile with silicone gasket mounts and carbon-fiber reinforced PETG construction.</p>',
                'image' => '/assets/images/products/figma-product-15.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => 'PETG-CF / Gasket Mount Isolation',
                'print_technology' => 'FDM Multi-Material Carbon',
                'lead_time' => '2-3 ngày',
            ],
            'moc-khoa-trai-tim-co-khi' => [
                'slug' => 'moc-khoa-trai-tim-co-khi',
                'title_vi' => 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                'title_en' => 'Interlocking Gear Mechanical Heart Keychain',
                'desc_vi' => 'Trái tim cơ khí độc đáo với cụm bánh răng lồng ghép chuyển động ăn khớp nhịp nhàng khi xoay chốt.',
                'desc_en' => 'Unique mechanical heart with interlocking gears that mesh smoothly as the pin turns.',
                'content_vi' => '<h3>Nhịp Đập Cơ Khí Phong Cách Steampunk</h3><p>In 3D nguyên khối Print-in-Place: Hệ thống 6 bánh răng xoắn ốc liên hoàn bên trong chuyển động nhịp nhàng.</p>',
                'content_en' => '<h3>Steampunk Kinetic Mechanical Heart</h3><p>Print-in-Place art with 6 interlocking spiral bevel gears moving in sync.</p>',
                'image' => '/assets/images/products/figma-product-16.png',
                'category_vi' => 'Móc khoá & Phụ kiện',
                'category_en' => 'Keychains & Accessories',
                'category_slug' => 'moc-khoa-phu-kien',
                'material' => 'Silk Metallic PLA / Print-in-Place',
                'print_technology' => 'FDM Print-in-Place 0.12mm',
                'lead_time' => '1 ngày',
            ],
            'mo-hinh-phi-hanh-gia' => [
                'slug' => 'mo-hinh-phi-hanh-gia',
                'title_vi' => 'Mô hình Phi hành gia Không gian',
                'title_en' => 'Space Astronaut Model',
                'desc_vi' => 'Tạo hình sắc nét, vật liệu PLA matte chống bám vân tay, độ bền màu cao.',
                'desc_en' => 'Crisp contours, matte PLA fingerprint-resistant finish, high color retention.',
                'content_vi' => '<h3>Chinh Phục Vũ Trụ Trên Bàn Làm Việc</h3><p>Mô hình phi hành gia Apollo trong tư thế không trọng lực bay bổng giữa không gian với kính che visor mạ vàng tuyệt mỹ.</p>',
                'content_en' => '<h3>Cosmic Explorer for Your Desk</h3><p>Apollo astronaut floating in zero-gravity with electroplated gold reflective visor.</p>',
                'image' => '/images/pdp/related_1.png',
                'category_vi' => 'Trang trí không gian',
                'category_en' => 'Space Decor',
                'category_slug' => 'trang-tri-khong-gian',
                'material' => 'PLA Matte Tuyết Trắng & Visor Mạ Vàng',
                'print_technology' => 'FDM High-Detail Sculpting',
                'lead_time' => '1-2 ngày',
            ],
            'chau-cay-da-giac-mini' => [
                'slug' => 'chau-cay-da-giac-mini',
                'title_vi' => 'Chậu cây đa giác Mini Geometric',
                'title_en' => 'Mini Geometric Plant Pot',
                'desc_vi' => 'Thiết kế thoát nước thông minh cho sen đá và cây để bàn văn phòng.',
                'desc_en' => 'Smart drainage design ideal for desk succulents and workspace greenery.',
                'content_vi' => '<h3>Sống Xanh Cùng Thiết Kế Đa Giác</h3><p>Chậu cây đa giác Mini Geometric sở hữu đáy thông minh với khay hứng nước ngầm có thể tháo rời.</p>',
                'content_en' => '<h3>Modern Faceted Planter</h3><p>Faceted geometric planter with detachable concealed drainage saucer.</p>',
                'image' => '/images/pdp/related_2.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PETG Waterproof Chống Thấm 100%',
                'print_technology' => 'FDM Watertight',
                'lead_time' => '1 ngày',
            ],
            'de-do-dien-thoai-mecha' => [
                'slug' => 'de-do-dien-thoai-mecha',
                'title_vi' => 'Đế đỡ điện thoại Mecha Cyberpunk',
                'title_en' => 'Mecha Cyberpunk Phone Stand',
                'desc_vi' => 'Góc nghiêng công thái học 60 độ, tương thích tất cả các dòng smartphone.',
                'desc_en' => 'Ergonomic 60-degree viewing angle, compatible with all smartphone models.',
                'content_vi' => '<h3>Chiến Giáp Mecha Giữ Vững Thiết Bị</h3><p>Góc nghiêng chuẩn công thái học 60 độ, tối ưu cho cuộc gọi video FaceTime/Zoom và rãnh luồn dây sạc tiện lợi.</p>',
                'content_en' => '<h3>Armored Mecha Phone Stand</h3><p>Ergonomic 60-degree viewing angle with cable pass-through channel for all devices.</p>',
                'image' => '/images/pdp/related_3.png',
                'category_vi' => 'Trang trí & Đồ dùng',
                'category_en' => 'Decor & Lifestyle',
                'category_slug' => 'trang-tri-do-dung',
                'material' => 'PLA+ Tough Gia Cường Chịu Lực',
                'print_technology' => 'FDM Ergonomic Engineering',
                'lead_time' => '1 ngày',
            ],
            'robot-khop-cu-dong' => [
                'slug' => 'robot-khop-cu-dong',
                'title_vi' => 'Bộ lắp ghép Robot Khớp cử động',
                'title_en' => 'Articulated Robot Assembly Kit',
                'desc_vi' => '14 khớp linh hoạt, in liền mạch không cần lắp ráp vít kim loại.',
                'desc_en' => '14 flexible articulated joints, seamless print-in-place without metal screws.',
                'content_vi' => '<h3>Robot Mô Hình Khớp Động Toàn Thân</h3><p>Robot chiến binh 14 điểm khớp cử động độc lập từ đầu, vai, khuỷu tay đến mắt cá chân, tạo hàng chục tư thế sống động.</p>',
                'content_en' => '<h3>Fully Articulated Action Figure</h3><p>Action figure robot featuring 14 articulation points holding combat poses securely without loose screws.</p>',
                'image' => '/images/pdp/related_4.png',
                'category_vi' => 'Gia công theo yêu cầu',
                'category_en' => 'Custom Manufacturing',
                'category_slug' => 'gia-cong-theo-yeu-cau',
                'material' => 'PETG Tough Kháng Gãy Khớp',
                'print_technology' => 'FDM Print-in-Place 14 Khớp Linh Hoạt',
                'lead_time' => '1-2 ngày',
            ],
        ];

        if (!isset($catalog[$slug])) {
            return null;
        }

        $item = $catalog[$slug];
        $isEn = current_locale() === 'en';
        $title = $isEn ? $item['title_en'] : $item['title_vi'];
        $desc = $isEn ? $item['desc_en'] : $item['desc_vi'];
        $content = $isEn ? $item['content_en'] : $item['content_vi'];
        $category = $isEn ? $item['category_en'] : $item['category_vi'];

        // Curate 4 related products from other catalog items
        $otherItems = array_values(array_filter($catalog, fn($k) => $k !== $slug, ARRAY_FILTER_USE_KEY));
        $relatedSlice = array_slice($otherItems, 0, 4);
        $relatedProducts = array_map(function ($rel) use ($isEn) {
            return [
                'id' => rand(100, 999),
                'title' => $isEn ? $rel['title_en'] : $rel['title_vi'],
                'slug' => $rel['slug'],
                'category' => [
                    'title' => $isEn ? $rel['category_en'] : $rel['category_vi'],
                    'slug' => $rel['category_slug'],
                ],
                'image' => [
                    'url' => $rel['image'],
                    'alt' => $isEn ? $rel['title_en'] : $rel['title_vi'],
                ],
                'description' => $isEn ? $rel['desc_en'] : $rel['desc_vi'],
                'material' => $rel['material'],
                'print_technology' => $rel['print_technology'],
            ];
        }, $relatedSlice);

        return [
            'product' => [
                'id' => 999,
                'title' => $title,
                'name' => $title,
                'description' => $desc,
                'content' => $content,
                'slug' => $slug,
                'images' => [
                    [
                        'url' => $item['image'],
                        'path' => $item['image'],
                        'alt' => $title,
                    ],
                    [
                        'url' => '/images/pdp/thumb_2.png',
                        'path' => '/images/pdp/thumb_2.png',
                        'alt' => $isEn ? 'Surface layer detail' : 'Chi tiết bề mặt in',
                    ],
                    [
                        'url' => '/images/pdp/thumb_3.png',
                        'path' => '/images/pdp/thumb_3.png',
                        'alt' => $isEn ? 'Actual size ratio' : 'Tỉ lệ kích thước thực tế',
                    ],
                ],
                'image' => [
                    'url' => $item['image'],
                    'path' => $item['image'],
                    'alt' => $title,
                ],
                'category' => [
                    'title' => $category,
                    'slug' => $item['category_slug'],
                ],
                'material' => $item['material'],
                'print_technology' => $item['print_technology'],
                'lead_time' => $item['lead_time'] ?? '1-2 ngày',
            ],
            'related_products' => $relatedProducts,
            'seo' => [
                'title' => $title . ($isEn ? ' - HH Maker | 3D Printing' : ' - HH Maker | Dịch Vụ In 3D Chuyên Nghiệp'),
                'description' => $desc,
                'image' => $item['image'],
            ],
        ];
    }

    public function galleries()
    {
        try {
            $data = Cache::remember('galleries_index_' . current_locale(), 3600, function () {
                $banner = Slider::getByPosition('HOME_SLIDER')->first();

                $products = Product::query()
                    ->ordered()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'banner' => $banner,
                    'products' => $products
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Gallery', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
