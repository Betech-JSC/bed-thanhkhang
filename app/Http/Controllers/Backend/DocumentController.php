<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post\Post;
use App\Traits\HasCrudActions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentController extends Controller
{
    use HasCrudActions;

    public $model = Post::class;

    public $with = [
        'form' => ['categories'],
    ];

    private function getTable($model = null)
    {
        return 'documents';
    }

    private function folder()
    {
        return 'Documents';
    }

    private function beforeIndex($query)
    {
        return $query->where('type', Post::TYPE_DOCUMENT)
            ->orderBy('id', 'DESC');
    }

    private function beforeStore(Request $request, $rules)
    {
        $request->merge(['type' => Post::TYPE_DOCUMENT]);
        return $rules;
    }
}
