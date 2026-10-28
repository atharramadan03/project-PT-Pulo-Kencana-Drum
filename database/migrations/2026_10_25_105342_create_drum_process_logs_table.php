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
        Schema::create('drum_process_logs', function (Blueprint $table) {
            $table->id();                                                   // LogID (Primary Key)
            $table->foreignId('drum_batch_id')->constrained('drum_batches')->onDelete('cascade'); // Relasi Menjalani: Batch drum yang diproses
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');              // Relasi Merekam: Admin Kantor pencatat
            $table->enum('jenis_proses', [                                  // 4 Tahap Proses Gudang resmi PRD
                'penyortiran', 
                'peniupan', 
                'pencucian', 
                'pengecatan'
            ]);
            $table->date('tanggal_aktivitas');                              // Tanggal fisik dikerjakan di gudang
            $table->dateTime('tanggal_input');                              // Tanggal & jam Admin mengetik ke sistem
            $table->integer('jumlah_diproses');                             // Jumlah drum yang diproses
            $table->integer('jumlah_afkir')->default(0);                    // Jumlah drum afkir / reject
            $table->string('pelapor_gudang');                               // Nama mandor / petugas gudang pelapor
            $table->text('hasil_catatan')->nullable();                      // Catatan hasil (misal: cat warna biru standar)
            $table->timestamps();                                           // Timestamp pembuatan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drum_process_logs');
    }
};
