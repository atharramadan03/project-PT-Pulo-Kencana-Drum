<?php

namespace App\Livewire\Satpam;

use App\Models\VehicleVisit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class KendaraanMasuk extends Component
{
    public string $nomor_kunjungan = '';

    #[Validate('required|string|max:20')]
    public string $nomor_polisi = '';

    #[Validate('required|string|max:100')]
    public string $nama_pengemudi = '';

    #[Validate('required|string|max:150')]
    public string $asal_tujuan = '';

    #[Validate('required|string|max:150')]
    public string $keperluan = '';

    #[Validate('required|date')]
    public string $waktu_masuk = '';

    public string $pos_gerbang = 'Gerbang Utama';

    #[Validate('nullable|string|max:100')]
    public ?string $informasi_surat_jalan = null;

    #[Validate('required|in:sesuai,tidak_sesuai,tidak_ada_b3')]
    public string $status_pemeriksaan_surat_jalan_b3 = 'tidak_ada_b3';

    #[Validate('nullable|string|max:255')]
    public ?string $keterangan_muatan = null;

    #[Validate('nullable|string')]
    public ?string $catatan = null;

    /**
     * Inisialisasi awal form saat dibuka
     */
    public function mount(): void
    {
        // Otomatis generate nomor kunjungan dan jam masuk saat ini
        $this->nomor_kunjungan = VehicleVisit::generateNomorKunjungan();
        $this->waktu_masuk = now()->format('Y-m-d\TH:i');
    }

    /**
     * Simpan data kunjungan kendaraan masuk
     */
    public function simpan()
    {
        $this->validate();

        // Regenerasi nomor kunjungan untuk menjamin tidak bentrok bila ada satpam lain input bersamaan
        $nomorUnik = VehicleVisit::generateNomorKunjungan();

        VehicleVisit::create([
            'user_id' => auth()->id(),
            'nomor_kunjungan' => $nomorUnik,
            'nomor_polisi' => strtoupper(trim($this->nomor_polisi)),
            'nama_pengemudi' => trim($this->nama_pengemudi),
            'asal_tujuan' => trim($this->asal_tujuan),
            'keperluan' => trim($this->keperluan),
            'waktu_masuk' => $this->waktu_masuk,
            'waktu_keluar' => null, // Belum keluar (truk masih di dalam)
            'pos_gerbang' => $this->pos_gerbang,
            'informasi_surat_jalan' => $this->informasi_surat_jalan ? trim($this->informasi_surat_jalan) : null,
            'status_pemeriksaan_surat_jalan_b3' => $this->status_pemeriksaan_surat_jalan_b3,
            'keterangan_muatan' => $this->keterangan_muatan ? trim($this->keterangan_muatan) : null,
            'catatan' => $this->catatan ? trim($this->catatan) : null,
        ]);

        session()->flash('success', "Kendaraan {$this->nomor_polisi} berhasil dicatat masuk! (No: {$nomorUnik})");

        // Alihkan langsung ke halaman daftar kendaraan aktif
        return $this->redirectRoute('satpam.kendaraan-aktif', navigate: true);
    }

    public function render()
    {
        return view('livewire.satpam.kendaraan-masuk');
    }
}
