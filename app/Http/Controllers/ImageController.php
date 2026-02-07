<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    /**
     * Serve images from storage through controller
     * This bypasses hosting restrictions on direct file access
     */
    public function serve($path)
    {
        // Sanitize path to prevent directory traversal
        $path = str_replace(['..', '//'], '', $path);
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Image not found');
        }
        
        $fullPath = Storage::disk('public')->path($path);
        $mimeType = $this->getMimeType($fullPath);
        $content = Storage::disk('public')->get($path);
        
        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Expires', now()->addYear()->toRfc7231String());
    }
    
    /**
     * Get mime type for image
     */
    private function getMimeType($path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return match($extension) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'application/octet-stream',
        };
    }
}
