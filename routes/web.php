<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CashManagementController;
use App\Models\Kepengurusan;

// Home page route
Route::get('/', function () {
    return view('home');
})->name('home');

// Image serve route - bypass hosting restrictions
Route::get('/img/{path}', [\App\Http\Controllers\ImageController::class, 'serve'])
    ->where('path', '.*')
    ->name('image.serve');

// Route: FORCE FIX & VERIFY
Route::get('/force-fix-storage', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    
    echo "<style>body{font-family:sans-serif;padding:20px;}</style>";
    echo "<h1>🔨 Force Fix Storage</h1>";

    // 1. CLEANUP: Hapus apapun yang ada di public/storage
    if (file_exists($link)) {
        if (is_link($link)) {
            unlink($link);
            echo "<p>✅ Link lama dihapus.</p>";
        } elseif (is_dir($link)) {
            // Coba rename dulu (lebih aman daripada delete recursive)
            $backup = public_path('storage_hambatan_' . rand(1000,9999));
            if (rename($link, $backup)) {
                 echo "<p>✅ Folder 'storage' yang menghalangi berhasil dipindahkan ke: $backup</p>";
            } else {
                 echo "<p style='color:red;font-weight:bold;'>❌ GAGAL MEMINDAHKAN FOLDER 'storage'. ADA PERMISSION ERROR.</p>";
                 echo "<p>Tindakan Manual Diperlukan: Masuk ke File Manager hosting Anda, buka folder 'public', dan rename/hapus folder 'storage' secara manual.</p>";
                 exit;
            }
        }
    }

    // 2. RECREATE: Buat Link Baru
    try {
        symlink($target, $link);
        echo "<p>✅ Symbolic Link baru berhasil dibuat.</p>";
    } catch (\Exception $e) {
        echo "<p style='color:red'>❌ Gagal membuat symlink: " . $e->getMessage() . "</p>";
    }

    // 3. VERIFY: Cek file update terakhir
    echo "<hr><h3>📂 Pengecekan File (Verification)</h3>";
    $uploadDir = $target . '/uploads/participants';
    
    if (is_dir($uploadDir)) {
        $files = scandir($uploadDir, SCANDIR_SORT_DESCENDING); // File terbaru di atas
        $found = false;
        
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse; width:100%'>";
        echo "<tr style='background:#eee'><th>Nama File (di Server)</th><th>Link Akses (Klik untuk Coba)</th><th>Status</th></tr>";
        
        // Cek 5 file terbaru
        for($i=0; $i<min(5, count($files)); $i++) {
            $file = $files[$i];
            if ($file == '.' || $file == '..') continue;
            
            $url = url('storage/uploads/participants/' . $file);
            $found = true;
            echo "<tr>";
            echo "<td>$file</td>";
            echo "<td><a href='$url' target='_blank'>$url</a></td>";
            echo "<td>(Cek Link)</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if (!$found) echo "<p>Folder uploads/participants kosong.</p>";
    } else {
        echo "<p style='color:red'>❌ Folder $uploadDir TIDAK DITEMUKAN.</p>";
    }
});

Route::get('/register', [RegistrationController::class, 'index'])->name('register.index');
Route::get('/register/group/{groupId}', [RegistrationController::class, 'form'])->name('register.form');
Route::post('/register/group/{groupId}', [RegistrationController::class, 'store'])->name('register.store');

// Authentication routes (dengan rate limiting untuk mencegah brute force)
Route::get('/login', [AuthController::class, 'showParticipantLoginForm'])->name('login'); // Main Participant Login
Route::post('/login', [AuthController::class, 'participantLogin'])->middleware('throttle:5,1')->name('participant.login'); // POST action for participant (5 attempts/minute)
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login.form'); // Admin Login Form
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('admin.login'); // Admin Login Action (5 attempts/minute)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (middleware will be added later)
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export-pdf', [AdminController::class, 'exportAuctionResultsPdf'])->name('dashboard.export-pdf');
    
    // Group management
    Route::get('/groups', [AdminController::class, 'groups'])->name('groups');
    Route::get('/groups/create', [AdminController::class, 'createGroup'])->name('groups.create');
    Route::post('/groups', [AdminController::class, 'storeGroup'])->name('groups.store');
    Route::put('/groups/{groupId}/periods/{periodId}/status', [AdminController::class, 'updatePeriodStatusToBidding'])->name('groups.periods.update.status');
    
    // Group-specific management
    Route::get('/groups/{groupId}/manage', [AdminController::class, 'manageGroup'])->name('groups.manage');
    Route::get('/groups/{groupId}/settings', [AdminController::class, 'groupSettings'])->name('groups.settings');
    Route::put('/groups/{groupId}/settings', [AdminController::class, 'updateGroupSettings'])->name('groups.settings.update');
    Route::delete('/groups/{groupId}', [AdminController::class, 'deleteGroup'])->name('groups.delete');
    Route::get('/groups/{groupId}/participants/manage', [AdminController::class, 'manageParticipants'])->name('groups.participants.manage');
    Route::get('/groups/{groupId}/cash', function ($groupId) {
        return redirect()->route('admin.groups.cash.manage', $groupId);
    })->name('groups.cash');
    Route::get('/groups/{groupId}/cash/manage', [AdminController::class, 'manageCash'])->name('groups.cash.manage');
    Route::get('/groups/{groupId}/cash/{monthKey}', [AdminController::class, 'cashMonthDetail'])->name('groups.cash.month.detail');
    Route::get('/groups/{groupId}/cash/{monthKey}/export-pdf', [AdminController::class, 'exportCashMonthPdf'])->name('groups.cash.month.export-pdf');
    Route::get('/groups/{groupId}/cash/{monthKey}/print-all-receipts', [AdminController::class, 'printAllReceipts'])->name('groups.cash.month.print-all-receipts');
    Route::delete('/groups/{groupId}/cash/{monthKey}', [AdminController::class, 'deleteCashMonth'])->name('groups.cash.month.delete');
    // createCashMonth route removed - cash should only be created when creating periods
    Route::post('/groups/{groupId}/cash/{monthKey}/add-installment', [AdminController::class, 'addInstallment'])->name('groups.cash.add.installment');
    Route::post('/groups/{groupId}/cash/bulk-installment', [AdminController::class, 'bulkInstallmentProcess'])->name('groups.cash.bulk.installment');
    Route::post('/groups/{groupId}/registration/toggle', [AdminController::class, 'toggleRegistration'])->name('groups.registration.toggle');
    Route::get('/payments/{paymentId}/receipt', [AdminController::class, 'generateReceipt'])->name('payments.receipt');
    Route::get('/groups/{groupId}/auction/process', [AdminController::class, 'processAuction'])->name('groups.auction.process');
    Route::get('/groups/{groupId}/auction/{periodId}/manual-bid', [AdminController::class, 'manualBidInput'])->name('groups.auction.manual-bid');
    Route::post('/groups/{groupId}/auction/{periodId}/manual-bid', [AdminController::class, 'storeManualBid'])->name('groups.auction.manual-bid.store');
    
    // Bid management
    Route::get('/bids/{bidId}', [AdminController::class, 'showBidDetail'])->name('bids.show');
    Route::get('/bids/{bidId}/edit', [AdminController::class, 'editBid'])->name('bids.edit');
    Route::put('/bids/{bidId}', [AdminController::class, 'updateBid'])->name('bids.update');
    Route::delete('/bids/{bidId}', [AdminController::class, 'deleteBid'])->name('bids.delete');
    Route::get('/bids/{bidId}/download-proof', [AdminController::class, 'downloadBidProof'])->name('bids.download-proof');
    
    // Participant management
    Route::get('/groups/{groupId}/participants', [AdminController::class, 'participants'])->name('participants');
    Route::get('/groups/{groupId}/participants/manage', [AdminController::class, 'manageParticipants'])->name('groups.participants.manage');
    Route::get('/participants/{participantId}', [AdminController::class, 'showParticipantDetail'])->name('participants.show');
    Route::post('/groups/{groupId}/participants/import', [AdminController::class, 'importParticipants'])->name('groups.participants.import');
    Route::delete('/groups/{groupId}/participants/delete-all', [AdminController::class, 'deleteAllParticipants'])->name('groups.participants.delete-all');
    Route::get('/groups/{groupId}/participants/export', [AdminController::class, 'exportParticipants'])->name('groups.participants.export');
    Route::get('/groups/{groupId}/participants/template', [AdminController::class, 'downloadParticipantTemplate'])->name('groups.participants.template');
    Route::post('/participants/{participantId}/reset-password', [AdminController::class, 'resetParticipantPassword'])->name('participants.reset-password');
    Route::post('/participants/{participantId}/approve', [AdminController::class, 'approveParticipant'])->name('participants.approve');
    Route::post('/groups/{groupId}/participants/approve-all', [AdminController::class, 'approveAllParticipants'])->name('groups.participants.approve-all');
    Route::put('/participants/{id}', [AdminController::class, 'updateParticipant'])->name('participants.update');
    Route::delete('/participants/{participantId}/delete', [AdminController::class, 'deleteParticipant'])->name('participants.delete');
    
    // Monthly periods (group-specific)
    Route::get('/periods', [AdminController::class, 'periods'])->name('periods');
    Route::get('/periods/create', [AdminController::class, 'createMonthlyPeriod'])->name('periods.create');
    Route::post('/periods', [AdminController::class, 'storePeriod'])->name('periods.store');
    Route::get('/groups/{groupId}/periods', [AdminController::class, 'groupPeriods'])->name('groups.periods');
    Route::get('/groups/{groupId}/periods/create', [AdminController::class, 'createGroupPeriod'])->name('groups.periods.create');
    Route::post('/groups/{groupId}/periods', [AdminController::class, 'storeGroupPeriod'])->name('groups.periods.store');
    Route::get('/periods/{id}', [AdminController::class, 'showPeriod'])->name('periods.show');
    Route::get('/periods/{id}/edit', [AdminController::class, 'editPeriod'])->name('periods.edit');
    Route::put('/periods/{id}', [AdminController::class, 'updatePeriod'])->name('periods.update');
    Route::delete('/periods/{id}', [AdminController::class, 'deletePeriod'])->name('periods.delete');
    
    // Winners (group-specific)
    Route::get('/groups/{groupId}/winners/export-pdf', [AdminController::class, 'exportGroupWinnersPdf'])->name('groups.winners.export-pdf');
    Route::get('/groups/{groupId}/winners', [AdminController::class, 'groupWinners'])->name('groups.winners');
    
    // Bidding management
    Route::get('/periods/{periodId}/bidding', [AdminController::class, 'bidding'])->name('bidding');
    Route::get('/periods/{periodId}/draw', [AdminController::class, 'startDrawing'])->name('draw.start');
    Route::post('/periods/{periodId}/draw', [AdminController::class, 'performDraw'])->name('draw.perform');
    
    // Winners
    Route::get('/winners', [AdminController::class, 'winners'])->name('winners');
    
    // Cash Management
    Route::get('/groups/{groupId}/cash/dashboard', [CashManagementController::class, 'dashboard'])->name('cash.dashboard');
    Route::get('/groups/{groupId}/cash/period/{periodId}', [CashManagementController::class, 'periodCashFlow'])->name('cash.period');
    Route::get('/groups/{groupId}/cash/history', [CashManagementController::class, 'cashHistory'])->name('cash.history');
    Route::post('/groups/{groupId}/cash/validate-bid', [CashManagementController::class, 'validateBid'])->name('cash.validate-bid');
    Route::get('/groups/{groupId}/cash/status', [CashManagementController::class, 'cashStatus'])->name('cash.status');
    Route::post('/groups/{groupId}/cash/simulate', [CashManagementController::class, 'simulateCashFlow'])->name('cash.simulate');
    Route::post('/groups/{groupId}/cash/update-bid', [CashManagementController::class, 'updateBid'])->name('cash.update-bid');
    
    // Kepengurusan Management
    Route::get('/saksi', [AdminController::class, 'saksi'])->name('saksi');
    Route::get('/saksi/create', [AdminController::class, 'createKepengurusan'])->name('saksi.create');
    Route::post('/saksi', [AdminController::class, 'storeKepengurusan'])->name('saksi.store');
    Route::get('/saksi/{id}/edit', [AdminController::class, 'editKepengurusan'])->name('saksi.edit');
    Route::put('/saksi/{id}', [AdminController::class, 'updateKepengurusan'])->name('saksi.update');
    Route::delete('/saksi/{id}', [AdminController::class, 'deleteKepengurusan'])->name('saksi.delete');
    
    // Drive Link (Global - 1 link untuk semua)
    Route::get('/drive-link', [\App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('drive-link.index');
    Route::post('/drive-link', [\App\Http\Controllers\Admin\DocumentationController::class, 'store'])->name('drive-link.store');
    Route::delete('/drive-link', [\App\Http\Controllers\Admin\DocumentationController::class, 'destroy'])->name('drive-link.destroy');

    
    // Kelola Jabatan (Integrated in Profile)
    Route::get('/positions', function() { return redirect()->route('admin.profile'); })->name('positions');
    Route::post('/positions', [AdminController::class, 'storePosition'])->name('positions.store');
    Route::put('/positions/{id}', [AdminController::class, 'updatePosition'])->name('positions.update');
    Route::delete('/positions/{id}', [AdminController::class, 'deletePosition'])->name('positions.delete');
    // Customer Service Management
    Route::resource('customer-service', \App\Http\Controllers\Admin\CustomerServiceController::class);

    // KTA Management
    Route::get('/kta/settings', [\App\Http\Controllers\Admin\KtaController::class, 'settings'])->name('kta.settings');
    Route::post('/kta/settings', [\App\Http\Controllers\Admin\KtaController::class, 'updateSettings'])->name('kta.settings.update');
    Route::get('/kta/scanner', [\App\Http\Controllers\Admin\KtaController::class, 'scanner'])->name('kta.scanner');
    Route::post('/kta/search', [\App\Http\Controllers\Admin\KtaController::class, 'search'])->name('kta.search');

    // Admin Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updatePassword'])->name('profile.update');
    Route::post('/profile/photo', [AdminController::class, 'uploadProfilePhoto'])->name('profile.photo');

    // Management (Pengurus) routes
    Route::post('/management', [\App\Http\Controllers\ManagementController::class, 'store'])->name('management.store');
    Route::put('/management/{id}', [\App\Http\Controllers\ManagementController::class, 'update'])->name('management.update');
    Route::delete('/management/{id}', [\App\Http\Controllers\ManagementController::class, 'destroy'])->name('management.delete');
});

// Participant routes
Route::prefix('participant')->name('participant.')->middleware('auth:participant')->group(function () {
    Route::get('/dashboard', [ParticipantController::class, 'dashboard'])->name('dashboard');
    Route::get('/bid', [ParticipantController::class, 'showBidForm'])->name('bid.create');
    Route::post('/bid', [ParticipantController::class, 'submitBid'])->name('bid.store');
    Route::post('/bid/store', [ParticipantController::class, 'storeBid'])->name('bid.store.dashboard');
    Route::post('/bid/{bidId}/permanent', [ParticipantController::class, 'makePermanent'])->name('bid.permanent');
    Route::get('/bid/{bidId}/permanent', function() {
        return redirect()->route('participant.bid.create')->with('error', 'Silakan gunakan tombol "Simpan Lelang Permanen" di halaman lelang.');
    });
    Route::get('/bid/{bidId}/download-proof', [ParticipantController::class, 'downloadBidProof'])->name('bid.download-proof');
    Route::put('/bid/{bidId}', [ParticipantController::class, 'updateBid'])->name('bid.update');
    Route::get('/results', [ParticipantController::class, 'results'])->name('results');
    Route::get('/winners', [ParticipantController::class, 'winners'])->name('winners');
    Route::get('/profile', [ParticipantController::class, 'profile'])->name('profile');
    Route::get('/terms', [ParticipantController::class, 'terms'])->name('terms');
    Route::put('/profile', [ParticipantController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/photo', [ParticipantController::class, 'deletePhoto'])->name('profile.delete-photo');
    Route::get('/auction/{periodId}', [ParticipantController::class, 'viewAuction'])->name('auction.view');
    Route::get('/bukti-angsuran', [ParticipantController::class, 'buktiAngsuran'])->name('bukti.angsuran');
    Route::get('/payments/{paymentId}/receipt', [ParticipantController::class, 'viewReceipt'])->name('participant.receipt');
    Route::get('/drive-link', [\App\Http\Controllers\Admin\DocumentationController::class, 'showForParticipant'])->name('drive-link');
    Route::post('/switch-account/{id}', [ParticipantController::class, 'switchAccount'])->name('switch-account');
    Route::get('/hubungi-kami', [ParticipantController::class, 'hubungiKami'])->name('hubungi-kami');
    Route::get('/kta', [ParticipantController::class, 'kta'])->name('kta');
    Route::get('/kta/download', [ParticipantController::class, 'downloadKta'])->name('kta.download');
    
    // DEBUG ROUTE
    Route::get('/debug-password', [ParticipantController::class, 'debugPasswordForm'])->name('debug.password');
    Route::post('/debug-password', [ParticipantController::class, 'debugPasswordCheck'])->name('debug.password.check');});