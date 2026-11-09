<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Traits\Searchable;
use App\Traits\Translatable;

class Service extends BaseModel
{
    use HasFactory, SoftDeletes, Searchable, Translatable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations'];

    public $fillable = [
        'status',
        'position',
        'view_count',
        'image',
        'benefit_image',
        'sliders',
        'is_content_by_tab',
        'email',

        'inject_head',
        'inject_body_start',
        'inject_body_end',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'description',
        'content',
        'title_content',
        'content_by_tab',
        'benefits',
        'benefit_title',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
    ];

    protected $casts = [
        'image' => 'array',
        'benefit_image' => 'array',
        'sliders' => 'array'
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }

    protected $searchable = [
        'columns' => [
            'service_translations.title' => 10,
            'service_translations.id' => 5,
            'service_translations.slug' => 5,
        ],
        'joins' => [
            'service_translations' => ['service_translations.service_id', 'services.id'],
        ],
    ];

    protected $appends = ['url'];

    protected static function booted()
    {
        static::saved(function (self $model) {
            static::clearServiceCaches($model);
        });

        static::deleted(function (self $model) {
            static::clearServiceCaches($model);
        });
    }

    public static function clearServiceCaches(?self $model = null): void
    {
        $locales = config('app.locales', ['vi', 'en']);
        $slugs = [];

        if ($model) {
            if (!empty($model->slug)) {
                $slugs[] = $model->slug;
            }
            if (!empty($model->seo_slug)) {
                $slugs[] = $model->seo_slug;
            }
            if ($model->relationLoaded('translations') || method_exists($model, 'translations')) {
                try {
                    foreach ($model->translations()->get() as $translation) {
                        if (!empty($translation->slug)) {
                            $slugs[] = $translation->slug;
                        }
                        if (!empty($translation->seo_slug)) {
                            $slugs[] = $translation->seo_slug;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore if table not ready
                }
            }
        }
        $slugs = array_values(array_unique(array_filter($slugs)));

        foreach ($locales as $locale) {
            \Illuminate\Support\Facades\Cache::forget('services_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('process_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('inertia_layout_shared_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('search_page_data_' . $locale . '_');

            foreach ($slugs as $slug) {
                \Illuminate\Support\Facades\Cache::forget('service_show_' . $slug . '_' . $locale);
            }
        }
    }

    public function getUrlAttribute(): array
    {
        $urls = [];

        if ($this->is_active) {
            foreach ($this->translations as $translation) {
                $urls[strtoupper($translation->locale)] = route("$translation->locale.services.show", [
                    'slug' => $translation->seo_slug ?? $translation->slug,
                ]);
            }
        }
        return $urls;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeSortByPosition($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC')
            ->orderBy('id', 'desc');
    }

    public function transform()
    {
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $isEn = ($locale === 'en');
        $serviceKey = $this->getServiceKey();
        $kb = self::getServiceKnowledgeBase($serviceKey, $locale);
        $kbVi = self::getServiceKnowledgeBase($serviceKey, 'vi');

        $rawTitle = trim((string) ($this->title ?? ''));
        $title = $rawTitle ?: ($kb['listing_title'] ?? $kb['title'] ?? '');
        if ($serviceKey === 'lam-do-custom' && in_array($rawTitle, ['', 'Custom'], true)) {
            $title = $kb['listing_title'] ?? ($isEn ? 'Custom Bespoke Creations' : 'Làm đồ custom');
        }

        $rawSubtitle = trim((string) ($this->title_content ?? ''));
        $heroHeadlineVi = trim((string) ($kbVi['hero']['headline'] ?? ''));
        $heroHeadlineEn = trim((string) ($kb['hero']['headline'] ?? ''));
        if (!$isEn && $rawSubtitle !== '' && $rawSubtitle !== $heroHeadlineVi && $rawSubtitle !== $heroHeadlineEn) {
            $subtitle = $rawSubtitle;
        } else {
            $subtitle = $kb['subtitle'] ?? $rawSubtitle;
        }

        $rawDesc = trim((string) ($this->description ?? ''));
        $overviewVi = trim((string) ($kbVi['overview_description'] ?? ''));
        $overviewEn = trim((string) ($kb['overview_description'] ?? ''));

        if ($rawDesc === '' || $rawDesc === $overviewVi || $rawDesc === $overviewEn) {
            $description = $kb['page_description'] ?? $kb['description'] ?? $rawDesc;
        } else {
            $description = $rawDesc;
        }

        $imageDetail = $this->getImageDetail($this->image);
        if (empty($imageDetail['url'])) {
            $defaultImgPath = $kb['listing_image'] ?? '/images/services/service-design-3d.png';
            $imageDetail = $this->getImageDetail(['path' => $defaultImgPath, 'alt' => $title]);
        }

        return [
            'id' => $this->id,
            'title' => $title,
            'home_title' => ($rawTitle !== '' && !($serviceKey === 'lam-do-custom' && $rawTitle === 'Làm đồ custom')) ? $rawTitle : ($kb['title'] ?? $title),
            'slug' => $this->seo_slug ?? $this->slug ?: $serviceKey,
            'subtitle' => $subtitle,
            'description' => $description,
            'overview_description' => $kb['overview_description'] ?? $rawDesc,
            'link' => $this->content ?: ('/dich-vu/' . ($this->seo_slug ?? $this->slug ?: $serviceKey)),
            'image' => $imageDetail,
            'tags' => $kb['tags'] ?? [],
            'prep_title' => $kb['prep_title'] ?? ($isEn ? 'YOU PREPARE' : 'BẠN CHUẨN BỊ'),
            'preparation' => $kb['preparation'] ?? '',
            'comp_title' => $kb['comp_title'] ?? ($isEn ? 'WE CO-CREATE' : 'CÙNG HOÀN THIỆN'),
            'completion' => $kb['completion'] ?? '',
            'cta_text' => $kb['cta_text'] ?? ($isEn ? 'Contact for consultation' : 'Liên hệ tư vấn dịch vụ'),
            'position' => $this->position ?? ($kb['position'] ?? 1),
            'listing_order' => $kb['listing_order'] ?? ($this->position ?? 1),
        ];
    }

    public function getServiceKey(): string
    {
        $slug = (string) ($this->slug ?? $this->seo_slug ?? '');
        $id = (int) $this->id;
        $title = (string) ($this->title ?? '');

        if ($id === 2 || str_contains($slug, 'gia-cong') || str_contains($slug, 'machining')) {
            return 'gia-cong-3d';
        }
        if ($id === 3 || str_contains($slug, 'custom') || str_contains(strtolower($title), 'custom') || str_contains(strtolower($title), 'làm đồ custom')) {
            return 'lam-do-custom';
        }
        return 'thiet-ke-3d';
    }

    public static function getKnowledgeBaseMap(): array
    {
        return [
            'thiet-ke-3d' => [
                'vi' => [
                    'title' => 'Thiết kế 3D',
                    'listing_title' => 'Thiết kế 3D',
                    'position' => 2,
                    'listing_order' => 1,
                    'subtitle' => 'Từ phác thảo đến hình khối rõ ràng.',
                    'overview_description' => 'Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế.',
                    'description' => 'Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế.',
                    'page_description' => 'Bạn có ý tưởng nhưng chưa có mô hình? HH Maker cùng bạn làm rõ tỉ lệ, cấu trúc và chi tiết, chuyển hình ảnh tham khảo hoặc bản phác thảo thành thiết kế 3D hướng đến khả năng chế tác thực tế.',
                    'listing_image' => '/images/services/service-design-3d.png',
                    'tags' => ['Phát triển ý tưởng', 'Dựng hình', 'Tối ưu để in'],
                    'prep_title' => 'BẠN CHUẨN BỊ',
                    'preparation' => 'Ý tưởng, ảnh tham khảo, kích thước dự kiến và mục đích sử dụng.',
                    'comp_title' => 'CÙNG HOÀN THIỆN',
                    'completion' => 'Phương án mô hình 3D và các chi tiết cần thống nhất trước khi gia công.',
                    'cta_text' => 'Liên hệ tư vấn thiết kế 3D',
                    'hero' => [
                        'headline' => 'Thiết kế 3D: Từ phác thảo đến hình khối rõ ràng',
                        'description' => 'Một ý tưởng chưa cần hoàn chỉnh để bắt đầu. Cùng HH Maker làm rõ tỉ lệ, cấu trúc và chi tiết, để mô hình 3D không chỉ dễ hình dung mà còn hướng đến khả năng chế tác thực tế.',
                        'input_items' => ['Ý tưởng', 'Ảnh tham khảo', 'Bản phác thảo'],
                        'process_items' => ['Tỉ lệ & hình khối', 'Cấu trúc & chi tiết', 'Khả năng chế tác'],
                        'image' => '/assets/images/services/hero-3d-illustration.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'Khi nào bạn cần thiết kế 3D?', 'shortTitle' => 'Khi nào cần 3D?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'Từ ý tưởng tới mô hình 3D', 'shortTitle' => 'Quy trình'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'Bạn cần chuẩn bị gì?', 'shortTitle' => 'Chuẩn bị gì?'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Cùng hoàn thiện trước khi gia công', 'shortTitle' => 'Hoàn thiện'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'Khi nào bạn cần thiết kế 3D?',
                            'paragraphs' => [
                                'Bạn đã hình dung một món đồ nhưng chưa có mô hình để triển khai? Một bức ảnh thường chỉ cho thấy một góc nhìn; bản phác thảo có thể chưa thể hiện rõ chiều sâu, tỉ lệ hay cách các phần kết nối với nhau.',
                                'Thiết kế 3D giúp làm rõ những điều đó trước khi chuyển sang sản phẩm vật lý. Đây là bước phù hợp khi bạn muốn phát triển một hình dáng mới, điều chỉnh mẫu tham khảo hoặc kiểm tra các chi tiết cần lắp ghép. HH Maker cùng bạn chuyển thông tin ban đầu thành phương án có thể xem xét và trao đổi cụ thể hơn.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'Từ ý tưởng tới mô hình 3D',
                            'intro' => 'Không nhất thiết phải chốt mọi chi tiết từ đầu. Điều quan trọng là làm rõ từng phần theo thứ tự, để những điều chỉnh sau đó có cơ sở.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'Bạn cần chuẩn bị gì?',
                            'intro' => 'Thông tin càng rõ, việc trao đổi càng sát với mong muốn của bạn. Trước khi gửi yêu cầu, hãy tập hợp những nội dung dưới đây; phần nào chưa chắc có thể ghi chú để cùng thảo luận.',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Cùng hoàn thiện trước khi gia công',
                            'paragraph' => 'Kết quả của giai đoạn thiết kế là phương án mô hình 3D cùng các chi tiết đã thống nhất: hình dáng, tỉ lệ, kích thước và những yêu cầu liên quan đến cách sử dụng. Đây là cơ sở để trao đổi bước gia công tiếp theo, không phải sản phẩm vật lý hoàn thiện.',
                            'callout' => 'Một mô hình đẹp trên màn hình vẫn cần được xem xét với vật liệu và cách chế tác thực tế. Hãy thống nhất các yêu cầu quan trọng trước khi chuyển sang gia công.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'Trao đổi ý tưởng và mục đích sử dụng',
                            'description' => 'Bạn chia sẻ sản phẩm muốn làm, hình ảnh tham khảo và điều quan trọng nhất với mình. HH Maker cùng bạn xác định công năng, kích thước dự kiến và những chi tiết cần ưu tiên.',
                        ],
                        [
                            'number' => '02',
                            'title' => 'Dựng hình khối và xác định tỉ lệ',
                            'description' => 'Từ thông tin đã trao đổi, ý tưởng được chuyển thành hình khối 3D. Ở bước này, hãy tập trung vào dáng tổng thể, tương quan kích thước và vị trí các bộ phận trước khi đi sâu vào chi tiết.',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Hoàn thiện cấu trúc và chi tiết',
                            'description' => 'Cùng xem lại độ dày, bề mặt, vị trí lắp ghép hoặc phần chữ cần thể hiện. Những chi tiết nhỏ cần được cân nhắc với kích thước thực tế và hướng gia công, thay vì chỉ đẹp trên màn hình.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Thống nhất phương án trước khi gia công',
                            'description' => 'Hai bên rà soát mô hình, kích thước và các chi tiết đã điều chỉnh. Nếu chuyển sang gia công, vật liệu, bề mặt và yêu cầu lắp ghép cần được trao đổi thêm để chọn cách thực hiện phù hợp.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => 'Ý tưởng & mục đích sử dụng',
                            'description' => 'Món đồ dùng để làm gì, dành cho ai? Có yêu cầu về cầm nắm, trưng bày hay lắp vào một bộ phận khác không?',
                        ],
                        [
                            'title' => 'Ảnh tham khảo hoặc phác thảo',
                            'description' => 'Gửi các góc nhìn bạn có và đánh dấu chi tiết muốn giữ hoặc thay đổi. Một bản vẽ tay có ghi chú cũng là điểm bắt đầu hữu ích.',
                        ],
                        [
                            'title' => 'Kích thước dự kiến',
                            'description' => 'Chiều dài, rộng, cao và đơn vị đo. Nếu cần lắp ghép, bổ sung kích thước hoặc ảnh của phần tiếp xúc liên quan.',
                        ],
                        [
                            'title' => 'Chi tiết cần ưu tiên',
                            'description' => 'Ghi rõ phần chữ, hình dáng, bề mặt hoặc giới hạn không thể thay đổi để cùng cân nhắc ngay từ đầu.',
                        ],
                    ],
                ],
                'en' => [
                    'title' => '3D Design',
                    'listing_title' => '3D Design',
                    'position' => 2,
                    'listing_order' => 1,
                    'subtitle' => 'From sketch to concrete geometry.',
                    'overview_description' => 'Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability.',
                    'description' => 'Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability.',
                    'page_description' => 'Have an idea but no 3D model yet? HH Maker works with you to define proportions, structure, and details, transforming reference photos or sketches into 3D designs tailored for practical manufacturing.',
                    'listing_image' => '/images/services/service-design-3d.png',
                    'tags' => ['Concept Development', '3D Modeling', 'Print Optimization'],
                    'prep_title' => 'YOU PREPARE',
                    'preparation' => 'Ideas, reference images, intended dimensions, and functional requirements.',
                    'comp_title' => 'WE CO-CREATE',
                    'completion' => '3D model blueprint and technical details aligned before production.',
                    'cta_text' => 'Consult 3D Design',
                    'hero' => [
                        'headline' => '3D Design: From sketch to concrete geometry',
                        'description' => 'An idea does not need to be complete to begin. Together with HH Maker, clarify proportions, structure, and details, ensuring the 3D model is not only easy to visualize but also ready for real-world manufacturing.',
                        'input_items' => ['Ideas & Concepts', 'Reference Photos', 'Sketches & Notes'],
                        'process_items' => ['Proportions & Forms', 'Structure & Details', 'Manufacturability'],
                        'image' => '/assets/images/services/hero-3d-illustration.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'When do you need 3D design?', 'shortTitle' => 'When need 3D?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'From concept to 3D model', 'shortTitle' => 'From concept'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'What should you prepare?', 'shortTitle' => 'What to prepare?'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Completing together before machining', 'shortTitle' => 'Completion'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'When do you need 3D design?',
                            'paragraphs' => [
                                'Have you envisioned an object but lack a digital model for implementation? A single photo usually shows only one angle; a hand sketch might not clearly convey depth, proportion, or how individual parts assemble.',
                                '3D design clarifies these fundamentals before moving into physical production. This is the ideal step when developing a new geometry, customizing reference models, or validating mechanical joints. HH Maker works alongside you to transform initial ideas into concrete, reviewable 3D models.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'From concept to 3D model',
                            'intro' => 'You don\'t need to lock down every detail from day one. What matters is clarifying each element in structured stages so adjustments are firmly grounded.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'What should you prepare?',
                            'intro' => 'The clearer your information, the more accurately we can align with your expectations. Before submitting a request, gather the details below; note anything uncertain for discussion.',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Completing together before machining',
                            'paragraph' => 'The output of 3D design is a validated digital 3D model with agreed proportions, dimensions, and operational requirements. This forms the foundation for the subsequent machining stage, not the physical end product.',
                            'callout' => 'A beautiful on-screen model must still be validated against actual physical materials and fabrication methods. Let\'s align on key constraints before manufacturing.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'Discuss Ideas and Intended Use',
                            'description' => 'Share the product you want to create, reference images, and what matters most to you. HH Maker works with you to define functionality, target dimensions, and priority details.',
                        ],
                        [
                            'number' => '02',
                            'title' => '3D Modeling & Proportion Alignment',
                            'description' => 'Based on our discussion, your concept is translated into a 3D form. At this stage, focus on overall silhouette, dimensional proportions, and part placement before diving into fine details.',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Refining Structure & Details',
                            'description' => 'Review wall thickness, surface texture, assembly joints, or embossed text together. Small details are balanced against actual physical dimensions and fabrication methods rather than just looking good on screen.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Aligning Blueprints Before Machining',
                            'description' => 'Both sides review the 3D model, dimensions, and adjusted details. When moving to fabrication, materials, surface finish, and assembly requirements are aligned to choose the best production method.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => 'Concept & Intended Use',
                            'description' => 'What is the item used for and who is it for? Are there specific requirements for grip, display, or fitting into another assembly?',
                        ],
                        [
                            'title' => 'Reference Images or Sketches',
                            'description' => 'Send any angles you have and highlight details to keep or modify. Even a hand-drawn sketch with notes is a helpful starting point.',
                        ],
                        [
                            'title' => 'Estimated Dimensions',
                            'description' => 'Length, width, height, and measurement units. If parts need to mate together, include dimensions or photos of the connecting interface.',
                        ],
                        [
                            'title' => 'Priority Details',
                            'description' => 'Specify any text, geometry, surface finish, or fixed constraints that cannot be changed so we can account for them from the start.',
                        ],
                    ],
                ],
            ],
            'gia-cong-3d' => [
                'vi' => [
                    'title' => 'Gia công 3D',
                    'listing_title' => 'Gia công 3D',
                    'position' => 1,
                    'listing_order' => 2,
                    'subtitle' => 'Đưa bản thiết kế ra ngoài màn hình.',
                    'overview_description' => 'Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm',
                    'description' => 'Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm',
                    'page_description' => 'Từ file 3D đã có, chúng tôi xem xét cấu trúc mô hình, tư vấn vật liệu và cách in phù hợp. Quá trình gia công chú trọng từng lớp in, bề mặt và các chi tiết liên quan đến công năng của sản phẩm.',
                    'listing_image' => '/images/services/service-manufacturing-3d.png',
                    'tags' => ['Kiểm tra mô hình', 'Tư vấn vật liệu', 'In & hoàn thiện'],
                    'prep_title' => 'BẠN CHUẨN BỊ',
                    'preparation' => 'File 3D, số lượng, kích thước và yêu cầu về bề mặt hoặc lắp ghép.',
                    'comp_title' => 'CÙNG HOÀN THIỆN',
                    'completion' => 'Sản phẩm vật lý theo phương án và yêu cầu gia công đã thống nhất.',
                    'cta_text' => 'Liên hệ tư vấn gia công 3D',
                    'hero' => [
                        'headline' => 'Gia công 3D: Hiện thực hóa mô hình với độ chính xác cao',
                        'description' => 'Đưa bản thiết kế ra ngoài màn hình. Gia công và in 3D chính xác từng milimet với vật liệu và công nghệ tối ưu cho từng mục đích sử dụng.',
                        'input_items' => ['File 3D (STL/STEP/OBJ)', 'Kích thước & độ bền', 'Số lượng cần làm'],
                        'process_items' => ['Công nghệ & vật liệu in', 'Độ mịn & độ dày thành', 'Phương án hậu kỳ bề mặt'],
                        'image' => '/assets/images/services/service-gia-cong-3d.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'Khi nào bạn cần gia công 3D?', 'shortTitle' => 'Khi nào cần gia công?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'Quy trình gia công chuẩn xác', 'shortTitle' => 'Quy trình'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'Thông số kỹ thuật cần chuẩn bị', 'shortTitle' => 'Thông số cần có'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Hậu kỳ & Tiêu chuẩn hoàn thiện', 'shortTitle' => 'Tiêu chuẩn hoàn thiện'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'Khi nào bạn cần gia công 3D?',
                            'paragraphs' => [
                                'Bạn đã có sẵn file thiết kế 3D và muốn hiện thực hóa thành sản phẩm thực tế với độ chính xác và độ hoàn thiện cao nhất? Gia công và in 3D là cầu nối đưa bản vẽ số hóa bước ra đời thực.',
                                'Dù bạn cần chế tạo mẫu thử nghiệm (prototype) để kiểm tra lắp ghép cơ khí, tạo mô hình mỹ thuật chi tiết hay sản xuất các lô phụ tùng số lượng vừa và nhỏ, HH Maker cung cấp các giải pháp công nghệ FDM, SLA, SLS tối ưu theo từng yêu cầu chịu lực, độ mịn và ngân sách.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'Quy trình gia công chuẩn xác',
                            'intro' => 'Mỗi sản phẩm in ấn và gia công tại HH Maker đều tuân thủ quy trình kiểm soát chất lượng khắt khe từ khâu nạp file đến khi thành phẩm xuất xưởng.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'Thông số kỹ thuật cần chuẩn bị',
                            'intro' => 'Để nhận báo giá nhanh chóng và phương án gia công tối ưu nhất, bạn vui lòng cung cấp các thông tin kỹ thuật sau:',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Hậu kỳ & Tiêu chuẩn hoàn thiện',
                            'paragraph' => 'Sản phẩm sau in mộc thường còn dấu vết support và đường vân lớp (layer lines). Tùy theo nhu cầu ứng dụng thực tế, bạn có thể lựa chọn mức độ hoàn thiện từ mộc kỹ thuật, tẩy bóng hóa chất đến chà nhám thủ công và sơn lót phủ màu cao cấp.',
                            'callout' => 'Lưu ý: Mọi công nghệ in 3D đều có hệ số co ngót vật liệu nhất định (0.2% - 0.5%). Nếu chi tiết cần lắp ráp chính xác với trục kim loại hay ổ bi, hãy thông báo trước để kỹ sư bù trừ kích thước khi cắt lớp.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'Kiểm tra và chuẩn hóa file 3D',
                            'description' => 'Đội ngũ kỹ thuật rà soát file STL/STEP, kiểm tra lỗi lưới (mesh errors), kiểm tra độ dày thành tối thiểu và định hướng góc in tối ưu độ bền.',
                        ],
                        [
                            'number' => '02',
                            'title' => 'Lựa chọn công nghệ & vật liệu',
                            'description' => 'Tư vấn công nghệ in (FDM sợi nhựa kỹ thuật, Resin độ nét cao SLA hoặc SLS công nghiệp) cùng vật liệu thích hợp (PLA, PETG, ABS, Resin, Nylon).',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Tiến hành in & giám sát quá trình',
                            'description' => 'Cài đặt thông số lát cắt (slicing layer), mật độ infill và vận hành hệ thống máy in đa trục với sự giám sát chặt chẽ từng lớp in.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Xử lý hậu kỳ & kiểm định dung sai',
                            'description' => 'Loại bỏ support, vệ sinh dung dịch, sấy nhiệt hóa cứng UV, xử lý nhám, sơn phủ hoàn thiện và đo kiểm kích thước thực tế bằng thước kẹp điện tử.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => 'Định dạng file 3D',
                            'description' => 'File .STL, .STEP, .STP hoặc .OBJ khép kín khối (manifold / watertight), không bị hở mặt mesh.',
                        ],
                        [
                            'title' => 'Yêu cầu vật liệu & môi trường làm việc',
                            'description' => 'Mô hình để trưng bày trong nhà, chi tiết chịu tải cơ học hay phụ tùng chịu nhiệt độ ngoài trời?',
                        ],
                        [
                            'title' => 'Dung sai kích thước & độ mịn',
                            'description' => 'Độ phân giải mong muốn theo lớp (0.05mm - 0.2mm) và các vị trí dung sai lắp ghép quan trọng.',
                        ],
                        [
                            'title' => 'Số lượng & thời gian bàn giao',
                            'description' => 'Số lượng sản phẩm cho từng chi tiết và mốc thời gian bạn cần nhận hàng.',
                        ],
                    ],
                ],
                'en' => [
                    'title' => '3D Machining & Printing',
                    'listing_title' => '3D Machining & Printing',
                    'position' => 1,
                    'listing_order' => 2,
                    'subtitle' => 'Bringing digital designs into the physical world.',
                    'overview_description' => 'Realizing models with 3D printing technology, focusing on precision and product quality.',
                    'description' => 'Realizing models with 3D printing technology, focusing on precision and product quality.',
                    'page_description' => 'From existing 3D files, we review geometry, recommend suitable materials, and select the optimal printing method. Manufacturing focuses on layer resolution, surface finish, and functional mechanical aspects.',
                    'listing_image' => '/images/services/service-manufacturing-3d.png',
                    'tags' => ['Model Verification', 'Material Consultation', 'Printing & Finishing'],
                    'prep_title' => 'YOU PREPARE',
                    'preparation' => '3D files, quantities, dimensions, and surface or assembly tolerances.',
                    'comp_title' => 'WE CO-CREATE',
                    'completion' => 'Physical parts produced to agreed specifications and machining standards.',
                    'cta_text' => 'Consult 3D Machining',
                    'hero' => [
                        'headline' => '3D Machining & Printing: Bringing digital models into reality with high precision',
                        'description' => 'Bringing digital designs into the physical world. Millimeter-accurate 3D printing and machining with optimal materials and technologies for every application.',
                        'input_items' => ['3D Files (STL/STEP/OBJ)', 'Dimensions & Strength', 'Production Quantity'],
                        'process_items' => ['Printing Tech & Materials', 'Smoothness & Wall Thickness', 'Surface Post-Processing'],
                        'image' => '/assets/images/services/service-gia-cong-3d.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'When do you need 3D machining & printing?', 'shortTitle' => 'When need machining?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'Precision fabrication workflow', 'shortTitle' => 'Workflow'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'Technical specifications to prepare', 'shortTitle' => 'Specs to prepare'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Post-processing & finishing standards', 'shortTitle' => 'Finishing standards'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'When do you need 3D machining & printing?',
                            'paragraphs' => [
                                'Do you already have a 3D CAD file and want to realize it into a high-precision physical component? 3D machining and additive printing bridge the gap between digital models and tangible reality.',
                                'Whether you need functional prototypes for assembly fitment, high-detail display models, or low-to-medium volume spare part production, HH Maker delivers optimal FDM, SLA, and SLS solutions tailored to structural strength, surface smoothness, and budget.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'Precision fabrication workflow',
                            'intro' => 'Every component manufactured at HH Maker follows a rigorous quality assurance workflow from digital slicing to final delivery inspection.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'Technical specifications to prepare',
                            'intro' => 'To receive an accurate quote and optimal machining approach quickly, please prepare the following technical parameters:',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Post-processing & finishing standards',
                            'paragraph' => 'Raw 3D prints retain minor support contact points and layer lines. Depending on your use case, choose between raw functional finish, chemical smoothing, hand sanding, and multi-stage spray painting.',
                            'callout' => 'Note: All 3D printing technologies entail material shrinkage factors (0.2% - 0.5%). If parts must interface with metal shafts or bearings, notify us beforehand for slicing offsets.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'File Inspection & Normalization',
                            'description' => 'Our engineering team checks STL/STEP files for mesh errors, minimum wall thickness, and optimizes print orientation for maximum layer strength.',
                        ],
                        [
                            'number' => '02',
                            'title' => 'Technology & Material Selection',
                            'description' => 'Consulting on the best printing process (engineering FDM, high-detail Resin SLA, or industrial SLS) and materials (PLA, PETG, ABS, Resin, Nylon).',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Printing Execution & Real-Time Monitoring',
                            'description' => 'Configuring slicing parameters, infill density, and running multi-axis print beds under continuous layer-by-layer supervision.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Post-Processing & Tolerance Inspection',
                            'description' => 'Support removal, chemical washing, UV curing, surface sanding, coating, and precision dimensional verification with digital calipers.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => '3D File Format',
                            'description' => 'Watertight manifold file in .STL, .STEP, .STP, or .OBJ format without open naked edges.',
                        ],
                        [
                            'title' => 'Material & Environment Needs',
                            'description' => 'Indoor aesthetic display, load-bearing mechanical part, or UV/heat-resistant outdoor functional component?',
                        ],
                        [
                            'title' => 'Tolerances & Surface Smoothness',
                            'description' => 'Target layer resolution (0.05mm - 0.2mm) and critical tolerance zones for mating interfaces.',
                        ],
                        [
                            'title' => 'Quantity & Delivery Timeline',
                            'description' => 'Total batch size per part and your expected delivery deadline.',
                        ],
                    ],
                ],
            ],
            'lam-do-custom' => [
                'vi' => [
                    'title' => 'Custom',
                    'listing_title' => 'Làm đồ custom',
                    'position' => 3,
                    'listing_order' => 3,
                    'subtitle' => 'Không chỉ khác biệt. Là của riêng bạn.',
                    'overview_description' => 'Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn.',
                    'description' => 'Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn.',
                    'page_description' => 'Cá nhân hoá phụ kiện, đồ trang trí hoặc phát triển một món đồ từ ý tưởng riêng. HH Maker kết hợp thiết kế và gia công để cân nhắc hình dáng, màu sắc, chi tiết chữ và cách sử dụng trong cùng một phương án.',
                    'listing_image' => '/images/services/service-custom-3d.png',
                    'tags' => ['Quà tặng cá nhân', 'Phụ kiện', 'Đồ trang trí'],
                    'prep_title' => 'BẠN CHUẨN BỊ',
                    'preparation' => 'Câu chuyện hoặc nhu cầu, mẫu tham khảo muốn cá nhân hoá.',
                    'comp_title' => 'CÙNG HOÀN THIỆN',
                    'completion' => 'Sản phẩm mang dấu ấn riêng, được phát triển theo nội dung đã trao đổi.',
                    'cta_text' => 'Liên hệ tư vấn làm đồ custom',
                    'hero' => [
                        'headline' => 'Làm đồ custom: Chế tác cá nhân hóa độc bản theo yêu cầu',
                        'description' => 'Không chỉ khác biệt. Là của riêng bạn. Chế tác các vật phẩm độc bản, quà tặng doanh nghiệp và phụ kiện theo phong cách riêng của bạn.',
                        'input_items' => ['Câu chuyện & ý tưởng riêng', 'Chữ/Logo cá nhân hóa', 'Kỷ vật hoặc mẫu có sẵn'],
                        'process_items' => ['Chất liệu & phối màu', 'Chi tiết khắc / gắn logo', 'Phong cách hoàn thiện'],
                        'image' => '/assets/images/services/service-lam-do-custom.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'Đồ custom dành cho ai?', 'shortTitle' => 'Đồ custom cho ai?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'Các bước chế tác độc bản', 'shortTitle' => 'Quy trình chế tác'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'Bạn chuẩn bị thông tin gì?', 'shortTitle' => 'Thông tin chuẩn bị'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Bàn giao & Bảo quản sản phẩm', 'shortTitle' => 'Bàn giao & bảo quản'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'Đồ custom dành cho ai?',
                            'paragraphs' => [
                                'Bạn muốn sở hữu một món đồ kỷ niệm độc nhất vô nhị, một món quà tặng doanh nghiệp khắc sâu dấu ấn thương hiệu, hay một phụ kiện cosplay/decor không thể tìm thấy ở bất kỳ cửa hàng nào trên thị trường?',
                                'Dịch vụ Làm đồ custom tại HH Maker kết hợp công nghệ tạo hình 3D tiên tiến và bàn tay chế tác thủ công tinh xảo, giúp hiện thực hóa những mong muốn mang đậm cá tính riêng của bạn thành những tạo tác hoàn mỹ.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'Các bước chế tác độc bản',
                            'intro' => 'Hành trình biến một ý tưởng cá nhân thành một tác phẩm nghệ thuật độc bản trải qua sự phối hợp tỉ mỉ giữa bạn và nghệ nhân HH Maker.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'Bạn chuẩn bị thông tin gì?',
                            'intro' => 'Để HH Maker nắm bắt trọn vẹn tinh thần món đồ bạn mong muốn chế tác, hãy chia sẻ cùng chúng tôi những gợi ý ban đầu sau:',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Bàn giao & Bảo quản sản phẩm',
                            'paragraph' => 'Sản phẩm custom là thành quả của sự sáng tạo cá nhân kết hợp kỹ thuật chế tác công phu. Mỗi sản phẩm được chế tác riêng theo từng đơn đặt hàng, đảm bảo tính độc bản và giá trị cảm xúc lâu dài.',
                            'callout' => 'Mẹo bảo quản: Để giữ màu sắc và bề mặt luôn sáng đẹp như mới, nên trưng bày sản phẩm ở nơi khô ráo, tránh ánh nắng mặt trời trực tiếp kéo dài và lau chùi nhẹ nhàng bằng khăn mềm khô.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'Lắng nghe câu chuyện & định hình ý tưởng',
                            'description' => 'Bạn chia sẻ mục đích, ý nghĩa kỷ niệm, phong cách mong muốn cùng những chi tiết cá nhân hóa như ngày tháng, tên riêng hoặc biểu tượng biểu trưng.',
                        ],
                        [
                            'number' => '02',
                            'title' => 'Phác thảo 3D & phối màu thử nghiệm',
                            'description' => 'Đội ngũ thiết kế tạo mẫu 3D và render phối cảnh màu sắc trực quan, giúp bạn hình dung sống động thành phẩm trước khi đưa vào sản xuất.',
                        ],
                        [
                            'number' => '03',
                            'title' => 'Gia công tạo hình & đắp thủ công',
                            'description' => 'Ứng dụng in 3D độ phân giải siêu nét kết hợp kỹ thuật xử lý bề mặt thủ công, dặm màu nghệ thuật, phủ bóng bảo vệ hoặc khắc laser theo yêu cầu.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Đóng gói sang trọng & bàn giao',
                            'description' => 'Mỗi sản phẩm hoàn thiện được kiểm tra từng góc cạnh, đóng hộp quà tặng cao cấp kèm hướng dẫn bảo quản và thẻ chứng nhận sản phẩm độc bản.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => 'Nội dung cá nhân hóa',
                            'description' => 'Tên, chữ ký, logo, câu trích dẫn hoặc ngày kỷ niệm cần khắc hoặc đúc nổi trên sản phẩm.',
                        ],
                        [
                            'title' => 'Kích thước & không gian trưng bày',
                            'description' => 'Kích thước tổng thể mong muốn (để bàn làm việc, treo tường hay mang theo bên người).'],
                        [
                            'title' => 'Tone màu & chất liệu chủ đạo',
                            'description' => 'Màu sắc yêu thích, phong cách mộc mạc tối giản, hiện đại công nghệ hay cổ điển kim loại giả cổ.',
                        ],
                        [
                            'title' => 'Ngân sách & thời điểm cần có',
                            'description' => 'Mức ngân sách dự kiến và ngày sự kiện bạn cần trao tặng món quà đặc biệt này.',
                        ],
                    ],
                ],
                'en' => [
                    'title' => 'Custom',
                    'listing_title' => 'Custom Bespoke Creations',
                    'position' => 3,
                    'listing_order' => 3,
                    'subtitle' => 'Not just different. Exclusively yours.',
                    'overview_description' => 'Custom design and manufacturing on demand, crafting unique pieces with your signature touch.',
                    'description' => 'Custom design and manufacturing on demand, crafting unique pieces with your signature touch.',
                    'page_description' => 'Personalizing accessories, home decor, or crafting custom items from scratch. HH Maker combines design and machining to balance form, color, typography, and ergonomics in a single unified solution.',
                    'listing_image' => '/images/services/service-custom-3d.png',
                    'tags' => ['Personal Gifts', 'Accessories', 'Spatial Decor'],
                    'prep_title' => 'YOU PREPARE',
                    'preparation' => 'Story or requirements, sample models you wish to personalize.',
                    'comp_title' => 'WE CO-CREATE',
                    'completion' => 'Unique signature product tailored to the discussed concept.',
                    'cta_text' => 'Consult Custom Creations',
                    'hero' => [
                        'headline' => 'Custom Bespoke Creations: Personalized one-of-a-kind fabrication',
                        'description' => 'Not just different. Exclusively yours. Bespoke keepsakes, personalized corporate gifts, and unique props crafted to reflect your distinct character.',
                        'input_items' => ['Unique Personal Story', 'Custom Names & Logos', 'Keepsakes or Existing Models'],
                        'process_items' => ['Materials & Color Palettes', 'Engraving & Badge Mounts', 'Bespoke Finishing Styles'],
                        'image' => '/assets/images/services/service-lam-do-custom.png',
                    ],
                    'toc_items' => [
                        ['id' => 'section-01', 'number' => '01', 'title' => 'Who is custom fabrication for?', 'shortTitle' => 'Who is it for?'],
                        ['id' => 'section-02', 'number' => '02', 'title' => 'Bespoke creation process', 'shortTitle' => 'Creation process'],
                        ['id' => 'section-03', 'number' => '03', 'title' => 'What information to prepare?', 'shortTitle' => 'Info to prepare'],
                        ['id' => 'section-04', 'number' => '04', 'title' => 'Delivery & product care', 'shortTitle' => 'Delivery & care'],
                    ],
                    'sections' => [
                        'section_01' => [
                            'id' => 'section-01',
                            'title' => 'Who is custom fabrication for?',
                            'paragraphs' => [
                                'Looking for a one-of-a-kind commemorative keepsake, a corporate gift with personalized branding, or a custom decor/cosplay prop that cannot be bought anywhere on the market?',
                                'HH Maker\'s Custom Bespoke Creations service merges modern 3D digital fabrication with meticulous handcrafting, turning your unique visions into distinctive, high-end tangible masterworks.',
                            ],
                        ],
                        'section_02' => [
                            'id' => 'section-02',
                            'title' => 'Bespoke creation process',
                            'intro' => 'The journey of turning a personal idea into a bespoke physical artifact involves close collaboration between you and HH Maker craftsmen.',
                        ],
                        'section_03' => [
                            'id' => 'section-03',
                            'title' => 'What information to prepare?',
                            'intro' => 'To help HH Maker capture the exact soul and vision of your bespoke piece, please share these initial details:',
                        ],
                        'section_04' => [
                            'id' => 'section-04',
                            'title' => 'Delivery & product care',
                            'paragraph' => 'A bespoke piece is the culmination of personal storytelling and meticulous hand craftsmanship. Every item is individually created to order, guaranteeing distinct uniqueness and lasting sentimental value.',
                            'callout' => 'Care tip: To keep colors and finishes looking pristine, display the product in a dry area away from extended direct sunlight, and gently clean with a soft microfiber cloth.',
                        ],
                    ],
                    'steps' => [
                        [
                            'number' => '01',
                            'title' => 'Story Discovery & Concept Definition',
                            'description' => 'Share your background story, milestone occasion, target aesthetic, and personal elements like engraved names, dates, or logos.',
                        ],
                        [
                            'number' => '02',
                            'title' => '3D Visual Mockups & Color Simulation',
                            'description' => 'Our team develops 3D models with photorealistic color rendering, letting you preview the tangible aesthetic before fabrication.',
                        ],
                        [
                            'number' => '03',
                            'title' => 'High-Resolution Fabrication & Hand-Finishing',
                            'description' => 'Combining high-detail 3D printing with hand sanding, artisan airbrush painting, protective clear coating, or precision laser engraving.',
                        ],
                        [
                            'number' => '04',
                            'title' => 'Luxury Packaging & Delivery',
                            'description' => 'Each bespoke piece undergoes strict final quality inspection, packed in high-end gift packaging with a care guide and certificate of uniqueness.',
                        ],
                    ],
                    'checklist_items' => [
                        [
                            'title' => 'Personalization Content',
                            'description' => 'Names, signatures, logos, quotes, or milestone dates to engrave or emboss.',
                        ],
                        [
                            'title' => 'Dimensions & Display Context',
                            'description' => 'Desired dimensions (desktop display, wall mounting, or wearable accessory).',
                        ],
                        [
                            'title' => 'Color Palette & Material Feel',
                            'description' => 'Preferred tones, minimalist industrial look, modern tech style, or antique patinated finish.',
                        ],
                        [
                            'title' => 'Budget & Event Date',
                            'description' => 'Your anticipated budget range and the event date for presenting this special piece.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getServiceKnowledgeBase(string $serviceKey, ?string $locale = null): array
    {
        $map = self::getKnowledgeBaseMap();
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $lang = $locale === 'en' ? 'en' : 'vi';

        if ($serviceKey === 'custom' || $serviceKey === 'do-custom') {
            $serviceKey = 'lam-do-custom';
        }

        $key = isset($map[$serviceKey]) ? $serviceKey : 'thiet-ke-3d';
        return $map[$key][$lang] ?? $map['thiet-ke-3d'][$lang];
    }

    public function resolveHeroData(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $serviceKey = $this->getServiceKey();
        $kb = self::getServiceKnowledgeBase($serviceKey, $locale);

        $decodeJson = static function ($value): ?array {
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : null;
            }
            return is_array($value) ? $value : null;
        };

        $raw = $decodeJson($this->content_by_tab);
        $heroInDb = is_array($raw) ? ($raw['hero'] ?? null) : null;

        $headline = !empty($this->title_content)
            ? $this->title_content
            : (!empty($heroInDb['headline']) ? $heroInDb['headline'] : $kb['hero']['headline']);

        $description = !empty($this->description)
            ? $this->description
            : (!empty($heroInDb['description']) ? $heroInDb['description'] : $kb['hero']['description']);

        $inputItems = !empty($heroInDb['input_items']) && is_array($heroInDb['input_items'])
            ? $heroInDb['input_items']
            : $kb['hero']['input_items'];

        $processItems = !empty($heroInDb['process_items']) && is_array($heroInDb['process_items'])
            ? $heroInDb['process_items']
            : $kb['hero']['process_items'];

        $image = null;
        if (!empty($this->benefit_image) && is_array($this->benefit_image) && !empty($this->benefit_image['path'])) {
            $image = $this->getImageDetail($this->benefit_image)['url'] ?? null;
        } elseif (!empty($this->image) && is_array($this->image) && !empty($this->image['path'])) {
            $image = $this->getImageDetail($this->image)['url'] ?? null;
        }

        if (!$image) {
            $image = $heroInDb['image'] ?? $kb['hero']['image'];
        }

        return [
            'headline' => $headline,
            'description' => $description,
            'input_items' => $inputItems,
            'inputItems' => $inputItems,
            'process_items' => $processItems,
            'processItems' => $processItems,
            'image' => $image,
        ];
    }

    public function resolveTocItems(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $serviceKey = $this->getServiceKey();
        $kb = self::getServiceKnowledgeBase($serviceKey, $locale);

        $decodeJson = static function ($value): ?array {
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : null;
            }
            return is_array($value) ? $value : null;
        };

        $raw = $decodeJson($this->content_by_tab);
        if (is_array($raw) && !empty($raw['toc_items']) && is_array($raw['toc_items'])) {
            return $raw['toc_items'];
        }

        return $kb['toc_items'] ?? self::getServiceKnowledgeBase('thiet-ke-3d', $locale)['toc_items'];
    }

    public function resolveArticleSections(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $serviceKey = $this->getServiceKey();
        $kb = self::getServiceKnowledgeBase($serviceKey, $locale);

        $decodeJson = static function ($value): ?array {
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : null;
            }
            return is_array($value) ? $value : null;
        };

        $raw = $decodeJson($this->content_by_tab);
        if (is_array($raw) && !empty($raw['sections']) && is_array($raw['sections'])) {
            return $raw['sections'];
        }

        return $kb['sections'] ?? self::getServiceKnowledgeBase('thiet-ke-3d', $locale)['sections'];
    }

    public function transformDetails()
    {
        $hero = $this->resolveHeroData();
        $tocItems = $this->resolveTocItems();
        $sections = $this->resolveArticleSections();
        $steps = $this->resolveSteps();
        $checklistItems = $this->resolveChecklistItems();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'title_content' => $this->title_content,
            'is_content_by_tab' => $this->is_content_by_tab,
            'content_by_tab' => $this->content_by_tab,
            'benefits' => $this->benefits,
            'benefit_title' => $this->benefit_title,
            'image' => $this->getImageDetail($this->image),
            'benefit_image' => $this->getImageDetail($this->benefit_image),
            'sliders' => collect($this->sliders)->map(fn($item) => $this->getImageDetail($item))->toArray(),
            'hero' => $hero,
            'toc_items' => $tocItems,
            'tocItems' => $tocItems,
            'sections' => $sections,
            'steps' => $steps,
            'checklist_items' => $checklistItems,
            'checklistItems' => $checklistItems,
        ];
    }

    public function resolveSteps(): array
    {
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $isEn = $locale === 'en';

        $decodeJson = static function ($value): ?array {
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : null;
            }
            return is_array($value) ? $value : null;
        };

        $raw = $decodeJson($this->content_by_tab);
        $viRaw = method_exists($this, 'translate')
            ? $decodeJson($this->translate('vi', false)?->content_by_tab)
            : null;

        $candidate = is_array($raw) && !empty($raw)
            ? ($raw['steps'] ?? (array_is_list($raw) ? $raw : null))
            : null;
        $viCandidate = is_array($viRaw) && !empty($viRaw)
            ? ($viRaw['steps'] ?? (array_is_list($viRaw) ? $viRaw : null))
            : null;

        if ((!is_array($candidate) || empty($candidate)) && is_array($viCandidate) && !empty($viCandidate)) {
            $candidate = $viCandidate;
        }

        if (is_array($candidate) && !empty($candidate)) {
            $mapped = [];
            $viList = is_array($viCandidate) ? array_values($viCandidate) : [];
            foreach (array_values($candidate) as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }
                $viItem = is_array($viList[$index] ?? null) ? $viList[$index] : [];
                $title = trim((string) (($isEn ? ($item['title_en'] ?? null) : null) ?? $item['title'] ?? ($viItem['title'] ?? '')));
                $desc = trim((string) (($isEn ? ($item['description_en'] ?? $item['content_en'] ?? null) : null) ?? $item['description'] ?? $item['content'] ?? ($viItem['description'] ?? $viItem['content'] ?? '')));
                if ($title === '' && $desc === '') {
                    continue;
                }
                $mapped[] = [
                    'number' => $item['number'] ?? ($viItem['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)),
                    'title' => $title,
                    'description' => $desc,
                ];
            }
            if (!empty($mapped)) {
                return $mapped;
            }
        }

        return [];
    }

    public function resolveChecklistItems(): array
    {
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $isEn = $locale === 'en';

        $decodeJson = static function ($value): ?array {
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : null;
            }
            return is_array($value) ? $value : null;
        };

        $raw = $decodeJson($this->benefits);
        $viRaw = method_exists($this, 'translate')
            ? $decodeJson($this->translate('vi', false)?->benefits)
            : null;

        $candidate = is_array($raw) && !empty($raw)
            ? ($raw['checklist_items'] ?? $raw['checklistItems'] ?? (array_is_list($raw) ? $raw : null))
            : null;
        $viCandidate = is_array($viRaw) && !empty($viRaw)
            ? ($viRaw['checklist_items'] ?? $viRaw['checklistItems'] ?? (array_is_list($viRaw) ? $viRaw : null))
            : null;

        if ((!is_array($candidate) || empty($candidate)) && is_array($viCandidate) && !empty($viCandidate)) {
            $candidate = $viCandidate;
        }

        if (is_array($candidate) && !empty($candidate)) {
            $mapped = [];
            $viList = is_array($viCandidate) ? array_values($viCandidate) : [];
            foreach (array_values($candidate) as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }
                $viItem = is_array($viList[$index] ?? null) ? $viList[$index] : [];
                $title = trim((string) (($isEn ? ($item['title_en'] ?? null) : null) ?? $item['title'] ?? ($viItem['title'] ?? '')));
                $desc = trim((string) (($isEn ? ($item['description_en'] ?? $item['content_en'] ?? null) : null) ?? $item['description'] ?? $item['content'] ?? ($viItem['description'] ?? $viItem['content'] ?? '')));
                if ($title === '' && $desc === '') {
                    continue;
                }
                $mapped[] = [
                    'title' => $title,
                    'description' => $desc,
                ];
            }
            if (!empty($mapped)) {
                return $mapped;
            }
        }

        return [];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function getImageDetail($image)
    {
        if (is_string($image)) {
            $image = ['path' => $image];
        }

        if (empty($image) || !isset($image['path']) || trim((string) $image['path']) === '') {
            return [
                'url' => null,
                'alt' => $image['alt'] ?? $this->title,
            ];
        }

        $path = trim((string) $image['path']);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path;
        } elseif (
            str_starts_with($path, '/assets/') ||
            str_starts_with($path, '/images/') ||
            str_starts_with($path, 'assets/') ||
            str_starts_with($path, 'images/') ||
            (function_exists('public_path') && file_exists(public_path(ltrim($path, '/'))))
        ) {
            $url = '/' . ltrim($path, '/');
        } else {
            $url = static_url($path);
        }

        return [
            'url' => $url,
            'path' => $path,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }

    public function getImageUrlAttribute()
    {
        $detail = $this->getImageDetail($this->image);
        return $detail['url'] ?? null;
    }
}
