<?php

namespace JamstackVietnam\Slider\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Translatable;
use JamstackVietnam\Core\Traits\Searchable;

class Slider extends BaseModel
{
    use HasFactory, SoftDeletes, Searchable, Translatable;

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Hiển thị',
        self::STATUS_INACTIVE => 'Ẩn',
    ];

    public $with = ['translations'];

    protected $searchable = [
        'columns' => [
            'slider_translations.title' => 10,
        ],
        'joins' => [
            'slider_translations' => ['slider_translations.slider_id', 'sliders.id'],
        ],
    ];

    public $fillable = [
        'status',
        'position_display',
        'position_sort',
        'started_at',
        'ended_at',
        'is_video'
    ];

    public $translatedAttributes = [
        'title',
        'subtitle',
        'description',
        'primary_cta_text',
        'primary_cta_link',
        'secondary_cta_text',
        'secondary_cta_link',
        'link',
        'target',
        'image_mobile',
        'image',
        'custom_fields'
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
        ];
    }

    protected static function booted()
    {
        static::saved(function () {
            static::clearSliderCaches();
        });

        static::deleted(function () {
            static::clearSliderCaches();
        });
    }

    public static function clearSliderCaches(): void
    {
        $locales = config('app.locales', ['vi', 'en']);
        foreach ($locales as $locale) {
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('inertia_layout_shared_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('history_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('process_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('products_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('documents_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('documents_library_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('galleries_index_' . $locale);
        }
    }

    public function getFormattedPositionDisplayAttribute()
    {
        return config('slider.positions.' . $this->position_display . '.title');
    }

    public function scopeActive($query)
    {
        return $query->whereLocaleActive()->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAvailable($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($q) {
                $q->whereNull('started_at')
                    ->orWhere('started_at', '')
                    ->orWhereDate('started_at', '<=', now());
            })->where(function ($q) {
                $q->whereNull('ended_at')
                    ->orWhere('ended_at', '')
                    ->orWhereDate('ended_at', '>=', now());
            });
        });
    }

    public function getAvailableAttribute(): bool
    {
        $started_at = $this->started_at;
        $ended_at = $this->ended_at;
        return (is_null($started_at) || $started_at == "" || $started_at <= now()) &&
            (is_null($ended_at) || $ended_at == "" || $ended_at >= now()->subDays(1)) &&
            $this->status == self::STATUS_ACTIVE;
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle ?? $this->custom_fields['subtitle'] ?? null,
            'description' => $this->description,
            'primary_cta_text' => $this->primary_cta_text ?? $this->custom_fields['primary_cta_text'] ?? null,
            'primary_cta_link' => $this->primary_cta_link ?? $this->custom_fields['primary_cta_link'] ?? null,
            'secondary_cta_text' => $this->secondary_cta_text ?? $this->custom_fields['secondary_cta_text'] ?? null,
            'secondary_cta_link' => $this->secondary_cta_link ?? $this->custom_fields['secondary_cta_link'] ?? null,
            'link' => $this->link,
            'is_video' => $this->is_video === 1 ? true : false,
            'position_display' => $this->position_display,
            'target' => $this->target,
            'custom_fields' => $this->custom_fields,
            'image' => $this->imageDetail($this->image),
            'image_mobile' => $this->imageDetail($this->image_mobile),
        ];
    }

    public function imageDetail($image)
    {
        return [
            'url' => isset($image['path']) ? static_url($image['path']) : null,
            'alt' => $image['alt'] ?? strip_tags($this->title),
        ];
    }

    public function scopeSortByPosition($query)
    {
        return $query->orderByRaw('ISNULL(position_sort) OR position_sort = 0, position_sort ASC')
            ->orderBy('id', 'desc');
    }

    public static function getByPosition($positionDisplay)
    {
        $query = self::query()
            ->active()
            ->where('position_display', $positionDisplay)
            ->available()
            ->orderBy('position_sort', 'desc')
            ->orderBy('id', 'desc');

        if (config('slider.positions.' . $positionDisplay . '.banner')) {
            return $query->first()?->transform();
        } else {
            return $query->get()
                ->map(fn($item) => $item->transform());
        }
    }
}
