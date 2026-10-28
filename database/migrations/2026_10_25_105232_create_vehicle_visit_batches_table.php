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
        // Tabel Pivot untuk Relasi M:N Mengangkut antara Kendaraan & Batch Drum
        Schema::create('vehicle_visit_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_visit_id')->constrained('vehicle_visits')->onDelete('cascade'); // ID Kunjungan Truk
            $table->foreignId('drum_batch_id')->constrained('drum_batches')->onDelete('cascade');       // ID Batch Drum
            $table->integer('jumlah_drum');                                                             // Atribut Relasi ERD: JumlahDrum
            $table->string('keterangan_muatan')->nullable();                                            // Atribut Relasi ERD: KeteranganMuatan
            $table->timestamps();

            // Mencegah duplikasi data: satu truk tidak boleh menautkan batch yang sama lebih dari sekali
            $table->unique(['vehicle_visit_id', 'drum_batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_visit_batches');
    }
};
