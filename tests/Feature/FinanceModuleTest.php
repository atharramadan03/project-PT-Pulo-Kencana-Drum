<?php

namespace Tests\Feature;

use App\Livewire\Keuangan\FinanceDashboard;
use App\Livewire\Keuangan\FinanceInvoices;
use App\Livewire\Keuangan\FinanceOperationalExpenses;
use App\Livewire\Keuangan\FinanceStockTransactions;
use App\Livewire\Keuangan\FinanceVehicleRepairs;
use App\Models\FinanceInvoice;
use App\Models\OperationalExpenseLog;
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

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.tagihan'))
            ->assertStatus(200)
            ->assertSee('Riwayat Tagihan');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.operasional'))
            ->assertStatus(200)
            ->assertSee('Biaya Operasional Pabrik');
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

    /**
     * Admin Keuangan dapat mencatat tagihan dan memperbarui status pelunasan.
     */
    public function test_admin_keuangan_can_record_invoice_and_settle(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceInvoices::class)
            ->set('nomor_invoice', 'INV/TEST/2026/01')
            ->set('jenis_tagihan', 'tagihan_pelanggan')
            ->set('pihak_terkait', 'PT Test Mitra Drum')
            ->set('kontak_pic', 'Bpk. Gunawan')
            ->set('nomor_wa', '081234567890')
            ->set('nominal', '10000000')
            ->set('tanggal_terbit', '2026-11-01')
            ->set('tanggal_jatuh_tempo', '2026-11-30')
            ->set('status_pembayaran', 'belum_dibayar')
            ->set('metode_pembayaran', 'transfer_bank')
            ->call('simpanInvoice')
            ->assertHasNoErrors();

        $invoice = FinanceInvoice::where('nomor_invoice', 'INV/TEST/2026/01')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(10000000, $invoice->nominal);
        $this->assertEquals('Bpk. Gunawan', $invoice->kontak_pic);
        $this->assertEquals('081234567890', $invoice->nomor_wa);
        $this->assertEquals('6281234567890', $invoice->formatted_nomor_wa);
        $this->assertNotNull($invoice->whatsapp_direct_url);
        $this->assertStringContainsString('6281234567890', $invoice->whatsapp_direct_url);

        // Uji cicilan pertama (pembayaran sebagian)
        Livewire::actingAs($this->keuangan)
            ->test(FinanceInvoices::class)
            ->call('bukaModalLunas', $invoice->id)
            ->set('lunas_nominal', '4000000')
            ->set('lunas_tanggal', '2026-11-02')
            ->call('konfirmasiPelunasan')
            ->assertHasNoErrors();

        $this->assertEquals('sebagian', $invoice->fresh()->status_pembayaran);
        $this->assertEquals(4000000, $invoice->fresh()->nominal_dibayar);
        $this->assertEquals(6000000, $invoice->fresh()->sisa_tagihan);

        // Uji cicilan kedua (pelunasan penuh)
        Livewire::actingAs($this->keuangan)
            ->test(FinanceInvoices::class)
            ->call('bukaModalLunas', $invoice->id)
            ->set('lunas_nominal', '6000000')
            ->set('lunas_tanggal', '2026-11-05')
            ->call('konfirmasiPelunasan')
            ->assertHasNoErrors();

        $this->assertEquals('lunas', $invoice->fresh()->status_pembayaran);
        $this->assertEquals(10000000, $invoice->fresh()->nominal_dibayar);
        $this->assertEquals(0, $invoice->fresh()->sisa_tagihan);
    }

    /**
     * Admin Keuangan dapat mencatat pengeluaran operasional pabrik.
     */
    public function test_admin_keuangan_can_record_operational_expense(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceOperationalExpenses::class)
            ->set('kategori_biaya', 'listrik_air')
            ->set('nama_biaya', 'Listrik PLN Bulan November')
            ->set('nominal', '3500000')
            ->set('tanggal_pengeluaran', '2026-11-01')
            ->set('metode_pembayaran', 'transfer_bank')
            ->set('penerima_dana', 'PT PLN Persero')
            ->call('simpanBiaya')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('operational_expense_logs', [
            'kategori_biaya' => 'listrik_air',
            'nama_biaya' => 'Listrik PLN Bulan November',
            'nominal' => 3500000,
            'penerima_dana' => 'PT PLN Persero',
        ]);
    }

    /**
     * Admin Keuangan dapat menggunakan fitur WhatsApp penagihan langsung dan melengkapi kontak WA.
     */
    public function test_admin_keuangan_can_use_direct_whatsapp_feature(): void
    {
        $invoice = FinanceInvoice::create([
            'user_id' => $this->keuangan->id,
            'kode_tagihan' => 'INV-202611-999',
            'nomor_invoice' => 'INV/PKDI/XI/2026/99',
            'jenis_tagihan' => 'tagihan_pelanggan',
            'pihak_terkait' => 'PT Semen Perkasa Abadi',
            'nominal' => 15000000,
            'nominal_dibayar' => 5000000,
            'tanggal_terbit' => '2026-10-01',
            'tanggal_jatuh_tempo' => '2026-10-31',
            'status_pembayaran' => 'sebagian',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        // 1. Tagihan baru belum memiliki nomor WA
        $this->assertEmpty($invoice->nomor_wa);
        $this->assertNull($invoice->whatsapp_direct_url);

        // 2. Admin membuka modal lengkapi WA dan menyimpan nomor
        Livewire::actingAs($this->keuangan)
            ->test(FinanceInvoices::class)
            ->call('bukaModalWa', $invoice->id)
            ->assertSet('modalWaTerbuka', true)
            ->assertSet('quickWaInvoiceId', $invoice->id)
            ->set('quick_wa_pic', 'Bpk. Rahmat (Finance)')
            ->set('quick_wa_nomor', '081298765432')
            ->call('simpanNomorWa')
            ->assertHasNoErrors()
            ->assertDispatched('buka-wa-langsung');

        // 3. Verifikasi nomor WA dan PIC tersimpan serta format URL WhatsApp langsung tersedia
        $freshInvoice = $invoice->fresh();
        $this->assertEquals('Bpk. Rahmat (Finance)', $freshInvoice->kontak_pic);
        $this->assertEquals('081298765432', $freshInvoice->nomor_wa);
        $this->assertEquals('6281298765432', $freshInvoice->formatted_nomor_wa);
        $this->assertNotNull($freshInvoice->terakhir_ditagih_at);

        // 4. Verifikasi URL langsung memuat nomor 628... dan template pesan resmi tagihan
        $waUrl = $freshInvoice->whatsapp_direct_url;
        $this->assertNotNull($waUrl);
        $this->assertStringContainsString('https://wa.me/6281298765432', $waUrl);
        $this->assertStringContainsString(urlencode('INV/PKDI/XI/2026/99'), $waUrl);
        $this->assertStringContainsString(urlencode('123-456-7890'), $waUrl);
    }

    /**
     * Admin Keuangan dapat menghapus data tagihan dengan konfirmasi modal.
     */
    public function test_admin_keuangan_can_delete_invoice_with_confirmation(): void
    {
        $invoice = FinanceInvoice::create([
            'user_id' => $this->keuangan->id,
            'kode_tagihan' => 'INV-202611-088',
            'nomor_invoice' => 'INV/TEST/DELETE/01',
            'jenis_tagihan' => 'tagihan_pelanggan',
            'pihak_terkait' => 'PT Hapus Data',
            'nominal' => 2000000,
            'nominal_dibayar' => 0,
            'tanggal_terbit' => '2026-10-01',
            'tanggal_jatuh_tempo' => '2026-10-31',
            'status_pembayaran' => 'belum_dibayar',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceInvoices::class)
            ->call('konfirmasiHapus', $invoice->id)
            ->assertSet('modalHapusTerbuka', true)
            ->assertSet('hapusInvoiceId', $invoice->id)
            ->call('hapusTagihan')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('finance_invoices', [
            'id' => $invoice->id,
        ]);
    }

    /**
     * Admin Keuangan dapat menghapus biaya operasional dengan konfirmasi modal.
     */
    public function test_admin_keuangan_can_delete_operational_expense(): void
    {
        $expense = OperationalExpenseLog::create([
            'user_id' => $this->keuangan->id,
            'kode_biaya' => 'BOP-202611-099',
            'kategori_biaya' => 'atk_kantor',
            'nama_biaya' => 'Beli Kertas HVS',
            'nominal' => 150000,
            'tanggal_pengeluaran' => '2026-11-01',
            'metode_pembayaran' => 'tunai_kas_kecil',
            'penerima_dana' => 'Toko Gramedia',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceOperationalExpenses::class)
            ->call('konfirmasiHapus', $expense->id)
            ->assertSet('modalHapusTerbuka', true)
            ->assertSet('hapusExpenseId', $expense->id)
            ->call('hapusBiaya')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('operational_expense_logs', [
            'id' => $expense->id,
        ]);
    }

    /**
     * Admin Keuangan dapat menghapus catatan perbaikan armada dengan konfirmasi modal.
     */
    public function test_admin_keuangan_can_delete_vehicle_repair(): void
    {
        $repair = VehicleMaintenanceLog::create([
            'user_id' => $this->keuangan->id,
            'nomor_polisi' => 'B 9999 TEST',
            'jenis_kendaraan' => 'Truk Tronton Box',
            'tanggal_perbaikan' => '2026-11-01',
            'jenis_perbaikan' => 'Ganti Oli Mesin',
            'nama_bengkel' => 'Bengkel Sejahtera',
            'biaya' => 850000,
            'status_perbaikan' => 'selesai',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceVehicleRepairs::class)
            ->call('konfirmasiHapus', $repair->id)
            ->assertSet('modalHapusTerbuka', true)
            ->assertSet('hapusRepairId', $repair->id)
            ->call('hapusPerbaikan')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('vehicle_maintenance_logs', [
            'id' => $repair->id,
        ]);
    }

    /**
     * Admin Keuangan dapat menghapus catatan transaksi keuangan stok dengan konfirmasi modal.
     */
    public function test_admin_keuangan_can_delete_stock_transaction(): void
    {
        $trx = WarehouseFinanceLog::create([
            'user_id' => $this->keuangan->id,
            'kode_transaksi' => 'TRX-202611-999',
            'jenis_transaksi' => 'pemasukan',
            'kategori' => 'penjualan_drum',
            'tanggal_transaksi' => '2026-11-01',
            'nominal' => 5000000,
            'metode_pembayaran' => 'transfer',
            'pihak_terkait' => 'PT Hapus Transaksi',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceStockTransactions::class)
            ->call('konfirmasiHapus', $trx->id)
            ->assertSet('modalHapusTerbuka', true)
            ->assertSet('hapusTrxId', $trx->id)
            ->call('hapusTransaksi')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('warehouse_finance_logs', [
            'id' => $trx->id,
        ]);
    }
}
