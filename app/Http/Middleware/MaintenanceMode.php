<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    /**
     * Daftar IP yang diizinkan akses penuh (developer)
     * Tambahkan IP Anda di sini
     */
    protected array $allowedIps = [
        // '127.0.0.1', // Di-comment sementara agar maintenance mode terlihat saat ditest di local
        '::1',
        '36.65.110.208',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Cek apakah maintenance mode aktif
        if (!config('app.maintenance.maintenance_mode', false)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        // Izinkan developer (berdasarkan IP)
        if (in_array($clientIp, $this->allowedIps)) {
            return $next($request);
        }

        // Cek secret token via query string: ?dev_token=RAHASIA123
        $devToken = $request->query('dev_token');
        if ($devToken && $devToken === config('app.maintenance.dev_token')) {
            // Simpan token ke session agar tidak perlu diketik terus
            session(['dev_access' => true]);
        }

        if (session('dev_access') === true) {
            return $next($request);
        }

        // Tampilkan halaman maintenance untuk pengunjung biasa
        return response()->view('maintenance.maintenance', [], 503);
    }
}