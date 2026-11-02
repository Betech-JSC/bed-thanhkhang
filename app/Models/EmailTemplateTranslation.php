<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class EmailTemplateTranslation extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'email_template_id',
        'locale',
        'subject',
        'content',
    ];

    public function email_template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}
