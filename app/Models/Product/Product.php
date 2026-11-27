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
        'position',
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
                'position' => 'nullable|integer',
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
            if (request()->has('categories') || request()->has('category_id')) {
                $model->saveCategories($model);
            }

            static::clearProductCaches($model);
        });

        static::deleted(function (self $model) {
            static::clearProductCaches($model);
        });
    }

    public static function clearProductCaches(?self $model = null, array $extraSlugs = []): void
    {
        $locales = config('app.locales', ['vi', 'en']);
        $identifiers = [];

        if ($model) {
            if (!empty($model->id)) {
                $identifiers[] = (string) $model->id;
            }
            if ($model->relationLoaded('translations')) {
                if (!empty($model->slug)) {
                    $identifiers[] = $model->slug;
                }
                if (!empty($model->seo_slug)) {
                    $identifiers[] = $model->seo_slug;
                }
            }
            if ($model->relationLoaded('translations') || method_exists($model, 'translations')) {
                try {
                    foreach ($model->translations()->get() as $translation) {
                        if (!empty($translation->slug)) {
                            $identifiers[] = $translation->slug;
                        }
                        if (!empty($translation->seo_slug)) {
                            $identifiers[] = $translation->seo_slug;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore if table not ready
                }
            }
        }

        foreach ($extraSlugs as $extraSlug) {
            if (is_string($extraSlug) && trim($extraSlug) !== '') {
                $identifiers[] = trim($extraSlug);
            }
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('products')) {
                foreach (\Illuminate\Support\Facades\DB::table('products')->pluck('id') as $productId) {
                    if (!empty($productId)) {
                        $identifiers[] = (string) $productId;
                    }
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('product_translations')) {
                $translationSlugs = \Illuminate\Support\Facades\DB::table('product_translations')
                    ->select('slug', 'seo_slug')
                    ->get();

                foreach ($translationSlugs as $row) {
                    if (!empty($row->slug)) {
                        $identifiers[] = $row->slug;
                    }
                    if (!empty($row->seo_slug)) {
                        $identifiers[] = $row->seo_slug;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if table not ready
        }

        $identifiers = array_values(array_unique(array_filter(array_map(
            static fn($val) => trim((string) $val),
            $identifiers
        ))));

        foreach ($locales as $locale) {
            \Illuminate\Support\Facades\Cache::forget('products_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('documents_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('inertia_layout_shared_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('search_page_data_' . $locale . '_');

            foreach ($identifiers as $identifier) {
                \Illuminate\Support\Facades\Cache::forget('product_show_' . $identifier . '_' . $locale);
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
            ->orderBy('created_at', 'desc')
            ->orderByDesc('id');
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
        $variant = $this->variants?->where('is_default')?->first();

        if (!empty($variant->id) && !empty($variant->images) && count($variant->images) > 0) {
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

    public function resolveProductSlug(?string $locale = null): ?string
    {
        $slug = $this->resolveSlugForLocale($locale);
        return $slug !== '' ? $slug : null;
    }

    public function resolveSlugForLocale(?string $locale = null): string
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $translation = $translations->firstWhere('locale', $locale);
        $slug = trim((string) ($translation?->seo_slug ?: $translation?->slug ?: ''));

        if ($slug === '' && config('app.use_default_locale', true)) {
            $defaultLocale = config('app.locale_default_frontend') ?: config('app.fallback_locale') ?: config('app.locale', 'vi');
            $defaultTranslation = $translations->firstWhere('locale', $defaultLocale);
            $slug = trim((string) ($defaultTranslation?->seo_slug ?: $defaultTranslation?->slug ?: ''));
        }

        if ($slug === '') {
            $slug = trim((string) ($this->seo_slug ?: $this->slug ?: ''));
        }

        return $slug;
    }

    public function resolveFrontendUrl(?string $locale = null, bool $absolute = false): ?string
    {
        if (!$this->is_active) {
            return null;
        }

        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $slug = $this->resolveSlugForLocale($locale);
        $routeName = "$locale.products.show";

        if ($slug !== '' && \Illuminate\Support\Facades\Route::has($routeName)) {
            return route($routeName, ['slug' => $slug], $absolute);
        }

        return null;
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        if ($this->is_active) {
            $translations = $this->relationLoaded('translations')
                ? $this->translations
                : $this->translations()->get();

            foreach ($translations as $translation) {
                $slug = trim((string) ($translation->seo_slug ?: $translation->slug ?: ''));
                $routeName = "$translation->locale.products.show";

                if ($slug !== '' && \Illuminate\Support\Facades\Route::has($routeName)) {
                    $urls[strtoupper($translation->locale)] = route($routeName, [
                        'slug' => $slug,
                    ]);
                }
            }

            if (config('app.use_default_locale', true)) {
                $defaultLocale = config('app.locale_default_frontend') ?: config('app.fallback_locale') ?: config('app.locale', 'vi');
                $defaultSlug = $this->resolveSlugForLocale($defaultLocale);
                if ($defaultSlug !== '') {
                    foreach (config('app.locales', ['vi', 'en']) as $loc) {
                        $upperLoc = strtoupper($loc);
                        $routeName = "$loc.products.show";
                        if (empty($urls[$upperLoc]) && \Illuminate\Support\Facades\Route::has($routeName)) {
                            $urls[$upperLoc] = route($routeName, [
                                'slug' => $defaultSlug,
                            ]);
                        }
                    }
                }
            }
        }
        return $urls;
    }

    public function getCurrentUrlAttribute()
    {
        if (!$this->is_active) {
            return null;
        }

        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $urls = $this->url;

        if (!empty($urls[strtoupper($locale)])) {
            return $urls[strtoupper($locale)];
        }

        if (config('app.use_default_locale', true)) {
            return $this->resolveFrontendUrl($locale, true);
        }

        return null;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getBreadcrumbsAttribute()
    {
        $activeCategories = $this->categories
            ->where('status', ProductCategory::STATUS_ACTIVE)
            ->values();

        if ($activeCategories->isEmpty()) {
            return [];
        }

        $category = $activeCategories
            ->sortByDesc(fn(ProductCategory $cat) => count(ProductCategory::flatAncestors($cat)))
            ->values()
            ->first();

        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $defaultLocale = config('app.locale_default_frontend') ?: config('app.fallback_locale') ?: config('app.locale', 'vi');
        $useFallback = (bool) config('app.use_default_locale', true);

        return collect(ProductCategory::flatAncestors($category))
            ->reverse()
            ->values()
            ->map(function (ProductCategory $item) use ($locale, $defaultLocale, $useFallback) {
                $translations = $item->relationLoaded('translations')
                    ? $item->translations
                    : $item->translations()->get();
                $trans = $translations->firstWhere('locale', $locale);
                $fallbackTrans = ($useFallback && $locale !== $defaultLocale)
                    ? $translations->firstWhere('locale', $defaultLocale)
                    : null;

                $title = trim((string) ($trans?->title ?: $fallbackTrans?->title ?: $item->title ?: ''));
                $slug = trim((string) (
                    $trans?->seo_slug
                    ?: $trans?->slug
                    ?: $fallbackTrans?->seo_slug
                    ?: $fallbackTrans?->slug
                    ?: $item->seo_slug
                    ?: $item->slug
                    ?: ''
                ));

                $urls = is_array($item->url) ? $item->url : [];
                $url = $urls[strtoupper($locale)] ?? (reset($urls) ?: null);

                return [
                    'id' => $item->id,
                    'title' => $title,
                    'slug' => $slug,
                    'url' => $url,
                ];
            })
            ->values()
            ->all();
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
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $slug = $this->resolveProductSlug($locale);
        $categories = $this->categories
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $slug,
            'position' => $this->position,
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
            'url' => $this->resolveTransformUrl($locale),
        ];

        return $data;
    }

    private function resolveTransformUrl(string $locale): ?string
    {
        $url = $this->resolveFrontendUrl($locale, false) ?: $this->current_url;
        if (!empty($url)) {
            return $url;
        }

        $urls = $this->url;
        if (is_array($urls)) {
            $fallback = $urls[strtoupper($locale)] ?? reset($urls);
            return $fallback ?: null;
        }

        return $urls ?: null;
    }

    public function transformCategory($conditions = ['options' => []])
    {
        $options = $this->optionDetails($conditions['options']);

        return array_merge($options, [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'slug' => $this->resolveProductSlug(),
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
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $images = collect($this->images)
            ->filter()
            ->values()
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

        $brandData = $this->brand
            ? array_merge($this->brand->transform(), [
                'name' => $this->brand->title ?? $this->brand->name ?? null,
            ])
            : null;

        $relatedDocs = [];
        try {
            if (
                \Illuminate\Support\Facades\Schema::hasTable('product_ref_posts')
                && \Illuminate\Support\Facades\Schema::hasTable('posts')
            ) {
                $relatedDocs = $this->posts()
                    ->active()
                    ->with('translations')
                    ->get()
                    ->map(function (Post $post) {
                        $postSlug = trim((string) ($post->seo_slug ?: $post->slug ?: ''));
                        $downloadUrl = $this->resolveDocumentDownloadUrl($post->download ?? null);
                        $postUrl = $downloadUrl ?: $post->current_url ?: ($post->link ?: null);

                        return [
                            'id' => $post->id,
                            'type' => $post->type,
                            'title' => $post->title,
                            'slug' => $postSlug !== '' ? $postSlug : null,
                            'description' => $post->description,
                            'url' => $postUrl,
                            'download_url' => $downloadUrl,
                            'has_file' => $downloadUrl !== null,
                            'image' => $post->getImageDetail($post->image),
                        ];
                    })
                    ->values()
                    ->all();
            }
        } catch (\Throwable $e) {
            $relatedDocs = [];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => transform_richtext($this->content),
            'slug' => $this->resolveProductSlug($locale),
            'position' => $this->position,
            'sku' => $this->sku,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price,
            'print_technology' => $this->print_technology,
            'material' => $this->material,
            'lead_time' => $this->lead_time,
            'brand' => $brandData,
            'brand_name' => $pdpMeta['brand_name'],
            'origin' => $pdpMeta['origin'],
            'warranty' => $pdpMeta['warranty'],
            'condition' => $pdpMeta['condition'],
            'stock_status' => $pdpMeta['stock_status'],
            'technical_support' => $pdpMeta['technical_support'],
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
            'breadcrumbs' => $this->breadcrumbs,
            'category' => $this->category,
            'categories' => $categories,
            'posts' => $relatedDocs,
            'documents' => $relatedDocs,
            'specifications' => $specifications,
            'filter_attributes' => [
                'print_technology' => $this->print_technology,
                'material' => $this->material,
                'lead_time' => $this->lead_time,
            ],
            'pdp_metadata' => $pdpMeta,
        ];
    }

    /**
     * Resolve downloadable document/attachment URL safely from post->download value.
     */
    private function resolveDocumentDownloadUrl(mixed $rawValue): ?string
    {
        if (is_string($rawValue) && trim($rawValue) !== '') {
            $trimmed = trim($rawValue);
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $path = $decoded['url'] ?? $decoded['path'] ?? null;
                if (is_string($path) && trim($path) !== '') {
                    $path = trim($path);
                    return str_starts_with($path, 'http') || str_starts_with($path, '/')
                        ? $path
                        : static_url($path);
                }
                return null;
            }
            if (
                str_starts_with($trimmed, 'http')
                || str_starts_with($trimmed, '/')
                || str_starts_with($trimmed, 'assets/')
                || str_starts_with($trimmed, 'images/')
                || str_starts_with($trimmed, 'static/')
            ) {
                return str_starts_with($trimmed, 'http') || str_starts_with($trimmed, '/')
                    ? $trimmed
                    : '/' . $trimmed;
            }
            return static_url($trimmed);
        }

        if (is_array($rawValue)) {
            $path = $rawValue['url'] ?? $rawValue['path'] ?? null;
            if (is_string($path) && trim($path) !== '') {
                $path = trim($path);
                return str_starts_with($path, 'http') || str_starts_with($path, '/')
                    ? $path
                    : static_url($path);
            }
        }

        return null;
    }

    /**
     * Decode JSON string or return array safely.
     */
    private function decodeJsonArray(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Normalize Tab 2 specification cards and drop completely empty rows.
     */
    private function normalizeSpecsCards(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $card) {
            if (!is_array($card)) {
                continue;
            }
            $label = trim((string) ($card['label'] ?? $card['title'] ?? ''));
            $value = trim((string) ($card['value'] ?? $card['heading'] ?? ''));
            $description = trim((string) ($card['description'] ?? $card['desc'] ?? ''));
            if ($label !== '' || $value !== '' || $description !== '') {
                $normalized[] = [
                    'label' => $label,
                    'value' => $value,
                    'description' => $description,
                ];
            }
        }

        return $normalized;
    }

    /**
     * Normalize Tab 3 customization steps and drop rows missing step, title, and description.
     */
    private function normalizeCustomSteps(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $step) {
            if (!is_array($step)) {
                continue;
            }
            $stepLabel = trim((string) ($step['step'] ?? $step['number'] ?? ''));
            $title = trim((string) ($step['title'] ?? $step['name'] ?? ''));
            $description = trim((string) ($step['description'] ?? $step['desc'] ?? ''));
            if ($stepLabel !== '' || $title !== '' || $description !== '') {
                $normalized[] = [
                    'step' => $stepLabel,
                    'title' => $title,
                    'description' => $description,
                ];
            }
        }

        return $normalized;
    }

    /**
     * Normalize PDP badge labels from string or ['label' => ...] entries.
     */
    private function normalizeBadges(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        return array_values(array_filter(array_map(static function ($badge): string {
            if (is_array($badge)) {
                return trim((string) ($badge['label'] ?? $badge['title'] ?? $badge['name'] ?? ''));
            }
            if (is_string($badge) || is_numeric($badge)) {
                return trim((string) $badge);
            }
            return '';
        }, $raw), static fn (string $val): bool => $val !== ''));
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
        $storedSpecs = $this->decodeJsonArray($rawSpecs);

        $normalizedSpecs = [];
        foreach ($storedSpecs as $row) {
            if (is_array($row) && isset($row['label']) && trim((string) $row['label']) !== '') {
                $normalizedSpecs[] = [
                    'label' => trim((string) $row['label']),
                    'value' => isset($row['value']) ? trim((string) $row['value']) : '',
                ];
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'specifications')) {
            $this->attributes['specifications'] = !empty($normalizedSpecs)
                ? json_encode($normalizedSpecs, JSON_UNESCAPED_UNICODE)
                : null;
        } else {
            unset($this->attributes['specifications']);
        }

        $originalMeta = $this->exists ? $this->decodeJsonArray($this->getRawOriginal('pdp_metadata')) : [];
        $attrMeta = $this->decodeJsonArray($this->attributes['pdp_metadata'] ?? null);
        $reqMeta = ($isCmsSave && request()->has('pdp_metadata') && is_array(request()->input('pdp_metadata')))
            ? request()->input('pdp_metadata')
            : null;

        if ($reqMeta !== null) {
            $storedMeta = array_merge($originalMeta, $attrMeta, $reqMeta);
        } elseif ($isCmsSave && $this->exists && empty($attrMeta) && !empty($originalMeta) && request()->input('pdp_metadata') === null) {
            $storedMeta = $originalMeta;
        } else {
            $storedMeta = $attrMeta;
        }

        if (array_key_exists('specs_cards', $storedMeta)) {
            $storedMeta['specs_cards'] = $this->normalizeSpecsCards($storedMeta['specs_cards']);
        }
        if (array_key_exists('custom_steps', $storedMeta)) {
            $storedMeta['custom_steps'] = $this->normalizeCustomSteps($storedMeta['custom_steps']);
        }
        if (array_key_exists('badges', $storedMeta)) {
            $storedMeta['badges'] = $this->normalizeBadges($storedMeta['badges']);
        }

        foreach ($storedMeta as $key => $value) {
            if (is_string($value)) {
                $storedMeta[$key] = trim($value);
            }
        }

        $syncedMeta = array_filter($storedMeta, static fn($v) => $v !== null && $v !== '' && $v !== []);

        if (!empty($this->image_color_label)) {
            $syncedMeta['image_color_label'] = trim((string) $this->image_color_label);
        } elseif ($this->isDirty('image_color_label')) {
            unset($syncedMeta['image_color_label'], $syncedMeta['color']);
        }

        if (!empty($this->customization_label)) {
            $syncedMeta['customization_label'] = trim((string) $this->customization_label);
        } elseif ($this->isDirty('customization_label')) {
            unset($syncedMeta['customization_label'], $syncedMeta['customization']);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'pdp_metadata')) {
            $this->attributes['pdp_metadata'] = !empty($syncedMeta)
                ? json_encode($syncedMeta, JSON_UNESCAPED_UNICODE)
                : null;
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
        $stored = $this->decodeJsonArray($this->attributes['specifications'] ?? null);

        if (!empty($stored)) {
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

        $isEn = (app()->getLocale() === 'en' || (function_exists('current_locale') && current_locale() === 'en'));
        $columnSpecs = [
            ['attr' => 'sku', 'vi' => 'Mã sản phẩm', 'en' => 'Part Number / SKU'],
            ['attr' => 'print_technology', 'vi' => 'Tiêu chuẩn / Công nghệ chế tạo', 'en' => 'Standard / Process'],
            ['attr' => 'material', 'vi' => 'Vật liệu', 'en' => 'Material'],
            ['attr' => 'sample_size', 'vi' => 'Kích thước / Bước xích', 'en' => 'Dimensions / Pitch'],
            ['attr' => 'layer_height', 'vi' => 'Tải trọng làm việc', 'en' => 'Working Load'],
            ['attr' => 'nozzle_size', 'vi' => 'Xử lý bề mặt', 'en' => 'Surface Finish'],
            ['attr' => 'infill', 'vi' => 'Tiêu chuẩn áp dụng', 'en' => 'Applicable Standard'],
            ['attr' => 'support_material', 'vi' => 'Chứng chỉ / Kiểm định', 'en' => 'Certification'],
            ['attr' => 'lead_time', 'vi' => 'Tiến độ cung ứng', 'en' => 'Supply & Lead Time'],
        ];

        $specs = [];
        foreach ($columnSpecs as $item) {
            $val = $this->getAttribute($item['attr']);
            if (is_string($val) || is_numeric($val)) {
                $trimmed = trim((string) $val);
                if ($trimmed !== '') {
                    $specs[] = [
                        'label' => $isEn ? $item['en'] : $item['vi'],
                        'value' => $trimmed,
                    ];
                }
            }
        }

        return $specs;
    }

    public function resolvePdpMetadata(?array $specifications = null): array
    {
        $specifications = $specifications ?? $this->resolveSpecifications();
        $storedMeta = $this->decodeJsonArray($this->attributes['pdp_metadata'] ?? null);

        $pickStr = static function (...$values): ?string {
            foreach ($values as $value) {
                if (is_string($value) || is_numeric($value)) {
                    $trimmed = trim((string) $value);
                    if ($trimmed !== '') {
                        return $trimmed;
                    }
                }
            }
            return null;
        };

        $colorLabel = $pickStr($this->image_color_label, $storedMeta['image_color_label'] ?? null, $storedMeta['color'] ?? null);
        $customLabel = $pickStr($this->customization_label, $storedMeta['customization_label'] ?? null, $storedMeta['customization'] ?? null);

        $badges = $this->normalizeBadges($storedMeta['badges'] ?? []);
        $specsCards = $this->normalizeSpecsCards(
            $storedMeta['specs_cards'] ?? $storedMeta['specsCards'] ?? $storedMeta['manufacturing_info'] ?? []
        );
        $customSteps = $this->normalizeCustomSteps(
            $storedMeta['custom_steps'] ?? $storedMeta['customSteps'] ?? $storedMeta['care_instructions'] ?? []
        );

        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $isEn = $locale === 'en';
        $pickLocalizedMeta = static function (string $key) use ($storedMeta, $isEn, $pickStr): ?string {
            return $isEn
                ? $pickStr($storedMeta[$key . '_en'] ?? null, $storedMeta[$key] ?? null)
                : $pickStr($storedMeta[$key] ?? null);
        };
        $brandEntityName = $pickStr($this->brand?->title ?? null, $this->brand?->name ?? null);
        $metaBrandName = $pickStr(
            $isEn ? ($storedMeta['brand_name_en'] ?? null) : null,
            $storedMeta['brand_name'] ?? null,
            $storedMeta['brand'] ?? null,
            $brandEntityName
        );

        return array_merge($storedMeta, [
            'brand_name' => $metaBrandName,
            'origin' => $pickLocalizedMeta('origin'),
            'warranty' => $pickLocalizedMeta('warranty'),
            'condition' => $pickLocalizedMeta('condition'),
            'stock_status' => $pickLocalizedMeta('stock_status'),
            'technical_support' => $pickLocalizedMeta('technical_support'),
            'print_technology' => $pickStr($this->print_technology, $storedMeta['print_technology'] ?? null),
            'material' => $pickStr($this->material, $storedMeta['material'] ?? null),
            'lead_time' => $pickStr($this->lead_time, $storedMeta['lead_time'] ?? null),
            'sample_size' => $pickStr($this->sample_size, $storedMeta['sample_size'] ?? null),
            'layer_height' => $pickStr($this->layer_height, $storedMeta['layer_height'] ?? null),
            'nozzle_size' => $pickStr($this->nozzle_size, $storedMeta['nozzle_size'] ?? null),
            'infill' => $pickStr($this->infill, $storedMeta['infill'] ?? null),
            'support_material' => $pickStr($this->support_material, $storedMeta['support_material'] ?? null),
            'color' => $colorLabel,
            'image_color_label' => $colorLabel,
            'customization' => $customLabel,
            'customization_label' => $customLabel,
            'badges' => $badges,
            'story_title' => $pickStr($storedMeta['story_title'] ?? null),
            'story_intro' => $pickStr($storedMeta['story_intro'] ?? null),
            'feature1_title' => $pickStr($storedMeta['feature1_title'] ?? null),
            'feature1_desc' => $pickStr($storedMeta['feature1_desc'] ?? null),
            'feature2_title' => $pickStr($storedMeta['feature2_title'] ?? null),
            'feature2_desc' => $pickStr($storedMeta['feature2_desc'] ?? null),
            'specs_title' => $pickStr($storedMeta['specs_title'] ?? null, $storedMeta['specsTitle'] ?? null),
            'specs_desc' => $pickStr($storedMeta['specs_desc'] ?? null, $storedMeta['specsDesc'] ?? null, $storedMeta['specs_description'] ?? null),
            'specs_content' => $pickStr($storedMeta['specs_content'] ?? null, $storedMeta['specsContent'] ?? null),
            'specs_cards' => $specsCards,
            'custom_title' => $pickStr($storedMeta['custom_title'] ?? null, $storedMeta['customTitle'] ?? null),
            'custom_desc' => $pickStr($storedMeta['custom_desc'] ?? null, $storedMeta['customDesc'] ?? null, $storedMeta['custom_description'] ?? null),
            'custom_content' => $pickStr($storedMeta['custom_content'] ?? null, $storedMeta['customContent'] ?? null),
            'custom_steps' => $customSteps,
            'custom_cta_text' => $pickStr($storedMeta['custom_cta_text'] ?? null, $storedMeta['customCtaText'] ?? null),
            'specifications' => $specifications,
            'manufacturing_info' => is_array($storedMeta['manufacturing_info'] ?? null) ? array_values($storedMeta['manufacturing_info']) : [],
            'care_instructions' => is_array($storedMeta['care_instructions'] ?? null) ? array_values($storedMeta['care_instructions']) : [],
        ]);
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
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $slug = $this->resolveProductSlug($locale);

        $data = [
            'id' => $this->id,

            'title' => $this->title,
            'slug' => $slug,
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
            'url' => $this->resolveTransformUrl($locale),
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
        $limit = empty($condition['limit']) ? 4 : (int) $condition['limit'];
        $relatedProducts = $this->relatedProducts
            ->where('status', self::STATUS_ACTIVE)
            ->where('id', '<>', $this->id)
            ->where(function ($item) {
                return $item['categories']->where('status', self::STATUS_ACTIVE)->count() > 0;
            })
            ->values()
            ->take($limit);

        $relatedProductIds = [$this->id];

        if ($relatedProducts->count() > 0) {
            $relatedProductIds = array_merge($relatedProductIds, $relatedProducts->pluck('id')->all());
        }

        if ($relatedProducts->count() < $limit) {
            $activeCats = $this->categories
                ->where('status', ProductCategory::STATUS_ACTIVE)
                ->sortByDesc(fn(ProductCategory $cat) => count(ProductCategory::flatAncestors($cat)))
                ->values();

            $directCatIds = [];
            foreach ($activeCats as $activeCat) {
                $directCatIds[] = $activeCat->id;
                $directCatIds = array_merge($directCatIds, $activeCat->allChildrenId([], $activeCat->id));
            }
            $directCatIds = array_values(array_unique($directCatIds));

            $addProducts = self::query()
                ->active()
                ->when(!empty($directCatIds), function ($query) use ($directCatIds) {
                    $query->whereHas('categories', function ($query) use ($directCatIds) {
                        $query->whereIn('product_categories.id', $directCatIds);
                    });
                })
                ->whereNotIn('products.id', array_values(array_unique($relatedProductIds)))
                ->ordered()
                ->take($limit - $relatedProducts->count())
                ->get();

            if ($addProducts->count() > 0) {
                $relatedProducts = $relatedProducts->concat($addProducts);
                $relatedProductIds = array_merge($relatedProductIds, $addProducts->pluck('id')->all());
            }
        }

        if ($relatedProducts->count() < $limit) {
            $parentIds = $this->categories
                ->where('status', ProductCategory::STATUS_ACTIVE)
                ->pluck('parent_id')
                ->filter(fn($id) => !empty($id))
                ->unique()
                ->values()
                ->all();

            if (!empty($parentIds)) {
                $siblingCatIds = [];
                foreach (ProductCategory::active()->whereIn('id', $parentIds)->get() as $parentCat) {
                    $siblingCatIds[] = $parentCat->id;
                    $siblingCatIds = array_merge($siblingCatIds, $parentCat->allChildrenId([], $parentCat->id));
                }
                $siblingCatIds = array_values(array_unique($siblingCatIds));

                if (!empty($siblingCatIds)) {
                    $siblingProducts = self::query()
                        ->active()
                        ->whereHas('categories', function ($query) use ($siblingCatIds) {
                            $query->whereIn('product_categories.id', $siblingCatIds);
                        })
                        ->whereNotIn('products.id', array_values(array_unique($relatedProductIds)))
                        ->ordered()
                        ->take($limit - $relatedProducts->count())
                        ->get();

                    if ($siblingProducts->count() > 0) {
                        $relatedProducts = $relatedProducts->concat($siblingProducts);
                    }
                }
            }
        }

        return $relatedProducts->take($limit)->values()->map(fn($item) => $item->transform());
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
