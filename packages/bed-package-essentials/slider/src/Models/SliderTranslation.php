<?php

namespace JamstackVietnam\Slider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;

class SliderTranslation extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'slider_id',
        'locale',
        'title',
        'subtitle',
        'description',
        'primary_cta_text',
        'primary_cta_link',
        'secondary_cta_text',
        'secondary_cta_link',
        'link',
        'target',
        'image',
        'image_mobile',
        'custom_fields'
    ];

    public $casts = [
        'image_mobile' => 'array',
        'image' => 'array',
        'custom_fields' => 'array'
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
