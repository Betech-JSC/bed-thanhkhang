<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;
use Iman\Streamer\VideoStreamer;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\UploadedFile;

class File
{
    protected $path;
    protected $disk;
    protected $storage;
    protected $publicStorage;
    protected $contents;

    public const MAX_SIZE_LIST = [
        'image' => 10,
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
            ->map(fn($item) => $this->transformDirectory($item))
            ->values()
            ->toArray();
    }

    private function transformDirectory($item)
    {
        $metadata = $item->jsonSerialize();
        $foldername = basename($metadata['path']);

        return array_merge($metadata, [
            'search_name' => str_replace('-', ' ', Str::slug($foldername)),
            'filename' => $foldername,
            'extension' => '',
            'static_url' => '',
            'formatted_file_size' => '-',
            'type' => 'dir',
        ]);
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

        // Đảm bảo $files là mảng
        if (!is_array($files) && !($files instanceof \Traversable)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $startTime = microtime(true);

            // Kiểm tra là instance UploadedFile
            if (!($file instanceof UploadedFile)) {
                $msg = 'Invalid upload item (not UploadedFile)';
                logger()->warning($msg, ['item' => is_object($file) ? get_class($file) : (string)$file]);
                $failureFiles[] = is_object($file) ? get_class($file) : (string)$file;
                continue;
            }

            $clientName = $file->getClientOriginalName();
            $clientSize = $file->getSize(); // bytes
            $clientSizeMB = round($clientSize / 1024 / 1024, 2);
            $clientMime = $file->getClientMimeType();
            $originalName = pathinfo($clientName, PATHINFO_FILENAME);
            $fileName = $originalName . '.webp';
            $contextBase = [
                'client_name' => $clientName,
                'size_bytes' => $clientSize,
                'size_mb' => $clientSizeMB,
                'mime' => $clientMime,
                'target_disk' => $this->disk,
                'target_folder' => $this->path,
            ];

            // Log start
            logger()->info('Start processing uploaded file', $contextBase);

            // Validate before processing (reuse fileValidation if exists)
            try {
                if (method_exists($this, 'fileValidation')) {
                    $valid = $this->fileValidation($file);
                    if (!$valid) {
                        logger()->warning('File validation failed (mime/size)', $contextBase);
                        $failureFiles[] = $clientName;
                        continue;
                    }
                } else {
                    // fallback: basic mime check
                    if (strpos($clientMime, 'image/') !== 0) {
                        logger()->warning('File is not image, skipped', $contextBase);
                        $failureFiles[] = $clientName;
                        continue;
                    }
                }

                // Load image
                $image = Image::make($file->getRealPath());

                // Resize if too large in dimensions
                $maxDimension = 3000;
                if ($image->width() > $maxDimension || $image->height() > $maxDimension) {
                    logger()->info('Resizing image for memory/performance', $contextBase + [
                        'orig_width' => $image->width(),
                        'orig_height' => $image->height(),
                        'max_dimension' => $maxDimension,
                    ]);
                    $image->resize($maxDimension, $maxDimension, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Nén & encode webp loop
                $targetBytes = 300 * 1024; // 300 KB
                $quality = 90;
                $minQuality = 20;
                $attempts = 0;
                $maxAttempts = 8;

                $contents = (string) $image->encode('webp', $quality);

                while (strlen($contents) > $targetBytes && $quality > $minQuality && $attempts < $maxAttempts) {
                    $attempts++;
                    $quality = max($minQuality, $quality - 10);
                    $contents = (string) $image->encode('webp', $quality);

                    if (strlen($contents) > $targetBytes && $quality <= $minQuality) {
                        $image->resize(
                            intval($image->width() * 0.9),
                            intval($image->height() * 0.9),
                            function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            }
                        );
                        $contents = (string) $image->encode('webp', $quality);
                    }
                }

                // Build final path
                $path = rtrim($this->path, '/') . '/' . $fileName;

                // Before writing, log memory usage
                $memoryBefore = memory_get_usage();
                $peakBefore = memory_get_peak_usage();

                logger()->debug('Attempting to store file to storage', $contextBase + [
                    'target_path' => $path,
                    'encoded_size' => strlen($contents),
                    'quality_used' => $quality,
                    'attempts' => $attempts,
                    'memory_before' => $memoryBefore,
                    'peak_before' => $peakBefore,
                ]);

                $stored = $this->storage->put($path, $contents);

                if ($stored) {
                    $successFiles[] = static_url($path, [], false);
                    $timeMs = round((microtime(true) - $startTime) * 1000, 2);
                    logger()->info('Stored file successfully', $contextBase + [
                        'target_path' => $path,
                        'final_size_bytes' => strlen($contents),
                        'quality' => $quality,
                        'attempts' => $attempts,
                        'time_ms' => $timeMs,
                    ]);
                } else {
                    // lưu thất bại
                    logger()->error('Storage put returned false', $contextBase + [
                        'target_path' => $path,
                    ]);

                    // Optional: lưu bản tạm để debug (bật khi cần)
                    // try {
                    //     $tmpPath = '/tmp/debug_upload_' . time() . '_' . uniqid() . '.webp';
                    //     file_put_contents($tmpPath, $contents);
                    //     logger()->error('Wrote debug temp file for failed store', ['tmp' => $tmpPath]);
                    // } catch (\Throwable $tw) {
                    //     logger()->error('Failed to write debug temp file', ['err' => $tw->getMessage()]);
                    // }

                    $failureFiles[] = $clientName;
                }
            } catch (\Throwable $e) {
                // Log exception with stack trace and context
                $timeMs = round((microtime(true) - $startTime) * 1000, 2);
                logger()->error('Exception while processing uploaded file', $contextBase + [
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                    'time_ms' => $timeMs,
                    'memory_get_usage' => memory_get_usage(),
                    'memory_peak' => memory_get_peak_usage(),
                ]);

                // Optional: save the original uploaded tmp file to /tmp for offline inspection
                try {
                    $tmpCopy = '/tmp/upload_error_' . time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $clientName);
                    copy($file->getRealPath(), $tmpCopy);
                    logger()->error('Saved original uploaded file for inspection', ['tmp_copy' => $tmpCopy]);
                } catch (\Throwable $tw) {
                    logger()->warning('Could not save original uploaded file for inspection', ['err' => $tw->getMessage()]);
                }

                $failureFiles[] = $clientName;
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
        $mimeType = $file->getMimeType();
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
            'type' => 'file',
        ]);
    }

    public function rename($oldPath, $newName)
    {
        if (!$this->storage->exists($oldPath)) {
            return [
                'success' => false,
                'message' => 'Source file or folder does not exist'
            ];
        }

        $pathInfo = pathinfo($oldPath);
        $isDir = is_dir($this->storage->path($oldPath));

        // Build new path
        $newPath = $pathInfo['dirname'] . '/' . $newName;

        // For files, preserve extension
        if (!$isDir && isset($pathInfo['extension'])) {
            $newPath .= '.' . $pathInfo['extension'];
        }

        // Normalize paths
        $newPath = str_replace('//', '/', $newPath);

        // Check if target exists
        if ($this->storage->exists($newPath)) {
            return [
                'success' => false,
                'message' => 'A file or folder with this name already exists'
            ];
        }

        // Perform rename
        try {
            $success = $this->storage->move($oldPath, $newPath);

            if ($success && !$isDir) {
                // Update cache folder for renamed file
                $oldCacheFolder = 'cache/' . str_replace('.', '_', $oldPath);
                $newCacheFolder = 'cache/' . str_replace('.', '_', $newPath);

                if ($this->publicStorage->exists($oldCacheFolder)) {
                    $this->publicStorage->move($oldCacheFolder, $newCacheFolder);
                }
            }

            return [
                'success' => $success,
                'newPath' => $newPath,
                'message' => $success ? 'Renamed successfully' : 'Failed to rename'
            ];
        } catch (\Throwable $e) {
            logger()->error('Error renaming file/folder', [
                'oldPath' => $oldPath,
                'newName' => $newName,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function copy($sourcePath, $destinationPath)
    {
        if (!$this->storage->exists($sourcePath)) {
            return [
                'success' => false,
                'message' => 'Source does not exist'
            ];
        }

        $isDir = is_dir($this->storage->path($sourcePath));

        if ($isDir) {
            return $this->copyDirectory($sourcePath, $destinationPath);
        }

        // Handle name conflicts
        $finalPath = $this->resolveNameConflict($destinationPath);

        try {
            // Copy file
            $contents = $this->storage->get($sourcePath);
            $success = $this->storage->put($finalPath, $contents);

            return [
                'success' => $success,
                'newPath' => $finalPath,
                'message' => $success ? 'Copied successfully' : 'Failed to copy'
            ];
        } catch (\Throwable $e) {
            logger()->error('Error copying file', [
                'sourcePath' => $sourcePath,
                'destinationPath' => $destinationPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    protected function copyDirectory($sourcePath, $destinationPath)
    {
        // Resolve name conflict for directory
        $finalPath = $this->resolveNameConflict($destinationPath);

        try {
            // Create destination directory
            if (!$this->storage->makeDirectory($finalPath)) {
                return [
                    'success' => false,
                    'message' => 'Failed to create destination folder'
                ];
            }

            // Recursively copy contents
            $contents = $this->storage->listContents($sourcePath, true);

            foreach ($contents as $item) {
                $itemPath = $item->path();
                $relativePath = str_replace($sourcePath, '', $itemPath);
                $newPath = rtrim($finalPath, '/') . $relativePath;

                if ($item->isDir()) {
                    $this->storage->makeDirectory($newPath);
                } else {
                    $fileContents = $this->storage->get($itemPath);
                    $this->storage->put($newPath, $fileContents);
                }
            }

            return [
                'success' => true,
                'newPath' => $finalPath,
                'message' => 'Folder copied successfully'
            ];
        } catch (\Throwable $e) {
            logger()->error('Error copying directory', [
                'sourcePath' => $sourcePath,
                'destinationPath' => $destinationPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function move($sourcePath, $destinationPath)
    {
        if (!$this->storage->exists($sourcePath)) {
            return [
                'success' => false,
                'message' => 'Source does not exist'
            ];
        }

        $isDir = is_dir($this->storage->path($sourcePath));

        // Prevent moving folder into itself
        if ($isDir && str_starts_with($destinationPath, $sourcePath)) {
            return [
                'success' => false,
                'message' => 'Cannot move a folder into itself'
            ];
        }

        // Handle name conflicts
        $finalPath = $this->resolveNameConflict($destinationPath);

        try {
            // Move operation
            $success = $this->storage->move($sourcePath, $finalPath);

            if ($success && !$isDir) {
                // Clean up cache folder for moved file
                $oldCacheFolder = 'cache/' . str_replace('.', '_', $sourcePath);

                if ($this->publicStorage->exists($oldCacheFolder)) {
                    $this->publicStorage->deleteDirectory($oldCacheFolder);
                }
            }

            return [
                'success' => $success,
                'newPath' => $finalPath,
                'message' => $success ? 'Moved successfully' : 'Failed to move'
            ];
        } catch (\Throwable $e) {
            logger()->error('Error moving file/folder', [
                'sourcePath' => $sourcePath,
                'destinationPath' => $destinationPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function batchCopy($items, $destinationPath)
    {
        $results = [];

        foreach ($items as $item) {
            $sourcePath = $item['path'];
            $filename = basename($sourcePath);
            $targetPath = rtrim($destinationPath, '/') . '/' . $filename;

            $results[] = $this->copy($sourcePath, $targetPath);
        }

        return $results;
    }

    public function batchMove($items, $destinationPath)
    {
        $results = [];

        foreach ($items as $item) {
            $sourcePath = $item['path'];
            $filename = basename($sourcePath);
            $targetPath = rtrim($destinationPath, '/') . '/' . $filename;

            $results[] = $this->move($sourcePath, $targetPath);
        }

        return $results;
    }

    protected function resolveNameConflict($path)
    {
        if (!$this->storage->exists($path)) {
            return $path;
        }

        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $counter = 1;
        do {
            $newPath = $directory . '/' . $filename . '_copy_' . $counter . $extension;
            $counter++;
        } while ($this->storage->exists($newPath));

        return $newPath;
    }
}
