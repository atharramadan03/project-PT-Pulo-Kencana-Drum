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
        Schema::create('vehicle_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_visit_id')->nullable()->constrained('vehicle_visits')->nullOnDelete();
            $table->string('nomor_polisi');
            $table->string('jenis_kendaraan'); // Truk Tronton, Engkel Box, Pickup, dll
            $table->date('tanggal_perbaikan');
            $table->string('jenis_perbaikan'); // Ganti Oli, Servis Rem, Ganti Ban, dll
            $table->string('nama_bengkel');
            $table->decimal('biaya', 15, 2);
            $table->enum('status_perbaikan', ['selesai', 'dalam_pengerjaan', 'dijadwalkan'])->default('selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_logs');
    }
};
