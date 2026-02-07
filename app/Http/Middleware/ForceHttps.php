<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     * Redirect ke HTTPS jika FORCE_HTTPS=true di .env
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya redirect jika FORCE_HTTPS=true dan bukan HTTPS
        if (config('app.force_https') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
