<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nik_karyawan',
    'nama_karyawan',
    'departemen',
    'jabatan',
    'nomor_telepon',
    'status_kerja',
    'tanggal_masuk',
    'catatan',
])]
class Employee extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
        ];
    }

    /**
     * Generator NIK unik karyawan: EMP-YYYYMM-001
     */
    public static function generateNikKaryawan(): string
    {
        $prefix = 'EMP-'.now()->format('Ym');
        $last = self::where('nik_karyawan', 'like', "{$prefix}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($last && preg_match('/-(\d+)$/', $last->nik_karyawan, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Relasi ke Log Biaya Operasional yang dikelola oleh karyawan
     */
    public function operationalExpenseLogs(): HasMany
    {
        return $this->hasMany(OperationalExpenseLog::class);
    }
}
