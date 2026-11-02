<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use App\Models\Post\PostCategory;
use App\Traits\HasCrudActions;

class ProjectCategoryController extends Controller
{
    use HasCrudActions;
    public $model = PostCategory::class;

    private function getTable()
    {
        return 'ProjectCategories';
    }

    private function beforeIndex($query)
    {
        return $query->where('type', 'PROJECT')
            ->orderBy('id', 'DESC');
    }
}
