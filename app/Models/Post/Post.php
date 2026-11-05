<?php

namespace App\Models\Post;

use App\Models\BaseModel;
use \Illuminate\Support\Facades\Route;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Traits\Searchable;
use App\Traits\Translatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spiritix\LadaCache\Database\LadaCacheTrait;

class Post extends BaseModel
{
    use LadaCacheTrait;
    use HasFactory, SoftDeletes, Searchable, Translatable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const TYPE_POST = 'POST';
    public const TYPE_FEEDBACK = 'FEEDBACK';
    public const TYPE_MEMBER = 'MEMBER';
    public const TYPE_FAQ = 'FAQ';

    public const TYPE_LEARNING_ENV = 'LEARNING_ENV';
    public const TYPE_PRIZE = 'PRIZE';
    public const TYPE_PROJECT = 'PROJECT';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public const TYPE_LIST = [
        self::TYPE_FEEDBACK => 'Feedback',
        self::TYPE_POST => 'Bài viết',
        self::TYPE_MEMBER => 'Đội ngũ',
        self::TYPE_FAQ => 'Faq',

        self::TYPE_LEARNING_ENV => 'Môi trường học',
        self::TYPE_PRIZE => 'Giải thưởng',
        self::TYPE_PROJECT => 'Đồ án',
    ];

    public $with = ['translations', 'categories'];

    public $fillable = [
        'status',
        'published_at',
        'is_home',
        'type',
        'is_featured',
        'position',
        'download',
        'home_position',
        'footer_position',
        'view_count',
        'virtual_views',
        'image',
        'banner',
        'banner_mobile',
        'show_table_of_contents',
        'link',
        'icon',
        'price',
        'author_avatar',
        'images',
        'link_video',

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
        'author',
        'description',
        'content',
        'sliders',
        'take_note',

        'author_name',
        'contact',
        'industry',
        'year',
        'email',

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
        'images' => 'array',
        'icon' => 'array',
        'banner' => 'array',
        'banner_mobile' => 'array',
        'author_avatar' => 'array',
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
            'price' => 'nullable',
            'view_count' => 'nullable|integer',
            'virtual_views' => 'nullable|integer',
        ];
    }

    protected $searchable = [
        'columns' => [
            'post_translations.title' => 10,
            'post_translations.id' => 5,
            'post_translations.slug' => 5,
        ],
        'joins' => [
            'post_translations' => ['post_translations.post_id', 'posts.id'],
        ],
    ];

    protected $appends = ['url'];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (request()->route() !== null && empty($model->published_at)) {
                $model->published_at = request()->input('published_at') ?? now();
            }
        });

        static::saved(function (self $model) {
            if (request()->route() !== null) {
                $model->saveRelatedPosts($model);
                $model->saveCategories($model);
                $model->saveP($model);
                $model->saveProducts($model);
            }

            static::clearPostCaches($model);
        });

        static::deleted(function (self $model) {
            static::clearPostCaches($model);
        });
    }

    public static function clearPostCaches(?self $model = null): void
    {
        $locales = config('app.locales', ['vi', 'en']);
        $slugs = [];
        $postId = $model?->id;

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
            Cache::forget('prizes_index_' . $locale);
            Cache::forget('projects_index_' . $locale);
            Cache::forget('posts_index_' . $locale);
            Cache::forget('process_index_' . $locale);
            Cache::forget('agencies_index_' . $locale);
            Cache::forget('contact_index_' . $locale);
            Cache::forget('feedback_index_' . $locale);
            Cache::forget('galleries_index_' . $locale);
            Cache::forget('home_page_data_' . $locale);
            Cache::forget('inertia_layout_shared_data_' . $locale);
            Cache::forget('search_page_data_' . $locale . '_');

            if ($postId) {
                Cache::forget('related_posts_' . $postId . '_' . $locale);
            }

            foreach ($slugs as $slug) {
                Cache::forget('project_show_' . $slug . '_' . $locale);
                Cache::forget('prize_show_' . $slug . '_' . $locale);
                Cache::forget('post_show_' . $slug . '_' . $locale);
            }

            for ($i = 1; $i <= 5; $i++) {
                Cache::forget('projects_index_' . $locale . '_page_' . $i);
                Cache::forget('prizes_index_' . $locale . '_page_' . $i);
            }
        }
    }
    public function saveP($model)
    {
        if ($model->type == self::TYPE_PRIZE) {
            if (!request()->has('related_awards')) return;
            $relatedPosts = array_column(request()->input('related_awards', []), 'id');
            $model->relatedPosts()->sync($relatedPosts, 'id');
        }
    }

    public function saveCategories($model)
    {
        if (!request()->has('categories')) return;
        $categories = array_column(request()->input('categories', []), 'id');
        $model->categories()->sync($categories, 'id');
    }

    public function saveProducts($model)
    {
        if (!request()->has('products')) return;
        $categories = array_column(request()->input('products', []), 'id');
        $model->products()->sync($categories, 'id');
    }

    public function saveRelatedPosts($model)
    {
        if ($model->type == self::TYPE_POST) {
            if (!request()->has('related_posts')) return;
            $relatedPosts = array_column(request()->input('related_posts', []), 'id');
            $model->relatedPosts()->sync($relatedPosts, 'id');
        }
    }

    public function categories()
    {
        return $this->belongsToMany(
            PostCategory::class,
            'post_ref_categories',
            'post_id',
            'post_category_id'
        );
    }

    public function tags()
    {
        return $this->belongsToMany(
            \JamstackVietnam\Tag\Models\Tag::class,
            'post_ref_tags',
            'post_id',
            'tag_id'
        );
    }


    public function relatedPosts()
    {
        return $this->belongsToMany(
            self::class,
            'related_posts',
            'post_id',
            'related_post_id'
        );
    }

    public function relatedAwards()
    {
        return $this->belongsToMany(
            self::class,
            'related_posts',
            'post_id',
            'related_post_id'
        );
    }

    public function getPostRelatedIdsAttribute()
    {
        return $this->relatedPosts;
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        $default_locale = config('app.locale');

        if (!$this->status == self::STATUS_ACTIVE || !$this->type) {
            return $urls;
        }

        // Map type => route name prefix
        $routeMap = [
            self::TYPE_POST => 'posts',
            self::TYPE_FEEDBACK => 'feedbacks',
            self::TYPE_MEMBER => 'members',
            self::TYPE_FAQ => 'faqs',
        ];

        if (!isset($routeMap[$this->type])) {
            return $urls; // Không có route tương ứng
        }

        $routePrefix = $routeMap[$this->type];

        if (!Route::has("{$default_locale}.{$routePrefix}.show")) {
            return $urls;
        }

        foreach ($this->translations as $translation) {
            $locale = $translation->locale;
            $slug = $translation->seo_slug ?? $translation->slug;

            if (Route::has("{$locale}.{$routePrefix}.show")) {
                $urls[strtoupper($locale)] = route("{$locale}.{$routePrefix}.show", [
                    'slug' => $slug,
                ]);
            }
        }

        return $urls;
    }


    public function getCategoryAttribute()
    {
        return $this->categories
            ->where('status', PostCategory::STATUS_ACTIVE)
            ->sortBy([['parent_id', 'desc']])
            ->values()
            ->first()?->transform();
    }

    public function getPosts()
    {
        return static::query()
            ->active()
            ->where('type', self::TYPE_POST)
            ->get();
    }

    public function getProjects()
    {
        return static::query()
            ->active()
            ->where('type', self::TYPE_PROJECT)
            ->get();
    }

    public function getPrizes()
    {
        return static::query()
            ->active()
            ->where('type', self::TYPE_PRIZE)
            ->get();
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE &&
            $this->published_at <= now();
    }

    public function scopeActive($query)
    {
        return $query->whereLocaleActive()
            ->where('status', self::STATUS_ACTIVE)
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC')
            ->orderBy('id', 'desc');
    }

    public function scopeWhereMenuFooter($query)
    {
        return $query->where(function ($query) {
            $query
                ->whereNotNull('footer_position')
                ->where('footer_position', '>', 0);
        })
            ->orderBy('footer_position', 'asc')
            ->orderBy('id', 'desc');
    }

    public function scopeActiveCategories($query)
    {
        return $query->whereHas('categories', function ($query) {
            $query->active();
        });
    }

    public function scopeWherePosts($query)
    {
        return $query->where('type', self::TYPE_POST);
    }

    public function scopeWhereFeedback($query)
    {
        return $query->where('type', self::TYPE_FEEDBACK);
    }

    public function scopeWhereMembers($query)
    {
        return $query->where('type', self::TYPE_MEMBER);
    }

    public function scopeWherePrizes($query)
    {
        return $query->where('type', self::TYPE_PRIZE);
    }

    public function scopeWhereLearningEnv($query)
    {
        return $query->where('type', self::TYPE_LEARNING_ENV);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_ref_posts',
            'post_id',
            'product_id'
        );
    }

    public function transform($conditions = ['categories' => false])
    {
        $categories = $this->categories
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'link_video' => $this->link_video,
            'slug' => $this->seo_slug ?? $this->slug,
            'published_at' => $this->published_at,
            'description' => $this->description,
            'category' => $this->category,
            'categories' => $categories,
            'image' => $this->getImageDetail($this->image),
            'icon' => $this->getImageDetail($this->icon),
            'url' => $this->current_url,
            'link' => $this->link,
            'price' => $this->price,

            'images' => array_map(fn($img) => $this->getImageDetail($img), $this->images ?? []),
            'author_avatar' => $this->getImageDetail($this->author_avatar),
            'author_name' => $this->author_name,
            'contact' => $this->contact,
            'industry' => $this->industry,
            'year' => $this->year,
            'email' => $this->email,
            'content' => transform_richtext($this->content),
            'related_awards' => $this->relationLoaded('relatedAwards') ? $this->related_awards : null,
        ];

        if (isset($conditions['categories']) && $conditions['categories']) {
            $data['categories'] = $this->categories
                ->where('status', self::STATUS_ACTIVE)
                ->values()
                ->map(fn($item) => $item->transform());
        }

        return $data;
    }

    public function transformApi()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'price' => $this->price,
        ];

        return $data;
    }

    public function transformWithProducts()
    {

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'image' => $this->getImageDetail($this->image),
            'icon' => $this->getImageDetail($this->icon),
            'products' => $this->products->map(fn($item) => $item->transform())
        ];

        return $data;
    }

    public function transformFeedback()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'image' => $this->getImageDetail($this->image),
            'link' => $this->link,
            'icon' => $this->icon,
        ];

        return $data;
    }

    public function transformMember()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'image' => $this->getImageDetail($this->image),
            'content' => $this->content,
        ];

        return $data;
    }

    public function transformFaqs()
    {
        $viTranslation = method_exists($this, 'translate') ? $this->translate('vi', false) : null;
        $title = $this->title ?: ($viTranslation?->title ?? '');
        $rawAnswer = $this->content
            ?: $this->description
            ?: ($viTranslation?->content ?: ($viTranslation?->description ?: ''));
        $cleanAnswer = trim(strip_tags((string) $rawAnswer));

        $data = [
            'id' => $this->id,
            'title' => $title,
            'question' => $title,
            'content' => $rawAnswer,
            'answer' => $cleanAnswer !== '' ? $cleanAnswer : (string) $rawAnswer,
        ];

        return $data;
    }

    public static function getFrontendFaqs(?string $locale = null): array
    {
        $targetLocale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $targetLocale === 'en';

        $defaultFaqsVi = [
            [
                'id' => 1,
                'title' => 'Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?',
                'question' => 'Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?',
                'content' => 'Có. HH MAKER hỗ trợ dựng file 3D từ ý tưởng, bản phác thảo hoặc hình ảnh tham khảo của bạn. Bạn chỉ cần cung cấp kích thước, công năng và yêu cầu mong muốn; đội ngũ sẽ tư vấn phương án thiết kế, báo chi phí dựng file và gửi bản 3D để bạn duyệt trước khi sản xuất.',
                'answer' => 'Có. HH MAKER hỗ trợ dựng file 3D từ ý tưởng, bản phác thảo hoặc hình ảnh tham khảo của bạn. Bạn chỉ cần cung cấp kích thước, công năng và yêu cầu mong muốn; đội ngũ sẽ tư vấn phương án thiết kế, báo chi phí dựng file và gửi bản 3D để bạn duyệt trước khi sản xuất.',
            ],
            [
                'id' => 2,
                'title' => 'Để nhận được bảng báo giá chính xác và nhanh chóng nhất cho dự án của mình, tôi cần cung cấp những gì?',
                'question' => 'Để nhận được bảng báo giá chính xác và nhanh chóng nhất cho dự án của mình, tôi cần cung cấp những gì?',
                'content' => 'Bạn chỉ cần gửi file thiết kế 3D (nếu có định dạng .STL, .OBJ, .STEP...), kích thước mong muốn, yêu cầu về độ mịn, vật liệu hoặc ảnh mô tả ý tưởng. Đội ngũ kỹ thuật HH Maker sẽ phản hồi và gửi báo giá chi tiết trong vòng 2-4 giờ làm việc.',
                'answer' => 'Bạn chỉ cần gửi file thiết kế 3D (nếu có định dạng .STL, .OBJ, .STEP...), kích thước mong muốn, yêu cầu về độ mịn, vật liệu hoặc ảnh mô tả ý tưởng. Đội ngũ kỹ thuật HH Maker sẽ phản hồi và gửi báo giá chi tiết trong vòng 2-4 giờ làm việc.',
            ],
            [
                'id' => 3,
                'title' => 'Tôi muốn đặt sản phẩm theo ý tưởng riêng hoặc điều chỉnh khác đi so với các mẫu có sẵn thì có được không?',
                'question' => 'Tôi muốn đặt sản phẩm theo ý tưởng riêng hoặc điều chỉnh khác đi so với các mẫu có sẵn thì có được không?',
                'content' => 'Hoàn toàn được! Dịch vụ làm đồ Custom và chế tác theo yêu cầu là thế mạnh hàng đầu tại HH Maker. Chúng tôi sẵn sàng tinh chỉnh kích thước, tỷ lệ, phối màu hoặc thiết kế riêng biệt hoàn toàn theo concept của bạn.',
                'answer' => 'Hoàn toàn được! Dịch vụ làm đồ Custom và chế tác theo yêu cầu là thế mạnh hàng đầu tại HH Maker. Chúng tôi sẵn sàng tinh chỉnh kích thước, tỷ lệ, phối màu hoặc thiết kế riêng biệt hoàn toàn theo concept của bạn.',
            ],
            [
                'id' => 4,
                'title' => 'Thời gian in và giao hàng thường mất bao lâu?',
                'question' => 'Thời gian in và giao hàng thường mất bao lâu?',
                'content' => 'Thời gian hoàn thiện phụ thuộc vào kích thước, công nghệ và số lượng sản phẩm. Với các mẫu in thử đơn chiếc, thời gian gia công thường từ 24 - 48 giờ. Đơn hàng số lượng lớn sẽ được lên lịch sản xuất chi tiết.',
                'answer' => 'Thời gian hoàn thiện phụ thuộc vào kích thước, công nghệ và số lượng sản phẩm. Với các mẫu in thử đơn chiếc, thời gian gia công thường từ 24 - 48 giờ. Đơn hàng số lượng lớn sẽ được lên lịch sản xuất chi tiết.',
            ],
            [
                'id' => 5,
                'title' => 'HH Maker hỗ trợ những định dạng file 3D nào?',
                'question' => 'HH Maker hỗ trợ những định dạng file 3D nào?',
                'content' => 'Chúng tôi tiếp nhận hầu hết các định dạng 3D tiêu chuẩn bao gồm STL, OBJ, STEP, 3MF, IGES, SLDPRT. Nếu bạn chỉ có bản vẽ 2D hoặc hình phác thảo, HH Maker sẽ hỗ trợ dựng lại thành file 3D.',
                'answer' => 'Chúng tôi tiếp nhận hầu hết các định dạng 3D tiêu chuẩn bao gồm STL, OBJ, STEP, 3MF, IGES, SLDPRT. Nếu bạn chỉ có bản vẽ 2D hoặc hình phác thảo, HH Maker sẽ hỗ trợ dựng lại thành file 3D.',
            ],
            [
                'id' => 6,
                'title' => 'Sản phẩm in 3D có độ bền và khả năng chịu lực như thế nào?',
                'question' => 'Sản phẩm in 3D có độ bền và khả năng chịu lực như thế nào?',
                'content' => 'Độ bền phụ thuộc vào loại vật liệu được lựa chọn: từ PLA+ thân thiện, PETG chịu va đập, ABS/Nylon chịu nhiệt và cơ tính cao, đến Carbon Fiber siêu cứng hoặc Resin 8K độ mịn tuyệt đối. Kỹ sư sẽ tư vấn loại nhựa tối ưu theo mục đích sử dụng của bạn.',
                'answer' => 'Độ bền phụ thuộc vào loại vật liệu được lựa chọn: từ PLA+ thân thiện, PETG chịu va đập, ABS/Nylon chịu nhiệt và cơ tính cao, đến Carbon Fiber siêu cứng hoặc Resin 8K độ mịn tuyệt đối. Kỹ sư sẽ tư vấn loại nhựa tối ưu theo mục đích sử dụng của bạn.',
            ],
        ];

        $defaultFaqsEn = [
            [
                'id' => 1,
                'title' => 'I do not have a 3D CAD file yet, does HH Maker support 3D modeling from scratch?',
                'question' => 'I do not have a 3D CAD file yet, does HH Maker support 3D modeling from scratch?',
                'content' => 'Yes. HH Maker provides full 3D modeling support from your concepts, sketches, or reference photos. You simply share dimensions, functions, and aesthetic preferences; our team will advise on designs, provide quote estimates, and send 3D renders for your approval before production.',
                'answer' => 'Yes. HH Maker provides full 3D modeling support from your concepts, sketches, or reference photos. You simply share dimensions, functions, and aesthetic preferences; our team will advise on designs, provide quote estimates, and send 3D renders for your approval before production.',
            ],
            [
                'id' => 2,
                'title' => 'What information should I provide to get the fastest and most accurate quotation?',
                'question' => 'What information should I provide to get the fastest and most accurate quotation?',
                'content' => 'Simply share your 3D design files (if available in .STL, .OBJ, .STEP, .3MF format), desired dimensions, surface finish expectations, material preferences, or reference imagery. Our engineering team will respond with a comprehensive quote within 2-4 business hours.',
                'answer' => 'Simply share your 3D design files (if available in .STL, .OBJ, .STEP, .3MF format), desired dimensions, surface finish expectations, material preferences, or reference imagery. Our engineering team will respond with a comprehensive quote within 2-4 business hours.',
            ],
            [
                'id' => 3,
                'title' => 'Can I request a custom product based on my own concept or modify an existing model?',
                'question' => 'Can I request a custom product based on my own concept or modify an existing model?',
                'content' => 'Absolutely! Bespoke custom crafting and on-demand fabrication are HH Maker\'s core strengths. We are ready to adjust dimensions, proportions, color schemes, or develop entirely new designs matching your vision.',
                'answer' => 'Absolutely! Bespoke custom crafting and on-demand fabrication are HH Maker\'s core strengths. We are ready to adjust dimensions, proportions, color schemes, or develop entirely new designs matching your vision.',
            ],
            [
                'id' => 4,
                'title' => 'How long does 3D printing and delivery typically take?',
                'question' => 'How long does 3D printing and delivery typically take?',
                'content' => 'Turnaround time depends on dimensions, printing technology, and batch volume. Single prototypes are typically completed within 24-48 hours. Volume orders are scheduled with transparent delivery timelines.',
                'answer' => 'Turnaround time depends on dimensions, printing technology, and batch volume. Single prototypes are typically completed within 24-48 hours. Volume orders are scheduled with transparent delivery timelines.',
            ],
            [
                'id' => 5,
                'title' => 'Which 3D file formats does HH Maker support?',
                'question' => 'Which 3D file formats does HH Maker support?',
                'content' => 'We accept standard 3D formats including STL, OBJ, STEP, 3MF, IGES, and SLDPRT. If you only have 2D drawings or reference images, our engineers will help convert them into 3D printable files.',
                'answer' => 'We accept standard 3D formats including STL, OBJ, STEP, 3MF, IGES, and SLDPRT. If you only have 2D drawings or reference images, our engineers will help convert them into 3D printable files.',
            ],
            [
                'id' => 6,
                'title' => 'What is the durability and mechanical strength of 3D printed products?',
                'question' => 'What is the durability and mechanical strength of 3D printed products?',
                'content' => 'Durability depends on the chosen material: eco-friendly PLA+, impact-resistant PETG, heat-resistant ABS/Nylon, high-stiffness Carbon Fiber, or ultra-smooth 8K Resin. Our engineers advise on the optimal material suited to your application.',
                'answer' => 'Durability depends on the chosen material: eco-friendly PLA+, impact-resistant PETG, heat-resistant ABS/Nylon, high-stiffness Carbon Fiber, or ultra-smooth 8K Resin. Our engineers advise on the optimal material suited to your application.',
            ],
        ];

        $defaultFaqs = $isEn ? $defaultFaqsEn : $defaultFaqsVi;

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('posts')) {
                $posts = static::query()
                    ->where('type', self::TYPE_FAQ)
                    ->where('status', self::STATUS_ACTIVE)
                    ->orderByPosition()
                    ->orderBy('id', 'asc')
                    ->get();

                if ($posts->isNotEmpty()) {
                    return $posts->map(function ($item) use ($targetLocale) {
                        $trans = method_exists($item, 'translate') ? $item->translate($targetLocale, true) : null;
                        $viTrans = method_exists($item, 'translate') ? $item->translate('vi', false) : null;

                        $title = $trans?->title ?: ($item->title ?: ($viTrans?->title ?? ''));
                        $rawAnswer = $trans?->content
                            ?: ($trans?->description
                            ?: ($item->content
                            ?: ($item->description
                            ?: ($viTrans?->content ?: ($viTrans?->description ?: '')))));
                        $cleanAnswer = trim(strip_tags((string) $rawAnswer));

                        return [
                            'id' => $item->id,
                            'title' => $title,
                            'question' => $title,
                            'content' => $rawAnswer,
                            'answer' => $cleanAnswer !== '' ? $cleanAnswer : (string) $rawAnswer,
                        ];
                    })->values()->toArray();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $defaultFaqs;
    }

    public function transformDetails()
    {
        $categories = $this->categories
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());

        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'author' => $this->author,
            'published_at' => $this->published_at,
            'download' => $this->download,
            'link_video' => $this->link_video,
            'description' => $this->description,
            'content' => transform_richtext($this->content),
            'take_note' => transform_richtext($this->take_note),
            'category' => $this->category,
            'categories' => $categories,
            'breadcrumbs' => $this->getBreadcrumbsAttribute(),
            'image' => $this->getImageDetail($this->image),

            'images' => array_map(fn($img) => $this->getImageDetail($img), $this->images ?? []),

            'banner' => $this->getImageDetail($this->banner),
            'banner_mobile' => $this->getImageDetail($this->banner_mobile),
            'author_avatar' => $this->getImageDetail($this->author_avatar),

            'url' => $this->current_url,
            'link' => $this->link,
            'icon' => $this->icon,
            'price' => $this->price,
            'view_count' => (int) (($this->view_count ?? 0) + ($this->virtual_views ?? 0)),

            'author_name' => $this->author_name,
            'contact' => $this->contact,
            'industry' => $this->industry,
            'year' => $this->year,

            'products' => $this->products
                ->map(fn($item) => $item->transform()),
        ];

        return $data;
    }

    public function getImageDetail($image)
    {
        return [
            'url' => isset($image['path']) ? static_url($image['path']) : null,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function getBreadcrumbsAttribute()
    {
        $category = $this->categories()
            ->where('status', PostCategory::STATUS_ACTIVE)
            ->orderBy('parent_id', 'desc')
            ->first();

        return PostCategory::transformAsBreadcrumb($category);
    }

    public function getTags()
    {
        return $this->tags
            ->where('status', self::STATUS_ACTIVE)
            ->values()
            ->map(fn($item) => $item->transform());
    }

    public function related($condition = [])
    {
        $limit = empty($condition['limit']) ? 8 : $condition['limit'];

        $relatedPosts = $this->relatedPosts
            ->where('status', self::STATUS_ACTIVE);

        if (!empty($condition['has_category'])) {
            $relatedPosts = $relatedPosts->where(function ($collection) {
                return $collection->categories
                    ->where('status', self::STATUS_ACTIVE)
                    ->count() > 0;
            });
        }

        $relatedPosts = $relatedPosts->values()->take($limit);

        $relatedPostIds = [];

        if ($relatedPosts->count() > 0) {
            $relatedPostIds = $relatedPosts->pluck('id');
        }

        if (count($relatedPosts) < $limit) {
            $addPosts = self::query()
                ->active()
                ->when($this->category['id'] ?? false, function ($query) {
                    $query->whereHas('categories', function ($query) {
                        $query->where('post_categories.id', $this->category['id']);
                    });
                })
                ->where('id', '<>', $this->id)
                ->whereNotIn('id', $relatedPostIds)
                ->take($limit - count($relatedPosts))
                ->get();

            if (count($addPosts) > 0) {
                $relatedPosts = $relatedPosts->concat($addPosts);
            }
        }

        return $relatedPosts->map(fn($item) => $item->transform());
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC');
    }

    public function scopeOrderByHomePosition($query)
    {
        return $query->orderByRaw('CASE WHEN home_position IS NULL OR home_position = 0 THEN 1 ELSE 0 END, home_position ASC');
    }

    public function scopeOrderByFooterPosition($query)
    {
        return $query->orderByRaw('CASE WHEN footer_position IS NULL OR footer_position = 0 THEN 1 ELSE 0 END, footer_position ASC');
    }

    public function scopeWhereFooter($query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('footer_position')->where('footer_position', '>', 0);
        })
            ->orderByFooterPosition()
            ->orderBy('id', 'desc');
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $query->when($filters['tag'] ?? false, function (Builder $query, $value) {
            $query->whereHas('categories', function ($query) use ($value) {
                $query->whereHas('translations', function ($query) use ($value) {
                    $query->whereSlug($value);
                });
            });
        });

        $query->when($filters['sort'] ?? 'default', function (Builder $query, $value) {
            switch ($value) {
                case 'position':
                    $query->orderByPosition();
                    break;
                case 'home_position':
                    $query->orderByHomePosition();
                    break;
                default:
                    $query->orderBy('published_at', 'desc');
                    break;
            }
        });

        $query->orderBy('id', 'desc');

        $query->when($filters['keyword'] ?? false, function (Builder $query, $keyword) {
            $query->search($keyword);
        });

        $query->when($filters['limit'] ?? false, function (Builder $query, $value) {
            $query->take($value);
        });

        $query->when($filters['limit'] ?? false && !$filters['paginate'] ?? true, function (Builder $query, $value) {
            $query->take($value);
        });

        return $query;
    }

    public function getImageUrlAttribute()
    {
        return isset($this->image['path']) ? static_url($this->image['path']) : null;
    }

    protected static function newFactory()
    {
        return \Database\Factories\JamstackVietnam\Blog\Models\PostFactory::new();
    }
}
