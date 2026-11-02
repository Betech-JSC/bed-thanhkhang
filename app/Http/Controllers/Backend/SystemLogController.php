<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class SystemLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $routeName = str_replace(current_locale() . '.', '', $request->route()->getName());
            if (!current_admin()->can($routeName)) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of system log files.
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            File::makeDirectory($logPath, 0755, true);
        }

        // Get all .log files in storage/logs
        $files = collect(File::glob($logPath . '/*.log'))
            ->map(function ($path) {
                return [
                    'name' => basename($path),
                    'size' => $this->formatBytes(File::size($path)),
                    'modified' => date('Y-m-d H:i:s', File::lastModified($path)),
                ];
            })
            ->sortByDesc('modified')
            ->values()
            ->all();

        $selectedFile = $request->input('file');
        $logContent = '';

        if ($selectedFile) {
            // Secure filename to prevent directory traversal attacks
            $selectedFile = basename($selectedFile);
            $filePath = $logPath . '/' . $selectedFile;

            if (File::exists($filePath)) {
                $content = File::get($filePath);
                $lines = explode("\n", $content);
                
                // Keep only last 1000 lines to prevent browser performance issues
                $lastLines = array_slice($lines, -1000);
                $logContent = implode("\n", $lastLines);
            } else {
                $selectedFile = null;
            }
        }

        return Inertia::render('SystemLogs/Index', [
            'files' => $files,
            'selectedFile' => $selectedFile,
            'logContent' => $logContent,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    /**
     * Clear the contents of a log file.
     */
    public function clear($file)
    {
        $file = basename($file);
        $filePath = storage_path('logs/' . $file);

        if (File::exists($filePath)) {
            File::put($filePath, '');
            return redirect()->route('admin.system-logs.index', ['file' => $file])
                ->with('success', "Đã xóa trắng nội dung file {$file} thành công.");
        }

        return back()->with('error', 'File log không tồn tại.');
    }

    /**
     * Delete a log file.
     */
    public function destroy($file)
    {
        $file = basename($file);
        $filePath = storage_path('logs/' . $file);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('admin.system-logs.index')
                ->with('success', "Đã xóa file {$file} thành công.");
        }

        return back()->with('error', 'File log không tồn tại.');
    }

    /**
     * Helper to format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
