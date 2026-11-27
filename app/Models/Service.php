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

        $title = trim((string) ($this->title ?? '')) ?: ($kb['listing_title'] ?? $kb['title'] ?? '');
        $subtitle = trim((string) ($this->title_content ?? '')) ?: ($kb['subtitle'] ?? '');
        $description = trim((string) ($this->description ?? '')) ?: ($kb['page_description'] ?? $kb['description'] ?? '');
        $imageDetail = $this->getImageDetail($this->image);
        $slug = $this->seo_slug ?? $this->slug ?: $serviceKey;

        return [
            'id' => $this->id,
            'title' => $title,
            'home_title' => $title,
            'slug' => $slug,
            'subtitle' => $subtitle,
            'description' => $description,
            'overview_description' => $description,
            'link' => $this->content ?: ('/dich-vu/' . $slug),
            'image' => $imageDetail,
            'tags' => $kb['tags'] ?? [],
            'prep_title' => $kb['prep_title'] ?? ($isEn ? 'YOU PREPARE' : 'BẠN CHUẨN BỊ'),
            'preparation' => $kb['preparation'] ?? '',
            'comp_title' => $kb['comp_title'] ?? ($isEn ? 'WE DELIVER' : 'THÀNH KHANG TRIỂN KHAI'),
            'completion' => $kb['completion'] ?? '',
            'cta_text' => $kb['cta_text'] ?? ($isEn ? 'Contact Technical Team' : 'Liên hệ tư vấn kỹ thuật'),
            'position' => $this->position ?? ($kb['position'] ?? 1),
            'listing_order' => $this->position ?? ($kb['listing_order'] ?? 1),
        ];
    }

    public function getServiceKey(): string
    {
        $slug = trim((string) ($this->slug ?? $this->seo_slug ?? ''));
        return $slug !== '' ? $slug : 'industrial-engineering';
    }

    public static function getKnowledgeBaseMap(): array
    {
        return [
            'industrial-engineering' => [
                'vi' => [
                    'title' => 'Dịch vụ kỹ thuật công nghiệp',
                    'listing_title' => 'Dịch vụ kỹ thuật công nghiệp',
                    'position' => 1,
                    'listing_order' => 1,
                    'subtitle' => 'Khảo sát, cung ứng phụ tùng & giải pháp kỹ thuật B2B.',
                    'overview_description' => 'Khảo sát hiện trường, đối chiếu thông số kỹ thuật và cung ứng linh kiện cơ khí chính hãng cho thang cuốn, băng tải và hệ thống công nghiệp.',
                    'description' => 'Khảo sát hiện trường, đối chiếu thông số kỹ thuật và cung ứng linh kiện cơ khí chính hãng cho thang cuốn, băng tải và hệ thống công nghiệp.',
                    'page_description' => 'Đội ngũ kỹ sư Thành Khang hỗ trợ khảo sát thực tế, tra cứu mã phụ tùng và đề xuất phương án cung ứng tối ưu cho công trình và nhà máy.',
                    'tags' => ['Khảo sát kỹ thuật', 'Cung ứng CO/CQ', 'Bảo trì B2B'],
                    'prep_title' => 'BẠN CHUẨN BỊ',
                    'preparation' => 'Mã phụ tùng, bản vẽ kỹ thuật hoặc thông số vận hành thiết bị.',
                    'comp_title' => 'THÀNH KHANG TRIỂN KHAI',
                    'completion' => 'Hồ sơ đối chiếu thông số, báo giá RFQ và kế hoạch cung ứng.',
                    'cta_text' => 'Liên hệ tư vấn kỹ thuật',
                    'hero' => [
                        'headline' => 'Giải pháp kỹ thuật & Cung ứng phụ tùng công nghiệp B2B',
                        'description' => 'Đồng hành cùng ban quản lý tòa nhà, nhà thầu M&E và nhà máy sản xuất với giải pháp phụ tùng thang cuốn, xích tải công nghiệp và tiết kiệm điện năng.',
                        'input_items' => ['Mã phụ tùng / Bản vẽ', 'Thông số tải trọng & bước xích', 'Tiến độ công trình'],
                        'process_items' => ['Khảo sát & đối chiếu', 'Đề xuất giải pháp & RFQ', 'Cung ứng & nghiệm thu CO/CQ'],
                        'image' => null,
                    ],
                    'toc_items' => [],
                    'sections' => [],
                ],
                'en' => [
                    'title' => 'Industrial Technical Services',
                    'listing_title' => 'Industrial Technical Services',
                    'position' => 1,
                    'listing_order' => 1,
                    'subtitle' => 'Site survey, precision spare parts supply & B2B engineering.',
                    'overview_description' => 'On-site technical inspection, specification verification, and genuine industrial spare parts supply for escalators, conveyors, and plant systems.',
                    'description' => 'On-site technical inspection, specification verification, and genuine industrial spare parts supply for escalators, conveyors, and plant systems.',
                    'page_description' => 'Thanh Khang engineering team assists with site surveys, part number cross-referencing, and optimal B2B procurement planning.',
                    'tags' => ['Technical Survey', 'CO/CQ Supply', 'B2B Maintenance'],
                    'prep_title' => 'YOU PREPARE',
                    'preparation' => 'Part numbers, engineering drawings, or operating equipment parameters.',
                    'comp_title' => 'WE DELIVER',
                    'completion' => 'Verified technical match, RFQ quotation, and supply schedule.',
                    'cta_text' => 'Contact Technical Team',
                    'hero' => [
                        'headline' => 'B2B Industrial Engineering & Spare Parts Supply',
                        'description' => 'Supporting facility managers, M&E contractors, and manufacturing plants with escalator parts, heavy conveyor chains, and energy-saving solutions.',
                        'input_items' => ['Part Numbers / Drawings', 'Pitch & Load Specifications', 'Project Schedule'],
                        'process_items' => ['Survey & Cross-Reference', 'Technical Proposal & RFQ', 'Delivery & CO/CQ Handover'],
                        'image' => null,
                    ],
                    'toc_items' => [],
                    'sections' => [],
                ],
            ],
        ];
    }

    public static function getServiceKnowledgeBase(string $serviceKey, ?string $locale = null): array
    {
        $map = self::getKnowledgeBaseMap();
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $lang = $locale === 'en' ? 'en' : 'vi';

        $key = isset($map[$serviceKey]) ? $serviceKey : 'industrial-engineering';
        return $map[$key][$lang] ?? $map['industrial-engineering'][$lang];
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

        return $kb['toc_items'] ?? [];
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

        return $kb['sections'] ?? [];
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
