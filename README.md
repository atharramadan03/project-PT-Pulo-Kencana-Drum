# Sistem Administrasi Operasional Berbasis Web untuk Rekapitulasi Laporan Gudang
**PT Pulo Kencana Drum Industri**

Proyek ini dibangun sebagai luaran mata kuliah **Manajemen Proyek Perangkat Lunak (MPPL)** dan dirancang untuk memfasilitasi kebutuhan administrasi manajemen operasional pabrik, mendigitalisasi pencatatan laporan fisik dari gudang, serta mencatat arus keluar-masuk kendaraan armada truk dan verifikasi surat jalan kemasan limbah B3 di pos gerbang.

---

## 1. Spesifikasi Teknologi (Tech Stack)

* **Framework Backend:** Laravel 13.x (PHP 8.4+)
* **Database:** MySQL / MariaDB (XAMPP Port 3306)
* **Frontend:** Laravel Livewire 3 (Starter Kit Resmi) & Tailwind CSS
* **Build Tool:** Vite & Node.js v24.x LTS (NPM)
* **Arsitektur:** Monolith MVC terintegrasi (Zero-API separation for MVP)

---

## 2. Struktur Hak Akses (Role-Based Access Control)

Sesuai dengan **PRD v1.2**, sistem memiliki 2 peran resmi:
1. **`admin_kantor` (Admin Kantor)**:
   * Menginput laporan penerimaan dan batch drum.
   * Mencatat status transit dan pengendapan drum.
   * Menginput data 4 tahap proses gudang (*Penyortiran, Peniupan, Pencucian, Pengecatan*) berdasarkan laporan fisik orang gudang.
   * Memisahkan pencatatan *Tanggal Aktivitas Lapangan* vs *Tanggal Input Sistem*.
   * Memantau catatan kendaraan untuk kebutuhan administrasi & audit.
   * Menghasilkan rekapitulasi laporan harian/bulanan serta ekspor ke Excel & PDF untuk pihak Manajemen.
2. **`satpam` (Petugas Pos Gerbang)**:
   * Mencatat kendaraan masuk (nomor kunjungan otomatis, identitas supir, nopol, asal-tujuan, keperluan).
   * **Memeriksa fisik surat jalan / manifest kemasan limbah B3** saat pengiriman/penerimaan.
   * Memantau daftar kendaraan yang masih aktif di dalam area pabrik (*belum memiliki waktu keluar*).
   * Mencatat kendaraan keluar dan menutup catatan kunjungan.

---

## 3. Struktur Database (Berdasarkan Diagram ERD Draw.io)

Database resmi bernama **`pkdi_gudang`** dengan struktur tabel terurut:

1. **`users`**: Menyimpan akun pengguna (`role`: `'admin_kantor'` | `'satpam'`, `username`, `shift_jaga`, `status_akun`).
2. **`vehicle_visits`**: Mencatat transaksi kunjungan kendaraan di pos gerbang oleh satpam (`status_pemeriksaan_surat_jalan_b3`, `pos_gerbang`, `waktu_masuk`, `waktu_keluar`).
3. **`drum_batches`**: Mencatat penerimaan batch drum dan status pengendapan (`kode_batch`, `status_pengendapan`, `status_proses`, `sumber_laporan`).
4. **`vehicle_visit_batches`**: Tabel pivot relasi Many-to-Many (*M:N*) `{Mengangkut}` antara kunjungan truk dan batch drum yang dibawa (`jumlah_drum`, `keterangan_muatan`).
5. **`drum_process_logs`**: Log catatan 4 tahap proses gudang (`jenis_proses`: `'penyortiran'`, `'peniupan'`, `'pencucian'`, `'pengecatan'`, `tanggal_aktivitas`, `tanggal_input`, `pelapor_gudang`, `jumlah_diproses`, `jumlah_afkir`).

---

## 4. Panduan Instalasi & Menjalankan Proyek dari Terminal

### Prasyarat:
* PHP versi 8.4+ (Pastikan ekstensi `pdo_mysql`, `intl`, `zip`, dan `gd` aktif di `php.ini`).
* Composer 2.x
* Node.js v24.x & NPM
* XAMPP MySQL aktif di port 3306.

### Langkah-Langkah Setup:

#### 1. Clone & Masuk ke Direktori Proyek
```bash
git clone <url-repository-anda>
cd Project_Akhir
```

#### 2. Install Dependensi Backend & Frontend
```bash
composer install
npm install
npm run build
```

#### 3. Membuat Database Kosong di MySQL
Buka terminal dan jalankan:
```bash
/c/xampp/mysql/bin/mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS pkdi_gudang;"
```
*(Atau buat database bernama `pkdi_gudang` lewat phpMyAdmin di `http://localhost/phpmyadmin`).*

#### 4. Konfigurasi File Lingkungan (`.env`)
Salin file template `.env.example` menjadi `.env` jika belum ada:
```bash
cp .env.example .env
php artisan key:generate
```
Pastikan pengaturan koneksi database di file `.env` mengarah ke:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pkdi_gudang
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Eksekusi Migrasi Database
Jalankan migrasi untuk membentuk seluruh tabel secara otomatis:
```bash
php artisan migrate
```

#### 6. Memeriksa Status Migrasi
Periksa apakah seluruh tabel sudah terbentuk sempurna:
```bash
php artisan migrate:status
```

#### 7. Menjalankan Aplikasi
Nyalakan server pengembang lokal:
```bash
php artisan serve
```
Buka browser di: **`http://127.0.0.1:8000`**

---

## 5. Tata Kelola Database & Perubahan Skema (Best Practices MPPL)

1. **Larangan `migrate:fresh` Sembarangan:**
   * Jangan menjalankan `php artisan migrate:fresh` pada database yang sudah berisi data riil karena perintah tersebut akan **menghapus permanen seluruh tabel beserta datanya**.
2. **Cara Menambahkan Perubahan Skema ERD Baru:**
   * Jika di kemudian hari ada kolom atau tabel baru hasil validasi observasi PT Pulo Kencana, buat file migrasi baru menggunakan:
     ```bash
     php artisan make:migration add_kolom_baru_to_nama_tabel_table
     ```
   * Tulis perubahannya pada method `up()` dan proses rollback pada method `down()`, lalu jalankan kembali `php artisan migrate`.
3. **Menyiapkan Database pada Komputer Lain (Portabilitas):**
   * Cukup buat database kosong `pkdi_gudang`, sesuaikan `.env`, lalu jalankan `php artisan migrate`. Seluruh skema akan otomatis terpasang identik 100%.
