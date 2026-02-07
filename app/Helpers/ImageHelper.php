<?php

if (!function_exists('storage_url')) {
    /**
     * Generate URL for storage files that works on restrictive hosts
     * Uses controller-based serving instead of direct file access
     * 
     * @param string|null $path Path relative to storage/app/public
     * @return string
     */
    function storage_url(?string $path): string
    {
        if (empty($path)) {
            return asset('images/default-avatar.png');
        }
        
        // Use the image controller route to serve files
        return route('image.serve', ['path' => $path]);
    }
}

if (!function_exists('management_photo_url')) {
    /**
     * Generate URL for management photos
     */
    function management_photo_url(?string $path): string
    {
        if (empty($path)) {
            return asset('images/default-avatar.png');
        }
        
        return route('image.serve', ['path' => $path]);
    }
}

if (!function_exists('cs_photo_url')) {
    /**
     * Generate URL for customer service photos
     */
    function cs_photo_url(?string $filename): string
    {
        if (empty($filename)) {
            return asset('images/default-avatar.png');
        }
        
        // CS photos are stored in public/uploads/customer_service
        return asset('uploads/customer_service/' . $filename);
    }
}
