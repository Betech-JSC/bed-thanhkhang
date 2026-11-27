<?php

namespace JamstackVietnam\Core\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\File;

class FileController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return $this->getData();
        }
        return Inertia::render('@Core/FileManager');
    }

    private function getData()
    {
        $file = new File(request()->input('path', '/'));

        return $file->items();
    }

    public function show(Request $request, $path = '/')
    {
        $cleanPath = ltrim((string) $path, '/');

        if (str_contains($cleanPath, '..') || str_contains($cleanPath, "\0")) {
            abort(404);
        }

        if (
            str_starts_with($cleanPath, 'images/') ||
            str_starts_with($cleanPath, 'assets/')
        ) {
            $realPath = realpath(public_path($cleanPath));
            $allowedRoots = array_filter([
                realpath(public_path('images')),
                realpath(public_path('assets')),
            ]);

            $isAllowed = false;
            if ($realPath !== false && is_file($realPath)) {
                foreach ($allowedRoots as $root) {
                    if (str_starts_with($realPath, $root . DIRECTORY_SEPARATOR)) {
                        $isAllowed = true;
                        break;
                    }
                }
            }

            if ($isAllowed) {
                return response()->file($realPath);
            }

            abort(404);
        }

        $file = new File($path);
        return $file->findOrFail($request->all());
    }

    public function store(Request $request)
    {
        $files = $request->file('files');

        $file = new File($request->input('path', '/'));
        return $file->store($files);
    }

    public function destroy(Request $request)
    {
        $file = new File();
        return $file->delete($request->input('files'));
    }

    public function folderCreate(Request $request)
    {
        $file = new File($request->input('path', '/'));
        return $file->folderCreate($request->input('name'));
    }

    public function folderDelete(Request $request)
    {
        $file = new File($request->input('path', '/'));
        return $file->folderDelete();
    }

    public function rename(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'newName' => 'required|string|max:255',
        ]);

        $file = new File();
        return $file->rename(
            $request->input('path'),
            $request->input('newName')
        );
    }

    public function copy(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.path' => 'required|string',
            'items.*.type' => 'required|in:file,dir',
            'destinationPath' => 'required|string',
        ]);

        $file = new File();
        return $file->batchCopy(
            $request->input('items'),
            $request->input('destinationPath')
        );
    }

    public function move(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.path' => 'required|string',
            'items.*.type' => 'required|in:file,dir',
            'destinationPath' => 'required|string',
        ]);

        $file = new File();
        return $file->batchMove(
            $request->input('items'),
            $request->input('destinationPath')
        );
    }
}
