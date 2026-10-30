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
        Schema::create('warehouse_finance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('drum_batch_id')->nullable()->constrained('drum_batches')->nullOnDelete();
            $table->string('kode_transaksi')->unique();
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran']);
            $table->string('kategori'); // penjualan_drum, pembelian_batch, bahan_operasional, lainnya
            $table->date('tanggal_transaksi');
            $table->integer('jumlah_unit')->nullable()->default(0);
            $table->decimal('nominal', 15, 2);
            $table->string('metode_pembayaran')->default('transfer'); // transfer, tunai, tempo
            $table->string('pihak_terkait'); // nama supplier / customer / vendor
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_finance_logs');
    }
};
