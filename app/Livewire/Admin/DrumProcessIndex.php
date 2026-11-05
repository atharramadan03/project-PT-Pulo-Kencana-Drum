<?php

namespace App\Livewire\Admin;

use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DrumProcessIndex extends Component
{
    use WithPagination;

    // Filter
    public string $search = '';

    public string $filterJenisProses = '';

    public string $filterBatchId = '';

    // Modal Input Log Proses Baru
    public bool $showCreateModal = false;

    // Properti Modal Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusLogId = null;

    public ?DrumProcessLog $targetLogHapus = null;

    #[Validate('required|exists:drum_batches,id')]
    public ?int $drum_batch_id = null;

    #[Validate('required|in:penyortiran,peniupan,pencucian,pengecatan')]
    public string $jenis_proses = 'penyortiran';

    #[Validate('required|date')]
    public string $tanggal_aktivitas = '';

    #[Validate('required|integer|min:1')]
    public int $jumlah_diproses = 50;

    #[Validate('required|integer|min:0')]
    public int $jumlah_afkir = 0;

    #[Validate('required|string|max:100')]
    public string $pelapor_gudang = '';

    #[Validate('nullable|string')]
    public ?string $hasil_catatan = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenisProses(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBatchId(): void
    {
        $this->resetPage();
    }

    /**
     * Pesan validasi kustom Bahasa Indonesia
     */
    protected function messages(): array
    {
        return [
            'drum_batch_id.required' => 'Pilih batch drum yang diproses.',
            'drum_batch_id.exists' => 'Batch drum yang dipilih tidak valid.',
            'jenis_proses.required' => 'Jenis tahapan proses gudang wajib dipilih.',
            'tanggal_aktivitas.required' => 'Tanggal aktivitas proses wajib diisi.',
            'jumlah_diproses.required' => 'Jumlah drum yang diproses wajib diisi.',
            'jumlah_diproses.min' => 'Jumlah drum diproses minimal 1 unit.',
            'jumlah_afkir.required' => 'Jumlah drum afkir/rusak wajib diisi (isi 0 jika tidak ada).',
            'jumlah_afkir.min' => 'Jumlah afkir tidak boleh negatif.',
            'pelapor_gudang.required' => 'Nama pelapor/mandor gudang wajib diisi.',
        ];
    }

    public function bukaCreateModal(): void
    {
        $this->resetValidation();
        $firstBatch = DrumBatch::orderBy('id', 'desc')->first();
        $this->drum_batch_id = $firstBatch?->id;
        $this->jenis_proses = 'penyortiran';
        $this->tanggal_aktivitas = now()->format('Y-m-d');
        $this->jumlah_diproses = 50;
        $this->jumlah_afkir = 0;
        $this->pelapor_gudang = 'Mandor Gudang';
        $this->hasil_catatan = null;
        $this->showCreateModal = true;
    }

    public function tutupCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function simpanLog(): void
    {
        $this->validate();

        // Validasi SOP: Jumlah reject tidak boleh melebihi jumlah yang diproses
        if ($this->jumlah_afkir > $this->jumlah_diproses) {
            $this->addError('jumlah_afkir', 'Jumlah reject tidak boleh melebihi jumlah drum yang diproses.');

            return;
        }

        $log = DrumProcessLog::create([
            'drum_batch_id' => $this->drum_batch_id,
            'user_id' => auth()->id(),
            'jenis_proses' => $this->jenis_proses,
            'tanggal_aktivitas' => $this->tanggal_aktivitas,
            'tanggal_input' => now(), // Waktu nyata saat Admin mengetik ke sistem
            'jumlah_diproses' => $this->jumlah_diproses,
            'jumlah_afkir' => $this->jumlah_afkir,
            'pelapor_gudang' => trim($this->pelapor_gudang),
            'hasil_catatan' => $this->hasil_catatan ? trim($this->hasil_catatan) : null,
        ]);

        // Perbarui status batch jika sebelumnya masih 'Diterima'
        $batch = DrumBatch::find($this->drum_batch_id);
        if ($batch && $batch->status_proses === 'Diterima') {
            $batch->update(['status_proses' => 'Dalam Proses']);
        }

        session()->flash('success', "Log proses {$log->label_proses} untuk batch {$batch?->kode_batch} berhasil dicatat!");

        $this->tutupCreateModal();
    }

    public function konfirmasiHapus(int $id): void
    {
        $log = DrumProcessLog::with('drumBatch')->findOrFail($id);
        $this->hapusLogId = $log->id;
        $this->targetLogHapus = $log;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusLogId = null;
        $this->targetLogHapus = null;
    }

    public function hapusLog(): void
    {
        $log = DrumProcessLog::findOrFail($this->hapusLogId);
        $jenis = $log->label_proses;
        $log->delete();

        $this->tutupModalHapus();
        session()->flash('success', "Catatan proses {$jenis} berhasil dihapus dari sistem.");
    }

    public function render()
    {
        $query = DrumProcessLog::with(['drumBatch', 'user']);

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('pelapor_gudang', 'like', "%{$s}%")
                    ->orWhere('hasil_catatan', 'like', "%{$s}%")
                    ->orWhereHas('drumBatch', function ($b) use ($s) {
                        $b->where('kode_batch', 'like', "%{$s}%");
                    });
            });
        }

        if ($this->filterJenisProses) {
            $query->where('jenis_proses', $this->filterJenisProses);
        }

        if ($this->filterBatchId) {
            $query->where('drum_batch_id', $this->filterBatchId);
        }

        $logs = $query->orderBy('tanggal_aktivitas', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $daftarBatch = DrumBatch::orderBy('id', 'desc')->get();

        return view('livewire.admin.drum-process-index', [
            'logs' => $logs,
            'daftarBatch' => $daftarBatch,
        ]);
    }
}
