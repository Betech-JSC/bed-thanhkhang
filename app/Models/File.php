<?php

namespace App\Models;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;
use Iman\Streamer\VideoStreamer;
use Image;

class File
{
    protected $path;
    protected $disk;
    protected $storage;
    protected $publicStorage;
    protected $contents;

    public const MAX_SIZE_LIST = [
        'image' => 5,
        'video' => 50,
        'application' => 100,
        'others' => 10,
    ];

    public function __construct($path = '/', $disk = null)
    {
        $this->path = $path;
        $this->disk = $disk ?? 'uploads';
        $this->storage = Storage::disk($this->disk);
        $this->publicStorage = Storage::disk('public');
    }

    public function items()
    {
        $this->contents = collect($this->storage->listContents($this->path));

        $tree = $this->tree();
        $directories = $this->directories();
        $files = $this->files();

        return compact('tree', 'directories', 'files');
    }

    public function tree()
    {
        $rootPath = $this->storage->path('/');
        $flatItems = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $tree = [];
        foreach ($flatItems as $item) {
            if (
                !$item->isDir() ||
                $this->firstCharIs($item->getFilename(), '.')
            ) continue;

            $path = [$item->getFilename() => []];

            for ($depth = $flatItems->getDepth() - 1; $depth >= 0; $depth--) {
                $path = [$flatItems->getSubIterator($depth)->current()->getFilename() => $path];
            }
            $tree = array_merge_recursive($tree, $path);
        }

        return $this->transformTree($tree);
    }

    public function transformTree($item, $name = null, $path = null)
    {
        $itemChildren = collect($item)
            ->map(fn($subItem, $subKey) => $this->transformTree($subItem, $subKey, implode('/', [$path, $subKey])))
            ->filter()
            ->sortBy('path')
            ->keyBy('path')
            ->values()
            ->toArray();

        if (is_null($path)) {
            return [[
                'slug' => Str::slug('/') .  '-' . generate_code(5),
                'name' => 'File Manager',
                'label' => 'File Manager',
                'path' => '/',
                'children' => $itemChildren,
            ]];
        }

        return [
            'slug' => Str::slug($path) .  '-' . generate_code(5),
            'name' => $name,
            'label' => $name,
            'path' => $path,
            'children' => $itemChildren,
        ];
    }

    public function directories()
    {
        return $this->contents
            ->filter(fn($item) => $item->isDir())
            ->values()
            ->toArray();
    }

    public function files()
    {
        $files = $this->contents
            ->filter(fn($item) => $item->isFile())
            ->values()
            ->map(fn($item) => $this->transformFile($item))
            ->reject(fn($item) => $this->firstCharIs($item['filename'], '.'))
            ->sortByDesc('last_modified')
            ->keyBy('path');

        if (request()->has('keyword')) {
            $files = $files->filter(fn($item) => str_contains($item['search_name'], (request()->input('keyword'))));
        } else if (request()->has('limit')) {
            $page = request()->input('page') ?? 1;
            $limit = request()->input('limit');

            return $files->skip(($page - 1) * $limit)->take($limit);
        }

        return $files;
    }

    public function findOrFail($options = [])
    {
        try {
            if ($this->isVideo()) {
                return $this->responseStreamingVideo();
            }

            if ($this->isPdf()) {
                return $this->responsePdf($options);
            }

            if ($this->isImage()) {
                return $this->responseImage($options);
            }

            return $this->responseDefault();
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());

            return response($exception->getMessage(), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function store($files)
    {
        $successFiles = [];
        $failureFiles = [];

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();

            if ($this->fileValidation($file)) {
                $filePath = $this->storage->putFileAs(
                    $this->path,
                    $file,
                    $fileName
                );
                $successFiles[] = static_url($filePath, [], false);

                if (!$filePath) {
                    logger('Store file');
                    logger($file);
                    logger("Disk: $this->disk");
                    logger("Folder: $this->path");
                    logger("File name: $fileName");
                    logger('End store file');
                }
            } else {
                $failureFiles[] = $fileName;
            }
        }
        return [
            'successFiles' => $successFiles,
            'failureFiles' => $failureFiles,
        ];
    }

    public function storeFromUrl($url)
    {
        try {
            $file = file_get_contents($url);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($file);

            $extension = explode('/', $mime)[1] ?? 'png';
            if (!in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif', 'tiff'])) {
                logger()->error('Can not store image: ' . $url);
                return false;
            }

            $filePath = Str::slug(urldecode(pathinfo($url)['filename'])) . '.' . $extension;

            if ($this->path != '/') {
                $filePath = $this->path . '/' . $filePath;
            }

            $this->storage->put($filePath, $file);

            return $filePath;
        } catch (\Throwable $th) {
            logger()->error('Can not store image: ' . $url);
            logger()->error($th->getMessage());
            return false;
        }
    }

    public function delete($items)
    {
        $deletedItems = [];

        foreach ($items as $item) {
            if (!$this->storage->exists($item['path'])) {
                continue;
            } else {
                if ($item['type'] === 'dir') {
                    $this->storage->deleteDirectory($item['path']);

                    $cacheFolder = 'cache/' . $item['path'];

                    $this->publicStorage->deleteDirectory($cacheFolder);
                } else {
                    $this->storage->delete($item['path']);
                    $cacheFolder = 'cache/' . str_replace('.', '_', $item['path']);

                    $this->publicStorage->deleteDirectory($cacheFolder);
                }
            }

            $deletedItems[] = $item;
        }

        return $deletedItems;
    }

    public function folderCreate($name)
    {
        $pathName = rtrim($this->path, '/') . '/' . ltrim($name, '/');

        if ($this->storage->exists($pathName)) {
            return false;
        }

        return (bool) $this->storage->makeDirectory($pathName);
    }

    public function folderDelete()
    {
        if (collect($this->storage->listContents($this->path))->count() > 0) {
            return false;
        }

        return (bool) $this->storage->deleteDirectory($this->path);
    }

    protected function responsePdf()
    {
        $filePath = $this->getFullPath();
        if (isset($options['download'])) {
            return response()->download($filePath, basename($filePath));
        }

        return $this->responseDefault();
    }

    protected function responseStreamingVideo(): bool
    {
        return VideoStreamer::streamFile($this->getFullPath());
    }

    public const ALLOWED_IMAGE_FORMATS = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
    public const MIN_IMAGE_WIDTH = 16;
    public const MAX_IMAGE_WIDTH = 2560;

    public static function sanitizeImageOptions($options): array
    {
        $options = is_array($options) ? $options : [];

        $rawFormat = isset($options['fm']) && is_scalar($options['fm'])
            ? strtolower(trim((string) $options['fm']))
            : 'webp';
        $format = in_array($rawFormat, self::ALLOWED_IMAGE_FORMATS, true) ? $rawFormat : 'webp';

        $sanitized = ['fm' => $format];

        if (isset($options['w']) && is_scalar($options['w']) && is_numeric($options['w'])) {
            $width = (int) $options['w'];
            $sanitized['w'] = max(self::MIN_IMAGE_WIDTH, min(self::MAX_IMAGE_WIDTH, $width));
        }

        return $sanitized;
    }

    protected function responseImage($options)
    {
        $pathinfo = pathinfo($this->path);
        $options = static::sanitizeImageOptions($options);

        $newFilename = implode('_', $options);

        $cacheFolder = 'cache/' . $pathinfo['dirname'] . '/' . str_replace('.', '_', $pathinfo['basename']);
        $cacheFilename = $pathinfo['filename'] . '_' . $newFilename . '.' . $options['fm'];
        $cacheFullPath = $cacheFolder . '/' . $cacheFilename;

        $contentType = 'image/' . ($options['fm'] === 'jpg' ? 'jpeg' : $options['fm']);

        if (!$this->publicStorage->exists($cacheFullPath)) {
            // Create cache folder if it doesn't exist
            $this->publicStorage->makeDirectory($cacheFolder, 0755, true);

            $imagePath = $this->storage->path($this->path);

            // Check if the image has been modified
            $lastModified = filemtime($imagePath);
            $etag = md5_file($imagePath);
            $headers = [
                'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                'ETag' => $etag,
            ];
            if (
                request()->headers->has('If-Modified-Since') &&
                strtotime(request()->headers->get('If-Modified-Since')) >= $lastModified
            ) {
                return response()->make('', 304, $headers);
            }
            if (
                request()->headers->has('If-None-Match') &&
                request()->headers->get('If-None-Match') == $etag
            ) {
                return response()->make('', 304, $headers);
            }

            $image = Image::make($imagePath);

            if (isset($options['w'])) {
                $image->resize($options['w'], null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $image
                ->encode($options['fm'], 100)
                ->save($this->publicStorage->path($cacheFullPath));

            // Set caching headers
            $headers = [
                'Content-Type' => $contentType,
                'Cache-Control' => 'max-age=86400',
                'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                'ETag' => $etag,
            ];
        } else {
            // Set caching headers
            $headers = [
                'Content-Type' => $contentType,
                'Cache-Control' => 'max-age=86400',
            ];
        }

        $imageData = $this->publicStorage->get($cacheFullPath);

        return response()
            ->make($imageData, 200, $headers);
    }


    protected function responseDefault()
    {
        return response()
            ->make($this->getFileData(), 200)
            ->header('Content-Type', $this->getMimeType());
    }

    protected function getFullPath(): string
    {
        return $this->storage->path($this->path);
    }

    protected function getFileData()
    {
        return $this->storage->get($this->path);
    }

    protected function getSize(): string
    {
        return $this->storage->size($this->path);
    }

    protected function getMimeType(): string
    {
        return $this->storage->mimeType($this->path);
    }

    protected function isPdf(): bool
    {
        return str_contains($this->path, '.pdf');
    }

    protected function isImage(): bool
    {
        $mimeType = $this->getMimeType();
        return str_contains($mimeType, 'image/') && $mimeType !== 'image/heic' && !str_contains($this->path, '.svg') && !str_contains($this->path, '.gif');
    }

    protected function isVideo(): bool
    {
        return str_contains($this->path, '.mp4');
    }

    private function formatBytes($size)
    {
        $base = log($size) / log(1024);
        $suffix = array("bytes", "KB", "MB", "GB", "TB")[floor($base)];
        return round(pow(1024, $base - floor($base)), 2) .  ' ' . $suffix;
    }

    private function firstCharIs($string, $char)
    {
        return mb_substr($string, 0, 1) === $char;
    }

    private function fileValidation($file)
    {
        $blockedExtensions = [
            'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht',
            'shtml', 'htaccess', 'sh', 'bash', 'exe', 'bat', 'cmd', 'jsp', 'asp', 'aspx',
        ];
        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension !== '' && in_array($extension, $blockedExtensions, true)) {
            return false;
        }

        $mimeType = strtolower((string) $file->getMimeType());
        if (str_contains($mimeType, 'php') || str_contains($mimeType, 'x-executable') || str_contains($mimeType, 'x-sh')) {
            return false;
        }

        $maxSize = self::MAX_SIZE_LIST['others'];
        foreach (self::MAX_SIZE_LIST as $key => $size) {
            if (str_contains($mimeType, $key)) {
                $maxSize = $size;
            }
        }

        return $file->getSize() / 1024 / 1024 <= $maxSize;
    }

    private function transformFile($item)
    {
        $metadata = $item->jsonSerialize();
        $filename = basename($metadata['path']);

        return array_merge($metadata, [
            'search_name' => str_replace('-', ' ', Str::slug($filename)),
            'filename' => $filename,
            'extension' => pathinfo($metadata['path'], PATHINFO_EXTENSION),
            'static_url' => $this->storage->url($metadata['path']),
            'formatted_file_size' => $this->formatBytes($metadata['file_size']),
        ]);
    }
}
