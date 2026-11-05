<?php

namespace Database\Seeders;

use App\Models\DrumBatch;
use App\Models\DrumProcessLog;
use App\Models\User;
use App\Models\VehicleVisit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WarehouseAndSecuritySeeder extends Seeder
{
    /**
     * Run the database seeds for Satpam and Admin Gudang.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin_kantor')->first() ?? User::first();
        $satpam = User::where('role', 'satpam')->first() ?? User::first();

        if (! $admin || ! $satpam) {
            return;
        }

        $today = Carbon::today();
        $todayStr = now()->format('Ymd');

        // =========================================================================
        // 1. DATA DUMMY BAGIAN SATPAM (5 Kunjungan Kendaraan Truk di Pos Gerbang)
        // =========================================================================
        $kunjunganData = [
            [
                'nomor_kunjungan' => "KJ-{$todayStr}-0001",
                'nomor_polisi' => 'B 9481 KDA',
                'nama_pengemudi' => 'Dedi Suryana',
                'asal_tujuan' => 'PT Mitra Logam Bersama (Cikarang)',
                'keperluan' => 'Bongkar muatan 150 unit drum besi bekas (Pengadaan Bahan Baku)',
                'waktu_masuk' => Carbon::now()->setTime(8, 15),
                'waktu_keluar' => Carbon::now()->setTime(10, 45),
                'pos_gerbang' => 'Gerbang Utama',
                'informasi_surat_jalan' => 'SJ/MLB/2026/XI/088',
                'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
                'keterangan_muatan' => '150 Drum Besi Tight Head ex-pelumas industri',
                'catatan' => 'Surat jalan B3 lengkap dengan manifes KLHK. Muatan bersih, drum selesai dibongkar di Staging Area A.',
            ],
            [
                'nomor_kunjungan' => "KJ-{$todayStr}-0002",
                'nomor_polisi' => 'B 9123 KDA',
                'nama_pengemudi' => 'Agus Prabowo',
                'asal_tujuan' => 'CV Sumber Kimia Cikarang',
                'keperluan' => 'Pemuatan & distribusi 90 unit drum rekondisi biru siap pakai',
                'waktu_masuk' => Carbon::now()->setTime(9, 30),
                'waktu_keluar' => Carbon::now()->setTime(11, 20),
                'pos_gerbang' => 'Gerbang Utama',
                'informasi_surat_jalan' => 'SJ/PKDI/2026/XI/104',
                'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
                'keterangan_muatan' => '90 Drum Rekondisi Siap Pakai (Coating Biru Oven)',
                'catatan' => 'Truk armada internal PKDI pengiriman penjualan batch selesai rekondisi. Segel pintu terpasang.',
            ],
            [
                'nomor_kunjungan' => "KJ-{$todayStr}-0003",
                'nomor_polisi' => 'B 9822 UXT',
                'nama_pengemudi' => 'Bambang Sutrisno',
                'asal_tujuan' => 'PT Petro Kimia Gresik (Depo Marunda)',
                'keperluan' => 'Pengiriman bahan kimia caustic soda cair & chemical degreaser drum',
                'waktu_masuk' => Carbon::now()->setTime(10, 40),
                'waktu_keluar' => null, // Truk masih AKTIF di dalam pabrik
                'pos_gerbang' => 'Gerbang Utama',
                'informasi_surat_jalan' => 'SJ-PKG/MRD/4412',
                'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
                'keterangan_muatan' => '25 Jerigen Caustic Soda & 10 Pail Degreaser Pembersih',
                'catatan' => 'Truk parkir di Dermaga Kimia Workshop 2. Pengemudi mematuhi APD helm & rompi K3.',
            ],
            [
                'nomor_kunjungan' => "KJ-{$todayStr}-0004",
                'nomor_polisi' => 'B 9301 SJA',
                'nama_pengemudi' => 'Wahyu Hidayat',
                'asal_tujuan' => 'PT Agro Pelita Nusantara (Karawang)',
                'keperluan' => 'Pengambilan 120 unit drum galvanis pesanan ekspor minyak nabati',
                'waktu_masuk' => Carbon::now()->setTime(13, 15),
                'waktu_keluar' => null, // Truk masih AKTIF di dalam pabrik
                'pos_gerbang' => 'Gerbang Utama',
                'informasi_surat_jalan' => 'PO-APN/XI/9901',
                'status_pemeriksaan_surat_jalan_b3' => 'tidak_ada_b3',
                'keterangan_muatan' => '120 Drum Galvanis Anti-Karat 200L',
                'catatan' => 'Sedang proses muat fork-lift di Gudang B. Estimasi selesai muat 1 jam.',
            ],
            [
                'nomor_kunjungan' => "KJ-{$todayStr}-0005",
                'nomor_polisi' => 'B 9088 TDA',
                'nama_pengemudi' => 'Joko Susilo',
                'asal_tujuan' => 'Toko Cat Kimia Mandiri (Pulogadung)',
                'keperluan' => 'Pasokan cat oven epoxy warna biru industri, thinner super, dan klem ring',
                'waktu_masuk' => Carbon::now()->setTime(14, 00),
                'waktu_keluar' => null, // Truk masih AKTIF di dalam pabrik
                'pos_gerbang' => 'Gerbang Timur',
                'informasi_surat_jalan' => 'TCKM/DO/2026/1102',
                'status_pemeriksaan_surat_jalan_b3' => 'sesuai',
                'keterangan_muatan' => '10 Drum Cat Epoxy Biru Industri & 8 Drum Thinner Cuci',
                'catatan' => 'Pemeriksaan surat jalan dan MSDS bahan cat telah diverifikasi di Pos Timur.',
            ],
        ];

        $createdVisits = [];
        foreach ($kunjunganData as $data) {
            $createdVisits[] = VehicleVisit::updateOrCreate(
                ['nomor_kunjungan' => $data['nomor_kunjungan']],
                array_merge($data, ['user_id' => $satpam->id])
            );
        }

        // =========================================================================
        // 2. DATA DUMMY BAGIAN ADMIN GUDANG (5 Batch Drum & Log Proses Rekondisi)
        // =========================================================================
        $batchData = [
            [
                'kode_batch' => 'BATCH-202611-001',
                'tanggal_penerimaan' => Carbon::now()->subDays(4)->toDateString(),
                'jumlah_diterima' => 150,
                'kategori_jenis' => 'Besi Tight Head',
                'status_pengendapan' => 'Selesai',
                'status_proses' => 'Selesai',
                'sumber_laporan' => 'Surat Jalan PT Mitra Logam Bersama (SJ/MLB/2026/XI/088)',
                'catatan' => 'Batch drum besi ex-pelumas pabrik industri. Telah selesai melewati seluruh lini rekondisi dan siap distribusi.',
                'logs' => [
                    [
                        'jenis_proses' => 'penyortiran',
                        'tanggal_aktivitas' => Carbon::now()->subDays(4)->toDateString(),
                        'jumlah_diproses' => 150,
                        'jumlah_afkir' => 4,
                        'pelapor_gudang' => 'Agus Prasetyo (Mandor)',
                        'hasil_catatan' => 'Penyortiran fisik: 146 unit layak rekondisi, 4 unit afkir (keropos berkarat parah pada tutup).',
                    ],
                    [
                        'jenis_proses' => 'peniupan',
                        'tanggal_aktivitas' => Carbon::now()->subDays(3)->toDateString(),
                        'jumlah_diproses' => 146,
                        'jumlah_afkir' => 2,
                        'pelapor_gudang' => 'Sugeng Supriyadi',
                        'hasil_catatan' => 'Perataan denting penyok body drum: 144 unit mulus, 2 unit gagal uji tekanan udara hydrotest.',
                    ],
                    [
                        'jenis_proses' => 'pencucian',
                        'tanggal_aktivitas' => Carbon::now()->subDays(2)->toDateString(),
                        'jumlah_diproses' => 144,
                        'jumlah_afkir' => 0,
                        'pelapor_gudang' => 'Sugeng Supriyadi',
                        'hasil_catatan' => 'Pencucian kimia rantai putar (chain rotary) dengan caustic soda dan air panas 80C. Bersih bebas residu.',
                    ],
                    [
                        'jenis_proses' => 'pengecatan',
                        'tanggal_aktivitas' => Carbon::now()->subDays(1)->toDateString(),
                        'jumlah_diproses' => 144,
                        'jumlah_afkir' => 0,
                        'pelapor_gudang' => 'Joko Purnomo',
                        'hasil_catatan' => 'Pelapisan cat epoxy oven warna biru standar industri (RAL 5015) ketebalan 45 mikron. Masuk rak siap kirim.',
                    ],
                ],
            ],
            [
                'kode_batch' => 'BATCH-202611-002',
                'tanggal_penerimaan' => Carbon::now()->subDays(3)->toDateString(),
                'jumlah_diterima' => 120,
                'kategori_jenis' => 'Besi Open Top',
                'status_pengendapan' => 'Selesai',
                'status_proses' => 'Dalam Proses',
                'sumber_laporan' => 'Pengadaan Suplier Cat Pulogadung',
                'catatan' => 'Drum penutup terbuka dengan klem ring. Memerlukan pencucian intensif dan pelepasan gasket lama.',
                'logs' => [
                    [
                        'jenis_proses' => 'penyortiran',
                        'tanggal_aktivitas' => Carbon::now()->subDays(3)->toDateString(),
                        'jumlah_diproses' => 120,
                        'jumlah_afkir' => 3,
                        'pelapor_gudang' => 'Agus Prasetyo (Mandor)',
                        'hasil_catatan' => 'Penyortiran: 117 unit bodi drum kokoh, 3 unit rim penyok berat tidak bisa dipasang klem ring.',
                    ],
                    [
                        'jenis_proses' => 'peniupan',
                        'tanggal_aktivitas' => Carbon::now()->subDays(2)->toDateString(),
                        'jumlah_diproses' => 117,
                        'jumlah_afkir' => 1,
                        'pelapor_gudang' => 'Sugeng Supriyadi',
                        'hasil_catatan' => 'Pengepresan bibir rim drum dan peniupan body: 116 lolos presisi klem.',
                    ],
                    [
                        'jenis_proses' => 'pencucian',
                        'tanggal_aktivitas' => Carbon::now()->subDays(1)->toDateString(),
                        'jumlah_diproses' => 116,
                        'jumlah_afkir' => 0,
                        'pelapor_gudang' => 'Sugeng Supriyadi',
                        'hasil_catatan' => 'Pembersihan kerak cat dan residu resin pelarut. Selesai cuci, sedang antri proses oven pengecatan.',
                    ],
                ],
            ],
            [
                'kode_batch' => 'BATCH-202611-003',
                'tanggal_penerimaan' => Carbon::now()->subDays(2)->toDateString(),
                'jumlah_diterima' => 100,
                'kategori_jenis' => 'Galvanis Anti Karat',
                'status_pengendapan' => 'Mengendap',
                'status_proses' => 'Dalam Proses',
                'sumber_laporan' => 'Pengadaan PT Sinar Mas Oleo (Karawang)',
                'catatan' => 'Drum galvanis kualitas food-grade ex-gliserin. Tahap pengendapan residu selesai, masuk jalur peniupan.',
                'logs' => [
                    [
                        'jenis_proses' => 'penyortiran',
                        'tanggal_aktivitas' => Carbon::now()->subDays(2)->toDateString(),
                        'jumlah_diproses' => 100,
                        'jumlah_afkir' => 1,
                        'pelapor_gudang' => 'Agus Prasetyo (Mandor)',
                        'hasil_catatan' => 'Pemeriksaan visual ketebalan lapisan zinc galvanis: 99 unit sangat baik, 1 unit retak las bibir.',
                    ],
                    [
                        'jenis_proses' => 'peniupan',
                        'tanggal_aktivitas' => Carbon::now()->subDays(1)->toDateString(),
                        'jumlah_diproses' => 99,
                        'jumlah_afkir' => 0,
                        'pelapor_gudang' => 'Sugeng Supriyadi',
                        'hasil_catatan' => 'Peniupan udara pneumatic presisi tinggi tanpa merusak lapisan galvanis. Siap cuci sanitasi.',
                    ],
                ],
            ],
            [
                'kode_batch' => 'BATCH-202611-004',
                'tanggal_penerimaan' => Carbon::now()->subDays(1)->toDateString(),
                'jumlah_diterima' => 80,
                'kategori_jenis' => 'Plastik HDPE Biru',
                'status_pengendapan' => 'Transit',
                'status_proses' => 'Dalam Proses',
                'sumber_laporan' => 'Kiriman Suplier B3 Cikarang Barat',
                'catatan' => 'Drum plastik polietilena densitas tinggi (HDPE). Selesai sortir penerimaan, antri bilas chemical neutralizer.',
                'logs' => [
                    [
                        'jenis_proses' => 'penyortiran',
                        'tanggal_aktivitas' => Carbon::now()->subDays(1)->toDateString(),
                        'jumlah_diproses' => 80,
                        'jumlah_afkir' => 0,
                        'pelapor_gudang' => 'Agus Prasetyo (Mandor)',
                        'hasil_catatan' => 'Pengecekan ulir drat bung cap dan dinding plastik: seluruh 80 unit mulus tanpa retak stres mikro.',
                    ],
                ],
            ],
            [
                'kode_batch' => 'BATCH-202611-005',
                'tanggal_penerimaan' => Carbon::now()->toDateString(),
                'jumlah_diterima' => 160,
                'kategori_jenis' => 'Besi Tight Head',
                'status_pengendapan' => 'Transit',
                'status_proses' => 'Diterima',
                'sumber_laporan' => 'Surat Jalan B3 PT Petro Kimia Gresik (SJ-PKG/MRD/4412)',
                'catatan' => 'Penerimaan muatan truk fuso di Dermaga A hari ini. Drum baru diturunkan ke area penumpukan transit.',
                'logs' => [],
            ],
        ];

        $createdBatches = [];
        foreach ($batchData as $data) {
            $batch = DrumBatch::updateOrCreate(
                ['kode_batch' => $data['kode_batch']],
                [
                    'user_id' => $admin->id,
                    'tanggal_penerimaan' => $data['tanggal_penerimaan'],
                    'jumlah_diterima' => $data['jumlah_diterima'],
                    'kategori_jenis' => $data['kategori_jenis'],
                    'status_pengendapan' => $data['status_pengendapan'],
                    'status_proses' => $data['status_proses'],
                    'sumber_laporan' => $data['sumber_laporan'],
                    'catatan' => $data['catatan'],
                ]
            );

            // Simpan log proses jika ada
            foreach ($data['logs'] as $log) {
                DrumProcessLog::updateOrCreate(
                    [
                        'drum_batch_id' => $batch->id,
                        'jenis_proses' => $log['jenis_proses'],
                    ],
                    [
                        'user_id' => $admin->id,
                        'tanggal_aktivitas' => $log['tanggal_aktivitas'],
                        'tanggal_input' => Carbon::parse($log['tanggal_aktivitas'])->setTime(16, 30),
                        'jumlah_diproses' => $log['jumlah_diproses'],
                        'jumlah_afkir' => $log['jumlah_afkir'],
                        'pelapor_gudang' => $log['pelapor_gudang'],
                        'hasil_catatan' => $log['hasil_catatan'],
                    ]
                );
            }

            $createdBatches[] = $batch;
        }

        // =========================================================================
        // 3. RELASI PIVOT KENDARAAN MENGANGKUT BATCH DRUM (vehicle_visit_batches)
        // =========================================================================
        if (isset($createdVisits[0], $createdBatches[0])) {
            $createdVisits[0]->drumBatches()->syncWithoutDetaching([
                $createdBatches[0]->id => [
                    'jumlah_drum' => 150,
                    'keterangan_muatan' => 'Bongkar bahan baku drum besi bekas 150 unit',
                ],
            ]);
        }

        if (isset($createdVisits[1], $createdBatches[1])) {
            $createdVisits[1]->drumBatches()->syncWithoutDetaching([
                $createdBatches[1]->id => [
                    'jumlah_drum' => 90,
                    'keterangan_muatan' => 'Distribusi 90 unit drum rekondisi siap pakai',
                ],
            ]);
        }

        if (isset($createdVisits[4], $createdBatches[4])) {
            $createdVisits[4]->drumBatches()->syncWithoutDetaching([
                $createdBatches[4]->id => [
                    'jumlah_drum' => 160,
                    'keterangan_muatan' => 'Penerimaan 160 drum besi bahan baku',
                ],
            ]);
        }
    }
}
