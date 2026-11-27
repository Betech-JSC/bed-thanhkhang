<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Routing\Controller;

class HelperController extends Controller
{
    public function getLogs()
    {
        if (request()->has('file') && request()->input('file') !== null && request()->input('file') !== '') {
            $file = request()->input('file');

            if (!is_string($file) || basename($file) !== $file || !str_ends_with($file, '.log')) {
                abort(404);
            }

            $logsDir = realpath(storage_path('logs'));
            $realPath = realpath(storage_path('logs/' . $file));

            if (
                $logsDir === false ||
                $realPath === false ||
                !is_file($realPath) ||
                !str_starts_with($realPath, $logsDir . DIRECTORY_SEPARATOR)
            ) {
                abort(404);
            }

            return response(file_get_contents($realPath))
                ->header('Content-Type', 'text/plain');
        } else {
            $files = glob(storage_path('logs/*.log'));
            $files = collect($files)->map(function ($file) {
                return [
                    'file' => basename($file),
                    'url' => route('admin.helper.logs', ['file' => basename($file)])
                ];
            })->reverse()->take(10)->values();
            return response()->json($files);
        }
    }

    public function getModelData()
    {
        $model = request()->input('model');

        if ($const = request()->input('const')) {
            $model = $this->modelNamespace();
            $items = collect(constant($model . '::' . $const))
                ->map(function ($value, $key) {
                    return [
                        'id' => $key,
                        'label' => $value
                    ];
                })->values()->toArray();
        } else {
            $model = $this->modelNamespace();
            $model = new $model;
            $method = request()->input('method');

            if (request()->has('except_ids')) {
                $model = $model->whereNotIn('id', request()->input('except_ids'));
            }

            if ($params = request()->input('params')) {
                $items = $model->$method($params);
            } else {
                $items = $model->$method();
            }

            $items = $this->appendFields($items);
            $items = $this->onlyFields($items);
        };

        return $items;
    }

    public function reloadOctane()
    {
        shell_exec('php artisan octane:reload');
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function clearCache()
    {
        clear_cache();
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function modelNamespace($model = null)
    {
        $model = $model ?: request()->input('model');
        if (class_exists("\App\Models\\$model")) {
            return "\App\Models\\$model";
        } else if (class_exists("\JamstackVietnam\\Core\\Models\\$model")) {
            return "\JamstackVietnam\\Core\\Models\\$model";
        } else {
            return $model;
        }
    }

    // Private

    private function onlyFields($items)
    {
        if (request()->has('only')) {
            $onlyKeys = request()->input('only');
            return $items->map(function ($item) use ($onlyKeys) {
                if (is_array($item)) {
                    return \Illuminate\Support\Arr::only($item, $onlyKeys);
                }
                if (is_object($item) && method_exists($item, 'only')) {
                    return $item->only($onlyKeys);
                }
                return $item;
            });
        } else if (request()->input('method') === 'get') {
            $label = request()->input('label', 'name');
            return $items->map(function ($item) use ($label) {
                if (is_array($item)) {
                    return \Illuminate\Support\Arr::only($item, ['id', $label]);
                }
                if (is_object($item) && method_exists($item, 'only')) {
                    return $item->only(['id', $label]);
                }
                return $item;
            });
        }

        return $items;
    }

    private function appendFields($items)
    {
        if (request()->has('appends')) {
            $appends = request()->input('appends');
            return $items->map(function ($item) use ($appends) {
                if (is_object($item) && method_exists($item, 'append')) {
                    return $item->append($appends);
                }
                return $item;
            });
        }

        return $items;
    }

    public function setSessionLocale()
    {
        request()->session()->put('session_locale', request()->input('locale'));

        return request()->session()->get('session_locale');
    }
}
