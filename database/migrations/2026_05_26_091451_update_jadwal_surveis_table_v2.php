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
        Schema::table('jadwal_surveis', function (Blueprint $table) {
            // 1. Mengubah pengajuan_sewa_id menjadi nullable
            // Kita gunakan unsignedBigInteger agar kompatibel dengan tipe foreign key asli
            $table->unsignedBigInteger('pengajuan_sewa_id')->nullable()->change();

            // 2. Menambahkan kolom unit_id agar survei mandiri punya referensi unit
            // Kita cek dulu apakah kolomnya sudah ada (prevent error)
            if (!Schema::hasColumn('jadwal_surveis', 'unit_id')) {
                $table->foreignId('unit_id')
                      ->after('user_id')
                      ->constrained('units')
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_surveis', function (Blueprint $table) {
            // Mengembalikan pengajuan_sewa_id menjadi wajib (not null)
            $table->unsignedBigInteger('pengajuan_sewa_id')->nullable(false)->change();

            // Menghapus foreign key dan kolom unit_id
            if (Schema::hasColumn('jadwal_surveis', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
        });
    }
};