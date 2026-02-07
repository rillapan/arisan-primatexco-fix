<?php

namespace App\Console\Commands;

use App\Models\Documentation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documentations:clean {--dry-run : Run without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus dokumentasi yang sudah lebih dari 6 bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $dryRun = $this->option('dry-run');

        $oldDocumentations = Documentation::where('created_at', '<', $sixMonthsAgo)->get();

        if ($oldDocumentations->isEmpty()) {
            $this->info('Tidak ada dokumentasi yang perlu dihapus.');
            return 0;
        }

        $this->info("Ditemukan {$oldDocumentations->count()} dokumentasi yang lebih dari 6 bulan.");

        if ($dryRun) {
            $this->warn('Mode dry-run aktif - tidak ada data yang dihapus.');
            foreach ($oldDocumentations as $doc) {
                $this->line("  - ID: {$doc->id}, Tipe: {$doc->type}, Dibuat: {$doc->created_at->format('d M Y')}");
            }
            return 0;
        }

        $deletedCount = 0;
        $errorCount = 0;

        foreach ($oldDocumentations as $doc) {
            try {
                // Delete file from storage if it's image or video
                if ($doc->type !== 'text' && Storage::disk('public')->exists($doc->content)) {
                    Storage::disk('public')->delete($doc->content);
                }

                $doc->delete();
                $deletedCount++;
                $this->line("Dihapus: ID {$doc->id} ({$doc->type})");
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Gagal menghapus ID {$doc->id}: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. Dihapus: {$deletedCount}, Gagal: {$errorCount}");
        return 0;
    }
}
