<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrak_sewas', function (Blueprint $table) {
            // Kolom terpisah untuk menyimpan rincian data pengunduran diri
            $table->text('alasan_undur_diri')->nullable()->after('status_perpanjangan');
            $table->date('tgl_rencana_keluar')->nullable()->after('alasan_undur_diri');
            $table->date('tgl_balik_kunci')->nullable()->after('tgl_rencana_keluar');
            $table->string('berkas_undur_diri')->nullable()->after('tgl_balik_kunci');
        });
    }

    public function down(): void
    {
        Schema::table('kontrak_sewas', function (Blueprint $table) {
            $table->dropColumn(['alasan_undur_diri', 'tgl_rencana_keluar', 'tgl_balik_kunci', 'berkas_undur_diri']);
        });
    }
};