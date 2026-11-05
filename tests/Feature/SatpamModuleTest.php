<?php

namespace Tests\Feature;

use App\Livewire\Satpam\KendaraanAktif;
use App\Livewire\Satpam\KendaraanMasuk;
use App\Livewire\Satpam\RiwayatKendaraan;
use App\Models\User;
use App\Models\VehicleVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SatpamModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $satpam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->satpam = User::factory()->create([
            'role' => 'satpam',
            'status_akun' => 'aktif',
            'shift_jaga' => 'Pagi',
        ]);
    }

    /**
     * Satpam dapat mengakses halaman form kendaraan masuk dan daftar kendaraan aktif.
     */
    public function test_satpam_can_access_module_pages(): void
    {
        $this->actingAs($this->satpam)
            ->get(route('satpam.kendaraan-masuk'))
            ->assertStatus(200)
            ->assertSee('Catat Kendaraan Masuk');

        $this->actingAs($this->satpam)
            ->get(route('satpam.kendaraan-aktif'))
            ->assertStatus(200)
            ->assertSee('Kendaraan Aktif di Area Pabrik');
    }

    /**
     * Satpam dapat mencatat kendaraan masuk dengan nomor kunjungan otomatis dan verifikasi B3.
     */
    public function test_satpam_can_record_vehicle_entry(): void
    {
        Livewire::actingAs($this->satpam)
            ->test(KendaraanMasuk::class)
            ->set('nomor_polisi', 'B 9876 PK')
            ->set('nama_pengemudi', 'Budi Santoso')
            ->set('asal_tujuan', 'PT Kimia Nusantara / Bekasi')
            ->set('keperluan', 'Pengiriman Kemasan Limbah B3')
            ->set('waktu_masuk', now()->format('Y-m-d\TH:i'))
            ->set('informasi_surat_jalan', 'SJ-2026-001')
            ->set('status_pemeriksaan_surat_jalan_b3', 'sesuai')
            ->set('keterangan_muatan', '100 Drum Limbah B3')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertRedirect(route('satpam.kendaraan-aktif'));

        $this->assertDatabaseHas('vehicle_visits', [
            'nomor_polisi' => 'B 9876 PK',
            'nama_pengemudi' => 'Budi Santoso',
            'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
            'waktu_keluar' => null,
        ]);
    }

    /**
     * Kendaraan aktif muncul dalam daftar kendaraan aktif.
     */
    public function test_active_vehicle_appears_in_active_list(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 1111 TST',
            'nama_pengemudi' => 'Ahmad Sopir',
            'asal_tujuan' => 'PT Maju Terus',
            'keperluan' => 'Kunjungan Uji',
            'waktu_masuk' => now()->subHours(2),
            'waktu_keluar' => null,
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
        ]);

        Livewire::actingAs($this->satpam)
            ->test(KendaraanAktif::class)
            ->assertSee('B 1111 TST')
            ->assertSee('Ahmad Sopir');
    }

    /**
     * Satpam dapat mencatat kendaraan keluar dan menutup kunjungan.
     */
    public function test_satpam_can_record_vehicle_exit_and_close_visit(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 2222 OUT',
            'nama_pengemudi' => 'Joko Supir',
            'asal_tujuan' => 'PT Sumber Rejeki',
            'keperluan' => 'Muat Drum Bersih',
            'waktu_masuk' => now()->subHours(3),
            'waktu_keluar' => null,
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
        ]);

        $exitTime = now()->format('Y-m-d\TH:i');

        Livewire::actingAs($this->satpam)
            ->test(KendaraanAktif::class)
            ->call('bukaModalKeluar', $visit->id)
            ->set('waktu_keluar', $exitTime)
            ->set('catatan_keluar', 'Selesai muat 150 drum')
            ->call('simpanKeluar')
            ->assertHasNoErrors();

        $visit->refresh();
        $this->assertNotNull($visit->waktu_keluar);
        $this->assertEquals('Selesai muat 150 drum', $visit->catatan);
        $this->assertCount(0, VehicleVisit::aktif()->get());
    }

    /**
     * Validasi SOP: Waktu keluar tidak boleh lebih awal dari waktu masuk.
     */
    public function test_exit_time_cannot_be_earlier_than_entry_time(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 3333 ERR',
            'nama_pengemudi' => 'Supir Salah Jam',
            'asal_tujuan' => 'PT Tes Waktu',
            'keperluan' => 'Uji Validasi',
            'waktu_masuk' => now(),
            'waktu_keluar' => null,
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
        ]);

        // Coba input waktu keluar 1 jam SEBELUM waktu masuk
        $earlierTime = now()->subHour()->format('Y-m-d\TH:i');

        Livewire::actingAs($this->satpam)
            ->test(KendaraanAktif::class)
            ->call('bukaModalKeluar', $visit->id)
            ->set('waktu_keluar', $earlierTime)
            ->call('simpanKeluar')
            ->assertHasErrors(['waktu_keluar']);

        $visit->refresh();
        $this->assertNull($visit->waktu_keluar);
    }

    /**
     * Satpam dapat menghapus data kunjungan aktif dengan konfirmasi modal.
     */
    public function test_satpam_can_delete_vehicle_visit_with_confirmation(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 7777 DEL',
            'nama_pengemudi' => 'Supir Salah Input',
            'asal_tujuan' => 'PT Salah Catat',
            'keperluan' => 'Uji Hapus Pos Satpam',
            'waktu_masuk' => now(),
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
        ]);

        Livewire::actingAs($this->satpam)
            ->test(KendaraanAktif::class)
            ->call('konfirmasiHapus', $visit->id)
            ->assertSet('modalHapusTerbuka', true)
            ->assertSet('hapusVisitId', $visit->id)
            ->call('hapusVisit')
            ->assertSet('modalHapusTerbuka', false);

        $this->assertDatabaseMissing('vehicle_visits', [
            'id' => $visit->id,
        ]);
    }

    /**
     * Satpam dapat mengakses halaman riwayat kendaraan dan melihat daftar kendaraan keluar masuk.
     */
    public function test_satpam_can_access_riwayat_kendaraan_page(): void
    {
        $this->actingAs($this->satpam)
            ->get(route('satpam.riwayat'))
            ->assertStatus(200)
            ->assertSee('Riwayat Kunjungan Kendaraan');
    }

    /**
     * Kendaraan yang sudah selesai keluar muncul di riwayat dan dapat dilihat detailnya.
     */
    public function test_exited_vehicle_appears_in_riwayat_and_can_show_detail(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 5555 OUT',
            'nama_pengemudi' => 'Supir Logistik Selesai',
            'asal_tujuan' => 'PT Mitra Logistik',
            'keperluan' => 'Kunjungan Pengiriman',
            'waktu_masuk' => now()->subHours(4),
            'waktu_keluar' => now()->subHour(),
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
            'keterangan_muatan' => '100 Drum Siap Pakai',
        ]);

        Livewire::actingAs($this->satpam)
            ->test(RiwayatKendaraan::class)
            ->assertSee('B 5555 OUT')
            ->assertSee('Supir Logistik Selesai')
            ->assertSee('Selesai Keluar')
            ->call('lihatDetail', $visit->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Detail Kunjungan Kendaraan');
    }

    /**
     * Satpam dapat membatalkan status keluar jika salah pencet sehingga kendaraan kembali aktif.
     */
    public function test_satpam_can_cancel_exit_status_to_restore_vehicle_to_active(): void
    {
        $visit = VehicleVisit::create([
            'user_id' => $this->satpam->id,
            'nomor_kunjungan' => VehicleVisit::generateNomorKunjungan(),
            'nomor_polisi' => 'B 6666 ACC',
            'nama_pengemudi' => 'Supir Salah Klik Keluar',
            'asal_tujuan' => 'PT Batal Keluar',
            'keperluan' => 'Kunjungan Uji Batal',
            'waktu_masuk' => now()->subHours(2),
            'waktu_keluar' => now()->subHour(),
            'pos_gerbang' => 'Gerbang Utama',
            'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
        ]);

        Livewire::actingAs($this->satpam)
            ->test(RiwayatKendaraan::class)
            ->call('batalkanKeluar', $visit->id)
            ->assertHasNoErrors();

        $visit->refresh();
        $this->assertNull($visit->waktu_keluar);
        $this->assertCount(1, VehicleVisit::aktif()->where('id', $visit->id)->get());
    }
}
