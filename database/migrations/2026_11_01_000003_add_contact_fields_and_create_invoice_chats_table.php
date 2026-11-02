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
        Schema::table('finance_invoices', function (Blueprint $table) {
            $table->string('kontak_pic')->nullable()->after('pihak_terkait');
            $table->string('nomor_wa')->nullable()->after('kontak_pic');
            $table->timestamp('terakhir_ditagih_at')->nullable()->after('keterangan');
        });

        Schema::create('finance_invoice_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_invoice_id')->constrained('finance_invoices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('pesan');
            $table->string('kanal')->default('whatsapp'); // whatsapp, telepon, email, tatap_muka
            $table->string('status_respon')->default('menunggu_respon'); // menunggu_respon, janji_bayar, menunggu_transfer, komplain, selesai
            $table->date('tanggal_janji_bayar')->nullable();
            $table->text('catatan_internal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_invoice_chats');

        Schema::table('finance_invoices', function (Blueprint $table) {
            $table->dropColumn(['kontak_pic', 'nomor_wa', 'terakhir_ditagih_at']);
        });
    }
};
