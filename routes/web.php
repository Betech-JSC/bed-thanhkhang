<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    $host = request()->getHost();
    if ($host === '127.0.0.1') {
        $port = request()->getPort();
        $targetHost = 'localhost' . ($port && $port != 80 && $port != 443 ? ':' . $port : '');
        return redirect()->to(request()->getScheme() . '://' . $targetHost . request()->getRequestUri());
    }
    abort(404);
});
