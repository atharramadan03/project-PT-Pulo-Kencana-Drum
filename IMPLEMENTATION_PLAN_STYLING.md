# RANCANGAN IMPLEMENTASI PLAN: UI STYLING & REDESIGN
## Tema Desain: Industrial Enterprise UI + Modern Minimalist Dashboard ("Industrial Pro")

**Perusahaan:** PT Pulo Kencana Drum Industri  
**Proyek:** Sistem Administrasi Operasional Berbasis Web untuk Rekapitulasi Laporan Gudang  
**Dokumen Referensi:** Panduan Visual *Industrial Pro* (Modern • Profesional • Fungsional)  
**Status:** Disetujui untuk Implementasi  
**Aturan Utama Pengujian:** Pengecekan visual antarmuka di browser dilakukan secara eksklusif oleh Pengguna (*User*). Asisten AI hanya memvalidasi integritas logika, build aset, dan pengujian PHPUnit otomatis.

---

## 1. Spesifikasi Desain & Design Tokens

### A. Palet Warna Resmi (Industrial Pro Palette)
| Token Nama | Nilai HEX | Kegunaan dalam Sistem |
| :--- | :--- | :--- |
| **Primary** | `#2563EB` | Tombol aksi utama, menu aktif sidebar, aksen sorotan utama |
| **Secondary** | `#3B82F6` | Tombol sekunder, ikon pendukung, hover state interaktif |
| **Accent / Action** | `#F59E0B` | Tombol peringatan, aksen perhatian khusus, status peniupan |
| **Sidebar Navy** | `#0F172A` | Background panel samping (Sidebar) bergaya Industrial Enterprise |
| **Canvas Background** | `#F7F9FC` | Latar belakang seluruh area kerja (lembut di mata, tidak menyilaukan) |
| **Surface Card** | `#FFFFFF` | Latar belakang kartu kontainer, modal popup, dan tabel data |
| **Text Primary** | `#0F172A` | Teks judul utama, angka metrik KPI, header tabel |
| **Text Secondary** | `#64748B` | Label form, sub-judul, keterangan waktu/audit |
| **Border / Divider** | `#E2E8F0` | Garis batas tipis pada kartu, input, dan pemisah kolom |

### B. Skema Warna Status Pill (Badges)
- **Status Selesai / Lolos**: Hijau Emerald (`bg-[#ECFDF5]`, `text-[#059669]`, dot `#10B981`)
- **Status Diproses / Berjalan**: Biru Royal (`bg-[#EFF6FF]`, `text-[#2563EB]`, dot `#3B82F6`)
- **Status 1. Penyortiran**: Biru Langit (`bg-[#E0F2FE]`, `text-[#0369A1]`)
- **Status 2. Peniupan**: Amber / Oranye (`bg-[#FEF3C7]`, `text-[#B45309]`)
- **Status 3. Pencucian**: Ungu Soft (`bg-[#F5F3FF]`, `text-[#7C3AED]`)
- **Status 4. Pengecatan**: Merah Rose (`bg-[#FEF2F2]`, `text-[#DC2626]`)
- **Status Transit / Mengendap**: Slate Abu-abu (`bg-[#F1F5F9]`, `text-[#475569]`)
- **Status Afkir (Reject)**: Merah Pekat (`bg-[#FEE2E2]`, `text-[#991B1B]`)

### C. Tipografi & Hirarki
- **Font Family**: `Plus Jakarta Sans` / `Inter`, sans-serif (Google Fonts)
- **H1 (Judul Dashboard/Halaman)**: 24px - 32px, Bold 700, warna `#0F172A`
- **H2 (Judul Bagian/Kartu)**: 18px - 20px, SemiBold 600
- **Body / Isi Form**: 14px, Regular 400
- **Label & Meta Audit**: 12px, Medium 500 / SemiBold 600, warna `#64748B`

### D. Standar Ikon: Lucide Icons (Vector SVG)
*Sesuai revisi arahan Pengguna: **Dilarang menggunakan emoji** sebagai ikon. Sistem mengadopsi **Lucide Icons** (vektor SVG garis bersih / minimalist outline, stroke 1.75px) yang terbukti standar untuk Enterprise Dashboard modern.*

**Pemetaan Ikon Resmi Lucide:**
- **Dashboard**: `<svg ...> (layout-dashboard)`
- **Data & Batch Drum**: `<svg ...> (package / layers)`
- **Penyortiran (Sorting)**: `<svg ...> (scan / filter-check)`
- **Peniupan (Denting)**: `<svg ...> (wind)`
- **Pencucian (Washing)**: `<svg ...> (droplets / sparkle)`
- **Pengecatan (Painting)**: `<svg ...> (palette / brush)`
- **Catatan Kendaraan / Gerbang**: `<svg ...> (truck)`
- **Rekapitulasi Laporan**: `<svg ...> (bar-chart-3 / file-text)`
- **Ekspor Dokumen**: `<svg ...> (file-spreadsheet & file-down)`
- **Pos Satpam & Verifikasi B3**: `<svg ...> (shield-check)`
- **Tombol Tambah / Input**: `<svg ...> (plus)`
- **Status Berhasil / Lolos**: `<svg ...> (check-circle-2)`
- **Status Transit / Mengendap**: `<svg ...> (clock / hourglass)`
- **Status Afkir (Reject)**: `<svg ...> (alert-triangle / x-circle)`
- **Profil & Keluar**: `<svg ...> (user & log-out)`

---

## 2. Struktur Tata Letak Utama (Layout Shell)

### A. Sidebar Samping (`#0F172A` Deep Navy)
1. **Header Brand**: Logo geometris PT PKDI + teks *"Pulo Kencana"* dan *"Drum Industri"*.
2. **Navigasi Role Admin Kantor** (Dengan Lucide SVG Icons):
   - **Dashboard** (`admin.dashboard`) — *Icon: layout-dashboard*
   - **Data & Batch Drum** (`admin.drum-batches`) — *Icon: package*
   - **4 Tahap Proses Gudang** (`admin.drum-process`) — *Icon: cog / layers*
   - **Catatan Kendaraan** (`admin.vehicles`) — *Icon: truck*
   - **Rekapitulasi & Ekspor** (`admin.reports`) — *Icon: bar-chart-3*
3. **Navigasi Role Satpam** (Dengan Lucide SVG Icons):
   - **Dashboard Gerbang** (`satpam.dashboard`) — *Icon: shield-check*
   - **Catat Masuk Baru** (`satpam.kendaraan-masuk`) — *Icon: log-in*
   - **Kendaraan Aktif (Di Dalam)** (`satpam.kendaraan-aktif`) — *Icon: clock / truck*
4. **Footer Sidebar**: Profil Pengguna (*Icon: user*) & Log Out (*Icon: log-out*).

### B. Top App Bar (`#FFFFFF`)
- **Kolom Pencarian Cepat**: Input pencarian global dengan ikon Lucide `search`.
- **Indikator Tanggal & Hari**: Format lokal Indonesia (contoh: *Selasa, 28 Oktober 2026*) dengan ikon Lucide `calendar`.
- **User Profile Pill**: Menampilkan nama akun pengguna dan badge role formal (`ADMIN` / `SATPAM`).

### C. Area Kerja Utama (Main Viewport `#F7F9FC`)
- Tata letak modular berbasis grid kartu yang konsisten.
- Seluruh elemen aksi penting berada dalam jangkauan 1-klik dengan ikon Lucide pendukung.

---

## 3. Rencana Tahapan Eksekusi Bertahap (Revisi: Prioritas Login Pertama)

### 📌 Langkah 1: Fondasi Desain & Redesign Halaman Login Dual-Panel (PRIORITAS UTAMA)
- Daftarkan Google Font `Plus Jakarta Sans` pada `resources/views/layouts/guest.blade.php` dan `app.blade.php`.
- Konfigurasi token warna *Industrial Pro* di `tailwind.config.js`.
- Rombak layout tamu (`resources/views/layouts/guest.blade.php`) dan halaman login (`resources/views/livewire/pages/auth/login.blade.php`) menjadi tampilan **Dual-Panel Industrial Pro** persis seperti referensi:
  - **Panel Kiri (Deep Navy `#0F172A`)**:
    - Logo geometris perusahaan + teks *"Pulo Kencana - Drum Industri"*.
    - Headline utama: *"Kelola Operasional, Tingkatkan Efisiensi"*.
    - Deskripsi: *"Sistem administrasi operasional untuk mendukung proses gudang dan pos jaga secara terstruktur."*.
    - Ornamen grafis industrial halus beraksen modern.
  - **Panel Kanan (Surface Card `#FFFFFF`)**:
    - Judul *"Masuk ke Akun Anda"* dan subjudul *"Silakan login untuk melanjutkan"*.
    - Form input dengan label jelas: *Email / Akun* dan *Password*.
    - Checkbox *Ingat saya* dan tombol aksi utama *"Masuk"* (`bg-[#2563EB]` hover `bg-[#1D4ED8]`).
    - Footer hak cipta resmi: *"© 2026 PT Pulo Kencana Drum Industri"*.

### 📌 Langkah 2: Layout Shell Utama (Sidebar Navy `#0F172A` + Top Bar Header)
- Transformasi `resources/views/layouts/app.blade.php` menjadi layout *Enterprise Sidebar Navy + Top Bar + Canvas `#F7F9FC`*.
- Bangun navigasi sidebar modular dengan ikon **Lucide SVG** dan penanda aktif (*active pill indicator*).
- Bangun bilah atas (*Top Bar*) dengan search input, tanggal lokal otomatis, dan chip profil pengguna.

### 📌 Langkah 3: Redesign Dashboard Admin Kantor
- Rombak `resources/views/admin/dashboard.blade.php`:
  - 4 Kartu KPI Metrik (Total Drum, Drum Diproses, Drum Selesai/Pengendapan, Kunjungan Truk) dengan ikon Lucide dan indikator sub-teks.
  - Grafik/Diagram ringkasan visual status operasional drum.
  - Kotak Akses Cepat (*Quick Actions*) ke seluruh modul operasional.
  - Panel pemantauan armada truk aktif di dalam pabrik.

### 📌 Langkah 4: Redesign Modul Penerimaan & Transit Batch Drum
- Rombak `resources/views/livewire/admin/drum-batch-index.blade.php`:
  - Bilah filter status pengendapan & pencarian kode batch bergaya modern pill.
  - Tabel data berdesain monospaced untuk kode batch, status pill berwarna sesuai referensi, dan tombol update cepat.
  - Modal input batch baru & modal status update dengan header gelap dan form input dua kolom yang rapi.

### 📌 Langkah 5: Redesign Modul Log 4 Proses Gudang
- Rombak `resources/views/livewire/admin/drum-process-index.blade.php`:
  - Identitas warna khusus per proses (🔍 Sortir = biru, 💨 Tiup = amber, 🧼 Cuci = ungu, 🎨 Cat = rose).
  - Tampilan visual tegas yang membedakan *Tanggal Aktivitas Fisik* vs *Tanggal Input Sistem*.
  - Desain modal input laporan rekondisi yang intuitif dan profesional.

### 📌 Langkah 6: Redesign Catatan Kendaraan & Rekapitulasi Laporan
- Rombak `resources/views/livewire/admin/vehicle-log-index.blade.php`:
  - Kartu tabel log kendaraan masuk/keluar dengan lencana verifikasi surat jalan B3 yang mencolok.
- Rombak `resources/views/livewire/admin/report-index.blade.php`:
  - Filter rentang periode yang ringkas dan modern.
  - Tombol ekspor spreadsheet (Excel CSV) dan dokumen PDF resmi.
  - Tab navigasi pill interaktif antar 3 sub-laporan.

### 📌 Langkah 7: Redesign Modul Satpam (Dashboard & Gerbang)
- Rombak `resources/views/satpam/dashboard.blade.php`, `kendaraan-masuk.blade.php`, dan `kendaraan-aktif.blade.php`:
  - Desain antarmuka pos jaga yang ergonomis, kontras tinggi, mudah diinput dengan cepat saat kendaraan tiba di gerbang.

---

## 4. Prosedur Jaminan Kualitas (QA) & Aturan Verifikasi

```
[ AI Agent ]                                              [ Pengguna / User ]
     │                                                            │
     ├─ 1. Tulis kode Blade/CSS bertahap                          │
     ├─ 2. Jalankan `npm run build` (Kompilasi CSS Vite)          │
     ├─ 3. Jalankan `php artisan test` (48 Test Logika Hijau)     │
     └─ 4. Serahkan kepada Pengguna untuk review visual           │
                                                                  ├─ 5. Pengguna buka browser (F5)
                                                                  └─ 6. Pengguna memberikan feedback/approval
```

1. **Aturan Uji Browser**: Asisten AI dilarang keras membuka browser subagent untuk inspeksi visual. Pengguna akan mengecek langsung di browser lokal (`http://127.0.0.1:8000`).
2. **Aturan Integritas Logika**: Setiap kali file Blade diubah, 48 feature tests PHPUnit harus tetap berstatus **100% Passed**.
3. **Penyusunan Kode**: Setiap modifikasi kode dilakukan secara modular per berkas dan dijelaskan tahapannya kepada pengguna.

---
*Dokumen ini menjadi acuan resmi implementasi styling Tahap 7 pada proyek Sistem Administrasi Gudang PT Pulo Kencana Drum Industri.*
