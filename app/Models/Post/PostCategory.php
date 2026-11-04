<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Searchable;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Route;

class PostCategory extends BaseModel
{
    use HasFactory, Translatable, SoftDeletes, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const TYPE_POST = 'POST';
    public const TYPE_PROJECT = 'PROJECT';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public const TYPE_LIST = [
        self::TYPE_POST => 'Bài viết',
    ];

    public $with = ['translations'];

    protected $appends = ['url'];

    public $fillable = [
        'parent_id',
        'status',
        'type',
        'position',
        'menu_position',
        'view_count',
        'image',
        'icon'
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'sliders',

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
    ];

    protected $searchable = [
        'columns' => [
            'post_category_translations.title' => 10,
            'post_category_translations.id' => 5,
            'post_category_translations.slug' => 5,
        ],
        'joins' => [
            'post_category_translations' => [
                'post_category_translations.post_category_id',
                'post_categories.id'
            ],
        ],
    ];

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
        ];
    }

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (request()->route() === null) return;
            $model->view_count = request()->input('view_count', 0);
        });

        static::saved(function (self $model) {
            static::clearPostCategoryCaches();
        });

        static::deleted(function (self $model) {
            static::clearPostCategoryCaches();
        });
    }

    public static function clearPostCategoryCaches(): void
    {
        $locales = config('app.locales', ['vi', 'en']);
        foreach ($locales as $locale) {
            Cache::forget('resources_index_' . $locale);
            Cache::forget('courses_index_' . $locale);
            Cache::forget('posts_index_' . $locale);
            Cache::forget('projects_index_' . $locale);
            Cache::forget('prizes_index_' . $locale);
            Cache::forget('home_page_data_' . $locale);
            Cache::forget('inertia_layout_shared_data_' . $locale);

            for ($i = 1; $i <= 5; $i++) {
                Cache::forget('projects_index_' . $locale . '_page_' . $i);
                Cache::forget('prizes_index_' . $locale . '_page_' . $i);
            }
        }
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function getRoot()
    {
        return static::orderBy('position', 'desc')
            ->with(['nodes'])
            ->orderBy('parent_id')
            ->where(function ($query) {
                $query->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->get();
    }

    public static function getParentNodes()
    {
        return static::whereNull('parent_id')->orWhere('parent_id', 0)->get();
    }

    public static function getNav()
    {
        return static::query()
            ->active()
            ->with('children.children')
            ->where('parent_id', 0)
            ->get();
    }

    public function nodes()
    {
        return $this->children()
            ->with('nodes')
            ->orderBy('parent_id')
            ->orderBy('id', 'desc');
    }

    public function parentNode()
    {
        return $this->parent()->with('parentNode');
    }

    public function scopeActive($query)
    {
        return $query->whereLocaleActive()->where('status', self::STATUS_ACTIVE);
    }

    public static function transformAsBreadcrumb($category)
    {
        if ($category) {
            return collect(self::flatAncestors($category))
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'slug' => $item->seo_slug ?? $item->slug,
                    ];
                })
                ->reverse()->values();
        }
        return collect([]);
    }

    public static function flatAncestors($model): array
    {
        if (!$model) return [];

        $result = [];
        $visited = [];
        $curr = $model;

        while ($curr) {
            if (in_array($curr->id, $visited)) {
                break; // Prevent infinite loop on circular parent-child relations
            }
            $visited[] = $curr->id;
            $result[] = $curr;

            if ($curr->parent_id > 0) {
                $curr = self::find($curr->parent_id);
            } else {
                $curr = null;
            }
        }

        return $result;
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        $default_locale = config('app.locale');

        if ($this->status == self::STATUS_ACTIVE) {
            if (Route::has($default_locale . ".posts.category")) {
                foreach ($this->translations as $translation) {
                    $urls[strtoupper($translation->locale)] = route("$translation->locale.posts.category", [
                        'slug' => $translation->seo_slug ?? $translation->slug,
                    ]);
                }
            }
        }
        return $urls;
    }
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            'post_ref_categories',
            'post_category_id',
            'post_id'
        );
    }

    public function getPosts()
    {
        return static::query()
            ->active()
            ->where('type', self::TYPE_POST)
            ->get()
            ->map(fn($item) => $item->transform());
    }

    public function getProject()
    {
        return static::query()
            ->active()
            ->where('type', self::TYPE_PROJECT)
            ->get()
            ->map(fn($item) => $item->transform());
    }

    public function transform()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'image' => $this->getImageDetail(),
            'icon' => $this->icon,
            'type' => $this->type,
            'url' => $this->current_url,
        ];

        return $data;
    }

    public function transformDetails()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'breadcrumbs' => self::transformAsBreadcrumb($this),
            'image' => $this->getImageDetail(),
            'url' => $this->current_url
        ];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function getImageDetail()
    {
        return [
            'url' => isset($this->image['path']) ? static_url($this->image['path']) : null,
            'alt' => $this->image['alt'] ?? $this->title,
        ];
    }

    public function scopeWithPosts($query, $limit)
    {
        return $query->with('posts')
            ->whereHas('posts', function ($query) use ($limit) {
                $query->active()->limit($limit);
            });
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderByRaw('CASE WHEN position IS NULL OR position = 0 THEN 1 ELSE 0 END, position ASC');
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $query->orderByPosition()
            ->orderBy('id', 'desc');

        $query->orderBy('id', 'desc');

        $query->when($filters['limit'] ?? false, function (Builder $query, $value) {
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
        return \Database\Factories\JamstackVietnam\Blog\Models\PostCategoryFactory::new();
    }
}
