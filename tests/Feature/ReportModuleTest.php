<?php

namespace Tests\Feature;

use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\VehicleLogIndex;
use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\User;
use App\Models\VehicleVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $satpam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);

        $this->satpam = User::factory()->create([
            'role' => 'satpam',
            'status_akun' => 'aktif',
        ]);
    }

    /**
     * Admin Kantor dapat mengakses halaman arsip catatan kendaraan.
     */
    public function test_admin_can_access_vehicle_logs_page(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => 'KJ-20261027-0001',
            'nomor_polisi' => 'B 9988 KDA',
            'nama_pengemudi' => 'Supir Budiman',
            'asal_tujuan' => 'PT Kimia Farma',
            'keperluan' => 'Kirim Drum Bekas',
            'waktu_masuk' => now()->subHours(2),
            'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.vehicles'))
            ->assertStatus(200)
            ->assertSee('Catatan Kunjungan Kendaraan')
            ->assertSee('B 9988 KDA')
            ->assertSee('Supir Budiman');
    }

    /**
     * Admin Kantor dapat melihat modal detail kunjungan kendaraan.
     */
    public function test_admin_can_view_vehicle_visit_details(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => 'KJ-20261027-0002',
            'nomor_polisi' => 'B 1234 XYZ',
            'nama_pengemudi' => 'Pak Bambang',
            'asal_tujuan' => 'PT Mitra Industri',
            'keperluan' => 'Kirim Drum Residu Oli',
            'waktu_masuk' => now()->subHours(3),
            'informasi_surat_jalan' => 'SJ-B3-99120 Tanggal 27/10/2026',
            'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
        ]);

        Livewire::actingAs($this->admin)
            ->test(VehicleLogIndex::class)
            ->call('lihatDetail', $visit->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('SJ-B3-99120 Tanggal 27/10/2026')
            ->call('tutupDetailModal')
            ->assertSet('showDetailModal', false);
    }

    /**
     * Admin Kantor dapat mengakses halaman Rekapitulasi Laporan dan berpindah tab.
     */
    public function test_admin_can_access_reports_page_and_switch_tabs(): void
    {
        $batch = DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => 'BATCH-202610-001',
            'tanggal_penerimaan' => now()->format('Y-m-d'),
            'jumlah_diterima' => 200,
            'kategori_jenis' => 'Besi Tight Head',
            'status_pengendapan' => 'Transit',
            'status_proses' => 'Diterima',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.reports'))
            ->assertStatus(200)
            ->assertSee('Rekapitulasi Laporan');

        Livewire::actingAs($this->admin)
            ->test(ReportIndex::class)
            ->assertSee('BATCH-202610-001')
            ->call('setTab', 'proses')
            ->assertSet('activeTab', 'proses')
            ->call('setTab', 'kendaraan')
            ->assertSet('activeTab', 'kendaraan');
    }

    /**
     * Satpam tidak berhak mengakses halaman laporan eksekutif maupun arsip kendaraan admin (Role Redirection ke Dashboard Satpam).
     */
    public function test_satpam_cannot_access_reports_or_admin_vehicle_logs(): void
    {
        $this->actingAs($this->satpam)
            ->get(route('admin.reports'))
            ->assertRedirect(route('satpam.dashboard'));

        $this->actingAs($this->satpam)
            ->get(route('admin.vehicles'))
            ->assertRedirect(route('satpam.dashboard'));
    }

    /**
     * Admin Kantor dapat mengunduh file Excel (.xlsx) rekap batch drum.
     */
    public function test_admin_can_export_batch_csv(): void
    {
        DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => 'BATCH-EXP-001',
            'tanggal_penerimaan' => now()->format('Y-m-d'),
            'jumlah_diterima' => 150,
            'kategori_jenis' => 'Besi Open Top',
            'status_pengendapan' => 'Mengendap',
            'status_proses' => 'Dalam Proses',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-batch', [
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Admin Kantor dapat mengunduh file Excel (.xlsx) rekap 4 tahap proses gudang dan export all.
     */
    public function test_admin_can_export_process_csv(): void
    {
        $batch = DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => 'BATCH-EXP-002',
            'tanggal_penerimaan' => now()->format('Y-m-d'),
            'jumlah_diterima' => 100,
            'status_pengendapan' => 'Transit',
            'status_proses' => 'Diterima',
        ]);

        DrumProcessLog::create([
            'user_id' => $this->admin->id,
            'drum_batch_id' => $batch->id,
            'jenis_proses' => 'penyortiran',
            'tanggal_aktivitas' => now()->format('Y-m-d'),
            'tanggal_input' => now(),
            'jumlah_diproses' => 100,
            'jumlah_afkir' => 4,
            'pelapor_gudang' => 'Mandor Bambang',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-process', [
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responseAll = $this->actingAs($this->admin)
            ->get(route('admin.reports.export-all', [
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $responseAll->assertStatus(200);
        $responseAll->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Admin Kantor dapat mengunduh file PDF resmi rekapitulasi operasional PKDI.
     */
    public function test_admin_can_download_official_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.pdf', [
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => now()->endOfMonth()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
