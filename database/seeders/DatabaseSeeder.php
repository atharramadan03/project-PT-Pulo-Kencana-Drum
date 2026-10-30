<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VehicleMaintenanceLog;
use App\Models\WarehouseFinanceLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Eksekusi seeder untuk mengisi data awal database pkdi_gudang.
     */
    public function run(): void
    {
        // 1. Akun Resmi Admin Kantor
        User::updateOrCreate(
            ['email' => 'admin@pkdi.co.id'],
            [
                'name' => 'Admin Kantor Gudang',
                'username' => 'admin_kantor',
                'password' => Hash::make('password123'),
                'role' => 'admin_kantor',
                'shift_jaga' => null,
                'status_akun' => 'aktif',
            ]
        );

        // 2. Akun Resmi Satpam Pos Gerbang
        User::updateOrCreate(
            ['email' => 'satpam@pkdi.co.id'],
            [
                'name' => 'Satpam Pos Gerbang',
                'username' => 'satpam_gerbang',
                'password' => Hash::make('password123'),
                'role' => 'satpam',
                'shift_jaga' => 'Pagi',
                'status_akun' => 'aktif',
            ]
        );

        // 3. Akun Resmi Admin Keuangan
        $keuangan = User::updateOrCreate(
            ['email' => 'keuangan@pkdi.co.id'],
            [
                'name' => 'Admin Keuangan',
                'username' => 'admin_keuangan',
                'password' => Hash::make('password123'),
                'role' => 'admin_keuangan',
                'shift_jaga' => null,
                'status_akun' => 'aktif',
            ]
        );

        // Data Awal Transaksi Keuangan Stok Gudang (Pemasukan & Pengeluaran)
        if (WarehouseFinanceLog::count() === 0) {
            WarehouseFinanceLog::create([
                'user_id' => $keuangan->id,
                'drum_batch_id' => null,
                'kode_transaksi' => 'TRX-STK-202610-001',
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'pembelian_batch',
                'tanggal_transaksi' => now()->subDays(6)->toDateString(),
                'jumlah_unit' => 150,
                'nominal' => 18750000,
                'metode_pembayaran' => 'transfer',
                'pihak_terkait' => 'PT Mitra Logam Bersama',
                'keterangan' => 'Pembelian bahan baku 150 drum besi open top',
            ]);

            WarehouseFinanceLog::create([
                'user_id' => $keuangan->id,
                'drum_batch_id' => null,
                'kode_transaksi' => 'TRX-STK-202610-002',
                'jenis_transaksi' => 'pengeluaran',
                'kategori' => 'bahan_operasional',
                'tanggal_transaksi' => now()->subDays(4)->toDateString(),
                'jumlah_unit' => 0,
                'nominal' => 4500000,
                'metode_pembayaran' => 'transfer',
                'pihak_terkait' => 'Toko Cat Kimia Mandiri',
                'keterangan' => 'Pembelian thinner & cat epoxy biru drum industri',
            ]);

            WarehouseFinanceLog::create([
                'user_id' => $keuangan->id,
                'drum_batch_id' => null,
                'kode_transaksi' => 'TRX-STK-202610-003',
                'jenis_transaksi' => 'pemasukan',
                'kategori' => 'penjualan_drum',
                'tanggal_transaksi' => now()->subDays(3)->toDateString(),
                'jumlah_unit' => 90,
                'nominal' => 22500000,
                'metode_pembayaran' => 'transfer',
                'pihak_terkait' => 'CV Sumber Kimia Cikarang',
                'keterangan' => 'Penjualan 90 drum rekondisi siap pakai',
            ]);

            WarehouseFinanceLog::create([
                'user_id' => $keuangan->id,
                'drum_batch_id' => null,
                'kode_transaksi' => 'TRX-STK-202610-004',
                'jenis_transaksi' => 'pemasukan',
                'kategori' => 'penjualan_drum',
                'tanggal_transaksi' => now()->subDays(1)->toDateString(),
                'jumlah_unit' => 120,
                'nominal' => 31200000,
                'metode_pembayaran' => 'tempo',
                'pihak_terkait' => 'PT Agro Pelita Nusantara',
                'keterangan' => 'Pengiriman 120 unit drum galvanis selesai rekondisi',
            ]);
        }

        // Data Awal Biaya Perbaikan Mobil / Armada Operasional
        if (VehicleMaintenanceLog::count() === 0) {
            VehicleMaintenanceLog::create([
                'user_id' => $keuangan->id,
                'nomor_polisi' => 'B 9481 KDA',
                'jenis_kendaraan' => 'Truk Tronton Box',
                'tanggal_perbaikan' => now()->subDays(5)->toDateString(),
                'jenis_perbaikan' => 'Ganti Oli Mesin & Filter Oli',
                'nama_bengkel' => 'Bengkel Sumber Diesel',
                'biaya' => 2450000,
                'status_perbaikan' => 'selesai',
                'keterangan' => 'Servis berkala rutin 10.000 KM armada distribusi',
            ]);

            VehicleMaintenanceLog::create([
                'user_id' => $keuangan->id,
                'nomor_polisi' => 'B 9123 KDA',
                'jenis_kendaraan' => 'Truk Engkel Bak',
                'tanggal_perbaikan' => now()->subDays(2)->toDateString(),
                'jenis_perbaikan' => 'Perbaikan Sistem Rem & Kampas Baru',
                'nama_bengkel' => 'Bengkel Mobil Maju Jaya',
                'biaya' => 1800000,
                'status_perbaikan' => 'selesai',
                'keterangan' => 'Penggantian kampas rem roda depan dan kuras minyak rem',
            ]);

            VehicleMaintenanceLog::create([
                'user_id' => $keuangan->id,
                'nomor_polisi' => 'B 9088 TDA',
                'jenis_kendaraan' => 'Pickup Operasional',
                'tanggal_perbaikan' => now()->toDateString(),
                'jenis_perbaikan' => 'Ganti 2 Ban Luar Belakang',
                'nama_bengkel' => 'Toko Ban Sentosa',
                'biaya' => 1650000,
                'status_perbaikan' => 'dalam_pengerjaan',
                'keterangan' => 'Ban belakang aus tergores material gudang',
            ]);
        }
    }
}
