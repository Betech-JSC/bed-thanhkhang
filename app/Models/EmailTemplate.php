<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Traits\Searchable;
use App\Traits\Translatable;

class EmailTemplate extends BaseModel
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
        'type',
        'status',
        'position',
    ];

    public $translatedAttributes = [
        'subject',
        'content',
    ];

    public function rules()
    {
        return [
            'type' => 'required|string',
            'status' => 'required|string',
        ];
    }

    protected $searchable = [
        'columns' => [
            'email_template_translations.subject' => 10,
            'email_templates.type' => 5,
        ],
        'joins' => [
            'email_template_translations' => ['email_template_translations.email_template_id', 'email_templates.id'],
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
