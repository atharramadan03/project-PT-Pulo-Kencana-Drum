<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'finance_invoice_id',
    'user_id',
    'pesan',
    'kanal',
    'status_respon',
    'tanggal_janji_bayar',
    'catatan_internal',
])]
class FinanceInvoiceChat extends Model
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
            'tanggal_janji_bayar' => 'date',
        ];
    }

    /**
     * Relasi ke Faktur Tagihan
     */
    public function financeInvoice(): BelongsTo
    {
        return $this->belongsTo(FinanceInvoice::class);
    }

    /**
     * Relasi ke Petugas yang menagih (Admin Keuangan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label status respon penagihan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_respon) {
            'janji_bayar' => 'Janji Bayar',
            'menunggu_transfer' => 'Menunggu Transfer',
            'komplain' => 'Komplain / Sengketa',
            'selesai' => 'Selesai Ditagih',
            default => 'Menunggu Respon',
        };
    }
}
