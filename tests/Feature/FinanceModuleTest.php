<?php

namespace Tests\Feature;

use App\Livewire\Keuangan\FinanceDashboard;
use App\Models\User;
use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $keuangan;

    private User $adminKantor;

    private User $satpam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->keuangan = User::factory()->create([
            'role' => 'admin_keuangan',
            'status_akun' => 'aktif',
        ]);

        $this->adminKantor = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $this->satpam = User::factory()->create([
            'role' => 'satpam',
            'status_akun' => 'aktif',
        ]);
    }

    /**
     * Admin Keuangan dapat membuka halaman Dashboard Keuangan, Keuangan Stok, dan Biaya Kendaraan.
     */
    public function test_admin_keuangan_can_access_module_pages(): void
    {
        $this->actingAs($this->keuangan)
            ->get(route('keuangan.dashboard'))
            ->assertStatus(200)
            ->assertSee('Dashboard Keuangan');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.stok-transaksi'))
            ->assertStatus(200)
            ->assertSee('Pencatatan Keuangan Stok Gudang');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.biaya-kendaraan'))
            ->assertStatus(200)
            ->assertSee('Biaya Perbaikan');
    }

    /**
     * Admin Keuangan dapat mencatat transaksi keuangan stok (pemasukan/pengeluaran).
     */
    public function test_admin_keuangan_can_record_stock_transaction(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceDashboard::class)
            ->set('jenis_transaksi', 'pemasukan')
            ->set('kategori', 'penjualan_drum')
            ->set('tanggal_transaksi', '2026-10-30')
            ->set('jumlah_unit', 100)
            ->set('nominal', '25000000')
            ->set('metode_pembayaran', 'transfer')
            ->set('pihak_terkait', 'PT Kimia Farma Rekan')
            ->set('keterangan', 'Penjualan 100 drum rekondisi')
            ->call('simpanTransaksi')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('warehouse_finance_logs', [
            'jenis_transaksi' => 'pemasukan',
            'kategori' => 'penjualan_drum',
            'jumlah_unit' => 100,
            'nominal' => 25000000,
            'pihak_terkait' => 'PT Kimia Farma Rekan',
        ]);
    }

    /**
     * Admin Keuangan dapat mencatat biaya perbaikan mobil/kendaraan.
     */
    public function test_admin_keuangan_can_record_vehicle_repair_cost(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceDashboard::class)
            ->set('nomor_polisi', 'B 9481 KDA')
            ->set('jenis_kendaraan', 'Truk Tronton Box')
            ->set('tanggal_perbaikan', '2026-10-30')
            ->set('jenis_perbaikan', 'Ganti Oli & Filter')
            ->set('nama_bengkel', 'Bengkel Sumber Diesel')
            ->set('biaya', '2450000')
            ->set('status_perbaikan', 'selesai')
            ->set('keterangan_perbaikan', 'Servis berkala rutin 10.000 KM')
            ->call('simpanPerbaikan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vehicle_maintenance_logs', [
            'nomor_polisi' => 'B 9481 KDA',
            'jenis_kendaraan' => 'Truk Tronton Box',
            'nama_bengkel' => 'Bengkel Sumber Diesel',
            'biaya' => 2450000,
            'status_perbaikan' => 'selesai',
        ]);
    }

    /**
     * Satpam dan Admin Kantor dilarang mengeksekusi aksi penyimpanan pada modul keuangan (hanya read-only).
     */
    public function test_other_roles_cannot_save_finance_data(): void
    {
        Livewire::actingAs($this->satpam)
            ->test(FinanceDashboard::class)
            ->set('nominal', '10000000')
            ->call('simpanTransaksi')
            ->assertForbidden();

        Livewire::actingAs($this->adminKantor)
            ->test(FinanceDashboard::class)
            ->set('biaya', '1500000')
            ->call('simpanPerbaikan')
            ->assertForbidden();
    }

    /**
     * Pengujian Ekspor Rekapitulasi Keuangan ke Excel dan PDF.
     */
    public function test_export_excel_and_pdf_finance(): void
    {
        WarehouseFinanceLog::create([
            'user_id' => $this->keuangan->id,
            'kode_transaksi' => 'TRX-STK-202610-001',
            'jenis_transaksi' => 'pemasukan',
            'kategori' => 'penjualan_drum',
            'tanggal_transaksi' => '2026-10-30',
            'jumlah_unit' => 50,
            'nominal' => 12500000,
            'metode_pembayaran' => 'transfer',
            'pihak_terkait' => 'PT Pelita',
        ]);

        VehicleMaintenanceLog::create([
            'user_id' => $this->keuangan->id,
            'nomor_polisi' => 'B 9123 KDA',
            'jenis_kendaraan' => 'Truk Engkel Bak',
            'tanggal_perbaikan' => '2026-10-30',
            'jenis_perbaikan' => 'Servis Rem',
            'nama_bengkel' => 'Bengkel Jaya',
            'biaya' => 1200000,
            'status_perbaikan' => 'selesai',
        ]);

        $responseExcel = $this->actingAs($this->keuangan)
            ->get(route('keuangan.export-excel'));

        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responsePdf = $this->actingAs($this->keuangan)
            ->get(route('keuangan.export-pdf'));

        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('content-type', 'application/pdf');
    }
}
