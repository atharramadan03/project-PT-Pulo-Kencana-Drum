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
        Schema::create('operational_expense_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kode_biaya')->unique(); // Format unik sistem: BOP-YYYYMM-001
            $table->enum('kategori_biaya', [
                'listrik_air',
                'bbm_solar',
                'perlengkapan_apd',
                'konsumsi_karyawan',
                'atk_kantor',
                'pemeliharaan_fasilitas',
                'lainnya',
            ])->default('lainnya');
            $table->string('nama_biaya'); // Deskripsi pengeluaran, misal: "Listrik PLN Pabrik & Pompa Cuci Drum"
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_pengeluaran');
            $table->enum('metode_pembayaran', ['tunai_kas_kecil', 'transfer_bank'])->default('tunai_kas_kecil');
            $table->string('nomor_bukti')->nullable(); // Nomor nota/bon/kwitansi pembelian
            $table->string('penerima_dana'); // Toko / Rekanan / Pihak penerima pembayaran
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_expense_logs');
    }
};
