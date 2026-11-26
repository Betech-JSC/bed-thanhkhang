<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post\PostCategory;
use App\Traits\HasCrudActions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentCategoryController extends Controller
{
    use HasCrudActions;

    public $model = PostCategory::class;

    private function getTable($model = null)
    {
        return 'document-categories';
    }

    private function folder()
    {
        return 'DocumentCategories';
    }

    private function beforeIndex($query)
    {
        return $query->where('type', PostCategory::TYPE_DOCUMENT)
            ->orderBy('id', 'DESC');
    }

    private function beforeStore(Request $request, $rules)
    {
        $request->merge(['type' => PostCategory::TYPE_DOCUMENT]);
        return $rules;
    }
}
