<?php

namespace App\Models\Product;

use App\Models\Brand\Brand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Traits\Searchable;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use JamstackVietnam\Option\Models\Option;
use App\Models\Post\Post;

class Product extends BaseModel
{
    use HasFactory, Translatable, SoftDeletes, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = [
        'translations',
        'categories',
        'brand',
    ];

    public $fillable = [
        'brand_id',
        'status',
        'is_featured',
        'is_new',
        'is_stock',
        'view_count',
        'images',
        'image',
        'video_url',
        'sku',
        'price',
        'compare_at_price',
        'print_technology',
        'material',
        'lead_time',
        'sample_size',
        'layer_height',
        'nozzle_size',
        'infill',
        'support_material',
        'image_color_label',
        'customization_label',
        'specifications',
        'pdp_metadata',

        'inject_head',
        'inject_body_start',
        'inject_body_end',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'content',
        'description',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
    ];

    protected $searchable = [
        'columns' => [
            'product_translations.title' => 10,
            'product_translations.slug' => 10,
            'product_translations.id' => 1,
            'products.sku' => 1
        ],
        'joins' => [
            'product_translations' => ['product_translations.product_id', 'products.id'],
        ]
    ];

    public $appends = ['url'];

    protected $casts = [
        'images' => 'array',
        'image' => 'array',
        'specifications' => 'array',
        'pdp_metadata' => 'array',
    ];

    public function rules(): array
    {
        return [
            'store' => [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255',
                'seo_slug' => 'nullable|string|max:255',
                'sku' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:ACTIVE,INACTIVE',
            ],
        ];
    }

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (strpos(request()->getRequestUri(), '/products/store') !== false) {
                $model->view_count = request()->input('view_count') ?? 0;
                $model->checkStatus($model);
            }

            $model->syncSpecificationsAndMetadata();
        });

        static::saved(function (self $model) {
            if (strpos(request()->getRequestUri(), '/products/store') !== false || request()->has('categories') || request()->has('category_id')) {
                $model->saveCategories($model);
            }

            static::clearProductCaches($model);
        });

        static::deleted(function (self $model) {
            static::clearProductCaches($model);
        });
    }

    public static function clearProductCaches(?self $model = null): void
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
            \Illuminate\Support\Facades\Cache::forget('products_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('galleries_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('inertia_layout_shared_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('search_page_data_' . $locale . '_');

            foreach ($slugs as $slug) {
                \Illuminate\Support\Facades\Cache::forget('product_show_' . $slug . '_' . $locale);
            }
        }
    }

    private function checkStatus($model)
    {
        $categories = array_column(request()->input('categories', []), 'id');
        $hasCategories = ProductCategory::active()->whereIn('id', $categories)->exists();
    }

    public function saveRelatedProducts($model)
    {
        $relatedProducts = array_column(request()->input('related_products', []), 'id');
        $model->relatedProducts()->sync($relatedProducts, 'id');
    }

    public function savePosts($model)
    {
        $posts = array_column(request()->input('posts', []), 'id');
        $model->posts()->sync($posts, 'id');
    }

    public function saveTags($model)
    {
        $tags = array_column(request()->input('tags', []), 'id');
        $model->tags()->sync($tags, 'id');
    }

    public function saveCategories($model)
    {
        $rawCategories = request()->input('categories', []);
        $categories = [];
        if (is_array($rawCategories)) {
            foreach ($rawCategories as $cat) {
                if (is_array($cat) && isset($cat['id'])) {
                    $categories[] = (int) $cat['id'];
                } elseif (is_numeric($cat)) {
                    $categories[] = (int) $cat;
                }
            }
        }
        if (empty($categories) && request()->filled('category_id')) {
            $categories[] = (int) request()->input('category_id');
        }
        $model->categories()->sync(array_values(array_unique($categories)), 'id');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            self::class,
            'related_products',
            'product_id',
            'related_product_id'
        )
            ->withPivot('id')
            ->orderBy('related_products.id', 'ASC');
    }

    public function scopeActiveLocale($query)
    {
        $locale = current_locale();
        $query =  $query->whereHas('translations', function ($q) use ($locale) {
            $q->where('status_locale', Product::STATUS_ACTIVE)->where('locale', '=', $locale);
        });
        return $query;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_ref_categories',
            'product_id',
            'product_category_id'
        );
    }

    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            'product_ref_posts',
            'product_id',
            'post_id'
        );
    }

    public static function getRootCategory()
    {
        if (request()->has('categoryId')) {
            return static::active()
                ->whereHas('categories', function ($query) {
                    $query->where('id', request()->input('categoryId'));
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => (string) $item['id'],
                        'label' => $item['title']
                    ];
                });
        }

        return static::active()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (string) $item['id'],
                    'label' => $item['title']
                ];
            });
    }

    public function getProducts()
    {
        $data = Product::where('products.status', self::STATUS_ACTIVE)
            ->get();

        return $data;
    }

    public function getCategoryAttribute()
    {
        return $this->categories
            ->values()
            ->first()
            ?->transform();
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC')
            ->orderBy('created_at', 'desc');
    }

    public function getCategoryIdsAttribute()
    {
        return $this->categories->pluck('id');
    }

    public function variantOptions()
    {
        return $this->options()->where('options.parent_id', '!=', 0);
    }

    public function options()
    {
        return $this->belongsToMany(
            Option::class,
            'product_variant_ref_options',
            'product_id',
            'option_id',
        )
            ->withPivot('position', 'is_required', 'product_variant_id')
            ->orderBy('is_required', 'DESC')
            ->orderBy('position', 'DESC');
    }

    public function productOptions()
    {
        return $this->options()->where('options.parent_id', 0);
    }

    public function childrenOptions()
    {
        return $this->options()->where('options.parent_id', '!=', 0);
    }

    public function getListOptionCoverAttribute()
    {
        $listOptions = Option::transformNested($this->productOptions, $this->childrenOptions);

        $listOptions = $listOptions->map(function ($item) {
            $option = $this->productOptions
                ->where('id', $item['id'])
                ->first();

            $item['id'] = strval($item['id']);
            $item['is_required'] = $option['pivot']['is_required'] ?? false;
            $item['position'] = $option['pivot']['position'] ?? 0;
            $item['is_show_detail'] = $option['is_show_detail'] ?? 0;
            $item['nodes'] = collect($item['nodes'])->map(function ($value) {
                $value['id'] = strval($value['id']);
                return $value;
            });

            return $item;
        });

        return $listOptions;
    }

    public function getListOptionAttribute()
    {
        $childrenOptions = $this->childrenOptions;
        return Option::transformNested($this->productOptions, $childrenOptions);
    }

    public static function lazySearch($data)
    {
        if ($data['keyword'] && !is_array($data['keyword'])) {
            $results = static::query()->search($data['keyword'])->limit(10)->get();
            if ($results->count() > 0) {
                return $results->merge(static::query()->whereNotIn('id', $results->pluck('id'))->get());
            }
        }

        return static::query()->get();
    }

    public function getImageDefaultAttribute()
    {
        $variant = $this->variants->where('is_default')->first();

        if (!empty($variant->id) && count($variant->images) > 0) {
            $img = $variant->images[0];
            $detail = $this->getImageDetail($img);
            return [
                'url' => $detail['url'],
                'path' => $img['path'] ?? null,
                'alt' => $detail['alt'],
            ];
        }

        if ($this->images && count($this->images) > 0) {
            $img = $this->images[0];
            $detail = $this->getImageDetail($img);
            return [
                'url' => $detail['url'],
                'path' => $img['path'] ?? null,
                'alt' => $detail['alt'],
            ];
        }

        return null;
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        if ($this->is_active) {
            foreach ($this->translations as $translation) {
                $slug = $translation->seo_slug ?? $translation->slug;

                $urls[strtoupper($translation->locale)] = route("$translation->locale.products.show", [
                    'slug' => $slug,
                ]);
            }
        }
        return $urls;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getBreadcrumbsAttribute()
    {
        $category = $this->categories
            ->where('status', ProductCategory::STATUS_ACTIVE)
            ->sortBy([['parent_id', 'desc']])
            ->values()
            ->first();

        return ProductCategory::transformAsBreadcrumb($category);
    }

    public function getImageDetail($image)
    {
        if (is_string($image)) {
            $image = ['path' => $image];
        }

        if (empty($image) || !isset($image['path'])) {
            return [
                'url' => null,
                'alt' => $image['alt'] ?? $this->title,
            ];
        }

        $path = $image['path'];
        $url = null;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $url = $path;
        } elseif (
            str_starts_with($path, '/assets/') ||
            str_starts_with($path, '/images/') ||
            str_starts_with($path, 'assets/') ||
            str_starts_with($path, 'images/') ||
            (function_exists('public_path') && file_exists(public_path(ltrim($path, '/'))))
        ) {
            $url = asset(ltrim($path, '/'));
        } else {
            $url = static_url($path);
        }

        return [
            'url' => $url,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }

    public function transform()
    {
        $categories = $this->categories
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'category' => $this->category,
            'categories' => $categories,
            'image' => $this->getImageDetail($this->image),
            'sku' => $this->sku,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'print_technology' => $this->print_technology,
            'material' => $this->material,
            'lead_time' => $this->lead_time,
            'sample_size' => $this->sample_size,
            'image_color_label' => $this->image_color_label,
            'customization_label' => $this->customization_label,
            'description' => $this->description,
            'is_new' => $this->is_new,
            'is_stock' => $this->is_stock,
            'is_featured' => (bool) $this->is_featured,
            'url' => $this->current_url ?: (is_array($this->url) ? ($this->url[strtoupper(current_locale())] ?? (reset($this->url) ?: null)) : $this->url),
        ];

        return $data;
    }

    public function transformCategory($conditions = ['options' => []])
    {
        $options = $this->optionDetails($conditions['options']);

        return array_merge($options, [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'slug' => $this->seo_slug ?? $this->slug,
            'sku' => $this->sku
        ]);
    }

    public function optionDetails($list = [])
    {
        $options = [];

        foreach ($list as $optionTitle) {
            $option = $this->list_option->filter(function ($item) use ($optionTitle) {
                return  $item['title'] === $optionTitle;
            })->first();

            if ($option) {
                $options[$optionTitle] = $option['nodes']->pluck('title')->join(', ');
            } else {
                $options[$optionTitle] = null;
            }
        }
        return $options;
    }

    public function transformDetails()
    {
        $images = $variant['images'] ?? collect($this->images)
            ->map(function ($item) {
                $detail = $this->getImageDetail($item);
                if (is_array($item)) {
                    return array_merge($item, [
                        'url' => $detail['url'],
                        'alt' => $detail['alt'],
                    ]);
                }
                return $detail;
            });

        $specifications = $this->resolveSpecifications();
        $pdpMeta = $this->resolvePdpMetadata($specifications);

        $categories = $this->categories
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => transform_richtext($this->content),
            'slug' => $this->seo_slug ?? $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'print_technology' => $this->print_technology,
            'material' => $this->material,
            'lead_time' => $this->lead_time,
            'sample_size' => $pdpMeta['sample_size'],
            'layer_height' => $pdpMeta['layer_height'],
            'nozzle_size' => $pdpMeta['nozzle_size'],
            'infill' => $pdpMeta['infill'],
            'support_material' => $pdpMeta['support_material'],
            'color' => $pdpMeta['image_color_label'],
            'image_color_label' => $pdpMeta['image_color_label'],
            'customization' => $pdpMeta['customization_label'],
            'customization_label' => $pdpMeta['customization_label'],
            'story_title' => $pdpMeta['story_title'],
            'story_intro' => $pdpMeta['story_intro'],
            'feature1_title' => $pdpMeta['feature1_title'],
            'feature1_desc' => $pdpMeta['feature1_desc'],
            'feature2_title' => $pdpMeta['feature2_title'],
            'feature2_desc' => $pdpMeta['feature2_desc'],
            'manufacturing_info' => $pdpMeta['manufacturing_info'],
            'care_instructions' => $pdpMeta['care_instructions'],
            'image' => $this->getImageDetail($this->image),
            'images' => $images,
            'category' => $this->category,
            'categories' => $categories,
            'specifications' => $specifications,
            'filter_attributes' => [
                'print_technology' => $this->print_technology,
                'material' => $this->material,
                'lead_time' => $this->lead_time,
            ],
            'pdp_metadata' => $pdpMeta,
        ];
    }

    public function syncSpecificationsAndMetadata(): void
    {
        $isCmsSave = strpos(request()->getRequestUri(), '/products/store') !== false;

        $rawSpecs = $this->attributes['specifications'] ?? null;
        if ($isCmsSave && request()->has('specifications')) {
            $reqSpecs = request()->input('specifications');
            if (is_array($reqSpecs) || is_string($reqSpecs)) {
                $rawSpecs = $reqSpecs;
            }
        }
        $storedSpecs = is_array($rawSpecs) ? $rawSpecs : (is_string($rawSpecs) ? json_decode($rawSpecs, true) : []);
        if (!is_array($storedSpecs)) {
            $storedSpecs = [];
        }

        $normalizedSpecs = [];
        foreach ($storedSpecs as $row) {
            if (is_array($row) && isset($row['label']) && trim((string) $row['label']) !== '') {
                $normalizedSpecs[] = [
                    'label' => trim((string) $row['label']),
                    'value' => isset($row['value']) ? trim((string) $row['value']) : '',
                ];
            }
        }

        if (empty($normalizedSpecs)) {
            $normalizedSpecs = static::getDefaultSpecificationsFor($this);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'specifications')) {
            $this->attributes['specifications'] = json_encode($normalizedSpecs, JSON_UNESCAPED_UNICODE);
        } else {
            unset($this->attributes['specifications']);
        }

        $defaultMeta = static::getDefaultPdpMetadataFor($this);
        $rawMeta = $this->attributes['pdp_metadata'] ?? null;
        $storedMeta = is_array($rawMeta) ? $rawMeta : (is_string($rawMeta) ? json_decode($rawMeta, true) : []);
        if (!is_array($storedMeta)) {
            $storedMeta = [];
        }

        if ($isCmsSave && request()->has('pdp_metadata') && is_array(request()->input('pdp_metadata'))) {
            $storedMeta = array_merge($storedMeta, request()->input('pdp_metadata'));
        }

        $syncedMeta = array_merge($defaultMeta, array_filter($storedMeta, fn($v) => $v !== null && $v !== ''));
        if (!empty($this->image_color_label)) {
            $syncedMeta['image_color_label'] = $this->image_color_label;
        }
        if (!empty($this->customization_label)) {
            $syncedMeta['customization_label'] = $this->customization_label;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'pdp_metadata')) {
            $this->attributes['pdp_metadata'] = json_encode($syncedMeta, JSON_UNESCAPED_UNICODE);
        } else {
            unset($this->attributes['pdp_metadata']);
        }

        foreach (['sample_size', 'layer_height', 'nozzle_size', 'infill', 'support_material', 'image_color_label', 'customization_label'] as $col) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('products', $col)) {
                unset($this->attributes[$col]);
            }
        }
    }

    public function getSpecificationsAttribute()
    {
        return $this->resolveSpecifications();
    }

    public function resolveSpecifications(): array
    {
        $raw = $this->attributes['specifications'] ?? null;
        $stored = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);

        if (is_array($stored) && !empty($stored)) {
            $normalized = [];
            foreach ($stored as $row) {
                if (is_array($row) && isset($row['label']) && trim((string) $row['label']) !== '') {
                    $normalized[] = [
                        'label' => trim((string) $row['label']),
                        'value' => isset($row['value']) ? trim((string) $row['value']) : '',
                    ];
                }
            }
            if (!empty($normalized)) {
                return $normalized;
            }
        }

        return static::getDefaultSpecificationsFor($this);
    }

    public function resolvePdpMetadata(?array $specifications = null): array
    {
        $specifications = $specifications ?? $this->resolveSpecifications();
        $defaults = static::getDefaultPdpMetadataFor($this);

        $rawMeta = $this->attributes['pdp_metadata'] ?? null;
        $storedMeta = is_array($rawMeta) ? $rawMeta : (is_string($rawMeta) ? json_decode($rawMeta, true) : []);
        if (!is_array($storedMeta)) {
            $storedMeta = [];
        }

        $merged = array_merge($defaults, array_filter($storedMeta, fn($v) => $v !== null && $v !== ''));

        $colorLabel = !empty($this->image_color_label)
            ? $this->image_color_label
            : ($merged['image_color_label'] ?? $defaults['image_color_label']);

        $customLabel = !empty($this->customization_label)
            ? $this->customization_label
            : ($merged['customization_label'] ?? $defaults['customization_label']);

        return [
            'print_technology' => $this->print_technology ?: ($merged['print_technology'] ?? ''),
            'material' => $this->material ?: ($merged['material'] ?? ''),
            'lead_time' => $this->lead_time ?: ($merged['lead_time'] ?? ''),
            'sample_size' => $this->sample_size ?: ($merged['sample_size'] ?? ''),
            'layer_height' => $this->layer_height ?: ($merged['layer_height'] ?? ''),
            'nozzle_size' => $this->nozzle_size ?: ($merged['nozzle_size'] ?? ''),
            'infill' => $this->infill ?: ($merged['infill'] ?? ''),
            'support_material' => $this->support_material ?: ($merged['support_material'] ?? ''),
            'color' => $colorLabel,
            'image_color_label' => $colorLabel,
            'customization' => $customLabel,
            'customization_label' => $customLabel,
            'story_title' => $merged['story_title'] ?? $defaults['story_title'],
            'story_intro' => $merged['story_intro'] ?? $defaults['story_intro'],
            'feature1_title' => $merged['feature1_title'] ?? $defaults['feature1_title'],
            'feature1_desc' => $merged['feature1_desc'] ?? $defaults['feature1_desc'],
            'feature2_title' => $merged['feature2_title'] ?? $defaults['feature2_title'],
            'feature2_desc' => $merged['feature2_desc'] ?? $defaults['feature2_desc'],
            'specifications' => $specifications,
            'manufacturing_info' => $merged['manufacturing_info'] ?? $defaults['manufacturing_info'],
            'care_instructions' => $merged['care_instructions'] ?? $defaults['care_instructions'],
        ];
    }

    public static function getDefaultPdpMetadataFor(self $product): array
    {
        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));

        if ($isEn) {
            return [
                'image_color_label' => 'Industrial Standard',
                'customization_label' => 'Part No. · Specs · Quantity',
                'story_title' => $product->title ?: 'Precision Industrial Component',
                'story_intro' => $product->description ?: 'Genuine Universal Chains industrial component distributed by Thanh Khang Tecsat with full CO/CQ certification.',
                'feature1_title' => 'Structure & Metallurgy',
                'feature1_desc' => 'Manufactured from high-tensile alloy and carbon steel with precision heat treatment for heavy-duty continuous operation.',
                'feature2_title' => 'Standards & Maintenance',
                'feature2_desc' => 'Engineered to ISO / JB/T / DIN / ANSI industrial standards. Periodic lubrication and pitch elongation inspection are recommended.',
                'manufacturing_info' => [
                    [
                        'title' => 'Genuine Industrial Supply',
                        'desc' => 'Supplied with complete Certificate of Origin (CO) and Certificate of Quality (CQ) for B2B projects.',
                    ],
                    [
                        'title' => 'Precision Quality Control',
                        'desc' => 'Inspected for dimensional pitch accuracy, tensile proof load, and surface hardness prior to dispatch.',
                    ],
                    [
                        'title' => 'Technical Fitment Support',
                        'desc' => 'Thanh Khang Tecsat engineering team verifies sprocket compatibility and operating clearances.',
                    ],
                ],
                'care_instructions' => [
                    'Inspect chain elongation and roller wear periodically according to equipment operating hours.',
                    'Use manufacturer-recommended industrial lubricants suited to ambient temperature and dust conditions.',
                    'Replace drive and driven chain pairs simultaneously on escalators and vertical lifts to maintain synchronization.',
                ],
            ];
        }

        return [
            'image_color_label' => 'Tiêu chuẩn công nghiệp',
            'customization_label' => 'Mã phụ tùng · Quy cách · Số lượng',
            'story_title' => $product->title ?: 'Linh kiện cơ khí công nghiệp chính hãng',
            'story_intro' => $product->description ?: 'Linh kiện cơ khí công nghiệp chính hãng do Thành Khang Tecsat phân phối, đáp ứng tiêu chuẩn vận hành liên tục và đầy đủ hồ sơ CO/CQ.',
            'feature1_title' => 'Cấu tạo & Vật liệu',
            'feature1_desc' => 'Chế tạo từ thép hợp kim và thép carbon cường độ cao, nhiệt luyện chính xác giúp chống mài mòn và chịu tải trọng kéo lớn.',
            'feature2_title' => 'Tiêu chuẩn & Bảo trì',
            'feature2_desc' => 'Tuân thủ các tiêu chuẩn công nghiệp quốc tế (ISO, JB/T, DIN, ANSI). Khuyến nghị kiểm tra độ giãn bước và bôi trơn định kỳ.',
            'manufacturing_info' => [
                [
                    'title' => 'Phân phối chính hãng B2B',
                    'desc' => 'Cung cấp đầy đủ chứng chỉ xuất xứ (CO), chứng nhận chất lượng (CQ) và hóa đơn VAT cho dự án.',
                ],
                [
                    'title' => 'Kiểm định thông số cơ khí',
                    'desc' => 'Kiểm tra dung sai bước xích, tải trọng kéo đứt và độ cứng bề mặt trước khi xuất kho bàn giao.',
                ],
                [
                    'title' => 'Hỗ trợ kỹ thuật lắp đặt',
                    'desc' => 'Đội ngũ kỹ sư Thành Khang Tecsat hỗ trợ đối chiếu bản vẽ, mã phụ tùng và thông số ăn khớp đĩa xích.',
                ],
            ],
            'care_instructions' => [
                'Kiểm tra độ giãn bước xích và độ mòn con lăn định kỳ theo số giờ vận hành của thiết bị.',
                'Sử dụng dầu/mỡ bôi trơn công nghiệp phù hợp với tải trọng và điều kiện môi trường làm việc.',
                'Đối với thang cuốn và thang nâng, nên thay đồng bộ cả hai dải xích trái/phải để đảm bảo độ đồng bộ.',
            ],
        ];
    }

    public static function getDefaultSpecificationsFor(self $product): array
    {
        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));
        $specs = [];

        if (!empty($product->sku)) {
            $specs[] = [
                'label' => $isEn ? 'Part Number / SKU' : 'Mã sản phẩm',
                'value' => (string) $product->sku,
            ];
        }

        if (!empty($product->material)) {
            $specs[] = [
                'label' => $isEn ? 'Material' : 'Vật liệu',
                'value' => (string) $product->material,
            ];
        }

        if (!empty($product->lead_time)) {
            $specs[] = [
                'label' => $isEn ? 'Supply & Lead Time' : 'Tiến độ cung ứng',
                'value' => (string) $product->lead_time,
            ];
        }

        return $specs;
    }

    public function optionsMappingThumbnail($options, $primaryOptionValues)
    {
        $values = $primaryOptionValues->pluck('thumbnail_url', 'primary_option.id');

        return $options->map(function ($option) use ($values) {
            $option['show_name'] = $option['id'] !== 1;
            $option['nodes'] = $option['nodes']->map(function ($node) use ($values) {
                if (isset($values[$node['id']])) {
                    $node['image_url'] = $values[$node['id']];
                }
                return $node;
            });
            return $option;
        });
    }

    public function transformGeneral()
    {
        $data = [
            'id' => $this->id,

            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'quantity_free_shipping' => $this->quantity_free_shipping,

            'category' => $this->category,
            'sku' => $this->sku,
            'rate_score' => round($this->rate_score, 1),
            'ratings_count' => $this->ratings_count,
            'variant' => $variant ?? $this->default_variant,
            'description' => $this->description,
            'brand' => $this->brand?->transform(),
            'origin' => $this->origin?->transform(),

            'image' => $this->image_default,

            'is_featured' => (bool) $this->is_featured,
            'url' => $this->current_url ?: (is_array($this->url) ? ($this->url[strtoupper(current_locale())] ?? (reset($this->url) ?: null)) : $this->url),
        ];

        return $data;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('products.status', self::STATUS_ACTIVE);
    }

    public function scopeIsFeatured(Builder $query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $query->when($filters['products'] ?? false, function (Builder $query, $products) {
            $ids = explode(',', $products);
            $query->whereIn('id', $ids);
        });

        $query->when($filters['categories'] ?? false, function (Builder $query, $categories) {
            $ids = explode(',', $categories);

            $categories = ProductCategory::query()
                ->with('children')
                ->whereIn('id', $ids)
                ->get();

            foreach ($categories as $category) {
                $categoryIds[] = $category->id;
                $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
            }

            $query->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            });
        });

        if (empty($filters['sort'])) {
            $query->orderByPosition();
        }

        return $query;
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC')
            ->orderByDesc('id');
    }

    public function scopeOrderByFeatured($query)
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderByDesc('id');
    }

    public function getImageUrlAttribute()
    {
        return isset($this->images[0]['path']) ? static_url($this->images[0]['path']) : null;
    }

    public function related($condition = [])
    {
        $limit = empty($condition['limit']) ? 4 : $condition['limit'];
        $relatedProducts = $this->relatedProducts
            ->where('status', self::STATUS_ACTIVE)
            ->where('id', '<>', $this->id)
            ->where(function ($item) {
                return $item['categories']->where('status', self::STATUS_ACTIVE)->count() > 0;
            })
            ->values()
            ->take($limit);

        $relatedProductIds = [];

        if ($relatedProducts->count() > 0) {
            $relatedProductIds = $relatedProducts->pluck('id');
        }

        if (count($relatedProducts) < $limit) {
            $addProducts = self::query()
                ->active()
                ->when($this->category['id'] ?? false, function ($query) {
                    $query->whereHas('categories', function ($query) {
                        $query->where('id', $this->category['id']);
                    });
                })
                ->where('id', '<>', $this->id)
                ->whereNotIn('id', $relatedProductIds)
                ->take($limit - count($relatedProducts))
                ->get();

            if (count($addProducts) > 0) {
                $relatedProducts = $relatedProducts->concat($addProducts);
            }
        }

        return $relatedProducts->map(fn($item) => $item->transform());
    }

    public function getFlashSale($variant)
    {
        $getSaleDate = $this->getSaleDate();

        if ($this->sale_available && $variant['flash_sale_quantity'] > 0) {
            return [
                'is_sale' => true,
                'flash_sale_from' => $getSaleDate['flash_sale_from'],
                'flash_sale_to' => $getSaleDate['flash_sale_to'],
                'price' => $variant['sale_price'] ?? $variant['price'],
                'last_sale' => $getSaleDate['flash_sale_to'],
                'day_to_sale' => 1,
            ];
        } else {
            return [
                'is_sale' => false,
                'flash_sale_to' => $this->flash_sale_from,
                'flash_sale_from' => $this->flash_sale_to,
            ];
        }
    }

    public function scopeSearchSlug($query, $slug)
    {
        $queryRaw = [];
        $words = collect(explode('-', $slug));

        if ($words->count() === 1 || $words->count() > 4) {
            $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) LIKE '%-$slug-%' then 50 else 0 end)";
            $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) LIKE '$slug-%' then 50 else 0 end)";
            $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) LIKE '%-$slug' then 50 else 0 end)";
            $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) LIKE '%$slug%' then 10 else 0 end)";
        } else {
            $rowWords = $this->combinations($words, count($words));

            foreach ($rowWords as $words) {
                $subQueryRaw1 = '';
                $subQueryRaw2 = '';
                $subQueryRaw3 = '';
                foreach ($words as $index => $word) {
                    if ($index == 0) {
                        $subQueryRaw1 .= "LIKE '%-$word-%'";
                        $subQueryRaw2 .= "LIKE '$word-%'";
                        $subQueryRaw3 .= "LIKE '%-$word-%'";
                    } else if ($index == count($words) - 1) {
                        $subQueryRaw1 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word-%'";
                        $subQueryRaw2 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word-%'";
                        $subQueryRaw3 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word'";
                    } else {
                        $subQueryRaw1 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word-%'";
                        $subQueryRaw2 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word-%'";
                        $subQueryRaw3 .= " AND LOWER(`product_translations`.`slug`) LIKE '%-$word-%'";
                    }
                }
                $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) " . $subQueryRaw1 . " then " . count($words) * 10 . " else 0 end)";
                $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) " . $subQueryRaw2 . " then " . count($words) * 10 . " else 0 end)";
                $queryRaw[] = "(case when LOWER(`product_translations`.`slug`) " . $subQueryRaw3 . " then " . count($words) * 10 . " else 0 end)";
            }
        }

        $query
            ->join('product_translations', function ($join) {
                $join->on('products.id', '=', 'product_translations.product_id');
            })
            ->select('products.id', 'products.images')
            ->selectRaw(
                "max(
                        " . join(' + ', $queryRaw) . "
                    ) as priority",
            )
            ->having('priority', '>', 0)
            ->orderBy('priority', 'DESC');

        return $query->groupBy('id');
    }

    public function combinations($arr, $count)
    {
        $combinations = array();
        $combination = array_fill(0, $count, 0);

        while (1) {
            $temp = array();
            for ($i = 0; $i < $count; ++$i)
                $temp[] = $arr[$combination[$i]];
            $combinations[] = $temp;

            $i = $count - 1;
            while ($i >= 0 && $combination[$i] == count($arr) - 1)
                --$i;

            if ($i < 0)
                break;

            ++$combination[$i];

            for ($j = $i + 1; $j < $count; ++$j)
                $combination[$j] = $combination[$i];
        }

        $combinations = collect($combinations)->map(function ($item) {
            return array_unique($item);
        })->filter(function ($item) {
            return count($item) > 1;
        })
            ->values()
            ->toArray();

        return $combinations;
    }
}
