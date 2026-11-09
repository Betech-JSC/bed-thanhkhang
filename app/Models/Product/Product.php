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
            'url' => $this->url,
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
        $defaults = static::getDefaultSpecificationsFor($this);
        $rawSpecs = $this->attributes['specifications'] ?? null;
        $storedSpecs = is_array($rawSpecs) ? $rawSpecs : (is_string($rawSpecs) ? json_decode($rawSpecs, true) : []);
        if (!is_array($storedSpecs)) {
            $storedSpecs = [];
        }

        // Backfill individual columns from stored specifications or defaults if empty
        if (empty($this->sample_size)) {
            $this->sample_size = $storedSpecs[2]['value'] ?? $defaults[2]['value'] ?? null;
        }
        if (empty($this->layer_height)) {
            $this->layer_height = $storedSpecs[3]['value'] ?? $defaults[3]['value'] ?? null;
        }
        if (empty($this->nozzle_size)) {
            $this->nozzle_size = $storedSpecs[4]['value'] ?? $defaults[4]['value'] ?? null;
        }
        if (empty($this->infill)) {
            $this->infill = $storedSpecs[5]['value'] ?? $defaults[5]['value'] ?? null;
        }
        if (empty($this->support_material)) {
            $this->support_material = $storedSpecs[6]['value'] ?? $defaults[6]['value'] ?? null;
        }

        $defaultMeta = static::getDefaultPdpMetadataFor($this);
        if (empty($this->image_color_label)) {
            $this->image_color_label = $defaultMeta['image_color_label'];
        }
        if (empty($this->customization_label)) {
            $this->customization_label = $defaultMeta['customization_label'];
        }

        $isCmsSave = strpos(request()->getRequestUri(), '/products/store') !== false;
        $techValue = ($isCmsSave && !empty($this->print_technology))
            ? $this->print_technology
            : ($storedSpecs[0]['value'] ?? $this->print_technology ?? $defaults[0]['value']);
        $matValue = ($isCmsSave && !empty($this->material))
            ? $this->material
            : ($storedSpecs[1]['value'] ?? $this->material ?? $defaults[1]['value']);

        $syncedSpecs = [
            ['label' => 'Công nghệ in', 'value' => $techValue],
            ['label' => 'Vật liệu', 'value' => $matValue],
            ['label' => 'Kích thước mẫu', 'value' => $this->sample_size ?: ($defaults[2]['value'] ?? '')],
            ['label' => 'Chiều cao lớp in', 'value' => $this->layer_height ?: ($defaults[3]['value'] ?? '')],
            ['label' => 'Đầu phun', 'value' => $this->nozzle_size ?: ($defaults[4]['value'] ?? '')],
            ['label' => 'Mật độ ruột', 'value' => $this->infill ?: ($defaults[5]['value'] ?? '')],
            ['label' => 'Vật liệu hỗ trợ', 'value' => $this->support_material ?: ($defaults[6]['value'] ?? '')],
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'specifications')) {
            $this->attributes['specifications'] = json_encode($syncedSpecs, JSON_UNESCAPED_UNICODE);
        } else {
            unset($this->attributes['specifications']);
        }

        $rawMeta = $this->attributes['pdp_metadata'] ?? null;
        $storedMeta = is_array($rawMeta) ? $rawMeta : (is_string($rawMeta) ? json_decode($rawMeta, true) : []);
        if (!is_array($storedMeta)) {
            $storedMeta = [];
        }

        if ($isCmsSave && request()->has('pdp_metadata') && is_array(request()->input('pdp_metadata'))) {
            $storedMeta = array_merge($storedMeta, request()->input('pdp_metadata'));
        }

        $syncedMeta = array_merge($defaultMeta, array_filter($storedMeta, fn($v) => $v !== null && $v !== ''), [
            'image_color_label' => $this->image_color_label ?: $defaultMeta['image_color_label'],
            'customization_label' => $this->customization_label ?: $defaultMeta['customization_label'],
        ]);

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
        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));
        $defaults = static::getDefaultSpecificationsFor($this);

        if ($isEn) {
            if (!empty($this->sample_size)) {
                $defaults[2]['value'] = $this->sample_size;
            }
            if (!empty($this->layer_height)) {
                $defaults[3]['value'] = $this->layer_height;
            }
            if (!empty($this->nozzle_size)) {
                $defaults[4]['value'] = $this->nozzle_size;
            }
            return $defaults;
        }

        $specs = $defaults;
        if (!empty($this->attributes['specifications'])) {
            $raw = $this->attributes['specifications'];
            $stored = is_array($raw) ? $raw : json_decode($raw, true);
            if (!empty($stored) && is_array($stored)) {
                foreach ($stored as $idx => $row) {
                    if (isset($specs[$idx]) && !empty($row['value'])) {
                        $specs[$idx]['value'] = $row['value'];
                    }
                }
            }
        }

        if (!empty($this->sample_size)) {
            $specs[2]['value'] = $this->sample_size;
        }
        if (!empty($this->layer_height)) {
            $specs[3]['value'] = $this->layer_height;
        }
        if (!empty($this->nozzle_size)) {
            $specs[4]['value'] = $this->nozzle_size;
        }
        if (!empty($this->infill)) {
            $specs[5]['value'] = $this->infill;
        }
        if (!empty($this->support_material)) {
            $specs[6]['value'] = $this->support_material;
        }

        return $specs;
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

        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));
        $merged = $isEn ? $defaults : array_merge($defaults, array_filter($storedMeta, fn($v) => $v !== null && $v !== ''));

        $colorLabel = (!$isEn && !empty($this->image_color_label))
            ? $this->image_color_label
            : ($merged['image_color_label'] ?? $defaults['image_color_label']);

        $customLabel = (!$isEn && !empty($this->customization_label))
            ? $this->customization_label
            : ($merged['customization_label'] ?? $defaults['customization_label']);

        return [
            'print_technology' => $this->print_technology ?: ($specifications[0]['value'] ?? 'FDM High-Precision'),
            'material' => $this->material ?: ($specifications[1]['value'] ?? 'Nhựa PLA Matte Cao Cấp'),
            'lead_time' => $this->lead_time,
            'sample_size' => $this->sample_size ?: ($specifications[2]['value'] ?? ''),
            'layer_height' => $this->layer_height ?: ($specifications[3]['value'] ?? ''),
            'nozzle_size' => $this->nozzle_size ?: ($specifications[4]['value'] ?? ''),
            'infill' => $this->infill ?: ($specifications[5]['value'] ?? ''),
            'support_material' => $this->support_material ?: ($specifications[6]['value'] ?? ''),
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
        $slug = $product->slug ?? $product->seo_slug ?? '';

        $colorMapVi = [
            'mo-hinh-cao-trang-tri' => 'Cam',
            'iron-man-mark-85-resin-8k' => 'Đỏ - Vàng Metallic',
            'mo-hinh-iron-man-chibi' => 'Đỏ - Vàng Metallic',
            'khung-vo-robot-carbon-fiber' => 'Đen Carbon Nhám',
            'hop-dung-danh-thiep-cyberpunk' => 'Đen Nhám - Vàng Neon',
            'moc-khoa-turbine-phan-luc' => 'Xám Titan - Xanh',
            'gia-do-tai-nghe-voronoi' => 'Đen Nhám (Matte Black)',
            'cum-banh-rang-hanh-tinh' => 'Xám Cơ Khí - Cam',
            'tuong-cyber-wolf-low-poly' => 'Xám Bạc Ánh Kim',
            'moc-khoa-dong-co-v8-piston' => 'Xám Kim Loại - Đỏ',
            'den-ngu-mat-trang-lithophane' => 'Trắng Ngà Ánh Vàng',
            'khop-noi-cau-bionic-sls' => 'Xám Nylon Kỹ Thuật',
            'mo-hinh-tau-vu-tru-discovery' => 'Trắng Vũ Trụ - Đen',
            'khay-to-chuc-day-cap-modular' => 'Đen Nhám - Cam',
            'moc-khoa-vo-lang-f1-quick-release' => 'Đen Carbon - Đỏ Racing',
            'den-ban-led-hexagon-to-ong' => 'Đen Nhám - Trắng Khuếch Tán',
            'vo-case-ban-phim-co-custom' => 'Xám Space Grey',
            'moc-khoa-trai-tim-co-khi' => 'Đỏ Ruby - Vàng Đồng',
            'mo-hinh-phi-hanh-gia' => 'Trắng Tuyết - Vàng Gold',
            'chau-cay-da-giac-mini' => 'Trắng Sữa (Matte White)',
            'de-do-dien-thoai-mecha' => 'Đen Nhám - Cam',
            'robot-khop-cu-dong' => 'Cam - Xám Cơ Khí',
        ];

        $colorMapEn = [
            'mo-hinh-cao-trang-tri' => 'Orange',
            'iron-man-mark-85-resin-8k' => 'Metallic Red & Gold',
            'mo-hinh-iron-man-chibi' => 'Metallic Red & Gold',
            'khung-vo-robot-carbon-fiber' => 'Matte Carbon Black',
            'hop-dung-danh-thiep-cyberpunk' => 'Matte Black & Neon Yellow',
            'moc-khoa-turbine-phan-luc' => 'Titanium Grey & Blue',
            'gia-do-tai-nghe-voronoi' => 'Matte Black',
            'cum-banh-rang-hanh-tinh' => 'Mechanical Grey & Orange',
            'tuong-cyber-wolf-low-poly' => 'Metallic Silver Grey',
            'moc-khoa-dong-co-v8-piston' => 'Gunmetal Grey & Red',
            'den-ngu-mat-trang-lithophane' => 'Warm Ivory White',
            'khop-noi-cau-bionic-sls' => 'Engineering Nylon Grey',
            'mo-hinh-tau-vu-tru-discovery' => 'Space White & Black',
            'khay-to-chuc-day-cap-modular' => 'Matte Black & Orange',
            'moc-khoa-vo-lang-f1-quick-release' => 'Carbon Black & Racing Red',
            'den-ban-led-hexagon-to-ong' => 'Matte Black & Frosted White',
            'vo-case-ban-phim-co-custom' => 'Space Grey',
            'moc-khoa-trai-tim-co-khi' => 'Ruby Red & Brass Gold',
            'mo-hinh-phi-hanh-gia' => 'Snow White & Gold',
            'chau-cay-da-giac-mini' => 'Matte White',
            'de-do-dien-thoai-mecha' => 'Matte Black & Orange',
            'robot-khop-cu-dong' => 'Orange & Mechanical Grey',
        ];

        $isIronMan = in_array($slug, ['mo-hinh-iron-man-chibi', 'iron-man-mark-85-resin-8k'], true);

        if ($isEn) {
            return [
                'image_color_label' => $colorMapEn[$slug] ?? 'Orange',
                'customization_label' => 'Color · Dimensions · Quantity',
                'story_title' => $isIronMan
                    ? 'Technological Legend. Sharp Down to Every Detail.'
                    : 'Compact. Full of Wonders to Explore.',
                'story_intro' => $product->description ?: 'The low-poly fox sculpture is a modern statement piece for living and working spaces. Crafted with crisp faceted geometry and a warm orange hue, it brings refined energy and creativity to any corner.',
                'feature1_title' => 'Sharp Low-Poly Contours',
                'feature1_desc' => 'Faceted edges are smoothly finished to catch light uniquely from every angle, showcasing the dimensional precision of FDM 3D printing.',
                'feature2_title' => 'Customizable on Request',
                'feature2_desc' => 'Work with HH Maker to customize colors, adjust scale dimensions, or order in bulk for corporate gifts and event decor.',
                'manufacturing_info' => [
                    [
                        'title' => 'Tailored Per-Model Fabrication',
                        'desc' => 'Each piece is 3D printed with slicing parameters optimized for its specific geometry, ensuring structural durability and crisp surface aesthetics.',
                    ],
                    [
                        'title' => 'Hand-Inspected Post-Processing',
                        'desc' => 'After printing, every model is carefully inspected, support structures are removed, and fine details are cleaned before protective packaging.',
                    ],
                    [
                        'title' => 'Authentic 3D Printed Surface Texture',
                        'desc' => 'FDM 3D printed products feature subtle characteristic layer lines—a signature hallmark of additive manufacturing rather than a surface defect.',
                    ],
                ],
                'care_instructions' => [
                    'Avoid placing the product in environments exceeding 55°C (such as car trunks, direct intense window sunlight, or near heat sources) to prevent softening or warping.',
                    'Clean gently using a soft brush, dry microfiber cloth, or damp cloth wrung dry. Do not use harsh chemical solvents or high-concentration alcohol.',
                    'Ideal for indoor display on work desks, bookshelves, studio setups, or living spaces.',
                    'Avoid dropping from height or applying excessive impact force to thin or pointed geometric edges.',
                ],
            ];
        }

        $defaultFoxIntro = 'Mô hình chú cáo low-poly là điểm nhấn hiện đại cho không gian sống và làm việc. Thiết kế tạo hình từ các mặt cắt đa giác sắc sảo, kết hợp cùng gam màu cam ấm áp mang đến cảm giác tinh tế, sáng tạo và đầy năng lượng';

        return [
            'image_color_label' => $colorMapVi[$slug] ?? 'Cam',
            'customization_label' => 'Màu sắc · Kích thước · Số lượng',
            'story_title' => $isIronMan
                ? 'Huyền thoại công nghệ. Sắc nét đến từng chi tiết.'
                : 'Nhỏ gọn. Nhiều điều để khám phá.',
            'story_intro' => ($slug === 'mo-hinh-cao-trang-tri')
                ? $defaultFoxIntro
                : ($product->description ?: $defaultFoxIntro),
            'feature1_title' => 'Tạo hình Low-Poly sắc nét',
            'feature1_desc' => 'Các góc cạnh đa giác được hoàn thiện mượt mà, phản chiếu ánh sáng độc đáo ở từng góc nhìn, thể hiện rõ thế mạnh độ chính xác của công nghệ in 3D FDM.',
            'feature2_title' => 'Tùy chỉnh theo yêu cầu',
            'feature2_desc' => 'Bạn có thể trao đổi với HH Maker để thay đổi màu sắc, điều chỉnh kích thước (tỉ lệ scale) hoặc đặt số lượng lớn làm quà tặng, decor sự kiện.',
            'manufacturing_info' => [
                [
                    'title' => 'Gia công theo từng mẫu',
                    'desc' => 'Sản phẩm được in 3D theo thông số kỹ thuật tối ưu cho từng hình khối, đảm bảo độ bền kết cấu và tính thẩm mỹ của bề mặt.',
                ],
                [
                    'title' => 'Hoàn thiện thủ công',
                    'desc' => 'Sau khi in, mẫu được kiểm tra, xử lý phần hỗ trợ (support) và làm sạch các chi tiết thừa trước khi đóng gói.',
                ],
                [
                    'title' => 'Đặc trưng bề mặt in 3D',
                    'desc' => 'Sản phẩm in 3D FDM sẽ có các đường vân lớp in (layer lines) mảnh đặc trưng. Đây là nét riêng của công nghệ chế tác bồi đắp, không phải lỗi sản phẩm.',
                ],
            ],
            'care_instructions' => [
                'Tránh đặt sản phẩm ở nơi có nhiệt độ cao trên 55°C (như cốp xe hơi, sát cửa sổ nắng gắt trực tiếp hoặc gần nguồn nhiệt) để tránh làm mềm hoặc biến dạng nhựa PLA.',
                'Vệ sinh nhẹ nhàng bằng chổi lông mềm, khăn khô hoặc khăn ẩm vắt kiệt nước. Không dùng hóa chất tẩy rửa mạnh hoặc cồn nồng độ cao.',
                'Phù hợp trưng bày trong nhà, bàn làm việc, kệ sách hoặc không gian studio.',
                'Hạn chế làm rơi từ độ cao hoặc tác động lực mạnh vào các chi tiết mảnh/nhọn của mô hình.',
            ],
        ];
    }

    public static function getDefaultSpecificationsFor(self $product): array
    {
        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));
        $slug = $product->slug ?? $product->seo_slug ?? '';

        $catalogSpecs = [
            'mo-hinh-cao-trang-tri' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM High-Precision'],
                    ['label' => 'Vật liệu', 'value' => 'Nhựa PLA Matte Cao Cấp'],
                    ['label' => 'Kích thước mẫu', 'value' => '120 x 85 x 140 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Zero-support)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM High-Precision'],
                    ['label' => 'Material', 'value' => 'Premium Matte PLA'],
                    ['label' => 'Model Dimensions', 'value' => '120 x 85 x 140 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Infill Density', 'value' => '20% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Zero-support)'],
                ],
            ],
            'iron-man-mark-85-resin-8k' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'SLA 8K Photochemical'],
                    ['label' => 'Vật liệu', 'value' => '8K High-Detail Tough Resin'],
                    ['label' => 'Kích thước mẫu', 'value' => '180 x 110 x 280 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                    ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV 405nm)'],
                    ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% (Solid Resin)'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support dễ tách (Light Touch)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'SLA 8K Photochemical'],
                    ['label' => 'Material', 'value' => '8K High-Detail Tough Resin'],
                    ['label' => 'Model Dimensions', 'value' => '180 x 110 x 280 mm'],
                    ['label' => 'Layer Height', 'value' => '0.025 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => 'Photochemical SLA (405nm UV Laser)'],
                    ['label' => 'Infill Density', 'value' => '100% Solid Resin'],
                    ['label' => 'Support Material', 'value' => 'Light Touch Peel Support'],
                ],
            ],
            'khung-vo-robot-carbon-fiber' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Industrial Hardened Nozzle'],
                    ['label' => 'Vật liệu', 'value' => 'PETG Gia Cường 20% Carbon Fiber (PETG-CF)'],
                    ['label' => 'Kích thước mẫu', 'value' => '220 x 160 x 85 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Thép Tôi Cứng (Hardened Steel)'],
                    ['label' => 'Mật độ ruột', 'value' => '40% Honeycomb Chịu Lực'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support Dễ Tách'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Industrial Hardened Nozzle'],
                    ['label' => 'Material', 'value' => 'PETG Reinforced 20% Carbon Fiber (PETG-CF)'],
                    ['label' => 'Model Dimensions', 'value' => '220 x 160 x 85 mm'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Infill Density', 'value' => '40% Structural Honeycomb'],
                    ['label' => 'Support Material', 'value' => 'Easy-Peel Tree Support'],
                ],
            ],
            'hop-dung-danh-thiep-cyberpunk' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Precision'],
                    ['label' => 'Vật liệu', 'value' => 'PLA+ Matte Futuristic Finish'],
                    ['label' => 'Kích thước mẫu', 'value' => '160 x 95 x 75 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '15% Grid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Precision'],
                    ['label' => 'Material', 'value' => 'PLA+ Matte Futuristic Finish'],
                    ['label' => 'Model Dimensions', 'value' => '160 x 95 x 75 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '15% Grid'],
                    ['label' => 'Support Material', 'value' => 'None (Print-in-Place)'],
                ],
            ],
            'moc-khoa-turbine-phan-luc' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'SLA Precision Micro-Detail'],
                    ['label' => 'Vật liệu', 'value' => 'Engineering Tough Resin / Titanium Coating'],
                    ['label' => 'Kích thước mẫu', 'value' => 'Ø30 x 48 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                    ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV)'],
                    ['label' => 'Mật độ ruột', 'value' => 'Đặc 100%'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Micro-support tách siêu mịn'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'SLA Precision Micro-Detail'],
                    ['label' => 'Material', 'value' => 'Engineering Tough Resin / Titanium Coating'],
                    ['label' => 'Model Dimensions', 'value' => 'Ø30 x 48 mm'],
                    ['label' => 'Layer Height', 'value' => '0.025 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => 'Photochemical SLA (UV Laser)'],
                    ['label' => 'Infill Density', 'value' => '100% Solid'],
                    ['label' => 'Support Material', 'value' => 'Ultra-fine Micro-support'],
                ],
            ],
            'gia-do-tai-nghe-voronoi' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Advanced Parametric'],
                    ['label' => 'Vật liệu', 'value' => 'PETG Gradient Bionic Structure'],
                    ['label' => 'Kích thước mẫu', 'value' => '140 x 130 x 250 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Mật độ ruột', 'value' => '25% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Cấu trúc Bionic tự chống đỡ)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Advanced Parametric'],
                    ['label' => 'Material', 'value' => 'PETG Gradient Bionic Structure'],
                    ['label' => 'Model Dimensions', 'value' => '140 x 130 x 250 mm'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Infill Density', 'value' => '25% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Self-supporting Bionic Lattice)'],
                ],
            ],
            'cum-banh-rang-hanh-tinh' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM High-Tolerance Engineering'],
                    ['label' => 'Vật liệu', 'value' => 'Nylon PA12 Kỹ Thuật Tự Bôi Trơn'],
                    ['label' => 'Kích thước mẫu', 'value' => '110 x 110 x 45 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Mật độ ruột', 'value' => '35% Rectilinear'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Dung sai trục 0.2mm)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM High-Tolerance Engineering'],
                    ['label' => 'Material', 'value' => 'Self-lubricating Engineering Nylon PA12'],
                    ['label' => 'Model Dimensions', 'value' => '110 x 110 x 45 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Infill Density', 'value' => '35% Rectilinear'],
                    ['label' => 'Support Material', 'value' => 'None (0.2mm axle clearance)'],
                ],
            ],
            'tuong-cyber-wolf-low-poly' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Multi-Axis Contour'],
                    ['label' => 'Vật liệu', 'value' => 'Silk Metallic PLA / Nano Coating'],
                    ['label' => 'Kích thước mẫu', 'value' => '210 x 95 x 165 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '15% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support tối giản'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Multi-Axis Contour'],
                    ['label' => 'Material', 'value' => 'Silk Metallic PLA / Nano Coating'],
                    ['label' => 'Model Dimensions', 'value' => '210 x 95 x 165 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '15% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'Minimal Tree Support'],
                ],
            ],
            'moc-khoa-dong-co-v8-piston' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place Khớp Liền Khối'],
                    ['label' => 'Vật liệu', 'value' => 'PLA+ Tough Print-in-Place'],
                    ['label' => 'Kích thước mẫu', 'value' => '55 x 35 x 30 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '30% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place liền khối)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Monolithic Print-in-Place'],
                    ['label' => 'Material', 'value' => 'PLA+ Tough Print-in-Place'],
                    ['label' => 'Model Dimensions', 'value' => '55 x 35 x 30 mm'],
                    ['label' => 'Layer Height', 'value' => '0.12 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '30% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Seamless Print-in-Place)'],
                ],
            ],
            'den-ngu-mat-trang-lithophane' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Lithophane Độ Phân Giải Cao'],
                    ['label' => 'Vật liệu', 'value' => 'Translucent White Optical PLA'],
                    ['label' => 'Kích thước mẫu', 'value' => 'Ø150 mm (Cầu)'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% (Vỏ thấu quang Lithophane)'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support chân đế'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'High-Resolution FDM Lithophane'],
                    ['label' => 'Material', 'value' => 'Translucent White Optical PLA'],
                    ['label' => 'Model Dimensions', 'value' => 'Ø150 mm (Sphere)'],
                    ['label' => 'Layer Height', 'value' => '0.12 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '100% Solid (Optical Shell)'],
                    ['label' => 'Support Material', 'value' => 'Base pedestal support'],
                ],
            ],
            'khop-noi-cau-bionic-sls' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'SLS (Selective Laser Sintering) Công Nghiệp'],
                    ['label' => 'Vật liệu', 'value' => 'Polyamide 12 (Nylon PA12) Bột Thiêu Kết'],
                    ['label' => 'Kích thước mẫu', 'value' => '65 x 65 x 75 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.10 mm'],
                    ['label' => 'Đầu phun', 'value' => 'Chùm tia Laser CO2 (Hệ buồng kín)'],
                    ['label' => 'Mật độ ruột', 'value' => 'Đặc 100% Đồng nhất'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Zero-support (Bột tự nâng đỡ)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'Industrial SLS (Selective Laser Sintering)'],
                    ['label' => 'Material', 'value' => 'Polyamide 12 (Nylon PA12) Sintered Powder'],
                    ['label' => 'Model Dimensions', 'value' => '65 x 65 x 75 mm'],
                    ['label' => 'Layer Height', 'value' => '0.10 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => 'CO2 Laser Beam (Enclosed Chamber)'],
                    ['label' => 'Infill Density', 'value' => '100% Homogeneous Solid'],
                    ['label' => 'Support Material', 'value' => 'Zero-support (Self-supporting powder)'],
                ],
            ],
            'mo-hinh-tau-vu-tru-discovery' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'Hybrid FDM + SLA Multi-Material'],
                    ['label' => 'Vật liệu', 'value' => 'PLA Pro Matte & Transparent Optical Resin'],
                    ['label' => 'Kích thước mẫu', 'value' => '260 x 140 x 95 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass + SLA Optics'],
                    ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support tách bề mặt mịn'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'Hybrid FDM + SLA Multi-Material'],
                    ['label' => 'Material', 'value' => 'PLA Pro Matte & Transparent Optical Resin'],
                    ['label' => 'Model Dimensions', 'value' => '260 x 140 x 95 mm'],
                    ['label' => 'Layer Height', 'value' => '0.12 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass + SLA Optics'],
                    ['label' => 'Infill Density', 'value' => '20% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'Fine Surface Tree Support'],
                ],
            ],
            'khay-to-chuc-day-cap-modular' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM High-Strength Modular'],
                    ['label' => 'Vật liệu', 'value' => 'PETG Matte & Neodymium N52 Magnets'],
                    ['label' => 'Kích thước mẫu', 'value' => '200 x 80 x 35 mm (Module)'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '20% Grid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM High-Strength Modular'],
                    ['label' => 'Material', 'value' => 'PETG Matte & Neodymium N52 Magnets'],
                    ['label' => 'Model Dimensions', 'value' => '200 x 80 x 35 mm (Module)'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '20% Grid'],
                    ['label' => 'Support Material', 'value' => 'None'],
                ],
            ],
            'moc-khoa-vo-lang-f1-quick-release' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'SLA Precision 25 Micron'],
                    ['label' => 'Vật liệu', 'value' => 'Tough Resin 8K & Steel Ball Bearings'],
                    ['label' => 'Kích thước mẫu', 'value' => '60 x 42 x 25 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.025 mm'],
                    ['label' => 'Đầu phun', 'value' => 'Quang hóa SLA (Laser UV)'],
                    ['label' => 'Mật độ ruột', 'value' => 'Đặc 100%'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support vi điểm bóc tay'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'SLA Precision 25 Micron'],
                    ['label' => 'Material', 'value' => 'Tough Resin 8K & Steel Ball Bearings'],
                    ['label' => 'Model Dimensions', 'value' => '60 x 42 x 25 mm'],
                    ['label' => 'Layer Height', 'value' => '0.025 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => 'Photochemical SLA (UV Laser)'],
                    ['label' => 'Infill Density', 'value' => '100% Solid'],
                    ['label' => 'Support Material', 'value' => 'Micro-touch hand-peel support'],
                ],
            ],
            'den-ban-led-hexagon-to-ong' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM High-Precision Optics'],
                    ['label' => 'Vật liệu', 'value' => 'Heat-Resistant PETG & Optical Diffusion Shell'],
                    ['label' => 'Kích thước mẫu', 'value' => '130 x 115 x 30 mm (1 Module)'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '15% Honeycomb'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM High-Precision Optics'],
                    ['label' => 'Material', 'value' => 'Heat-Resistant PETG & Optical Diffusion Shell'],
                    ['label' => 'Model Dimensions', 'value' => '130 x 115 x 30 mm (1 Module)'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '15% Honeycomb'],
                    ['label' => 'Support Material', 'value' => 'None'],
                ],
            ],
            'vo-case-ban-phim-co-custom' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Multi-Material Carbon Reinforced'],
                    ['label' => 'Vật liệu', 'value' => 'PETG-CF / Gasket Mount Isolation'],
                    ['label' => 'Kích thước mẫu', 'value' => '325 x 140 x 38 mm (Layout 75%)'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Mật độ ruột', 'value' => '35% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Support khu vực cổng kết nối'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Multi-Material Carbon Reinforced'],
                    ['label' => 'Material', 'value' => 'PETG-CF / Gasket Mount Isolation'],
                    ['label' => 'Model Dimensions', 'value' => '325 x 140 x 38 mm (Layout 75%)'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Hardened Steel'],
                    ['label' => 'Infill Density', 'value' => '35% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'I/O Port Area Support'],
                ],
            ],
            'moc-khoa-trai-tim-co-khi' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place Siêu Mịn 0.12mm'],
                    ['label' => 'Vật liệu', 'value' => 'Silk Metallic PLA / Print-in-Place Mechanism'],
                    ['label' => 'Kích thước mẫu', 'value' => '50 x 45 x 22 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.12 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '25% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Print-in-Place)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'Fine 0.12mm FDM Print-in-Place'],
                    ['label' => 'Material', 'value' => 'Silk Metallic PLA / Print-in-Place Mechanism'],
                    ['label' => 'Model Dimensions', 'value' => '50 x 45 x 22 mm'],
                    ['label' => 'Layer Height', 'value' => '0.12 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '25% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Print-in-Place)'],
                ],
            ],
            'mo-hinh-phi-hanh-gia' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM High-Detail Sculpting'],
                    ['label' => 'Vật liệu', 'value' => 'PLA Matte Tuyết Trắng & Visor Mạ Vàng'],
                    ['label' => 'Kích thước mẫu', 'value' => '95 x 80 x 160 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '20% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Tree Support hữu cơ'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM High-Detail Sculpting'],
                    ['label' => 'Material', 'value' => 'Pure White Matte PLA & Gold-Plated Visor'],
                    ['label' => 'Model Dimensions', 'value' => '95 x 80 x 160 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '20% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'Organic Tree Support'],
                ],
            ],
            'chau-cay-da-giac-mini' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Watertight In Chống Rò Rỉ'],
                    ['label' => 'Vật liệu', 'value' => 'PETG Waterproof Chống Thấm 100%'],
                    ['label' => 'Kích thước mẫu', 'value' => 'Ø90 x 80 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '20% Grid (Thành vỏ 4 lớp chống thấm)'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Góc vát tự chống đỡ)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Watertight Leakproof Printing'],
                    ['label' => 'Material', 'value' => '100% Waterproof PETG'],
                    ['label' => 'Model Dimensions', 'value' => 'Ø90 x 80 mm'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '20% Grid (4-wall watertight)'],
                    ['label' => 'Support Material', 'value' => 'None (Self-supporting chamfer angles)'],
                ],
            ],
            'de-do-dien-thoai-mecha' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Ergonomic Engineering'],
                    ['label' => 'Vật liệu', 'value' => 'PLA+ Tough Gia Cường Chịu Lực'],
                    ['label' => 'Kích thước mẫu', 'value' => '85 x 75 x 110 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.20 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '25% Gyroid Chịu Lực'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Thiết kế góc nghiêng 60°)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Ergonomic Engineering'],
                    ['label' => 'Material', 'value' => 'Reinforced Heavy-Duty PLA+'],
                    ['label' => 'Model Dimensions', 'value' => '85 x 75 x 110 mm'],
                    ['label' => 'Layer Height', 'value' => '0.20 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '25% High-Strength Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Ergonomic 60° incline)'],
                ],
            ],
            'robot-khop-cu-dong' => [
                'vi' => [
                    ['label' => 'Công nghệ in', 'value' => 'FDM Print-in-Place 14 Khớp Linh Hoạt'],
                    ['label' => 'Vật liệu', 'value' => 'PETG Tough Kháng Gãy Khớp'],
                    ['label' => 'Kích thước mẫu', 'value' => '90 x 50 x 175 mm'],
                    ['label' => 'Chiều cao lớp in', 'value' => '0.16 mm'],
                    ['label' => 'Đầu phun', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Mật độ ruột', 'value' => '30% Gyroid'],
                    ['label' => 'Vật liệu hỗ trợ', 'value' => 'Không cần (Khớp cầu in liền khối)'],
                ],
                'en' => [
                    ['label' => 'Print Technology', 'value' => 'FDM Print-in-Place 14 Flexible Joints'],
                    ['label' => 'Material', 'value' => 'Shatter-Resistant Tough PETG'],
                    ['label' => 'Model Dimensions', 'value' => '90 x 50 x 175 mm'],
                    ['label' => 'Layer Height', 'value' => '0.16 mm'],
                    ['label' => 'Extruder / Nozzle', 'value' => '0.40 mm Brass Nozzle'],
                    ['label' => 'Infill Density', 'value' => '30% Gyroid'],
                    ['label' => 'Support Material', 'value' => 'None (Seamless Ball Joints)'],
                ],
            ],
        ];

        if (!empty($slug) && isset($catalogSpecs[$slug])) {
            $langKey = $isEn ? 'en' : 'vi';
            return $catalogSpecs[$slug][$langKey] ?? $catalogSpecs[$slug]['vi'];
        }

        // Dynamic fallback based on product properties
        $tech = $product->print_technology ?: 'FDM High-Precision';
        $mat = $product->material ?: 'Nhựa PLA Matte Cao Cấp';
        $isSla = stripos($tech, 'SLA') !== false || stripos($mat, 'Resin') !== false;
        $isSls = stripos($tech, 'SLS') !== false || stripos($mat, 'Nylon') !== false;

        if ($isEn) {
            return [
                ['label' => 'Print Technology', 'value' => $tech],
                ['label' => 'Material', 'value' => $mat],
                ['label' => 'Model Dimensions', 'value' => 'Standard (Customizable on request)'],
                ['label' => 'Layer Height', 'value' => $isSla ? '0.025 mm' : ($isSls ? '0.10 mm' : '0.16 mm')],
                ['label' => 'Extruder / Nozzle', 'value' => $isSla ? 'SLA Photochemical UV' : ($isSls ? 'CO2 Laser Beam' : '0.40 mm Hardened Steel')],
                ['label' => 'Infill Density', 'value' => ($isSla || $isSls) ? '100% Solid' : '20% Gyroid'],
                ['label' => 'Support Material', 'value' => $isSls ? 'Zero-support (Self-supporting powder)' : ($isSla ? 'Light Touch Peel Support' : 'None / Easy-Peel Support')],
            ];
        }

        return [
            ['label' => 'Công nghệ in', 'value' => $tech],
            ['label' => 'Vật liệu', 'value' => $mat],
            ['label' => 'Kích thước mẫu', 'value' => 'Tiêu chuẩn (Gia công theo yêu cầu)'],
            ['label' => 'Chiều cao lớp in', 'value' => $isSla ? '0.025 mm' : ($isSls ? '0.10 mm' : '0.16 mm')],
            ['label' => 'Đầu phun', 'value' => $isSla ? 'Quang hóa SLA (Laser UV)' : ($isSls ? 'Chùm tia Laser CO2' : '0.40 mm Hardened Steel')],
            ['label' => 'Mật độ ruột', 'value' => ($isSla || $isSls) ? 'Đặc 100%' : '20% Gyroid'],
            ['label' => 'Vật liệu hỗ trợ', 'value' => $isSls ? 'Zero-support (Bột tự nâng đỡ)' : ($isSla ? 'Support dễ tách' : 'Không cần / Support dễ tách')],
        ];
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
            'url' => $this->url,
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
