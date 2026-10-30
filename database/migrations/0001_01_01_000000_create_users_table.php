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
        // 1. Tabel Utama Pengguna (Users) sesuai ERD & PRD
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                                   // Primary Key (ID Pengguna)
            $table->string('name');                                         // Nama Lengkap (NamaAdmin / NamaSatpam)
            $table->string('username')->unique()->nullable();               // Username login unik
            $table->string('email')->unique();                              // Email resmi
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                                     // Password terenkripsi (Bcrypt)
            $table->enum('role', ['admin_kantor', 'satpam', 'admin_keuangan'])->default('admin_kantor'); // Role resmi sistem
            $table->string('shift_jaga')->nullable();                       // Atribut Satpam di ERD: Pagi/Siang/Malam
            $table->string('status_akun')->default('aktif');                // Status: aktif / nonaktif
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Tabel Bawaan Laravel untuk Reset Password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Tabel Bawaan Laravel untuk Pengelolaan Sesi Login (Sessions)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
