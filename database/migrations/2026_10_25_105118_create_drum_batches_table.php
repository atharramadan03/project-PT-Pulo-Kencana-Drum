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
        Schema::create('drum_batches', function (Blueprint $table) {
            $table->id();                                                   // BatchID (Primary Key)
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Relasi Menginput: Admin Kantor penginput
            $table->string('kode_batch')->unique();                         // Kode Batch Unik (misal: BATCH-202610-001)
            $table->date('tanggal_penerimaan');                             // Tanggal drum diterima di gudang
            $table->integer('jumlah_diterima');                             // Jumlah total drum yang diterima
            $table->string('kategori_jenis')->nullable();                   // Jenis drum: Besi Tight Head, Open Top, Plastik
            $table->string('status_pengendapan')->default('Transit');       // Atribut ERD: Transit / Mengendap / Selesai
            $table->string('status_proses')->default('Diterima');           // Atribut ERD: Diterima / Dalam Proses / Selesai
            $table->string('sumber_laporan')->nullable();                   // Catatan siapa/dari mana laporan fisik diterima
            $table->text('catatan')->nullable();                            // Catatan kondisi awal drum
            $table->timestamps();                                           // Timestamp pembuatan & update
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drum_batches');
    }
};
