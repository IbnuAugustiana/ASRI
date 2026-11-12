<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dibuat ulang dari struktur database asli (file migrasi lama hilang).
 * Nama file dipertahankan ("taghans") agar cocok dengan tabel migrations yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->decimal('jumlah', 12, 2);
            $table->string('periode');                 // format YYYY-MM
            $table->date('jatuh_tempo');
            $table->enum('status', ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
