<?php

namespace Tests\Feature;

use App\Livewire\Admin\DrumBatchIndex;
use App\Livewire\Admin\DrumProcessIndex;
use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin_kantor',
            'status_akun' => 'aktif',
        ]);
    }

    /**
     * Admin Kantor dapat membuka halaman Batch Drum dan Log Proses Gudang.
     */
    public function test_admin_can_access_module_pages(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.drum-batches'))
            ->assertStatus(200)
            ->assertSee('Data Penerimaan');

        $this->actingAs($this->admin)
            ->get(route('admin.drum-process'))
            ->assertStatus(200)
            ->assertSee('Catatan 4 Tahap Proses Gudang');
    }

    /**
     * Admin dapat membuat batch drum baru dengan kode otomatis dan status transit.
     */
    public function test_admin_can_create_drum_batch(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DrumBatchIndex::class)
            ->set('tanggal_penerimaan', '2026-10-27')
            ->set('jumlah_diterima', 150)
            ->set('kategori_jenis', 'Besi Tight Head')
            ->set('status_pengendapan', 'Transit')
            ->set('status_proses', 'Diterima')
            ->set('sumber_laporan', 'Nota Gudang Mandor Joko')
            ->set('catatan', 'Residu oli ringan')
            ->call('simpanBatch')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('drum_batches', [
            'jumlah_diterima' => 150,
            'kategori_jenis' => 'Besi Tight Head',
            'status_pengendapan' => 'Transit',
            'sumber_laporan' => 'Nota Gudang Mandor Joko',
        ]);
    }

    /**
     * Admin dapat memperbarui status pengendapan dan status proses batch drum.
     */
    public function test_admin_can_update_batch_status(): void
    {
        $batch = DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => DrumBatch::generateKodeBatch(),
            'tanggal_penerimaan' => '2026-10-27',
            'jumlah_diterima' => 200,
            'kategori_jenis' => 'Besi Open Top',
            'status_pengendapan' => 'Transit',
            'status_proses' => 'Diterima',
        ]);

        Livewire::actingAs($this->admin)
            ->test(DrumBatchIndex::class)
            ->call('bukaStatusModal', $batch->id)
            ->set('edit_status_pengendapan', 'Mengendap')
            ->set('edit_status_proses', 'Dalam Proses')
            ->call('simpanStatus')
            ->assertHasNoErrors();

        $batch->refresh();
        $this->assertEquals('Mengendap', $batch->status_pengendapan);
        $this->assertEquals('Dalam Proses', $batch->status_proses);
    }

    /**
     * Admin dapat mencatat log 4 proses gudang dengan pemisahan tanggal aktivitas vs tanggal input.
     */
    public function test_admin_can_record_drum_process_log(): void
    {
        $batch = DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => DrumBatch::generateKodeBatch(),
            'tanggal_penerimaan' => '2026-10-26',
            'jumlah_diterima' => 100,
            'kategori_jenis' => 'Besi Tight Head',
            'status_pengendapan' => 'Selesai',
            'status_proses' => 'Diterima',
        ]);

        Livewire::actingAs($this->admin)
            ->test(DrumProcessIndex::class)
            ->set('drum_batch_id', $batch->id)
            ->set('jenis_proses', 'penyortiran')
            ->set('tanggal_aktivitas', '2026-10-26')
            ->set('jumlah_diproses', 100)
            ->set('jumlah_afkir', 5)
            ->set('pelapor_gudang', 'Mandor Joko Shift Pagi')
            ->set('hasil_catatan', '95 drum lolos ke tahap peniupan, 5 afkir bocor')
            ->call('simpanLog')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('drum_process_logs', [
            'drum_batch_id' => $batch->id,
            'jenis_proses' => 'penyortiran',
            'jumlah_diproses' => 100,
            'jumlah_afkir' => 5,
            'pelapor_gudang' => 'Mandor Joko Shift Pagi',
        ]);

        $log = DrumProcessLog::first();
        $this->assertNotNull($log);
        $this->assertEquals('2026-10-26', $log->tanggal_aktivitas->format('Y-m-d'));

        // Status batch otomatis diperbarui ke 'Dalam Proses'
        $batch->refresh();
        $this->assertEquals('Dalam Proses', $batch->status_proses);
    }

    /**
     * Validasi SOP: Jumlah afkir tidak boleh melebihi jumlah yang diproses.
     */
    public function test_reject_count_cannot_exceed_processed_count(): void
    {
        $batch = DrumBatch::create([
            'user_id' => $this->admin->id,
            'kode_batch' => DrumBatch::generateKodeBatch(),
            'tanggal_penerimaan' => '2026-10-27',
            'jumlah_diterima' => 50,
            'status_pengendapan' => 'Transit',
            'status_proses' => 'Diterima',
        ]);

        Livewire::actingAs($this->admin)
            ->test(DrumProcessIndex::class)
            ->set('drum_batch_id', $batch->id)
            ->set('jenis_proses', 'pencucian')
            ->set('tanggal_aktivitas', '2026-10-27')
            ->set('jumlah_diproses', 50)
            ->set('jumlah_afkir', 60) // Lebih besar dari 50
            ->set('pelapor_gudang', 'Regu Cuci')
            ->call('simpanLog')
            ->assertHasErrors(['jumlah_afkir']);

        $this->assertCount(0, DrumProcessLog::all());
    }
}
