<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'kode_tagihan',
    'nomor_invoice',
    'jenis_tagihan',
    'pihak_terkait',
    'kontak_pic',
    'nomor_wa',
    'nominal',
    'nominal_dibayar',
    'tanggal_terbit',
    'tanggal_jatuh_tempo',
    'tanggal_lunas',
    'status_pembayaran',
    'metode_pembayaran',
    'keterangan',
    'terakhir_ditagih_at',
])]
class FinanceInvoice extends Model
{
    use HasFactory;

    /**
     * Tipe data casting atribut
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_lunas' => 'date',
            'terakhir_ditagih_at' => 'datetime',
            'nominal' => 'decimal:2',
            'nominal_dibayar' => 'decimal:2',
        ];
    }

    /**
     * Generator kode unik tagihan: INV-YYYYMM-001
     */
    public static function generateKodeTagihan(): string
    {
        $prefix = 'INV-'.now()->format('Ym');
        $lastInvoice = self::where('kode_tagihan', 'like', "{$prefix}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastInvoice && preg_match('/-(\d+)$/', $lastInvoice->kode_tagihan, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Relasi ke User (Admin Keuangan yang mencatat)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Riwayat Chat / Follow-up Penagihan
     */
    public function chats(): HasMany
    {
        return $this->hasMany(FinanceInvoiceChat::class)->latest();
    }

    /**
     * Helper pembersih format nomor WhatsApp ke format internasional 628...
     */
    public function getFormattedNomorWaAttribute(): string
    {
        if (empty($this->nomor_wa)) {
            return '';
        }

        $cleaned = preg_replace('/[^0-9]/', '', $this->nomor_wa);

        if (str_starts_with($cleaned, '0')) {
            return '62'.substr($cleaned, 1);
        }

        if (! str_starts_with($cleaned, '62') && strlen($cleaned) >= 8) {
            return '62'.$cleaned;
        }

        return $cleaned;
    }

    /**
     * Label waktu terakhir ditagih
     */
    public function getTerakhirDitagihHumanAttribute(): ?string
    {
        return $this->terakhir_ditagih_at ? $this->terakhir_ditagih_at->locale('id')->diffForHumans() : null;
    }

    /**
     * Pesan penagihan default untuk tautan WhatsApp langsung
     */
    public function getDefaultReminderMessageAttribute(): string
    {
        $pic = $this->kontak_pic ?: 'Bapak/Ibu Manajemen';
        $pihak = $this->pihak_terkait;
        $total = $this->formatted_nominal;
        $sisa = $this->formatted_sisa_tagihan;
        $nomor = $this->nomor_invoice;
        $tglTempo = $this->tanggal_jatuh_tempo ? $this->tanggal_jatuh_tempo->locale('id')->isoFormat('D MMMM Y') : '-';

        $rincianNilai = ($this->nominal_dibayar > 0)
            ? "sisa tagihan sebesar {$sisa} (dari total nilai faktur {$total}, pembayaran diterima Rp ".number_format((float) $this->nominal_dibayar, 0, ',', '.').')'
            : "total tagihan sebesar {$total}";

        if ($this->tanggal_jatuh_tempo && $this->tanggal_jatuh_tempo < now()->toDateString()) {
            return "Yth. {$pic} - {$pihak},\n\nMelalui pesan ini kami dari Departemen Keuangan PT Pulo Kencana Industri menginformasikan bahwa Faktur No. {$nomor} dengan {$rincianNilai} per hari ini TELAH MELEWATI batas jatuh tempo ({$tglTempo}).\n\nMohon konfirmasi penyelesaian pembayaran ke rekening resmi kami:\n- BCA: 123-456-7890 a.n. PT Pulo Kencana Industri\n- Mandiri: 156-00-1234567-8 a.n. PT Pulo Kencana Industri\n\nTerima kasih atas perhatian dan kerjasamanya.";
        }

        return "Yth. {$pic} - {$pihak},\n\nKami dari Departemen Keuangan PT Pulo Kencana Industri menginformasikan bahwa Faktur No. {$nomor} dengan {$rincianNilai} akan jatuh tempo pada {$tglTempo}.\n\nPembayaran dapat ditransfer ke rekening resmi kami:\n- BCA: 123-456-7890 a.n. PT Pulo Kencana Industri\n- Mandiri: 156-00-1234567-8 a.n. PT Pulo Kencana Industri\n\nMohon konfirmasi setelah melakukan transfer. Terima kasih.";
    }

    /**
     * Tautan langsung WhatsApp (wa.me)
     */
    public function getWhatsappDirectUrlAttribute(): ?string
    {
        if (empty($this->formatted_nomor_wa)) {
            return null;
        }

        return 'https://wa.me/'.$this->formatted_nomor_wa.'?text='.urlencode($this->default_reminder_message);
    }

    /**
     * Sisa tagihan nominal
     */
    public function getSisaTagihanAttribute(): float
    {
        return max(0, (float) $this->nominal - (float) $this->nominal_dibayar);
    }

    /**
     * Format nominal Rupiah
     */
    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    /**
     * Format sisa tagihan Rupiah
     */
    public function getFormattedSisaTagihanAttribute(): string
    {
        return 'Rp '.number_format($this->sisa_tagihan, 0, ',', '.');
    }
}
