# PRD Singkat v1.2 — Sistem Administrasi Operasional Berbasis Web untuk Rekapitulasi Laporan Gudang

**Perusahaan:** PT Pulo Kencana Drum Industri  
**Jenis proyek:** Project akhir / MVP aplikasi web  
**Status:** Draft revisi berdasarkan hasil klarifikasi awal; perlu divalidasi melalui observasi lanjutan  
**Role pengguna:** Admin Kantor dan Satpam  
**Teknologi yang direncanakan:** PHP 8.3+, Laravel 13, MySQL, Laravel Livewire Starter Kit, Tailwind CSS

## 1. Latar Belakang

Pencatatan kendaraan masuk dan keluar di pos satpam masih dilakukan menggunakan buku manual. Aktivitas gudang seperti penyortiran, peniupan, pencucian, pengecatan, serta transit/pengendapan drum juga dicatat secara manual dan direkap ke Excel setiap bulan.

Petugas gudang menyampaikan catatan aktivitas kepada Admin Kantor. Admin Kantor kemudian memasukkan data tersebut ke aplikasi dan menyiapkan rekapitulasi laporan untuk manajemen. Satpam berfokus pada pencatatan kendaraan di gerbang dan pemeriksaan surat jalan yang menyertai pengiriman atau penerimaan kemasan limbah B3.

Aplikasi ini berfokus pada administrasi operasional dan rekapitulasi laporan gudang. Pencatatan kendaraan menjadi modul pendukung untuk mendigitalisasi kegiatan gerbang. Sistem tidak menggantikan kegiatan fisik petugas gudang maupun menentukan kewenangan persetujuan pengeluaran drum.

## 2. Tujuan Produk

1. Memudahkan Admin Kantor menginput catatan operasional gudang berdasarkan laporan dari petugas gudang.
2. Mendigitalisasi pencatatan penerimaan/batch drum, penyortiran, peniupan, pencucian, pengecatan, serta transit/pengendapan berdasarkan data yang tersedia.
3. Membantu Admin Kantor membuat rekapitulasi laporan operasional harian dan bulanan untuk manajemen.
4. Menyediakan ekspor laporan administrasi ke Excel dan/atau PDF bagi Admin Kantor.
5. Mendigitalisasi pencatatan kendaraan masuk dan keluar oleh Satpam.
6. Mendukung pencatatan informasi serta hasil pemeriksaan surat jalan ketika kendaraan mengirim atau menerima kemasan limbah B3.
7. Menyimpan tanggal aktivitas sebenarnya dan tanggal input agar laporan dapat ditelusuri dengan lebih baik.

## 3. Pengguna dan Hak Akses

### A. Admin Kantor

Admin Kantor bertugas mengelola administrasi, menginput laporan yang diterima dari bagian gudang, dan menyiapkan rekapitulasi laporan untuk manajemen. Admin Kantor **bukan secara otomatis pihak yang berwenang menyetujui pengeluaran drum**.

Fitur Admin Kantor:

- Melihat dashboard ringkasan administrasi dan operasional berdasarkan data yang tersedia.
- Mencatat atau memperbarui data penerimaan/batch drum berdasarkan laporan yang diterima.
- Menginput laporan penyortiran, peniupan, pencucian, dan pengecatan.
- Mencatat informasi transit/pengendapan berdasarkan laporan yang diterima.
- Melihat data dan riwayat catatan proses drum.
- Melihat catatan kendaraan untuk keperluan administrasi.
- Membuat rekapitulasi laporan harian/bulanan dan mengekspor laporan ke Excel/PDF.
- Mengelola akun Satpam dan data master dasar jika diperlukan.
- Mengoreksi data administrasi sesuai hak akses dengan mencatat alasan dan jejak perubahan jika data sudah tersimpan sebelumnya.

Status operasional yang dicatat Admin Kantor merepresentasikan laporan atau informasi yang diterima, bukan persetujuan pengeluaran drum.

### B. Satpam

Satpam bertugas mencatat aktivitas kendaraan di gerbang dan memeriksa surat jalan fisik yang menyertai kegiatan pengiriman atau penerimaan kemasan limbah B3.

Fitur Satpam:

- Melihat dashboard gerbang sederhana, bila diperlukan, untuk mengetahui ringkasan kendaraan hari ini dan kendaraan yang catatan kunjungannya masih aktif.
- Mencatat kendaraan masuk.
- Melengkapi catatan kendaraan saat keluar dengan menutup kunjungan yang masih aktif.
- Mengisi identitas kendaraan dan pengemudi, seperti nomor polisi, nama pengemudi, perusahaan atau asal/tujuan, dan keperluan.
- Mencatat informasi surat jalan sesuai format yang digunakan perusahaan.
- Mencatat hasil pemeriksaan surat jalan saat kendaraan mengirim atau menerima kemasan limbah B3, sesuai prosedur perusahaan.

Fitur yang **tidak dibutuhkan Satpam** berdasarkan klarifikasi pihak PT:

- Tidak ada menu untuk melihat atau mencari riwayat kendaraan yang sudah selesai. Riwayat kendaraan dikelola dan dapat dilihat oleh Admin Kantor.
- Tidak mencetak atau mengekspor dokumen kendaraan/surat jalan ke PDF melalui aplikasi.
- Tidak memeriksa status drum di sistem ketika kendaraan akan keluar.
- Tidak dapat mengubah data batch drum, proses gudang, maupun status transit/pengendapan.

Satpam tetap dapat melihat daftar kunjungan yang **belum ditutup** karena diperlukan untuk melengkapi pencatatan waktu keluar. Daftar kunjungan aktif ini bukan menu riwayat pencarian kendaraan lampau.

### Hak Akses Umum

- Tidak ada registrasi publik.
- Route, halaman, dan aksi harus dilindungi otorisasi di sisi server.
- Akun pengguna dibuat oleh Admin Kantor/pengelola aplikasi sesuai kebijakan perusahaan.
- Tidak menambahkan role lain dalam MVP ini.

## 4. Alur Bisnis Utama

### 4.1 Pencatatan Kendaraan oleh Satpam

1. Satpam login ke aplikasi.
2. Saat kendaraan datang, Satpam membuat catatan kunjungan. Sistem membuat nomor kunjungan otomatis dan menyimpan waktu masuk.
3. Satpam mengisi nomor polisi, nama pengemudi, perusahaan atau asal/tujuan jika diperlukan, keperluan, keterangan muatan, dan informasi surat jalan.
4. Jika kunjungan berkaitan dengan pengiriman atau penerimaan kemasan limbah B3, Satpam memeriksa surat jalan fisik sesuai prosedur perusahaan dan mencatat informasi serta hasil pemeriksaannya ke aplikasi.
5. Saat kendaraan meninggalkan area, Satpam membuka catatan kunjungan aktif yang belum ditutup lalu melengkapi waktu keluar dan informasi terkait.
6. Sistem menyimpan waktu keluar dan menutup catatan kunjungan tersebut.
7. Catatan kendaraan tersedia untuk kebutuhan administrasi dan rekap oleh Admin Kantor. Satpam tidak mendapatkan menu pencarian riwayat lampau atau ekspor PDF.

Jika satu kunjungan perlu dikaitkan dengan batch drum dan jumlah yang dibawa, hal tersebut hanya diterapkan apabila kebutuhan ini dikonfirmasi dalam observasi dan informasi tersebut tersedia.

### 4.2 Input Laporan Gudang oleh Admin Kantor

1. Petugas gudang melakukan aktivitas fisik dan membuat/menyampaikan catatan sesuai praktik perusahaan.
2. Admin Kantor menerima catatan atau laporan dari petugas gudang.
3. Admin Kantor memasukkan data ke aplikasi, minimal mencakup tanggal aktivitas, jenis proses, kelompok/batch drum jika digunakan, jumlah yang diproses jika tersedia, dan keterangan.
4. Admin Kantor mencatat informasi status transit/pengendapan berdasarkan laporan yang diterima.
5. Aplikasi menyimpan catatan pembuatan/perubahan sehingga data dapat ditelusuri saat rekapitulasi bulanan dibuat.
6. Admin Kantor memilih rentang tanggal untuk melihat dan mengekspor laporan.

### 4.3 Status Operasional Drum

- Aplikasi dapat menampilkan status yang tercatat, misalnya Diterima, Pengendapan, Dalam Proses, Proses Selesai, atau Status Belum Diketahui, selama status tersebut sesuai informasi yang tersedia.
- Status adalah informasi operasional berdasarkan data yang dimasukkan ke aplikasi.
- Admin Kantor tidak otomatis menjadi pihak yang menyetujui pengeluaran drum.
- Sistem tidak menyatakan bahwa status proses selesai berarti drum telah mendapat izin untuk keluar.
- MVP tidak memblokir transaksi kendaraan keluar berdasarkan status drum.
- Satpam tidak ditugaskan memeriksa status drum di sistem saat kendaraan keluar.
- Jika informasi status belum tersedia atau belum lengkap, aplikasi dapat menampilkannya sebagai informasi untuk Admin Kantor, bukan sebagai keputusan persetujuan atau penolakan.

## 5. Fitur MVP

### 5.1 Modul Login dan Role

- Login dan logout.
- Dashboard/menu berbeda sesuai role Admin Kantor dan Satpam.
- Pembatasan akses dilakukan di sisi server.
- Tidak tersedia pendaftaran akun publik.

### 5.2 Modul Kendaraan — Satpam

- Pencatatan kendaraan masuk dan keluar dalam satu catatan kunjungan.
- Nomor kunjungan otomatis.
- Data nomor polisi, nama pengemudi, perusahaan/asal-tujuan, keperluan, keterangan muatan jika diperlukan, waktu masuk, dan waktu keluar.
- Pencatatan informasi surat jalan.
- Pencatatan hasil pemeriksaan surat jalan untuk pengiriman/penerimaan kemasan limbah B3.
- Daftar kunjungan yang belum ditutup agar Satpam dapat melengkapi waktu keluar.
- Tidak ada menu riwayat kendaraan lampau, ekspor PDF Satpam, atau pemeriksaan status drum.

### 5.3 Modul Administrasi Gudang — Admin Kantor

- Pencatatan penerimaan/batch drum berdasarkan informasi yang diterima.
- Pencatatan status transit/pengendapan.
- Input laporan penyortiran, peniupan, pencucian, dan pengecatan.
- Pencatatan tanggal aktivitas dan tanggal input.
- Riwayat catatan proses serta koreksi data.
- Daftar batch dan filter tanggal/status jika data batch digunakan oleh perusahaan.
- Input dilakukan Admin Kantor berdasarkan laporan dari petugas gudang; tidak dibuat akun khusus Petugas Gudang dalam MVP.

### 5.4 Modul Laporan — Admin Kantor

- Filter laporan berdasarkan tanggal/periode.
- Ringkasan kendaraan masuk dan keluar untuk administrasi.
- Rekap penerimaan drum dan jumlah yang tercatat pada setiap proses jika datanya tersedia.
- Ringkasan status transit/pengendapan dan status proses.
- Ekspor laporan ke Excel dan/atau PDF.
- Laporan membedakan tanggal aktivitas sebenarnya dan tanggal/waktu data dimasukkan apabila keduanya berbeda.
- Ekspor PDF hanya untuk laporan administrasi oleh Admin Kantor; bukan untuk dokumen gerbang yang dibuat Satpam.

## 6. Struktur Menu

### Admin Kantor

1. Dashboard Administrasi
2. Data Penerimaan/Batch Drum
3. Transit dan Pengendapan
4. Laporan Proses Gudang
   - Penyortiran
   - Peniupan
   - Pencucian
   - Pengecatan
5. Catatan Kendaraan (untuk administrasi)
6. Rekapitulasi Laporan dan Ekspor
7. Manajemen Akun, jika diperlukan
8. Profil dan Logout

### Satpam

1. Dashboard Gerbang sederhana, jika diperlukan
2. Kendaraan Masuk
3. Kendaraan Aktif/Belum Ditutup (untuk melengkapi transaksi keluar)
4. Kendaraan Keluar
5. Profil dan Logout

Menu Satpam tidak mencakup pencarian riwayat kendaraan lampau, ekspor PDF, atau pemeriksaan status drum.

## 7. Rancangan Data Inti

Rancangan data bersifat awal dan perlu disesuaikan setelah melihat buku Satpam dan contoh file Excel atau laporan gudang perusahaan.

- **`users`**: nama, username/email, password hash, role (`admin_kantor` atau `satpam`), status akun.
- **`vehicle_visits`**: nomor kunjungan, nomor polisi, nama pengemudi, perusahaan/asal-tujuan, keperluan, waktu masuk, waktu keluar, informasi surat jalan, status pencatatan pemeriksaan surat jalan B3, catatan, pengguna pencatat.
- **`drum_batches`**: kode batch jika digunakan, tanggal penerimaan, kategori/jenis drum jika tersedia, jumlah diterima, informasi transit/pengendapan, status proses, sumber laporan, dan catatan.
- **`drum_process_logs`**: batch ID jika menggunakan batch, jenis proses, tanggal aktivitas sebenarnya, tanggal input, jumlah yang diproses jika tersedia, hasil/catatan, serta pengguna yang memasukkan data.
- **`vehicle_visit_items` (opsional)**: kunjungan kendaraan ID, batch ID, dan jumlah drum yang dibawa. Tambahkan hanya jika observasi mengonfirmasi bahwa kunjungan perlu ditautkan ke batch dan jumlahnya.

Tabel khusus pemeriksaan surat jalan dapat dibuat terpisah jika satu kunjungan dapat memiliki banyak dokumen atau pemeriksaan. Jika satu kunjungan hanya memerlukan satu rangkaian informasi pemeriksaan, data tersebut bisa disimpan pada `vehicle_visits`. Pilihan akhir mengikuti hasil observasi.

Rekap bulanan dihitung dari data kunjungan dan catatan operasional. Tidak perlu membuat tabel laporan bulanan tersendiri pada MVP.

## 8. Aturan Data dan Validasi

1. Waktu keluar tidak boleh lebih awal daripada waktu masuk.
2. Kunjungan yang sudah ditutup tidak dapat ditutup kembali secara biasa; koreksi dilakukan dengan alasan dan jejak perubahan.
3. Satpam hanya dapat membuat dan melengkapi catatan kunjungan sesuai hak akses.
4. Hanya Admin Kantor yang dapat menginput atau mengubah data batch dan catatan proses gudang.
5. Sistem menyimpan pengguna pencatat serta timestamp pembuatan/perubahan.
6. Tanggal aktivitas dalam laporan dapat berbeda dari tanggal input; kedua nilai tidak boleh tertukar.
7. Status drum tidak otomatis menjadi persetujuan pengeluaran.
8. Sistem tidak memblokir transaksi kendaraan keluar berdasarkan asumsi atau status drum yang belum diverifikasi.
9. Satpam tidak dapat melihat riwayat kunjungan lampau melalui menu pencarian, tetapi dapat melihat kunjungan yang belum ditutup agar dapat menyelesaikan pencatatan keluar.
10. Penghapusan transaksi dibatasi; utamakan koreksi atau pembatalan dengan alasan agar jejak administrasi tetap tersedia.
11. Hasil pemeriksaan surat jalan dicatat berdasarkan prosedur perusahaan. Aplikasi tidak secara otomatis menyatakan dokumen sah secara hukum.

## 9. Teknologi yang Disarankan

- **Backend:** PHP 8.3+ dan Laravel 13.
- **Database:** MySQL.
- **Frontend:** Laravel Livewire Starter Kit resmi dan Tailwind CSS.
- **PDF:** paket `barryvdh/laravel-dompdf` hanya untuk ekspor laporan Admin Kantor, dengan versi yang kompatibel dengan Laravel.
- **Excel:** paket `maatwebsite/excel` untuk ekspor laporan Admin Kantor, dengan versi yang kompatibel.
- **Lingkungan lokal:** Composer, Node.js/NPM, dan MySQL. XAMPP dapat digunakan jika versi PHP aktif memenuhi persyaratan Laravel.
- **Arsitektur:** satu aplikasi Laravel (monolith); tidak perlu memisahkan API/backend dan frontend pada MVP.

## 10. Kriteria Penerimaan

1. Admin Kantor dan Satpam dapat login dan memperoleh menu sesuai hak akses masing-masing.
2. Satpam dapat mencatat kendaraan masuk dan melengkapi waktu keluar pada kunjungan yang sama.
3. Aplikasi menampilkan daftar kunjungan yang belum ditutup untuk membantu Satpam menyelesaikan pencatatan keluar, tanpa menyediakan menu pencarian riwayat lampau.
4. Satpam dapat mencatat informasi surat jalan dan hasil pemeriksaan dokumen ketika mengirim atau menerima kemasan limbah B3.
5. Admin Kantor dapat memasukkan catatan batch/penerimaan, transit/pengendapan, dan empat jenis proses berdasarkan laporan petugas gudang.
6. Catatan proses memuat tanggal aktivitas dan tanggal input secara terpisah.
7. Admin Kantor dapat membuat rekapitulasi berdasarkan periode dan mengekspor laporan ke Excel/PDF.
8. Satpam tidak memiliki fitur ekspor PDF, pencarian riwayat kendaraan lampau, atau pemeriksaan status drum.
9. Admin Kantor tidak diberi fitur atau label sebagai pihak pemberi persetujuan pengeluaran drum.
10. Sistem tidak memblokir kendaraan keluar berdasarkan asumsi alur atau status drum yang belum diverifikasi.
11. Pembatasan akses tetap bekerja meskipun URL atau permintaan HTTP dimanipulasi.
12. Hasil rekap konsisten dengan data yang tersimpan dan periode yang dipilih.

## 11. Di Luar Cakupan MVP

- Akun khusus Petugas Gudang.
- Persetujuan pengeluaran drum atau persetujuan berjenjang sebelum kewenangan dan SOP dikonfirmasi.
- Pemeriksaan status drum oleh Satpam.
- Pembuatan atau ekspor PDF dokumen gerbang oleh Satpam.
- Menu riwayat/pencarian kendaraan lampau untuk Satpam.
- Barcode/QR per drum.
- Akuntansi, payroll, pembelian, penjualan, dan manajemen supplier lengkap.
- Integrasi CCTV, sensor, timbangan, atau IoT.
- Aplikasi Android/iOS terpisah.
- Otomatisasi atau penetapan kepatuhan lingkungan secara hukum.

## 12. Tahapan Pengerjaan

1. **Finalisasi kebutuhan dan desain:** validasi PRD, alur, ERD, struktur tabel, serta data yang perlu dikumpulkan dari buku Satpam dan laporan gudang.
2. **Fondasi aplikasi:** setup Laravel, MySQL, login, role, dan layout dashboard.
3. **Modul Satpam:** pencatatan kendaraan masuk/keluar, kunjungan aktif yang belum ditutup, informasi surat jalan, dan pencatatan pemeriksaan surat jalan kemasan limbah B3.
4. **Modul Admin Kantor:** batch/penerimaan drum, transit/pengendapan, empat kategori laporan proses, serta tanggal aktivitas dan tanggal input.
5. **Rekapitulasi laporan:** filter periode, ringkasan operasional, dan ekspor Excel/PDF oleh Admin Kantor.
6. **Pengujian dan persiapan demo:** uji role, validasi, konsistensi data, laporan, perbaikan bug, dokumentasi, dan demo aplikasi.

## 13. Hal yang Harus Dikonfirmasi saat Observasi

- Kolom pada buku Satpam dan pihak yang membuat/menandatangani surat jalan.
- Informasi apa saja yang perlu dicatat saat pemeriksaan surat jalan kemasan limbah B3.
- Data apa saja dalam laporan gudang yang diterima Admin Kantor.
- Bentuk laporan gudang: formulir, buku, atau file Excel.
- Apakah proses menggunakan kode batch atau hanya pencatatan jumlah.
- Data apa yang dicatat pada setiap proses penyortiran, peniupan, pencucian, dan pengecatan.
- Lama dan kriteria selesai pengendapan.
- Apakah satu kendaraan dapat membawa beberapa batch atau pengeluaran sebagian batch.
- Format dan rumus pada Excel rekap bulanan.
- Apakah dashboard gerbang diperlukan atau cukup halaman pencatatan kendaraan.

---

**Catatan:** Dokumen ini adalah PRD revisi untuk kebutuhan MVP project akhir. Detail yang belum dikonfirmasi melalui observasi ditandai sebagai hal yang perlu divalidasi, bukan dianggap sebagai prosedur resmi perusahaan.
