<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_visits', function (Blueprint $table) {
            $table->id();                                                   // VisitID (Primary Key)
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Relasi Mencatat: Satpam pencatat
            $table->string('nomor_kunjungan')->unique();                    // Nomor Kunjungan Unik (Auto-generated)
            $table->string('nomor_polisi');                                 // Nomor Polisi Kendaraan (Plat Nomor)
            $table->string('nama_pengemudi');                               // Nama Supir / Pengemudi
            $table->string('asal_tujuan');                                  // Perusahaan / Kota Asal / Tujuan
            $table->string('keperluan');                                    // Keperluan kunjungan
            $table->dateTime('waktu_masuk');                                // Waktu Masuk gerbang
            $table->dateTime('waktu_keluar')->nullable();                   // Waktu Keluar (nullable: diisi saat keluar)
            $table->string('pos_gerbang')->default('Gerbang Utama');        // Atribut relasi Mencatat di ERD
            $table->string('informasi_surat_jalan')->nullable();            // Nomor fisik surat jalan pengemudi
            $table->enum('status_pemeriksaan_surat_jalan_b3', [             // Pemeriksaan surat jalan kemasan limbah B3
                'sesuai', 
                'tidak_sesuai', 
                'tidak_ada_b3'
            ])->default('tidak_ada_b3');
            $table->string('keterangan_muatan')->nullable();                // Keterangan ringkas muatan
            $table->text('catatan')->nullable();                            // Catatan tambahan satpam
            $table->timestamps();                                           // Menyimpan created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_visits');
    }
};
