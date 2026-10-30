<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'drum_batch_id',
    'kode_transaksi',
    'jenis_transaksi',
    'kategori',
    'tanggal_transaksi',
    'jumlah_unit',
    'nominal',
    'metode_pembayaran',
    'pihak_terkait',
    'keterangan',
])]
class WarehouseFinanceLog extends Model
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
            'tanggal_transaksi' => 'date',
            'nominal' => 'decimal:2',
            'jumlah_unit' => 'integer',
        ];
    }

    /**
     * Generator kode unik transaksi keuangan stok: TRX-STK-YYYYMM-001
     */
    public static function generateKodeTransaksi(): string
    {
        $prefix = 'TRX-STK-'.now()->format('Ym');
        $lastLog = self::where('kode_transaksi', 'like', "{$prefix}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastLog && preg_match('/-(\d+)$/', $lastLog->kode_transaksi, $matches)) {
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
     * Relasi ke DrumBatch (opsional)
     */
    public function drumBatch(): BelongsTo
    {
        return $this->belongsTo(DrumBatch::class);
    }

    /**
     * Format nominal ke format Rupiah standar
     */
    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }
}
