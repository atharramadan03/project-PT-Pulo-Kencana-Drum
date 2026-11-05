<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_supplier',
    'nama_supplier',
    'kategori_pasokan',
    'nama_pic',
    'nomor_telepon',
    'alamat',
    'status_kerjasama',
    'catatan',
])]
class Supplier extends Model
{
    use HasFactory;

    /**
     * Generator kode unik supplier: SUP-YYYYMM-001
     */
    public static function generateKodeSupplier(): string
    {
        $prefix = 'SUP-'.now()->format('Ym');
        $last = self::where('kode_supplier', 'like', "{$prefix}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($last && preg_match('/-(\d+)$/', $last->kode_supplier, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Relasi ke Transaksi Keuangan Barang Masuk
     */
    public function warehouseFinanceLogs(): HasMany
    {
        return $this->hasMany(WarehouseFinanceLog::class);
    }
}
