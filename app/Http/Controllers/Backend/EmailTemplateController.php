<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use App\Models\EmailTemplate;
use App\Traits\HasCrudActions;

class EmailTemplateController extends Controller
{
    use HasCrudActions;
    public $model = EmailTemplate::class;

    private function getTable()
    {
        return 'EmailTemplates';
    }

    private function beforeIndex($query)
    {
        return $query->orderBy('id', 'DESC');
    }
}
