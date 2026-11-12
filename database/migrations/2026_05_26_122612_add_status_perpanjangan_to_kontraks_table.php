<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrak_sewas', function (Blueprint $table) {
            // Menambahkan kolom status perpanjangan setelah kolom status sewa
            $table->string('status_perpanjangan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('kontrak_sewas', function (Blueprint $table) {
            $table->dropColumn('status_perpanjangan');
        });
    }
};