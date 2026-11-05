<?php

use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Keuangan\FinanceExportController;
use App\Livewire\Admin\DrumBatchIndex;
use App\Livewire\Admin\DrumProcessIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\VehicleLogIndex;
use App\Livewire\Keuangan\FinanceCashFlowHistory;
use App\Livewire\Keuangan\FinanceDashboard;
use App\Livewire\Keuangan\FinanceEmployees;
use App\Livewire\Keuangan\FinanceIncomingGoods;
use App\Livewire\Keuangan\FinanceInvoices;
use App\Livewire\Keuangan\FinanceOperationalExpenses;
use App\Livewire\Keuangan\FinanceOutgoingGoods;
use App\Livewire\Keuangan\FinanceReportsPrint;
use App\Livewire\Keuangan\FinanceStockTransactions;
use App\Livewire\Keuangan\FinanceSuppliers;
use App\Livewire\Keuangan\FinanceVehicleRepairs;
use App\Livewire\Satpam\KendaraanAktif;
use App\Livewire\Satpam\KendaraanMasuk;
use App\Livewire\Satpam\RiwayatKendaraan;
use Illuminate\Support\Facades\Route;

// Halaman Utama: Otomatis arahkan ke Dashboard (jika sudah login) atau ke Halaman Login PKDI (jika belum login)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Rute Pengarah Otomatis (Redirector Central)
Route::get('dashboard', function () {
    if (auth()->user()?->role === 'satpam') {
        return redirect()->route('satpam.dashboard');
    }

    if (auth()->user()?->role === 'admin_keuangan') {
        return redirect()->route('keuangan.dashboard');
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
        Route::get('drum-batches', DrumBatchIndex::class)->name('drum-batches');
        Route::get('drum-process', DrumProcessIndex::class)->name('drum-process');
        Route::get('vehicles', VehicleLogIndex::class)->name('vehicles');
        Route::get('reports', ReportIndex::class)->name('reports');

        // Endpoint Unduh Laporan PDF & Excel (.xlsx)
        Route::get('reports/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/export-all', [ReportExportController::class, 'exportAllExcel'])->name('reports.export-all');
        Route::get('reports/export-batch', [ReportExportController::class, 'exportBatchExcel'])->name('reports.export-batch');
        Route::get('reports/export-process', [ReportExportController::class, 'exportProcessExcel'])->name('reports.export-process');
        Route::get('reports/export-vehicle', [ReportExportController::class, 'exportVehicleExcel'])->name('reports.export-vehicle');
    });

// ==========================================
// 2. RUTE KHUSUS SATPAM POS GERBANG
// ==========================================
Route::middleware(['auth', 'role:satpam'])
    ->prefix('satpam')
    ->name('satpam.')
    ->group(function () {
        Route::view('dashboard', 'satpam.dashboard')->name('dashboard');
        Route::get('kendaraan-masuk', KendaraanMasuk::class)->name('kendaraan-masuk');
        Route::get('kendaraan-aktif', KendaraanAktif::class)->name('kendaraan-aktif');
        Route::get('riwayat', RiwayatKendaraan::class)->name('riwayat');
    });

// ==========================================
// 3. RUTE MODUL KEUANGAN & TRACKER REKAPITULASI
// Hak Akses Eksklusif untuk Admin Keuangan
// ==========================================
Route::middleware(['auth', 'role:admin_keuangan'])
    ->prefix('keuangan')
    ->name('keuangan.')
    ->group(function () {
        Route::get('dashboard', FinanceDashboard::class)->name('dashboard');
        Route::get('barang-masuk', FinanceIncomingGoods::class)->name('barang-masuk');
        Route::get('barang-keluar', FinanceOutgoingGoods::class)->name('barang-keluar');
        Route::get('suppliers', FinanceSuppliers::class)->name('suppliers');

        // Modul Biaya Operasional & Turunannya
        Route::get('operasional', FinanceOperationalExpenses::class)->name('operasional');
        Route::get('biaya-kendaraan', FinanceVehicleRepairs::class)->name('biaya-kendaraan');
        Route::get('laporan-cetak', FinanceReportsPrint::class)->name('laporan-cetak');
        Route::get('riwayat-keuangan', FinanceCashFlowHistory::class)->name('riwayat-keuangan');

        // Riwayat Tagihan & Data Driver Truk
        Route::get('tagihan', FinanceInvoices::class)->name('tagihan');
        Route::get('driver-truk', FinanceEmployees::class)->name('driver-truk');
        Route::get('karyawan', fn () => redirect()->route('keuangan.driver-truk'))->name('karyawan');

        // Backward compatibility
        Route::get('stok-transaksi', FinanceStockTransactions::class)->name('stok-transaksi');

        // Endpoint Unduh Laporan Rekapitulasi Keuangan
        Route::get('export-excel', [FinanceExportController::class, 'exportExcel'])->name('export-excel');
        Route::get('export-pdf', [FinanceExportController::class, 'exportPdf'])->name('export-pdf');
    });

require __DIR__.'/auth.php';
