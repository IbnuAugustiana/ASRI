<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dibuat ulang dari struktur database asli (file migrasi lama hilang).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            $table->string('nik', 16)->default('')->after('unit_id');
            $table->string('no_hp', 20)->default('')->after('nik');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_sewas', function (Blueprint $table) {
            $table->dropColumn(['nik', 'no_hp']);
        });
    }
};
