<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     * Menambahkan security headers untuk mencegah berbagai serangan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cegah clickjacking - hanya izinkan iframe dari domain yang sama
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Cegah MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Aktifkan XSS filter browser
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Kontrol informasi referrer yang dikirim
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy - batasi sumber konten
        // Disesuaikan untuk mengizinkan CDN yang digunakan aplikasi
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://unpkg.com; " .
               "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: blob: https:; " .
               "connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com; " .
               "frame-ancestors 'self';";
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Strict Transport Security (hanya jika HTTPS)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
