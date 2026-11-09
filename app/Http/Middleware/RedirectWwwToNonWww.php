<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectWwwToNonWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('host') ?: $request->getHost();
        $host = explode(':', $host)[0];

        if (str_starts_with($host, 'www.')) {
            $nonWwwHost = substr($host, 4);
            $targetUrl = 'https://' . $nonWwwHost . $request->getRequestUri();

            return redirect()->away($targetUrl, 301);
        }

        return $next($request);
    }
}
