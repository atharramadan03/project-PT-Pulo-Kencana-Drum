<?php

namespace Tests\Feature;

use App\Livewire\Keuangan\FinanceDashboard;
use App\Livewire\Keuangan\FinanceEmployees;
use App\Livewire\Keuangan\FinanceIncomingGoods;
use App\Livewire\Keuangan\FinanceInvoices;
use App\Livewire\Keuangan\FinanceOperationalExpenses;
use App\Livewire\Keuangan\FinanceReportsPrint;
use App\Livewire\Keuangan\FinanceStockTransactions;
use App\Livewire\Keuangan\FinanceSuppliers;
use App\Livewire\Keuangan\FinanceVehicleRepairs;
use App\Models\Employee;
use App\Models\FinanceInvoice;
use App\Models\OperationalExpenseLog;
use App\Models\Supplier;
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

    /**
     * Admin Keuangan dapat mengakses seluruh menu terstruktur baru (Barang Masuk, Barang Keluar, Supplier, Laporan Cetak, Riwayat Keuangan, Karyawan).
     */
    public function test_admin_keuangan_can_access_all_restructured_navigation_pages(): void
    {
        $this->actingAs($this->keuangan)
            ->get(route('keuangan.barang-masuk'))
            ->assertStatus(200)
            ->assertSee('Data Barang Masuk');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.barang-keluar'))
            ->assertStatus(200)
            ->assertSee('Data Barang Keluar');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.suppliers'))
            ->assertStatus(200)
            ->assertSee('Data Supplier');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.laporan-cetak'))
            ->assertStatus(200)
            ->assertSee('Laporan Keuangan');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.riwayat-keuangan'))
            ->assertStatus(200)
            ->assertSee('Riwayat Keuangan');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.driver-truk'))
            ->assertStatus(200)
            ->assertSee('Data Driver Truk');

        $this->actingAs($this->keuangan)
            ->get(route('keuangan.karyawan'))
            ->assertRedirect(route('keuangan.driver-truk'));
    }

    /**
     * Admin Keuangan dapat memuat halaman Laporan untuk di Cetak dengan data faktur tagihan.
     */
    public function test_admin_keuangan_can_view_reports_print_with_invoices(): void
    {
        FinanceInvoice::create([
            'user_id' => $this->keuangan->id,
            'kode_tagihan' => 'INV-TEST-001',
            'nomor_invoice' => 'INV/2026/001',
            'jenis_tagihan' => 'tagihan_pelanggan',
            'pihak_terkait' => 'PT Mitra Sejahtera',
            'nominal' => 12500000,
            'nominal_dibayar' => 12500000,
            'tanggal_terbit' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(14)->toDateString(),
            'tanggal_lunas' => now()->toDateString(),
            'status_pembayaran' => 'lunas',
        ]);

        FinanceInvoice::create([
            'user_id' => $this->keuangan->id,
            'kode_tagihan' => 'INV-TEST-002',
            'nomor_invoice' => 'INV/2026/002',
            'jenis_tagihan' => 'tagihan_pelanggan',
            'pihak_terkait' => 'CV Sumber Makmur',
            'nominal' => 8500000,
            'nominal_dibayar' => 0,
            'tanggal_terbit' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
            'status_pembayaran' => 'jatuh_tempo',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceReportsPrint::class)
            ->assertStatus(200)
            ->assertSee('PT Pulo Kencana Drum Industri')
            ->assertSee('PT Mitra Sejahtera')
            ->assertSee('CV Sumber Makmur');
    }

    /**
     * Admin Keuangan dapat menambah, mengedit, dan menghapus Supplier baru.
     */
    public function test_admin_keuangan_can_manage_suppliers(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceSuppliers::class)
            ->call('bukaModalTambah')
            ->set('nama_supplier', 'CV Baja Unggul Perkasa')
            ->set('kategori_pasokan', 'Bahan Baku Drum Logam')
            ->set('nama_pic', 'Hendra Gunawan')
            ->set('nomor_telepon', '081299887766')
            ->set('alamat', 'Jl. Industri Logam No. 12')
            ->set('status_kerjasama', 'aktif')
            ->call('simpanSupplier')
            ->assertHasNoErrors()
            ->assertSet('modalTerbuka', false);

        $this->assertDatabaseHas('suppliers', [
            'nama_supplier' => 'CV Baja Unggul Perkasa',
            'nama_pic' => 'Hendra Gunawan',
            'nomor_telepon' => '081299887766',
        ]);

        $supplier = Supplier::where('nama_supplier', 'CV Baja Unggul Perkasa')->first();

        // Edit
        Livewire::actingAs($this->keuangan)
            ->test(FinanceSuppliers::class)
            ->call('bukaModalEdit', $supplier->id)
            ->set('nama_supplier', 'CV Baja Unggul Perkasa Reborn')
            ->call('simpanSupplier')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'nama_supplier' => 'CV Baja Unggul Perkasa Reborn',
        ]);

        // Hapus
        Livewire::actingAs($this->keuangan)
            ->test(FinanceSuppliers::class)
            ->call('konfirmasiHapus', $supplier->id)
            ->call('hapusSupplier')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    /**
     * Admin Keuangan dapat mengelola Data Karyawan pengelola biaya operasional.
     */
    public function test_admin_keuangan_can_manage_employees(): void
    {
        Livewire::actingAs($this->keuangan)
            ->test(FinanceEmployees::class)
            ->call('bukaModalTambah')
            ->set('nama_karyawan', 'Budi Santoso Mandiri')
            ->set('departemen', 'Logistik Armada')
            ->set('jabatan', 'Koordinator Supir Truk')
            ->set('nomor_telepon', '085712345678')
            ->set('status_kerja', 'aktif')
            ->set('tanggal_masuk', '2026-01-15')
            ->set('catatan', 'Penanggung jawab biaya BBM dan tol armada tim barat')
            ->call('simpanKaryawan')
            ->assertHasNoErrors()
            ->assertSet('modalTerbuka', false);

        $this->assertDatabaseHas('employees', [
            'nama_karyawan' => 'Budi Santoso Mandiri',
            'departemen' => 'Logistik Armada',
            'nomor_telepon' => '085712345678',
        ]);

        $employee = Employee::where('nama_karyawan', 'Budi Santoso Mandiri')->first();

        // Edit
        Livewire::actingAs($this->keuangan)
            ->test(FinanceEmployees::class)
            ->call('bukaModalEdit', $employee->id)
            ->set('jabatan', 'Manajer Operasional Logistik')
            ->call('simpanKaryawan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'jabatan' => 'Manajer Operasional Logistik',
        ]);

        // Hapus
        Livewire::actingAs($this->keuangan)
            ->test(FinanceEmployees::class)
            ->call('konfirmasiHapus', $employee->id)
            ->call('hapusKaryawan')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

    /**
     * Admin Keuangan dapat mencatat pengadaan barang masuk dengan mengaitkan supplier.
     */
    public function test_admin_keuangan_can_record_incoming_goods_with_supplier(): void
    {
        $supplier = Supplier::create([
            'kode_supplier' => 'SUP-TEST-001',
            'nama_supplier' => 'PT Mitra Pengadaan Drum Logam',
            'kategori_pasokan' => 'Bahan Baku Drum Logam',
            'nama_pic' => 'Pak Joko',
            'nomor_telepon' => '08123456789',
            'status_kerjasama' => 'aktif',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceIncomingGoods::class)
            ->call('bukaCreateModal')
            ->set('tanggal_transaksi', '2026-11-04')
            ->set('supplier_id', $supplier->id)
            ->set('kategori', 'pembelian_bahan_baku')
            ->set('jumlah_unit', 150)
            ->set('nominal', '15000000')
            ->set('metode_pembayaran', 'transfer')
            ->set('keterangan', 'Pengadaan 150 drum polos dari PT Mitra')
            ->call('simpanBarangMasuk')
            ->assertHasNoErrors()
            ->assertSet('isCreateModalOpen', false);

        $this->assertDatabaseHas('warehouse_finance_logs', [
            'supplier_id' => $supplier->id,
            'jenis_transaksi' => 'pengeluaran',
            'kategori' => 'pembelian_bahan_baku',
            'jumlah_unit' => 150,
            'nominal' => 15000000,
        ]);
    }

    /**
     * Admin Keuangan dapat mencatat biaya operasional dengan memilih karyawan PIC pengelola.
     */
    public function test_admin_keuangan_can_record_operational_expense_with_employee_pic(): void
    {
        $employee = Employee::create([
            'nik_karyawan' => 'EMP-TEST-999',
            'nama_karyawan' => 'Slamet Riyadi',
            'departemen' => 'Workshop Maintenance',
            'jabatan' => 'Teknisi Utama',
            'nomor_telepon' => '087788990011',
            'status_kerja' => 'aktif',
            'tanggal_masuk' => '2026-02-01',
        ]);

        Livewire::actingAs($this->keuangan)
            ->test(FinanceOperationalExpenses::class)
            ->call('bukaModal')
            ->set('kategori_biaya', 'pemeliharaan_fasilitas')
            ->set('nama_biaya', 'Penggantian Seal Pompa High-Pressure Cuci Drum')
            ->set('nominal', '1850000')
            ->set('tanggal_pengeluaran', '2026-11-04')
            ->set('metode_pembayaran', 'tunai_kas_kecil')
            ->set('penerima_dana', 'Toko Teknik Sejahtera')
            ->set('employee_id', $employee->id)
            ->set('keterangan', 'Biaya perbaikan pompa workshop di bawah tanggung jawab Mas Slamet')
            ->call('simpanBiaya')
            ->assertHasNoErrors()
            ->assertSet('modalTerbuka', false);

        $this->assertDatabaseHas('operational_expense_logs', [
            'employee_id' => $employee->id,
            'kategori_biaya' => 'pemeliharaan_fasilitas',
            'nama_biaya' => 'Penggantian Seal Pompa High-Pressure Cuci Drum',
            'nominal' => 1850000,
        ]);
    }
}
