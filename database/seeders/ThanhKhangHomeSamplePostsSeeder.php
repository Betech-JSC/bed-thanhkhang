<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhKhangHomeSamplePostsSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 6 — Blog / News (Figma 595:15483).
     * - Reuses existing active POST category or creates 1 active category (VI + EN).
     * - Seeds 3 sample technical articles (type='POST') matched idempotently by VI slug
     *   in post_translations so re-running never duplicates and never touches FAQ posts.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $articles = [
            [
                'position' => 1,
                'is_featured' => 1,
                'is_home' => 1,
                'published_at' => '2024-10-26 09:00:00',
                'image' => [
                    'path' => '/assets/images/homepage/blog-ansi-roller-chain.png',
                    'alt' => 'Xích con lăn tiêu chuẩn ANSI là gì? Kiến thức cơ bản cho người mới',
                ],
                'vi' => [
                    'title' => 'Xích con lăn tiêu chuẩn ANSI là gì? Kiến thức cơ bản cho người mới',
                    'slug' => 'xich-con-lan-tieu-chuan-ansi-la-gi',
                    'description' => 'Tìm hiểu cấu tạo, quy ước đánh mã theo chuẩn ANSI B29.1, bước xích và cách chọn xích con lăn truyền động chính xác cho dây chuyền công nghiệp.',
                    'content' => '<h2>Tổng quan về xích con lăn tiêu chuẩn ANSI B29.1</h2>'
                        . '<p>Xích con lăn tiêu chuẩn ANSI (American National Standards Institute — tiêu chuẩn B29.1) là dòng xích truyền động cơ khí phổ biến nhất trong các hệ thống băng tải, máy đóng gói và thiết bị công nghiệp hiện đại. Việc nắm vững thông số bước xích (pitch), đường kính con lăn và tải trọng phá hủy giúp kỹ sư chọn đúng linh kiện thay thế ngay từ lần đầu.</p>'
                        . '<h2>Cấu tạo và cách đọc mã xích ANSI</h2>'
                        . '<p>Một sợi xích con lăn chuẩn gồm 5 chi tiết chính: má trong, má ngoài, chốt (pin), bạc lót (bushing) và con lăn (roller). Mã xích ANSI thường kết thúc bằng số 0 (xích chuẩn), số 1 (xích tải nhẹ) hoặc số 5 (xích không con lăn), trong đó chữ số đầu biểu thị bước xích theo bội số 1/8 inch.</p>'
                        . '<ul>'
                        . '<li><strong>Bước xích (Pitch):</strong> Ví dụ xích ANSI 40 có bước xích 4/8 = 1/2 inch (12.7 mm); xích ANSI 60 có bước xích 6/8 = 3/4 inch (19.05 mm).</li>'
                        . '<li><strong>Số dãy xích:</strong> Hậu tố -1, -2, -3 thể hiện xích đơn, xích đôi hoặc xích ba chịu tải nặng.</li>'
                        . '<li><strong>Vật liệu chế tạo:</strong> Thép hợp kim nhiệt luyện tăng độ bền mỏi hoặc thép không gỉ SUS304/SUS316 cho môi trường ẩm và thực phẩm.</li>'
                        . '</ul>'
                        . '<h2>Tiêu chí lựa chọn và bảo quản đúng kỹ thuật</h2>'
                        . '<p>Khi thay thế xích con lăn, doanh nghiệp nên đối chiếu đồng thời bước xích, chiều rộng trong (W), đường kính con lăn và tốc độ vòng quay của nhông xích. Thành Khang Tecsat luôn cung cấp đầy đủ bảng thông số kỹ thuật và chứng chỉ CO/CQ chính hãng cho mọi dòng xích ANSI.</p>',
                ],
                'en' => [
                    'title' => 'What Is an ANSI Standard Roller Chain? Essential Guide for Beginners',
                    'slug' => 'what-is-ansi-standard-roller-chain',
                    'description' => 'Understand ANSI B29.1 roller chain anatomy, numbering conventions, pitch sizing, and how to select reliable drive chains for industrial lines.',
                    'content' => '<h2>Overview of ANSI B29.1 Standard Roller Chains</h2>'
                        . '<p>ANSI standard roller chains (governed by ASME/ANSI B29.1) are among the most widely adopted mechanical power transmission components across packaging machinery, conveyors, and heavy manufacturing lines. Understanding chain pitch, roller diameter, and tensile strength ensures accurate OEM replacement.</p>'
                        . '<h2>Chain Anatomy and Numbering System</h2>'
                        . '<p>Every standard roller chain consists of five precision-machined elements: inner plates, outer plates, pins, bushings, and rollers. In ANSI numbering, the leading digit(s) indicate chain pitch in 1/8-inch increments, while the final digit denotes construction type (0 for standard roller, 1 for lightweight, 5 for bushing chain).</p>'
                        . '<ul>'
                        . '<li><strong>Pitch Sizing:</strong> ANSI 40 equals 4/8 = 1/2 inch (12.7 mm) pitch; ANSI 60 equals 6/8 = 3/4 inch (19.05 mm) pitch.</li>'
                        . '<li><strong>Multiple Strands:</strong> Suffixes -1, -2, and -3 designate simplex, duplex, and triplex heavy-load configurations.</li>'
                        . '<li><strong>Material Selection:</strong> Heat-treated alloy steel for high fatigue endurance, or SUS304/SUS316 stainless steel for washdown and food-grade facilities.</li>'
                        . '</ul>'
                        . '<h2>Selection and Maintenance Best Practices</h2>'
                        . '<p>Always verify pitch, inner width, roller outer diameter, and sprocket tooth wear together when ordering replacements. Thanh Khang Tecsat supplies genuine ANSI chains with complete dimensional datasheets and CO/CQ certification.</p>',
                ],
            ],
            [
                'position' => 2,
                'is_featured' => 1,
                'is_home' => 1,
                'published_at' => '2024-12-12 09:00:00',
                'image' => [
                    'path' => '/assets/images/homepage/blog-escalator-chain-maintenance.png',
                    'alt' => 'Bảo trì xích thang cuốn: Lịch kiểm tra, bôi trơn và tiêu chí thay thế',
                ],
                'vi' => [
                    'title' => 'Bảo trì xích thang cuốn: Lịch kiểm tra, bôi trơn và tiêu chí thay thế',
                    'slug' => 'bao-tri-xich-thang-cuon-lich-kiem-tra-boi-tron-va-tieu-chi-thay-the',
                    'description' => 'Hướng dẫn lịch kiểm tra định kỳ xích bậc thang cuốn, quy chuẩn dầu bôi trơn và ngưỡng độ giãn chốt xích để đảm bảo vận hành êm ái, an toàn.',
                    'content' => '<h2>Tầm quan trọng của bảo trì xích bậc thang cuốn</h2>'
                        . '<p>Xích bậc thang cuốn làm việc liên tục nhiều giờ mỗi ngày tại trung tâm thương mại, sân bay và nhà ga. Bảo trì định kỳ đúng quy trình giúp hạn chế mài mòn chốt bạc, giảm rung ồn và kéo dài tuổi thọ toàn bộ cụm truyền động.</p>'
                        . '<h2>Lịch kiểm tra và quy chuẩn bôi trơn định kỳ</h2>'
                        . '<ul>'
                        . '<li><strong>Kiểm tra hàng tháng:</strong> Quan sát độ căng hai nhánh xích trái – phải, vệ sinh bụi bẩn bám trên rãnh dẫn hướng và kiểm tra hoạt động của hệ thống bơm dầu tự động.</li>'
                        . '<li><strong>Kiểm tra hàng quý:</strong> Đo độ mài mòn con lăn PU/thép, kiểm tra độ rơ chốt xích và cân chỉnh đồng bộ cặp xích để tránh lệch bậc thang.</li>'
                        . '<li><strong>Bôi trơn đúng độ nhớt:</strong> Sử dụng dầu bôi trơn chuyên dụng có khả năng thẩm thấu vào khe chốt – bạc, tránh dùng mỡ đặc gây tích tụ bụi kim loại.</li>'
                        . '</ul>'
                        . '<h2>Tiêu chí đánh giá và thay thế cặp xích thang cuốn</h2>'
                        . '<p>Khi độ giãn dài tích lũy của xích vượt ngưỡng khuyến nghị của nhà sản xuất (thường từ 1.5% – 2% tổng chiều dài đo) hoặc xuất hiện nứt rỗ bề mặt con lăn, ban quản lý tòa nhà cần thay đồng bộ cả cặp xích trái – phải chính hãng để duy trì bước bậc chính xác tuyệt đối.</p>',
                ],
                'en' => [
                    'title' => 'Escalator Chain Maintenance: Inspection Schedule, Lubrication & Replacement Criteria',
                    'slug' => 'escalator-chain-maintenance-inspection-lubrication-replacement',
                    'description' => 'Practical guide to escalator step chain inspection intervals, proper lubrication standards, and elongation thresholds for safe, quiet operation.',
                    'content' => '<h2>Why Preventive Escalator Step Chain Maintenance Matters</h2>'
                        . '<p>Escalator step chains operate continuously under heavy passenger loads in shopping malls, airports, and transit hubs. Structured preventive maintenance minimizes pin-bushing wear, suppresses operating noise, and prevents unplanned downtime.</p>'
                        . '<h2>Inspection Intervals and Lubrication Guidelines</h2>'
                        . '<ul>'
                        . '<li><strong>Monthly Checks:</strong> Verify balanced tension across left and right chain strands, clear debris from guide tracks, and confirm automatic oilers deliver consistent flow.</li>'
                        . '<li><strong>Quarterly Audits:</strong> Measure PU/steel roller wear, inspect pin articulation play, and confirm synchronized step alignment.</li>'
                        . '<li><strong>Correct Lubricant Viscosity:</strong> Apply penetrating chain oil engineered to reach internal pin-bushing clearances instead of heavy grease that traps abrasive dust.</li>'
                        . '</ul>'
                        . '<h2>Replacement Criteria for Matched Step Chains</h2>'
                        . '<p>Once cumulative chain elongation exceeds OEM limits (typically 1.5%–2.0%) or roller fatigue cracking appears, replace both left and right strands as a factory-matched pair to preserve exact step pitch and passenger safety.</p>',
                ],
            ],
            [
                'position' => 3,
                'is_featured' => 0,
                'is_home' => 1,
                'published_at' => '2024-12-22 09:00:00',
                'image' => [
                    'path' => '/assets/images/homepage/blog-industrial-conveyor-chain.png',
                    'alt' => 'Ứng dụng xích tải công nghiệp: chọn đúng loại cho từng dây chuyền',
                ],
                'vi' => [
                    'title' => 'Ứng dụng xích tải công nghiệp: chọn đúng loại cho từng dây chuyền',
                    'slug' => 'ung-dung-xich-tai-cong-nghiep-chon-dung-loai-cho-tung-day-chuyen',
                    'description' => 'Phân loại xích tai gá, xích chốt rỗng, xích con lăn trên và xích tải nặng M/FV series giúp tối ưu hiệu suất từng dây chuyền sản xuất.',
                    'content' => '<h2>Vì sao cần chọn đúng chủng loại xích tải theo dây chuyền?</h2>'
                        . '<p>Mỗi dây chuyền sản xuất có đặc thù riêng về tải trọng, tốc độ băng tải, nhiệt độ và môi trường làm việc. Chọn đúng dòng xích tải ngay từ khâu thiết kế giúp giảm tiêu hao điện năng động cơ và hạn chế tối đa sự cố dừng máy ngoài kế hoạch.</p>'
                        . '<h2>Các nhóm xích tải công nghiệp phổ biến</h2>'
                        . '<ul>'
                        . '<li><strong>Xích chốt rỗng (Hollow Pin Chain):</strong> Cho phép lắp trực tiếp thanh ngang và gá kẹp qua lỗ chốt, lý tưởng cho máy đóng gói và băng tải định vị sản phẩm.</li>'
                        . '<li><strong>Xích con lăn trên &amp; con lăn cạnh (TR/SR):</strong> Vận chuyển hàng hóa trơn tru theo tuyến thẳng hoặc cong, ứng dụng rộng rãi trong băng tải hành lý sân bay và kho vận logistics.</li>'
                        . '<li><strong>Xích tải hệ mét M Series &amp; FV Series:</strong> Kết cấu má xích dày chịu tải nặng, phù hợp cho nhà máy vật liệu xây dựng, chế biến gỗ và sản xuất công nghiệp nặng.</li>'
                        . '</ul>'
                        . '<h2>Tư vấn kỹ thuật chuyên sâu từ Thành Khang Tecsat</h2>'
                        . '<p>Đội ngũ kỹ sư Thành Khang Tecsat sẵn sàng khảo sát bản vẽ, tính toán hệ số an toàn tải kéo và đề xuất mã xích Universal Chains chuẩn ISO/DIN/ANSI tối ưu nhất cho từng nhà máy.</p>',
                ],
                'en' => [
                    'title' => 'Industrial Conveyor Chain Applications: Choosing the Right Type for Every Line',
                    'slug' => 'industrial-conveyor-chain-applications-choosing-the-right-type',
                    'description' => 'Compare attachment, hollow-pin, top-roller, and heavy-duty M/FV series conveyor chains to match load, speed, and environment on every line.',
                    'content' => '<h2>Matching Conveyor Chain Design to Production Requirements</h2>'
                        . '<p>Every industrial conveyor line presents distinct load profiles, line speeds, and environmental conditions. Selecting the proper chain architecture during specification lowers drive power consumption and eliminates premature fatigue failures.</p>'
                        . '<h2>Core Industrial Conveyor Chain Families</h2>'
                        . '<ul>'
                        . '<li><strong>Hollow Pin Chains:</strong> Enable direct mounting of cross-rods and custom fixtures through hollow pins, ideal for packaging lines and precision indexing conveyors.</li>'
                        . '<li><strong>Top-Roller &amp; Side-Roller Chains (TR/SR):</strong> Deliver low-friction conveying across straight and curved tracks for airport baggage handling and automated logistics hubs.</li>'
                        . '<li><strong>Metric M Series &amp; FV Series Chains:</strong> Heavy-gauge plates engineered for high tensile loads in building materials, timber processing, and heavy manufacturing plants.</li>'
                        . '</ul>'
                        . '<h2>Engineering Selection Support from Thanh Khang Tecsat</h2>'
                        . '<p>Our engineering team reviews drawings, calculates working load safety factors, and recommends ISO/DIN/ANSI certified Universal Chains tailored to your facility.</p>',
                ],
            ],
        ];

        DB::transaction(function () use ($articles) {
            // 1. Reuse existing active POST category or create 1 active category
            $category = PostCategory::query()
                ->where('type', PostCategory::TYPE_POST)
                ->where('status', PostCategory::STATUS_ACTIVE)
                ->orderBy('id')
                ->first();

            if (!$category) {
                $category = PostCategory::create([
                    'parent_id' => 0,
                    'status' => PostCategory::STATUS_ACTIVE,
                    'type' => PostCategory::TYPE_POST,
                    'position' => 1,
                    'view_count' => 0,
                ]);

                $categoryTranslations = [
                    'vi' => [
                        'title' => 'Kiến thức kỹ thuật',
                        'slug' => 'kien-thuc-ky-thuat',
                        'seo_slug' => 'kien-thuc-ky-thuat',
                    ],
                    'en' => [
                        'title' => 'Technical Insights',
                        'slug' => 'technical-insights',
                        'seo_slug' => 'technical-insights',
                    ],
                ];

                foreach ($categoryTranslations as $locale => $tr) {
                    DB::table('post_category_translations')->updateOrInsert(
                        [
                            'post_category_id' => $category->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'slug' => $tr['slug'],
                            'seo_slug' => $tr['seo_slug'],
                        ]
                    );
                }
            }

            // 2. Idempotently seed 3 sample type='POST' articles matched by VI slug
            foreach ($articles as $article) {
                $viSlug = $article['vi']['slug'];
                $existingPostId = DB::table('post_translations')
                    ->join('posts', 'posts.id', '=', 'post_translations.post_id')
                    ->whereNull('posts.deleted_at')
                    ->where('posts.type', Post::TYPE_POST)
                    ->where('post_translations.locale', 'vi')
                    ->where('post_translations.slug', $viSlug)
                    ->value('posts.id');

                if ($existingPostId) {
                    $post = Post::find($existingPostId);
                    if ($post) {
                        $post->categories()->syncWithoutDetaching([$category->id]);
                    }
                    continue;
                }

                $post = Post::create([
                    'status' => Post::STATUS_ACTIVE,
                    'type' => Post::TYPE_POST,
                    'position' => $article['position'],
                    'is_featured' => $article['is_featured'],
                    'is_home' => $article['is_home'],
                    'published_at' => $article['published_at'],
                    'image' => $article['image'],
                    'view_count' => 0,
                    'virtual_views' => 0,
                    'price' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $article[$locale];
                    DB::table('post_translations')->updateOrInsert(
                        [
                            'post_id' => $post->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'slug' => $tr['slug'],
                            'seo_slug' => $tr['slug'],
                            'description' => $tr['description'],
                            'content' => $tr['content'],
                            'seo_meta_title' => $tr['title'],
                            'seo_meta_description' => $tr['description'],
                            'status_locale' => 'ACTIVE',
                        ]
                    );
                }

                $post->categories()->syncWithoutDetaching([$category->id]);
            }
        });

        Post::clearPostCaches();
        PostCategory::clearPostCategoryCaches();
        Config::clearBrandCaches();
    }
}
