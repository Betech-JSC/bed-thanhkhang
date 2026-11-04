<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Keyword;
use App\Models\KeywordRefDate;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Models\Service;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $locale = current_locale();
            $data = Cache::remember('home_page_data_' . $locale, 3600, function () {
                // Get Home Slider
                $sliders = Slider::getByPosition('HOME_SLIDER');
                $announcements = Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER');

                // Fetch Active Services
                $services = Service::query()
                    ->active()
                    ->sortByPosition()
                    ->get()
                    ->map(fn($service) => $service->transform());

                // Fetch Featured Products
                $products = Product::query()
                    ->where('status', Product::STATUS_ACTIVE)
                    ->orderByDesc('id')
                    ->take(20)
                    ->get()
                    ->map(fn($product) => $product->transform());

                // Fetch Featured Posts
                $featured_posts = Post::query()
                    ->active()
                    ->wherePosts()
                    ->latest('id')
                    ->take(3)
                    ->get()
                    ->map(fn($post) => $post->transform());

                // Fetch Feedback Posts
                $feedback = Post::query()
                    ->active()
                    ->whereFeedback()
                    ->latest('id')
                    ->get()
                    ->map(fn($post) => $post->transformFeedback());

                // Prepare Response Data
                return [
                    'sliders' => $sliders,
                    'announcements' => $announcements,
                    'services' => $services,
                    'products' => $products,
                    'featured_posts' => $featured_posts,
                    'feedback' => $feedback,
                ];
            });

            $seo = [
                'title' => $locale === 'en'
                    ? 'HH Maker - Turning Every Idea into Reality with 3D Printing'
                    : 'HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D',
                'description' => $locale === 'en'
                    ? 'No limits to creative space, HH Maker breathes life into each creation through modern 3D printing and precise machining.'
                    : 'Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại.',
            ];

            // Return JSON or Inertia View
            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('Home/Index', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function search()
    {
        try {
            $keyword = trim((string) (request()->input('keyword') ?? request()->input('search') ?? request()->input('q') ?? ''));

            if (!empty($keyword)) {
                try {
                    $searchKeyword = Keyword::firstOrCreate(['keyword' => $keyword]);
                    $searchKeyword->update(['updated_at' => now()]);
                    KeywordRefDate::create(['keyword_id' => $searchKeyword->id]);
                } catch (\Throwable $e) {
                    // Ignore keyword logging error
                }

                return redirect()->route(current_locale() . '.products.index', ['search' => $keyword]);
            }

            return redirect()->route(current_locale() . '.products.index');
        } catch (\Throwable $th) {
            report($th);
            return redirect()->route(current_locale() . '.products.index');
        }
    }

    public function searchV2()
    {
        try {
            $keyword = trim((string) (request()->input('keyword') ?? request()->input('search') ?? request()->input('q') ?? ''));
            $limit = (int) (request()->input('limit') ?? 8);
            if ($limit <= 0 || $limit > 20) {
                $limit = 8;
            }

            $query = Product::query()->active();

            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('translations', function ($t) use ($keyword) {
                        $t->where('title', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%")
                            ->orWhere('slug', 'like', "%{$keyword}%");
                    })
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('material', 'like', "%{$keyword}%")
                    ->orWhere('print_technology', 'like', "%{$keyword}%");
                });
            }

            $products = $query->with(['categories', 'translations'])
                ->take($limit)
                ->get()
                ->map(function ($product) {
                    $firstCat = $product->categories->first();
                    $imageDetail = $product->getImageDetail($product->image);
                    $imageUrl = $imageDetail['url'] ?? null;
                    if (empty($imageUrl)) {
                        $imageUrl = '/assets/images/products/figma-sample-1.png';
                    }

                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'title' => $product->title,
                        'slug' => $product->seo_slug ?? $product->slug,
                        'image' => [
                            'url' => $imageUrl,
                            'alt' => $product->title,
                        ],
                        'price' => $product->price ? (float)$product->price : null,
                        'compare_at_price' => $product->compare_at_price ? (float)$product->compare_at_price : null,
                        'category_id' => $firstCat?->id,
                        'category' => $firstCat ? [
                            'id' => $firstCat->id,
                            'name' => $firstCat->title,
                            'title' => $firstCat->title,
                            'slug' => $firstCat->slug,
                        ] : null,
                        'category_name' => $firstCat?->title ?? '',
                        'material' => $product->material,
                        'print_technology' => $product->print_technology,
                        'url' => route(current_locale() . '.products.show', ['slug' => $product->seo_slug ?? $product->slug]),
                    ];
                });

            // Bổ sung các mock catalog items nếu kết quả DB ít hơn $limit để người dùng trải nghiệm trọn vẹn 16 mẫu thiết kế Figma
            $mockCatalog = $this->getMockProductsCatalog();
            if ($products->count() < $limit && !empty($mockCatalog)) {
                $dbSlugs = $products->pluck('slug')->toArray();
                $dbIds = $products->pluck('id')->toArray();
                $keywordLower = mb_strtolower($keyword);

                $filteredMocks = collect($mockCatalog)->filter(function ($item) use ($keywordLower, $dbSlugs, $dbIds) {
                    if (in_array($item['slug'], $dbSlugs) || in_array($item['id'], $dbIds)) {
                        return false;
                    }
                    if (empty($keywordLower)) {
                        return true;
                    }
                    $nameVi = mb_strtolower($item['name_vi'] ?? '');
                    $nameEn = mb_strtolower($item['name_en'] ?? '');
                    $descVi = mb_strtolower($item['description_vi'] ?? '');
                    $descEn = mb_strtolower($item['description_en'] ?? '');
                    $cat = mb_strtolower($item['category'] ?? '');
                    $mat = mb_strtolower($item['material'] ?? '');

                    return str_contains($nameVi, $keywordLower) ||
                           str_contains($nameEn, $keywordLower) ||
                           str_contains($descVi, $keywordLower) ||
                           str_contains($descEn, $keywordLower) ||
                           str_contains($cat, $keywordLower) ||
                           str_contains($mat, $keywordLower);
                });

                $isEn = current_locale() === 'en';
                foreach ($filteredMocks->take($limit - $products->count()) as $mock) {
                    $name = $isEn ? ($mock['name_en'] ?? $mock['name_vi']) : $mock['name_vi'];
                    $products->push([
                        'id' => $mock['id'],
                        'name' => $name,
                        'title' => $name,
                        'slug' => $mock['slug'],
                        'image' => [
                            'url' => $mock['image'],
                            'alt' => $name,
                        ],
                        'price' => $mock['price'] ?? null,
                        'compare_at_price' => $mock['compare_at_price'] ?? null,
                        'category_id' => $mock['categoryId'] ?? null,
                        'category' => [
                            'id' => $mock['categoryId'] ?? 'custom',
                            'name' => $mock['category'] ?? 'Gia công theo yêu cầu',
                            'title' => $mock['category'] ?? 'Gia công theo yêu cầu',
                            'slug' => $mock['categoryId'] ?? 'custom',
                        ],
                        'category_name' => $mock['category'] ?? '',
                        'material' => $mock['material'] ?? '',
                        'print_technology' => $mock['print_technology'] ?? '',
                        'url' => route(current_locale() . '.products.show', ['slug' => $mock['slug']]),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'products' => $products->values(),
            ]);
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'success' => false,
                'products' => [],
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function getMockProductsCatalog(): array
    {
        return [
            [
                'id' => 101,
                'slug' => 'iron-man-mark-85-resin-8k',
                'categoryId' => 'custom',
                'category' => 'Gia công theo yêu cầu',
                'material' => 'Resin 8K',
                'print_technology' => 'SLA',
                'price' => 650000,
                'image' => '/assets/images/products/figma-product-1.png',
                'name_vi' => 'Mô hình Iron Man Mark 85 - Resin 8K Chi Tiết Cao',
                'description_vi' => 'Mô hình giáp Iron Man tỉ lệ 1:10, độ chi tiết sắc nét từng đường rãnh cơ khí với công nghệ SLA quang hóa.',
                'name_en' => 'Iron Man Mark 85 Model - High Detail 8K Resin',
                'description_en' => '1:10 scale Iron Man armor model with crisp mechanical grooves produced using photochemical SLA technology.',
            ],
            [
                'id' => 102,
                'slug' => 'khung-vo-robot-carbon-fiber',
                'categoryId' => 'custom',
                'category' => 'Gia công theo yêu cầu',
                'material' => 'Carbon Fiber PETG',
                'print_technology' => 'FDM',
                'price' => 480000,
                'image' => '/assets/images/products/figma-product-2.png',
                'name_vi' => 'Khung Vỏ Cơ Khí Robot Mini - Carbon Fiber Kháng Lực',
                'description_vi' => 'Khung robot gia cường sợi carbon chịu lực va đập vượt trội, kháng uốn và ổn định kích thước tuyệt đối.',
                'name_en' => 'Mini Robot Mechanical Chassis - Impact Resistant Carbon Fiber',
                'description_en' => 'Carbon fiber reinforced robot chassis offering outstanding impact resistance, flexural strength, and dimensional stability.',
            ],
            [
                'id' => 103,
                'slug' => 'hop-dung-danh-thiep-cyberpunk',
                'categoryId' => 'stationery',
                'category' => 'Văn phòng phẩm',
                'material' => 'PLA+ Matt Grey',
                'print_technology' => 'FDM',
                'price' => 150000,
                'image' => '/assets/images/products/figma-product-3.png',
                'name_vi' => 'Hộp Đựng Danh Thiếp & Bút Cyberpunk Hexagon',
                'description_vi' => 'Khay bút tổ ong đa giác phong cách Futuristic, tối ưu không gian làm việc công nghệ cao.',
                'name_en' => 'Cyberpunk Hexagon Business Card & Pen Holder',
                'description_en' => 'Futuristic honeycomb polygon organizer tray, optimizing high-tech desktop workspaces.',
            ],
            [
                'id' => 104,
                'slug' => 'moc-khoa-turbine-phan-luc',
                'categoryId' => 'keychains',
                'category' => 'Móc khoá',
                'material' => 'Nhựa Tough Resin',
                'print_technology' => 'SLA',
                'price' => 95000,
                'image' => '/assets/images/products/figma-product-4.png',
                'name_vi' => 'Móc Khoá Turbine Phản Lực Có Cánh Quạt Xoay',
                'description_vi' => 'Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt.',
                'name_en' => 'Jet Turbine Keychain with Spinning Fan Blades',
                'description_en' => 'Aerodynamic turbine blades that spin smoothly when blown, finished with an eye-catching metallic titanium coating.',
            ],
            [
                'id' => 105,
                'slug' => 'gia-do-tai-nghe-voronoi',
                'categoryId' => 'stationery',
                'category' => 'Trang trí không gian',
                'material' => 'PLA+ Silk Black',
                'print_technology' => 'FDM',
                'price' => 280000,
                'image' => '/assets/images/products/figma-product-5.png',
                'name_vi' => 'Giá Đỡ Tai Nghe Headphone Geometrical Voronoi',
                'description_vi' => 'Giá đỡ tai nghe dạng lưới cấu trúc bionic Voronoi siêu bền, tôn lên vẻ thẩm mỹ cho góc làm việc.',
                'name_en' => 'Voronoi Geometrical Headphone Stand',
                'description_en' => 'Ultra-durability bionic Voronoi lattice headphone holder, elevating desktop aesthetics.',
            ],
            [
                'id' => 106,
                'slug' => 'cum-banh-rang-hanh-tinh',
                'categoryId' => 'custom',
                'category' => 'Gia công theo yêu cầu',
                'material' => 'Nylon PA12',
                'print_technology' => 'SLS',
                'price' => 320000,
                'image' => '/assets/images/products/figma-product-6.png',
                'name_vi' => 'Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox',
                'description_vi' => 'Hộp số hành tinh tỉ số truyền 1:5, khớp răng chuẩn dung sai cơ khí, xoay êm ái linh hoạt.',
                'name_en' => 'Planetary Gearbox Assembly Set',
                'description_en' => '1:5 gear reduction planetary gearbox with precise mechanical tolerances and smooth rotation.',
            ],
            [
                'id' => 107,
                'slug' => 'tuong-cyber-wolf-low-poly',
                'categoryId' => 'stationery',
                'category' => 'Trang trí không gian',
                'material' => 'Resin Cao Cấp',
                'print_technology' => 'SLA',
                'price' => 520000,
                'image' => '/assets/images/products/figma-product-7.png',
                'name_vi' => 'Tượng Điêu Khắc Nghệ Thuật Low-Poly Cyber Wolf',
                'description_vi' => 'Tượng sói phong cách đa giác góc cạnh, bề mặt được xử lý mịn màng và phủ lớp nano bảo vệ bền bỉ.',
                'name_en' => 'Low-Poly Cyber Wolf Art Sculpture',
                'description_en' => 'Geometric faceted wolf sculpture featuring smooth surfaces and protective nano-coating.',
            ],
            [
                'id' => 108,
                'slug' => 'moc-khoa-dong-co-v8-piston',
                'categoryId' => 'keychains',
                'category' => 'Móc khoá',
                'material' => 'PLA Metal Fill',
                'print_technology' => 'FDM',
                'price' => 120000,
                'image' => '/assets/images/products/figma-product-8.png',
                'name_vi' => 'Móc Khoá Động Cơ V8 Piston Chuyển Động',
                'description_vi' => 'Mô phỏng trục khuỷu và piston động cơ V8 xe đua cơ bắp chuyển động tịnh tiến khi vặn núm xoay.',
                'name_en' => 'Moving Piston V8 Engine Keychain',
                'description_en' => 'Simulates crankshaft and reciprocating pistons of a muscle car V8 engine when rotating the dial.',
            ],
            [
                'id' => 109,
                'slug' => 'den-ngu-mat-trang-lithophane',
                'categoryId' => 'stationery',
                'category' => 'Trang trí không gian',
                'material' => 'PLA Translucent',
                'print_technology' => 'FDM',
                'price' => 350000,
                'image' => '/assets/images/products/figma-product-9.png',
                'name_vi' => 'Đèn Ngủ Mặt Trăng 3D Lithophane Kèm Đế Gỗ',
                'description_vi' => 'Quả cầu địa hình bề mặt mặt trăng với công nghệ thấu quang Lithophane, phát sáng dịu nhẹ ấm áp.',
                'name_en' => '3D Lithophane Moon Night Lamp with Wooden Base',
                'description_en' => 'Moon surface topography sphere with Lithophane light-transmitting technology, emitting soft warm ambient glow.',
            ],
            [
                'id' => 110,
                'slug' => 'khop-noi-cau-bionic-sls',
                'categoryId' => 'custom',
                'category' => 'Gia công theo yêu cầu',
                'material' => 'Nylon PA12',
                'print_technology' => 'SLS',
                'price' => 450000,
                'image' => '/assets/images/products/figma-product-10.png',
                'name_vi' => 'Khớp Nối Cầu Đa Hướng Ball-Joint Bionic',
                'description_vi' => 'Khớp nối linh hoạt in nguyên khối không cần support, độ bền cơ học và chịu mài mòn chuẩn công nghiệp.',
                'name_en' => 'Bionic Multi-Directional Ball-Joint',
                'description_en' => 'Monolithic print-in-place flexible joint with zero support material, offering industrial-grade wear resistance.',
            ],
            [
                'id' => 111,
                'slug' => 'mo-hinh-tau-vu-tru-discovery',
                'categoryId' => 'stationery',
                'category' => 'Mô hình không gian',
                'material' => 'Resin Trong Suốt',
                'print_technology' => 'SLA',
                'price' => 750000,
                'image' => '/assets/images/products/figma-product-11.png',
                'name_vi' => 'Mô Hình Tàu Vũ Trụ Discovery 3D Kèm Chân Đế Led',
                'description_vi' => 'Mô hình phi thuyền không gian viễn tưởng tỉ mỉ tới từng tấm khiên nhiệt, buồng lái hoàn thiện trong suốt.',
                'name_en' => 'Discovery 3D Spacecraft Model with LED Base',
                'description_en' => 'Sci-fi spaceship model meticulously crafted down to heat shields and transparent cockpit canopy.',
            ],
            [
                'id' => 112,
                'slug' => 'khay-to-chuc-day-cap-modular',
                'categoryId' => 'stationery',
                'category' => 'Văn phòng phẩm',
                'material' => 'PETG Bền Bỉ',
                'print_technology' => 'FDM',
                'price' => 180000,
                'image' => '/assets/images/products/figma-product-12.png',
                'name_vi' => 'Khay Tổ Chức Dây Cáp & Phụ Kiện Nam Châm Modular',
                'description_vi' => 'Khay cáp bàn làm việc ghép nối nam châm Neodymium linh hoạt, gom gọn mọi loại dây sạc gọn gàng.',
                'name_en' => 'Modular Magnetic Cable & Accessories Organizer',
                'description_en' => 'Flexible desktop cable tray connected with Neodymium magnets, organizing all charging cables neatly.',
            ],
            [
                'id' => 113,
                'slug' => 'moc-khoa-vo-lang-f1-quick-release',
                'categoryId' => 'keychains',
                'category' => 'Móc khoá',
                'material' => 'ABS Phủ Titan',
                'print_technology' => 'SLA',
                'price' => 135000,
                'image' => '/assets/images/products/figma-product-13.png',
                'name_vi' => 'Móc Khoá Vô Lăng F1 Quick Release Thể Thao',
                'description_vi' => 'Mô phỏng vô lăng xe đua F1 với cơ chế tháo lắp khớp bi cực kỳ chân thực, kim loại phối nhựa dẻo cao cấp.',
                'name_en' => 'F1 Quick Release Racing Steering Wheel Keychain',
                'description_en' => 'Replicates F1 race car steering wheel with authentic ball-lock quick-release mechanism.',
            ],
            [
                'id' => 114,
                'slug' => 'den-ban-led-hexagon-to-ong',
                'categoryId' => 'stationery',
                'category' => 'Trang trí không gian',
                'material' => 'PLA Diffuser',
                'print_technology' => 'FDM',
                'price' => 490000,
                'image' => '/assets/images/products/figma-product-14.png',
                'name_vi' => 'Đèn Bàn LED Hexagon Tổ Ong Chạm Cảm Ứng',
                'description_vi' => 'Các mô-đun đèn lục giác ghép nối tự do theo sở thích, điều khiển ánh sáng bằng cảm ứng bề mặt.',
                'name_en' => 'Touch-Sensitive Hexagonal Honeycomb LED Lamp',
                'description_en' => 'Modular hexagon light modules freely assembled and operated via capacitive surface touch controls.',
            ],
            [
                'id' => 115,
                'slug' => 'vo-case-ban-phim-co-custom',
                'categoryId' => 'custom',
                'category' => 'Gia công theo yêu cầu',
                'material' => 'Polycarbonate',
                'print_technology' => 'SLA',
                'price' => 890000,
                'image' => '/assets/images/products/figma-product-15.png',
                'name_vi' => 'Vỏ Case Bàn Phím Cơ Custom In 3D Đa Sắc',
                'description_vi' => 'Vỏ phím cơ layout 75% cấu trúc gasket mount tối ưu âm gõ, chịu nhiệt và chống biến dạng xuất sắc.',
                'name_en' => 'Custom 3D Printed Multi-Color Mechanical Keyboard Case',
                'description_en' => '75% layout keyboard case with gasket-mount structure for acoustic optimization and thermal resistance.',
            ],
            [
                'id' => 116,
                'slug' => 'moc-khoa-trai-tim-co-khi',
                'categoryId' => 'keychains',
                'category' => 'Móc khoá',
                'material' => 'Resin Đỏ Ruby',
                'print_technology' => 'SLA',
                'price' => 110000,
                'image' => '/assets/images/products/figma-product-16.png',
                'name_vi' => 'Móc Khoá Trái Tim Cơ Khí Bánh Răng Lồng Nhau',
                'description_vi' => 'Trái tim cơ khí độc đáo với cụm bánh răng lồng ghép chuyển động ăn khớp nhịp nhàng khi xoay chốt.',
                'name_en' => 'Interlocking Gear Mechanical Heart Keychain',
                'description_en' => 'Unique mechanical heart with interlocking gears that mesh smoothly as the pin turns.',
            ],
        ];
    }
}
