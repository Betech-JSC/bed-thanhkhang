<?php

namespace JamstackVietnam\Core\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    use HasCrudActions;

    public $model = Setting::class;

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    public function form($id = null)
    {
        $this->checkAuthorize($id ? 'edit' : 'create');

        if (!config('core.setting.form.' . $id . '.enable', true)) {
            $id = config('core.setting.id_default', null);

            if (!empty($id)) {
                return redirect()->route('admin.settings.form', ['id' =>  $id]);
            }
            else {
                return redirect()->route(config('core.setting.route_default', 'admin.dashboard.index'));
            }
        }

        $settingName =  Str::studly($id);

        $breadcrumbs = [
            [
                'url' => route($this->getResource() . '.index'),
                'name' => __('models.table_list.' . $this->getTable(), [], current_locale()),
            ],
            [
                'url' => route($this->getResource() . '.form', ['id' => $id]),
                'name' => __('models.table_list.' . $settingName, [], current_locale()),
            ]
        ];

        $item = settings()->group($id)->all();
        $item['id'] = $id;

        if (request()->wantsJson()) {
            return response()->json($item);
        }

        return Inertia::render($this->folder() . '/' . $settingName, [
            'item' => $item,
            'breadcrumbs' => $breadcrumbs,
            'schema' => $this->getSchema(),
            'setting_bar' => setting_bar(),
        ]);
    }

    public function store(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');
        $this->checkAuthorize($id ? 'update' : 'store');

        $rules = $this->model::rules($id);

        $validated = $request->validate($rules);

        foreach($validated as $key => $data) {
            if (is_array($data)) {
                $validated[$key] = json_encode($data);
            } elseif (in_array($key, ['map_iframe', 'general_map_iframe'], true) && is_string($data)) {
                if (str_contains(mb_strtolower($data), '<iframe') && preg_match('/src=["\']([^"\']+)["\']/i', $data, $matches)) {
                    $validated[$key] = trim($matches[1]);
                }
            }
        }

        settings()->group($id)->set($validated);

        if (class_exists(\App\Models\Config::class) && method_exists(\App\Models\Config::class, 'clearBrandCaches')) {
            \App\Models\Config::clearBrandCaches();
        }

        if (in_array($id, ['smtp', 'notification'])) {
            Artisan::call('queue:restart');
        }

        return $this->redirectBack( __('models.has_crud_action.store', [], current_locale()));
    }
}
