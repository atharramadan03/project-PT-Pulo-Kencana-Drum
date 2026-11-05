<?php

namespace App\Livewire\Keuangan;

use App\Models\FinanceInvoice;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceInvoices extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterJenis = '';

    public string $filterStatus = '';

    public ?string $tanggalMulai = null;

    public ?string $tanggalSelesai = null;

    // State Modal Tambah
    public bool $modalTerbuka = false;

    public string $kode_tagihan = '';

    public string $nomor_invoice = '';

    public string $jenis_tagihan = 'tagihan_pelanggan';

    public string $pihak_terkait = '';

    public string $kontak_pic = '';

    public string $nomor_wa = '';

    public string $nominal = '';

    public string $nominal_dibayar = '0';

    public string $tanggal_terbit = '';

    public string $tanggal_jatuh_tempo = '';

    public string $status_pembayaran = 'belum_dibayar';

    public string $metode_pembayaran = 'transfer_bank';

    public string $keterangan = '';

    // State Modal Pelunasan Cepat
    public bool $modalLunasTerbuka = false;

    public ?int $selectedInvoiceId = null;

    public string $lunas_tanggal = '';

    public string $lunas_metode = 'transfer_bank';

    public string $lunas_nominal = '';

    public ?FinanceInvoice $selectedInvoice = null;

    // State Modal Lengkapi WhatsApp Cepat
    public bool $modalWaTerbuka = false;

    public ?int $quickWaInvoiceId = null;

    public string $quick_wa_nomor = '';

    public string $quick_wa_pic = '';

    public ?FinanceInvoice $quickWaInvoice = null;

    // State Modal Konfirmasi Hapus
    public bool $modalHapusTerbuka = false;

    public ?int $hapusInvoiceId = null;

    public ?FinanceInvoice $hapusInvoice = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterJenis' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->tanggal_terbit = now()->toDateString();
        $this->tanggal_jatuh_tempo = now()->addDays(30)->toDateString();
        $this->lunas_tanggal = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenis(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function bukaModal(): void
    {
        $this->resetForm();
        $this->kode_tagihan = FinanceInvoice::generateKodeTagihan();
        $this->modalTerbuka = true;
    }

    public function tutupModal(): void
    {
        $this->modalTerbuka = false;
        $this->resetValidation();
    }

    public function resetForm(): void
    {
        $this->nomor_invoice = '';
        $this->jenis_tagihan = 'tagihan_pelanggan';
        $this->pihak_terkait = '';
        $this->kontak_pic = '';
        $this->nomor_wa = '';
        $this->nominal = '';
        $this->nominal_dibayar = '0';
        $this->tanggal_terbit = now()->toDateString();
        $this->tanggal_jatuh_tempo = now()->addDays(30)->toDateString();
        $this->status_pembayaran = 'belum_dibayar';
        $this->metode_pembayaran = 'transfer_bank';
        $this->keterangan = '';
    }

    public function simpanInvoice(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mencatat tagihan.');
        }

        $nominalClean = (float) str_replace(['.', ','], ['', '.'], $this->nominal);
        $nominalDibayarClean = (float) str_replace(['.', ','], ['', '.'], $this->nominal_dibayar);

        $this->validate([
            'nomor_invoice' => ['required', 'string', 'max:100'],
            'jenis_tagihan' => ['required', 'in:tagihan_pelanggan,tagihan_vendor'],
            'pihak_terkait' => ['required', 'string', 'max:255'],
            'kontak_pic' => ['nullable', 'string', 'max:100'],
            'nomor_wa' => ['nullable', 'string', 'max:30'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'nominal_dibayar' => ['nullable', 'numeric', 'min:0'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_terbit'],
            'status_pembayaran' => ['required', 'in:belum_dibayar,sebagian,lunas,jatuh_tempo'],
            'metode_pembayaran' => ['required', 'string'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nomor_invoice.required' => 'Nomor invoice / faktur cetak wajib diisi.',
            'jenis_tagihan.required' => 'Klasifikasi tagihan wajib dipilih.',
            'pihak_terkait.required' => 'Nama pihak terkait (klien/vendor) wajib diisi.',
            'nominal.required' => 'Total nilai tagihan wajib diisi.',
            'nominal.min' => 'Nominal tagihan minimal Rp 1.000.',
            'tanggal_terbit.required' => 'Tanggal terbit faktur wajib diisi.',
            'tanggal_jatuh_tempo.required' => 'Batas jatuh tempo faktur wajib diisi.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal terbit.',
            'status_pembayaran.required' => 'Status pembayaran awal wajib dipilih.',
            'metode_pembayaran.required' => 'Rencana metode pembayaran wajib dipilih.',
        ]);

        $kodeBaru = FinanceInvoice::generateKodeTagihan();

        // Tentukan tanggal lunas otomatis jika status lunas
        $tanggalLunas = null;
        if ($this->status_pembayaran === 'lunas') {
            $tanggalLunas = now()->toDateString();
            if ($nominalDibayarClean < $nominalClean) {
                $nominalDibayarClean = $nominalClean;
            }
        }

        FinanceInvoice::create([
            'user_id' => auth()->id(),
            'kode_tagihan' => $kodeBaru,
            'nomor_invoice' => $this->nomor_invoice,
            'jenis_tagihan' => $this->jenis_tagihan,
            'pihak_terkait' => $this->pihak_terkait,
            'kontak_pic' => $this->kontak_pic ?: null,
            'nomor_wa' => $this->nomor_wa ?: null,
            'nominal' => $nominalClean,
            'nominal_dibayar' => $nominalDibayarClean,
            'tanggal_terbit' => $this->tanggal_terbit,
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo,
            'tanggal_lunas' => $tanggalLunas,
            'status_pembayaran' => $this->status_pembayaran,
            'metode_pembayaran' => $this->metode_pembayaran,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->tutupModal();
        session()->flash('success', "Tagihan {$kodeBaru} ({$this->nomor_invoice}) berhasil dicatat.");
    }

    public function bukaModalLunas(int $id): void
    {
        $invoice = FinanceInvoice::findOrFail($id);
        $this->selectedInvoiceId = $invoice->id;
        $this->selectedInvoice = $invoice;
        $this->lunas_tanggal = now()->toDateString();
        $this->lunas_metode = $invoice->metode_pembayaran ?: 'transfer_bank';
        $this->lunas_nominal = (string) $invoice->sisa_tagihan;
        $this->modalLunasTerbuka = true;
    }

    public function tutupModalLunas(): void
    {
        $this->modalLunasTerbuka = false;
        $this->selectedInvoiceId = null;
        $this->selectedInvoice = null;
    }

    public function konfirmasiPelunasan(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mengubah status tagihan.');
        }

        $this->validate([
            'lunas_nominal' => ['required', 'numeric', 'min:1'],
            'lunas_tanggal' => ['required', 'date'],
            'lunas_metode' => ['required', 'string'],
        ], [
            'lunas_nominal.required' => 'Nominal pembayaran wajib diisi.',
            'lunas_nominal.min' => 'Nominal pembayaran minimal Rp 1.',
            'lunas_tanggal.required' => 'Tanggal pembayaran wajib diisi.',
            'lunas_metode.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $invoice = FinanceInvoice::findOrFail($this->selectedInvoiceId);

        $nominalBayar = (float) str_replace(['.', ','], ['', '.'], $this->lunas_nominal);

        $totalDibayarBaru = (float) $invoice->nominal_dibayar + $nominalBayar;
        $statusBaru = ($totalDibayarBaru >= (float) $invoice->nominal) ? 'lunas' : 'sebagian';
        $tanggalLunas = ($statusBaru === 'lunas') ? $this->lunas_tanggal : null;

        $invoice->update([
            'nominal_dibayar' => min($totalDibayarBaru, (float) $invoice->nominal),
            'status_pembayaran' => $statusBaru,
            'tanggal_lunas' => $tanggalLunas,
            'metode_pembayaran' => $this->lunas_metode,
        ]);

        $this->tutupModalLunas();
        session()->flash('success', "Status pembayaran tagihan {$invoice->kode_tagihan} berhasil diperbarui.");
    }

    public function bukaModalWa(int $id): void
    {
        $invoice = FinanceInvoice::findOrFail($id);
        $this->quickWaInvoiceId = $invoice->id;
        $this->quickWaInvoice = $invoice;
        $this->quick_wa_pic = $invoice->kontak_pic ?? '';
        $this->quick_wa_nomor = $invoice->nomor_wa ?? '';
        $this->modalWaTerbuka = true;
    }

    public function tutupModalWa(): void
    {
        $this->modalWaTerbuka = false;
        $this->quickWaInvoiceId = null;
        $this->quickWaInvoice = null;
        $this->quick_wa_nomor = '';
        $this->quick_wa_pic = '';
        $this->resetValidation();
    }

    public function simpanNomorWa(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak mengubah kontak penagihan.');
        }

        $this->validate([
            'quick_wa_nomor' => ['required', 'string', 'min:9', 'max:25'],
            'quick_wa_pic' => ['nullable', 'string', 'max:100'],
        ], [
            'quick_wa_nomor.required' => 'Nomor WhatsApp pelanggan wajib diisi.',
            'quick_wa_nomor.min' => 'Nomor WhatsApp minimal 9 digit angka.',
        ]);

        $invoice = FinanceInvoice::findOrFail($this->quickWaInvoiceId);

        $invoice->update([
            'nomor_wa' => $this->quick_wa_nomor,
            'kontak_pic' => $this->quick_wa_pic ?: $invoice->kontak_pic,
            'terakhir_ditagih_at' => now(),
        ]);

        $directUrl = $invoice->fresh()->whatsapp_direct_url;

        $this->tutupModalWa();
        session()->flash('success', "Nomor WhatsApp berhasil disimpan untuk tagihan {$invoice->kode_tagihan}.");

        if ($directUrl) {
            $this->dispatch('buka-wa-langsung', url: $directUrl);
        }
    }

    public function konfirmasiHapus(int $id): void
    {
        $invoice = FinanceInvoice::findOrFail($id);
        $this->hapusInvoiceId = $invoice->id;
        $this->hapusInvoice = $invoice;
        $this->modalHapusTerbuka = true;
    }

    public function tutupModalHapus(): void
    {
        $this->modalHapusTerbuka = false;
        $this->hapusInvoiceId = null;
        $this->hapusInvoice = null;
    }

    public function hapusTagihan(): void
    {
        if (! auth()->user()->isAdminKeuangan()) {
            abort(403, 'Hanya Admin Keuangan yang berhak menghapus tagihan.');
        }

        $invoice = FinanceInvoice::findOrFail($this->hapusInvoiceId);
        $kode = $invoice->kode_tagihan;
        $nomor = $invoice->nomor_invoice;

        // Hapus relasi chat jika ada
        $invoice->chats()->delete();
        $invoice->delete();

        $this->tutupModalHapus();
        session()->flash('success', "Tagihan {$kode} ({$nomor}) berhasil dihapus dari sistem.");
    }

    public function render()
    {
        $query = FinanceInvoice::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_tagihan', 'like', "%{$this->search}%")
                    ->orWhere('nomor_invoice', 'like', "%{$this->search}%")
                    ->orWhere('pihak_terkait', 'like', "%{$this->search}%")
                    ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterJenis) {
            $query->where('jenis_tagihan', $this->filterJenis);
        }

        if ($this->filterStatus) {
            $query->where('status_pembayaran', $this->filterStatus);
        }

        if ($this->tanggalMulai) {
            $query->whereDate('tanggal_terbit', '>=', $this->tanggalMulai);
        }

        if ($this->tanggalSelesai) {
            $query->whereDate('tanggal_terbit', '<=', $this->tanggalSelesai);
        }

        $invoices = $query->orderBy('tanggal_terbit', 'desc')->paginate(10);

        // Perhitungan Statistik KPI
        $totalSemuaTagihan = (float) FinanceInvoice::sum('nominal');
        $totalBelumLunas = (float) FinanceInvoice::whereIn('status_pembayaran', ['belum_dibayar', 'sebagian'])->selectRaw('SUM(nominal - nominal_dibayar) as sisa')->value('sisa') ?? 0;
        $totalSudahLunas = (float) FinanceInvoice::where('status_pembayaran', 'lunas')->sum('nominal');
        $tagihanJatuhTempoCount = FinanceInvoice::whereIn('status_pembayaran', ['belum_dibayar', 'sebagian'])
            ->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->count();

        return view('livewire.keuangan.finance-invoices', [
            'invoices' => $invoices,
            'totalSemuaTagihan' => $totalSemuaTagihan,
            'totalBelumLunas' => $totalBelumLunas,
            'totalSudahLunas' => $totalSudahLunas,
            'tagihanJatuhTempoCount' => $tagihanJatuhTempoCount,
        ]);
    }
}
