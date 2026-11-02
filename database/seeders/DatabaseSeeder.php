<?php

namespace Database\Seeders;

use App\Models\FinanceInvoice;
use App\Models\OperationalExpenseLog;
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

        if (FinanceInvoice::count() === 0) {
            FinanceInvoice::create([
                'user_id' => $keuangan->id,
                'kode_tagihan' => 'INV-202611-001',
                'nomor_invoice' => 'INV/PKDI/2026/XI/012',
                'jenis_tagihan' => 'tagihan_pelanggan',
                'pihak_terkait' => 'PT Agro Pelita Nusantara',
                'nominal' => 31200000,
                'nominal_dibayar' => 31200000,
                'tanggal_terbit' => now()->subDays(5)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(25)->toDateString(),
                'tanggal_lunas' => now()->subDays(1)->toDateString(),
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => 'transfer_bank',
                'keterangan' => 'Pelunasan PO 120 Unit Drum Metal Coating Kuning',
            ]);

            FinanceInvoice::create([
                'user_id' => $keuangan->id,
                'kode_tagihan' => 'INV-202611-002',
                'nomor_invoice' => 'FKT-SUP/SKM/8812',
                'jenis_tagihan' => 'tagihan_vendor',
                'pihak_terkait' => 'PT Surya Kimia Murni',
                'nominal' => 14500000,
                'nominal_dibayar' => 5000000,
                'tanggal_terbit' => now()->subDays(3)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(11)->toDateString(),
                'status_pembayaran' => 'sebagian',
                'metode_pembayaran' => 'transfer_bank',
                'keterangan' => 'Pengadaan chemical degreaser dan soda api pencucian drum',
            ]);

            FinanceInvoice::create([
                'user_id' => $keuangan->id,
                'kode_tagihan' => 'INV-202611-003',
                'nomor_invoice' => 'INV/PKDI/2026/XI/015',
                'jenis_tagihan' => 'tagihan_pelanggan',
                'pihak_terkait' => 'CV Sumber Kimia Abadi',
                'nominal' => 18750000,
                'nominal_dibayar' => 0,
                'tanggal_terbit' => now()->subDays(1)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(13)->toDateString(),
                'status_pembayaran' => 'belum_dibayar',
                'metode_pembayaran' => 'transfer_bank',
                'keterangan' => 'Tagihan termin 1 pengiriman 90 unit drum rekondisi biru',
            ]);
        }

        if (OperationalExpenseLog::count() === 0) {
            OperationalExpenseLog::create([
                'user_id' => $keuangan->id,
                'kode_biaya' => 'BOP-202611-001',
                'kategori_biaya' => 'listrik_air',
                'nama_biaya' => 'Tagihan Listrik PLN Pabrik & Pompa Cuci Drum',
                'nominal' => 4850000,
                'tanggal_pengeluaran' => now()->subDays(4)->toDateString(),
                'metode_pembayaran' => 'transfer_bank',
                'nomor_bukti' => 'PLN-REK-202610-9921',
                'penerima_dana' => 'PT PLN (Persero)',
                'keterangan' => 'Daya 33.000 VA operasional workshop dan gudang',
            ]);

            OperationalExpenseLog::create([
                'user_id' => $keuangan->id,
                'kode_biaya' => 'BOP-202611-002',
                'kategori_biaya' => 'bbm_solar',
                'nama_biaya' => 'Pengisian Solar Truk Armada B 9123 KDA & Genset',
                'nominal' => 1400000,
                'tanggal_pengeluaran' => now()->subDays(2)->toDateString(),
                'metode_pembayaran' => 'tunai_kas_kecil',
                'nomor_bukti' => 'SPBU-34-17129-A',
                'penerima_dana' => 'SPBU Pertamina Cikarang',
                'keterangan' => 'Pengiriman drum ke kawasan industri Cikarang',
            ]);

            OperationalExpenseLog::create([
                'user_id' => $keuangan->id,
                'kode_biaya' => 'BOP-202611-003',
                'kategori_biaya' => 'perlengkapan_apd',
                'nama_biaya' => 'Pengadaan Masker Gas Respirator B3 & Sarung Tangan Karet',
                'nominal' => 850000,
                'tanggal_pengeluaran' => now()->subDays(1)->toDateString(),
                'metode_pembayaran' => 'tunai_kas_kecil',
                'nomor_bukti' => 'NOTA-K3-4410',
                'penerima_dana' => 'Toko Safety Mandiri',
                'keterangan' => 'Kebutuhan APD tim peniupan & pencucian drum chemical',
            ]);
        }
    }
}
