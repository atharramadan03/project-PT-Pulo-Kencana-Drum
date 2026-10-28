<?php

use Illuminate\Support\Facades\Route;

// Halaman Utama: Otomatis arahkan ke Dashboard (jika sudah login) atau ke Halaman Login PKDI (jika belum login)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rute Pengarah Otomatis (Redirector Central)
// Mengarahkan user yang membuka /dashboard langsung ke dashboard role masing-masing
Route::get('dashboard', function () {
    if (auth()->user()?->role === 'satpam') {
        return redirect()->route('satpam.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Profil Pengguna (Bisa diakses kedua role yang sudah login)
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ==========================================
// 1. RUTE KHUSUS ADMIN KANTOR
// ==========================================
Route::middleware(['auth', 'role:admin_kantor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');
        Route::get('drum-batches', \App\Livewire\Admin\DrumBatchIndex::class)->name('drum-batches');
        Route::get('drum-process', \App\Livewire\Admin\DrumProcessIndex::class)->name('drum-process');
        Route::get('vehicles', \App\Livewire\Admin\VehicleLogIndex::class)->name('vehicles');
        Route::get('reports', \App\Livewire\Admin\ReportIndex::class)->name('reports');

        // Endpoint Unduh Laporan PDF & Excel (CSV)
        Route::get('reports/pdf', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/export-batch', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportBatchCsv'])->name('reports.export-batch');
        Route::get('reports/export-process', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportProcessCsv'])->name('reports.export-process');
        Route::get('reports/export-vehicle', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportVehicleCsv'])->name('reports.export-vehicle');
    });


// ==========================================
// 2. RUTE KHUSUS SATPAM POS GERBANG
// ==========================================
Route::middleware(['auth', 'role:satpam'])
    ->prefix('satpam')
    ->name('satpam.')
    ->group(function () {
        Route::view('dashboard', 'satpam.dashboard')->name('dashboard');
        Route::get('kendaraan-masuk', \App\Livewire\Satpam\KendaraanMasuk::class)->name('kendaraan-masuk');
        Route::get('kendaraan-aktif', \App\Livewire\Satpam\KendaraanAktif::class)->name('kendaraan-aktif');
    });

require __DIR__.'/auth.php';
