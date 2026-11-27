<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Traits\Sluggable;

class ProductTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    protected array $pendingOldSlugs = [];

    protected static function booted()
    {
        static::saving(function (self $translation) {
            $oldSlugs = array_filter([
                $translation->getOriginal('slug'),
                $translation->getOriginal('seo_slug'),
            ]);
            $translation->pendingOldSlugs = array_values(array_unique($oldSlugs));
        });

        static::saved(function (self $translation) {
            $extraSlugs = $translation->pendingOldSlugs;
            $translation->pendingOldSlugs = [];
            Product::clearProductCaches(null, $extraSlugs);
        });

        static::deleted(function (self $translation) {
            $extraSlugs = array_values(array_unique(array_filter([
                $translation->slug,
                $translation->seo_slug,
                $translation->getOriginal('slug'),
                $translation->getOriginal('seo_slug'),
            ])));
            Product::clearProductCaches(null, $extraSlugs);
        });
    }

    public $fillable = [
        'slug',
        'locale',
        'title',
        'description',

        'specification',
        "info_other",
        'summary',
        'content',
        'ingredient',
        'promotion',

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
        'questions' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
