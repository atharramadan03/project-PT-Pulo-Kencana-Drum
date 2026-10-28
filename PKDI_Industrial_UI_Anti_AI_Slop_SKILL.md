---
name: pkdi-industrial-ui
description: Gunakan saat membuat, mengubah, atau meninjau UI frontend Laravel untuk Sistem Administrasi Operasional dan Rekapitulasi Laporan Gudang PT Pulo Kencana Drum Industri. Ikuti referensi visual Industrial Pro, palet warna resmi, Lucide Icons, dan prinsip anti-AI-slop.
---

# PKDI Industrial UI — Industrial Pro, Anti AI-Slop

## 1. Peran dan tujuan

Bertindak sebagai Senior Product Designer dan Frontend Engineer yang memahami aplikasi internal perusahaan industri. Bangun UI yang terasa dirancang untuk pekerjaan administrasi gudang dan gerbang PKDI, bukan dashboard template generik yang hanya terlihat menarik di screenshot.

**Nama produk:** Sistem Administrasi Operasional Berbasis Web untuk Rekapitulasi Laporan Gudang  
**Perusahaan:** PT Pulo Kencana Drum Industri (PKDI)  
**Stack yang diharapkan:** Laravel, Blade/Livewire sesuai codebase yang benar-benar ada, Tailwind CSS, Vite, Lucide Icons.

### Sumber kebenaran desain

Gunakan urutan ini saat mengambil keputusan:
1. Palet warna dan spesifikasi pada skill ini — nilainya tetap dan tidak boleh diganti.
2. Gambar referensi **Industrial Pro** yang disediakan pengguna — ikuti hierarki, komposisi, dan karakter visualnya, bukan menyalin angka contoh secara literal. Jika memungkinkan, simpan gambar di `docs/references/industrial-pro-reference.png` agar tersedia sebagai referensi di repository.
3. Dokumen implementasi styling PKDI yang disediakan pengguna — ikuti scope dan urutan halaman yang direncanakan.
4. Struktur, dependency, route, komponen, database, dan pola kode aktual dalam repository.
5. Keputusan desain kecil yang diperlukan agar implementasi konsisten.

Jika gambar atau dokumen menampilkan data contoh yang belum ada di database, jangan tampilkan seolah-olah data sungguhan. Jika dokumen menyebut file atau route yang tidak cocok dengan repository, periksa struktur aktual terlebih dahulu dan jangan membuat nama route/fungsi palsu.

## 2. Prinsip anti-AI-slop

UI harus **profesional, industrial, fungsional, tenang, dan mudah dipindai**.

- Utamakan tugas pengguna: mencari data drum, membaca status, menginput laporan, mencatat kendaraan, dan merekap data.
- Gunakan bahasa Indonesia yang wajar, singkat, dan berorientasi tugas. Hindari slogan startup, istilah Inggris yang tidak perlu, copy generik, dan teks pengisi.
- Jangan menambah fitur, metrik, aktivitas, grafik, menu, atau tombol yang tidak didukung scope atau backend hanya agar halaman tampak lengkap.
- Jangan membuat semua elemen menjadi kartu. Gunakan struktur halaman, section title, tabel, dan whitespace untuk membentuk hierarki; kartu hanya untuk ringkasan penting atau kelompok informasi yang memang perlu dipisahkan.
- Hindari gradient berlebihan, glassmorphism, neon, glow, blur dekoratif, background abstrak, ilustrasi stok, animasi yang tidak membantu pekerjaan, dan bayangan besar.
- Hindari radius berlebihan. Gunakan radius kecil-menengah secara konsisten; bentuk pill dipakai terutama untuk status badge atau profil kecil jika cocok.
- Jangan menambahkan warna baru atau mengganti token warna. Warna status khusus di bawah hanya digunakan untuk makna status yang sesuai.
- Jangan mencampur library ikon, emoji, ikon 3D, atau beberapa gaya stroke. Gunakan Lucide Icons saja.
- Jangan menambahkan dependency UI baru sebelum memeriksa `composer.json`, `package.json`, dan implementasi yang sudah tersedia.
- Jangan melakukan rewrite besar jika perubahan lokal pada layout atau komponen sudah cukup.

## 3. Palet resmi — nilai wajib dipertahankan

**Jangan mengganti, menggeser, atau mengarang nilai warna berikut.** Gunakan token CSS/Tailwind yang konsisten jika konfigurasi proyek memungkinkan.

| Token | HEX | Penggunaan |
|---|---|---|
| Primary | `#2563EB` | Tombol utama, menu aktif, aksi utama, fokus interaktif |
| Secondary | `#3B82F6` | Tombol sekunder, ikon pendukung, hover yang sesuai |
| Accent / Action | `#F59E0B` | Peringatan dan perhatian khusus; status Peniupan sesuai spesifikasi |
| Sidebar Navy | `#0F172A` | Background sidebar dan panel brand login |
| Canvas Background | `#F7F9FC` | Latar area kerja utama |
| Surface Card | `#FFFFFF` | Permukaan form, tabel, modal, top bar, dan panel |
| Text Primary | `#0F172A` | Heading, data utama, label tabel penting |
| Text Secondary | `#64748B` | Label, helper text, metadata, keterangan |
| Border / Divider | `#E2E8F0` | Garis pembatas, border input dan tabel |
| Primary Hover | `#1D4ED8` | Hover tombol utama |

Warna status resmi:

| Makna | Background | Text | Aturan |
|---|---|---|---|
| Selesai / Lolos | `#ECFDF5` | `#059669` | Status berhasil |
| Diproses / Berjalan | `#EFF6FF` | `#2563EB` | Status berjalan |
| Penyortiran | `#E0F2FE` | `#0369A1` | Tahap penyortiran |
| Peniupan | `#FEF3C7` | `#B45309` | Tahap peniupan |
| Pencucian | `#F5F3FF` | `#7C3AED` | Tahap pencucian |
| Pengecatan | `#FEF2F2` | `#DC2626` | Tahap pengecatan |
| Transit / Mengendap | `#F1F5F9` | `#475569` | Status transit/pengendapan |
| Afkir / Reject | `#FEE2E2` | `#991B1B` | Status afkir jika memang ada dalam model data |

Status badge boleh memakai bentuk pill yang ringkas seperti pada gambar referensi, tetapi selalu tampilkan teks status. Jangan memakai warna sebagai satu-satunya penanda.

## 4. Tipografi, jarak, dan komposisi

- Prioritaskan font yang sudah ada. Jika belum ditetapkan, gunakan **Plus Jakarta Sans**; gunakan Inter hanya jika sudah menjadi font proyek atau terdapat alasan kompatibilitas.
- H1 halaman umumnya 24–32 px, bold 700; jangan memperbesar judul hanya untuk memenuhi ruang.
- H2 section/kartu 18–20 px, semibold 600.
- Body/form 14 px, regular 400.
- Label dan metadata 12 px, medium 500 atau semibold 600, warna `#64748B`.
- Gunakan spacing yang mengikuti skala Tailwind dan konsisten antarhalaman. Hindari nilai acak yang hanya mempercantik satu komponen.
- Gunakan border `#E2E8F0` dan shadow sangat ringan bila diperlukan. Pisahkan bagian terutama melalui alignment, whitespace, dan border, bukan banyak efek visual.
- Hindari teks terlalu kecil, terutama data tabel, label input, status, dan tombol.
- Tata letak harus tetap masuk akal di layar laptop standar dan dapat menyesuaikan layar sempit. Di layar kecil, tabel boleh memakai scroll horizontal yang disengaja; jangan sampai seluruh halaman melebar tanpa kontrol.

## 5. Lucide Icons — standar tunggal

- Gunakan **Lucide Icons** dalam gaya SVG outline yang konsisten, dengan stroke visual sekitar 1.75–2 px. Jangan menggunakan emoji sebagai ikon.
- Ikuti integrasi yang sudah ada di repository, misalnya Blade Lucide, package SVG, atau komponen yang sudah dipakai. Jangan mengasumsikan package tertentu tersedia.
- Sebelum memakai ikon, pastikan nama ikonnya tersedia di versi library yang terpasang. Jika tidak ada, pilih ikon Lucide lain yang maknanya paling dekat.
- Ukuran umum: 18–20 px untuk navigasi, sekitar 16 px untuk aksi di tabel, dan 20–24 px untuk ringkasan jika memang diperlukan.
- Gunakan ikon yang sama untuk makna yang sama di seluruh aplikasi.

Pemetaan ikon yang disarankan:

| Fungsi | Nama ikon Lucide |
|---|---|
| Dashboard | `layout-dashboard` |
| Data/batch drum | `package` atau `layers` |
| Penyortiran | `scan` atau `filter-check` jika tersedia |
| Peniupan | `wind` |
| Pencucian | `droplets` |
| Pengecatan | `paintbrush` atau `palette` |
| Catatan kendaraan | `truck` |
| Rekap laporan | `chart-no-axes-combined`, `chart-column`, atau `file-text` sesuai versi dan kebutuhan |
| Ekspor | `file-down` atau `file-spreadsheet` jika tersedia |
| Gerbang/keamanan | `shield-check` |
| Tambah | `plus` |
| Berhasil | `circle-check` atau `check-circle` sesuai versi |
| Transit/menunggu | `clock` atau `hourglass` |
| Peringatan/afkir | `triangle-alert` atau `circle-x` sesuai versi |
| Pengguna | `user-round` atau `users` |
| Keluar | `log-out` |
| Cari/filter/tanggal | `search`, `filter`, `calendar` |

Jangan biarkan ikon berdiri sendiri tanpa label/tooltip atau accessible name saat maknanya tidak jelas. Jangan membuat ikon dekoratif besar di setiap kartu KPI.

## 6. Bentuk halaman berdasarkan referensi Industrial Pro

### A. Login — dual panel

Ikuti komposisi referensi: panel brand navy di satu sisi dan panel form putih di sisi lain, di atas canvas yang terang.

- Panel navy berisi logo perusahaan yang sudah ada, nama “Pulo Kencana” dan “Drum Industri”, headline “Kelola Operasional, Tingkatkan Efisiensi”, serta deskripsi singkat tentang sistem administrasi operasional.
- Jangan membuat logo resmi baru dari imajinasi. Jika asset logo belum ada, gunakan placeholder/monogram sederhana dan beri tahu pengguna bahwa itu sementara.
- Ornamen industrial harus sangat halus dan tidak mengganggu; tidak perlu ilustrasi pabrik dekoratif besar.
- Panel putih berisi judul “Masuk ke Akun Anda”, subjudul, field berlabel, error validasi, checkbox “Ingat saya” jika didukung, tombol “Masuk”, serta footer hak cipta.
- Pertahankan autentikasi, nama field, mekanisme Livewire, route, error, dan keamanan yang sudah berjalan. Jangan mengubah alur login hanya demi desain.
- Pada layar kecil, panel brand boleh dipadatkan agar form tetap mudah digunakan.

### B. Layout shell aplikasi

- Sidebar berwarna `#0F172A`, berisi brand di bagian atas, navigasi yang sesuai role, dan profil/keluar di bagian bawah jika cocok dengan layout aktual.
- Menu aktif memakai Primary `#2563EB` dengan kontras teks yang baik. Menu tidak aktif menggunakan teks terang yang tenang; jangan menyorot semua menu sekaligus.
- Top bar putih dan sederhana. Search global, tanggal lokal Indonesia, notifikasi, dan profil hanya ditampilkan/diaktifkan bila memang didukung oleh aplikasi. Jangan membuat tombol hiasan yang tidak berfungsi.
- Konten utama menggunakan canvas `#F7F9FC`, dengan lebar konten dan padding konsisten. Hindari garis pemisah pada setiap baris jika spacing sudah cukup.
- Navigasi admin kantor dan pengelola pencatatan gerbang harus berbeda sesuai kebutuhan role dan otorisasi di backend.

### C. Dashboard admin kantor

Ikuti struktur referensi, tetapi tetap informatif dan tidak penuh dekorasi:

- Empat KPI yang direncanakan: Total Drum, Drum Diproses, Drum Selesai/Pengendapan, dan Kunjungan Truk. Gunakan query/data aktual; jika sumber data belum ada, jangan mengarang angkanya.
- Grafik status operasional hanya ditampilkan jika data aktual tersedia dan chart benar-benar membantu pemahaman. Jika data belum tersedia, gunakan empty state yang jujur, bukan chart palsu.
- Panel aktivitas terbaru hanya mengambil aktivitas dari sumber nyata yang tersedia. Jangan hardcode nama pengguna, waktu, atau kegiatan palsu.
- Quick actions hanya menuju tugas yang ada dan diizinkan oleh role.
- Jangan menambah dashboard armada/monitoring kendaraan jika data dan scope aktual tidak mendukungnya.

### D. Halaman data drum/batch

- Tabel harus menjadi fokus utama: kode/nomor drum atau batch, jenis/kategori yang memang tersimpan, status, tanggal, dan aksi yang sesuai.
- Kode referensi dapat menggunakan font tabular/monospace secara terbatas untuk meningkatkan keterbacaan, bukan untuk seluruh isi tabel.
- Filter dan pencarian harus sesuai query backend. Jangan membuat kontrol visual yang tidak memfilter data.
- Form/modal gunakan field yang berhubungan, label eksplisit, validasi dan pesan error yang mudah dimengerti.
- Pertahankan perbedaan tanggal aktivitas fisik dan tanggal input sistem jika memang ada di skema/proses.

### E. Empat tahap proses gudang

Gunakan pemetaan warna status yang telah ditentukan untuk Penyortiran, Peniupan, Pencucian, dan Pengecatan. Jangan menggunakan emoji. Ikon boleh membantu identifikasi, tetapi label tahap wajib terlihat.

- Bedakan tanggal aktivitas fisik dari tanggal input sistem jika dua informasi tersebut ada.
- Tampilkan status dan riwayat berdasarkan model/fitur aktual, jangan mengarang alur persetujuan.
- Form laporan harus memudahkan input cepat dengan urutan field yang masuk akal dan error yang spesifik.

### F. Catatan kendaraan/gerbang dan laporan

- Catatan kendaraan menonjolkan identitas kendaraan/pengemudi dan surat jalan hanya jika field tersebut tersedia dalam proses aktual.
- Untuk pencatatan B3, tampilkan status/verifikasi yang benar-benar ditentukan sistem; jangan menyiratkan izin atau approval yang belum ada.
- Halaman laporan menyediakan filter periode dan ringkasan yang sesuai dengan sumber data. Tombol ekspor CSV/Excel atau PDF hanya ditampilkan jika ekspornya benar-benar diimplementasikan.
- Jangan menambahkan tab, status, atau fitur cetak/ekspor yang tidak ada dalam scope dan kode aktual.

### G. Antarmuka pengelola pencatatan gerbang

- Utamakan input cepat, target interaksi cukup besar, label yang jelas, dan urutan form yang cocok untuk situasi kendaraan tiba/keluar.
- Tampilkan catatan masuk/keluar dan surat jalan sesuai scope yang telah disepakati.
- Jangan memasukkan pemeriksaan atau persetujuan pelepasan drum, riwayat kendaraan, atau fitur lain yang belum menjadi kebutuhan.

## 7. Komponen dan perilaku interaksi

### Tombol

- Primary: background `#2563EB`, teks putih, hover `#1D4ED8`.
- Secondary: putih/permukaan dengan border `#E2E8F0` dan teks yang jelas; gunakan `#3B82F6` untuk aksen sesuai konteks.
- Warning: gunakan `#F59E0B` hanya saat tindakan atau informasi memang perlu perhatian.
- Label tombol berupa kata kerja yang jelas: “Simpan”, “Tambah Data”, “Terapkan Filter”, “Unduh Laporan”.
- Setiap tombol/tautan wajib memiliki fungsi aktual atau ditandai secara eksplisit belum diimplementasikan; jangan membuat aksi palsu.

### Form

- Field memiliki label yang terlihat dan pesan validasi yang spesifik. Placeholder hanya contoh singkat, bukan pengganti label.
- Pertahankan validasi server-side dan error binding Laravel/Livewire.
- Gunakan grouping seperlunya. Dua kolom cocok hanya untuk field pendek yang berkaitan; textarea panjang tetap mendapat lebar memadai.
- Tampilkan disabled/loading state untuk aksi yang sedang diproses jika pola proyek mendukungnya.

### Tabel

- Header tabel mudah dibedakan dari isi; ukuran teks tetap terbaca.
- Angka, tanggal, dan kode disejajarkan secara konsisten. Teks panjang memiliki perilaku wrap/truncate yang disengaja.
- Gunakan empty state, loading state, dan error state jika relevan dan didukung implementasi.
- Aksi edit/detail/hapus dikelompokkan secara konsisten. Tindakan destruktif perlu konfirmasi yang sesuai.

### Status

- Badge status ringkas dengan teks dan warna semantik sesuai tabel token.
- Jangan menambah warna khusus untuk setiap status baru. Jika ada status di luar daftar, cek model/proses terlebih dahulu kemudian pilih warna netral yang sudah tersedia.

## 8. Aturan implementasi Laravel dan Tailwind

1. **Inspect dulu.** Baca struktur repo, `composer.json`, `package.json`, route, layout, Blade/Livewire components, konfigurasi Tailwind, dan test terkait sebelum mengedit.
2. **Pastikan versi dan pola aktual.** Jangan mengasumsikan Tailwind v3/v4, Livewire versi tertentu, atau package ikon tertentu. Periksa sebelum membuat konfigurasi.
3. **Gunakan ulang komponen.** Cari button, input, table, badge, modal, layout, dan navigation yang telah ada. Konsolidasikan pola bila perlu; jangan duplikasi gaya di banyak file.
4. **Token warna tetap.** Konfigurasikan token sesuai struktur Tailwind proyek. Jangan mengubah palet dalam skill ini atau mengganti seluruh konfigurasi yang sudah ada tanpa kebutuhan teknis nyata.
5. **Jaga backend.** Hindari perubahan pada autentikasi, otorisasi, route, validasi, query, database, dan status proses jika tugasnya hanya styling.
6. **Tidak ada data palsu di production.** Gunakan query aktual untuk KPI, grafik, dan aktivitas. Bila data belum tersedia, tampilkan empty state yang jelas.
7. **Perubahan bertahap.** Implementasikan satu kelompok halaman per tahap. Sebelum perubahan luas, sebutkan file yang akan diubah dan alasan singkatnya.
8. **Jangan mengubah scope.** Jangan menambah fitur karena terlihat menarik di referensi jika tidak didukung requirement dan backend.
9. **Jaga kualitas.** Jalankan `npm run build` dan `php artisan test` jika tersedia. Catat baseline test sebelum perubahan jika mudah dilakukan dan pastikan test yang sudah lulus tidak menjadi gagal.
10. **Ikuti batas verifikasi proyek.** Sesuai dokumen implementasi PKDI, pemeriksaan visual browser dilakukan oleh pengguna di browser lokal. Jangan membuka browser/subagent untuk inspeksi visual; serahkan perubahan setelah build/test dan beri tahu pengguna bagian yang perlu dicek.

## 9. Urutan kerja yang diutamakan

Ikuti urutan dalam dokumen implementasi PKDI, kecuali kondisi repository atau instruksi pengguna mengharuskan penyesuaian:

1. Fondasi token dan login dual-panel.
2. Layout shell: sidebar, top bar, dan area kerja.
3. Dashboard admin kantor.
4. Data/batch drum dan transit/pengendapan.
5. Log empat tahap proses gudang.
6. Catatan kendaraan dan rekapitulasi laporan.
7. Dashboard dan form pengelola pencatatan gerbang.

Sebelum berpindah tahap, laporkan file yang berubah, hasil build/test, dan hal yang perlu diperiksa pengguna. Jangan mengklaim visual telah diverifikasi jika pengguna belum memeriksanya sendiri.

## 10. Checklist anti-AI-slop sebelum selesai

- [ ] Palet warna sama persis dengan token resmi; tidak ada warna tema baru.
- [ ] Referensi Industrial Pro diikuti dalam hierarki, bukan ditiru secara buta.
- [ ] Lucide Icons digunakan konsisten; tidak ada emoji atau campuran ikon.
- [ ] Layout memprioritaskan tugas operasional, data, form, dan tabel.
- [ ] Tidak ada slogan marketing atau teks Inggris generik yang tidak perlu.
- [ ] Tidak ada KPI, chart, aktivitas, kendaraan, atau data drum palsu yang terlihat seperti data nyata.
- [ ] Tombol, filter, pencarian, menu, dan ekspor terhubung ke fungsi yang benar.
- [ ] Tidak ada kartu/ornamen/animasi berlebihan atau semua elemen dibuat pill.
- [ ] Role admin dan pengelola pencatatan gerbang hanya melihat fungsi yang sesuai izin.
- [ ] Autentikasi, validasi, route, query, dan otorisasi tetap utuh.
- [ ] `npm run build` dan test yang tersedia telah dijalankan, atau keterbatasannya dilaporkan.
- [ ] Pengguna diberi daftar ringkas yang perlu diperiksa secara visual di browser lokal.

**Standar akhir:** interface harus terasa seperti sistem administrasi operasional industri yang dirancang dengan sengaja: jelas, konsisten, praktis, dan dapat dipercaya. Jika sebuah ornamen atau komponen tidak membantu pengguna memahami data atau menyelesaikan tugas, hapus atau sederhanakan.
