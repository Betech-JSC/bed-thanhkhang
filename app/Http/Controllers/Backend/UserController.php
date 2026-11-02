<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\Routing\Controller;
use App\Traits\HasCrudActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    use HasCrudActions;
    public $model = User::class;
    public function searchUsers(Request $request)
    {
        $q = trim($request->input('q', ''));

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json($users);
    }

    public function form($id = null)
    {
        $this->checkAuthorize();

        $item = $this->model();

        $emptyFields = $this->getEmptyFields();

        $breadcrumbs = [[
            'url' => route($this->getResource() . '.index'),
            'name' => 'models.table_list.' . $this->getTable(),
        ]];

        if (!empty($id)) {
            $item = $this->loadRelations($item);

            if (!is_null($item->getMacro('withTrashed'))) {
                $item = $item->withTrashed();
            }

            $item = $item->findOrFail($id);
            $item = $this->setAppends($item);
            
            // Use transformDetails to get complete details
            $itemData = method_exists($item, 'transformDetails') ? $item->transformDetails() : $item->toArray();

            $itemData = array_merge($emptyFields, $itemData);
            $breadcrumbs[] = [
                'url' => url()->current(),
                'name' => 'Chỉnh sửa Học viên',
            ];
        } else {
            $itemData = $emptyFields;
            $breadcrumbs[] = [
                'url' => url()->current(),
                'name' => 'Tạo Học viên',
            ];
        }

        $roles = collect(\App\Models\User::ROLE_LIST)
            ->map(fn($label, $value) => compact('value', 'label'))
            ->values();

        return Inertia::render($this->folder() . '/Form', [
            'item' => $itemData,
            'breadcrumbs' => $breadcrumbs,
            'schema' => $this->getSchema(),
            'roles' => $roles,
        ]);
    }

    private function beforeStore(Request $request, $rules)
    {
        $id = request()->route('id');

        if ($id) {
            $rules['password'] = 'nullable|string|min:6';
        }

        // Validate profile fields
        $rules['role'] = 'nullable|string|max:50';
        $rules['phone'] = 'nullable|string|max:50';
        $rules['address'] = 'nullable|string|max:500';
        $rules['note'] = 'nullable|string|max:2000';

        // Loại bỏ các quy tắc kiểm tra (validation rules) gây lỗi
        unset($rules['points']);
        unset($rules['banner']);
        unset($rules['avatar']);

        return $rules;
    }
}
