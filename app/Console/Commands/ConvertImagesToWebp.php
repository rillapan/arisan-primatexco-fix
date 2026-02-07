<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp {--path=storage/app/public/img : Path ke folder gambar}';
    protected $description = 'Konversi semua gambar PNG/JPG/JPEG ke format WebP';

    public function handle()
    {
        $path = base_path($this->option('path'));
        
        if (!is_dir($path)) {
            $this->error("Folder tidak ditemukan: $path");
            return 1;
        }

        $extensions = ['jpg', 'jpeg', 'png'];
        $files = [];
        
        foreach ($extensions as $ext) {
            $files = array_merge($files, glob("$path/*.$ext"));
        }

        if (empty($files)) {
            $this->info("Tidak ada gambar PNG/JPG/JPEG yang ditemukan.");
            return 0;
        }

        $this->info("Ditemukan " . count($files) . " gambar untuk dikonversi...");
        
        $imageManager = new ImageManager(new Driver());
        $converted = 0;

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $webpPath = $path . '/' . $filename . '.webp';
            
            try {
                $image = $imageManager->read($file);
                $webpData = $image->toWebp(80);
                file_put_contents($webpPath, (string) $webpData);
                
                $originalSize = filesize($file);
                $webpSize = filesize($webpPath);
                $reduction = round((1 - $webpSize / $originalSize) * 100);
                
                $this->line("✓ {$filename} → WebP (ukuran berkurang {$reduction}%)");
                $converted++;
            } catch (\Exception $e) {
                $this->error("✗ Gagal konversi {$filename}: " . $e->getMessage());
            }
        }

        $this->info("\nSelesai! $converted gambar berhasil dikonversi ke WebP.");
        $this->warn("Jangan lupa update referensi di blade files dari .jpg/.png ke .webp");
        
        return 0;
    }
}
