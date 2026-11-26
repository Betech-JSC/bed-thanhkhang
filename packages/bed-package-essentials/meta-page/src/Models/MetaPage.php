<?php

namespace JamstackVietnam\MetaPage\Models;
use JamstackVietnam\Core\Models\BaseModel;

class MetaPage extends BaseModel
{
    public $fillable = [
        'url',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',

        'inject_head',
        'inject_body_start',
        'inject_body_end'
    ];

    public $rules = [
        'url' => 'required',
    ];

    protected static function booted()
    {
        static::saved(function () {
            static::clearMetaPageCaches();
        });

        static::deleted(function () {
            static::clearMetaPageCaches();
        });
    }

    public static function clearMetaPageCaches(): void
    {
        try {
            \Illuminate\Support\Facades\Cache::tags(['meta_pages'])->flush();
        } catch (\Throwable $e) {
            // Ignore if cache driver does not support tags
        }

        $locales = config('app.locales', ['vi', 'en']);
        foreach ($locales as $locale) {
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('history_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('products_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('documents_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('contact_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('process_index_' . $locale);
        }
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'seo_meta_title' => $this->seo_meta_title,
            'seo_slug' => $this->seo_slug,
            'seo_meta_description' => $this->seo_meta_description,
            'seo_meta_keywords' => $this->seo_meta_keywords,
            'seo_meta_robots' => $this->seo_meta_robots,
            'seo_canonical' => $this->seo_canonical,
            'seo_image' => $this->seo_image,
            'seo_schemas' => $this->seo_schemas,

            'inject_head' => $this->inject_head,
            'inject_body_start' => $this->inject_body_start,
            'inject_body_end' => $this->inject_body_end
        ];
    }

    public function transformDetails()
    {
        return $this->transform();
    }
}
