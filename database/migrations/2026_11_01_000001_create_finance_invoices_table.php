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
        Schema::create('finance_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kode_tagihan')->unique(); // Format unik sistem: INV-YYYYMM-001
            $table->string('nomor_invoice'); // Nomor faktur/nota resmi fisik
            $table->enum('jenis_tagihan', ['tagihan_pelanggan', 'tagihan_vendor'])->default('tagihan_pelanggan');
            $table->string('pihak_terkait'); // Nama PT / CV / Vendor / Klien
            $table->decimal('nominal', 15, 2);
            $table->decimal('nominal_dibayar', 15, 2)->default(0);
            $table->date('tanggal_terbit');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_lunas')->nullable();
            $table->enum('status_pembayaran', ['belum_dibayar', 'sebagian', 'lunas', 'jatuh_tempo'])->default('belum_dibayar');
            $table->string('metode_pembayaran')->default('transfer_bank');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_invoices');
    }
};
