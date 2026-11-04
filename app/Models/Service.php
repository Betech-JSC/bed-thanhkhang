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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'link' => $this->content,
            'image' => $this->getImageDetail($this->image),
        ];
    }

    public function transformDetails()
    {
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
            'steps' => $steps,
            'checklist_items' => $checklistItems,
            'checklistItems' => $checklistItems,
        ];
    }

    public static function getDefaultSteps(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());

        if ($locale === 'en') {
            return [
                [
                    'number' => '01',
                    'title' => 'Discuss Ideas and Intended Use',
                    'description' => 'Share the product you want to create, reference images, and what matters most to you. HH Maker works with you to define functionality, target dimensions, and priority details.',
                ],
                [
                    'number' => '02',
                    'title' => 'Block Out 3D Form and Proportions',
                    'description' => 'Based on our discussion, your concept is translated into a 3D form. At this stage, focus on overall silhouette, dimensional proportions, and part placement before diving into fine details.',
                ],
                [
                    'number' => '03',
                    'title' => 'Refine Structure and Fine Details',
                    'description' => 'Review wall thickness, surface texture, assembly joints, or embossed text together. Small details are balanced against actual physical dimensions and fabrication methods rather than just looking good on screen.',
                ],
                [
                    'number' => '04',
                    'title' => 'Finalize Plan Before Fabrication',
                    'description' => 'Both sides review the 3D model, dimensions, and adjusted details. When moving to fabrication, materials, surface finish, and assembly requirements are aligned to choose the best production method.',
                ],
            ];
        }

        return [
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
        ];
    }

    public static function getDefaultChecklistItems(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());

        if ($locale === 'en') {
            return [
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
            ];
        }

        return [
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

        return self::getDefaultSteps($locale);
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

        return self::getDefaultChecklistItems($locale);
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function getImageDetail($image)
    {
        return [
            'url' => isset($image['path']) ? static_url($image['path']) : null,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }

    public function getImageUrlAttribute()
    {
        return isset($this->image['path']) ? static_url($this->image['path']) : null;
    }
}
