<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware - berlaku untuk semua request
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\ForceHttps::class);
        
        // Alias untuk throttle sudah built-in di Laravel 11
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle rate limiting (429 Too Many Requests) dengan pesan ramah
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
            $minutes = ceil($retryAfter / 60);
            
            $message = "Terlalu banyak percobaan gagal. Silakan tunggu {$minutes} menit sebelum mencoba lagi.";
            
            // Determine which login page to redirect to
            if (str_contains($request->path(), 'admin')) {
                return redirect()->route('admin.login.form')
                    ->withErrors(['login' => $message])
                    ->withInput($request->except('password'));
            }
            
            return redirect()->route('login')
                ->withErrors(['login' => $message])
                ->withInput($request->except('password'));
        });
    })->create();
