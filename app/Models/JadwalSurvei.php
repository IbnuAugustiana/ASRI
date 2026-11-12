<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalSurvei extends Model
{
    protected $fillable = [
        'pengajuan_sewa_id', 
        'unit_id', 
        'user_id', 
        'tanggal_survei', 
        'status', 
        'catatan'
    ];

    protected $casts = [
        'tanggal_survei' => 'datetime'
    ];

    /**
     * Relasi ke Unit (PENTING: untuk menampilkan detail unit di riwayat survei)
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relasi ke User/Calon Penghuni
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Pengajuan Sewa (Bisa null jika survei mandiri)
     */
    public function pengajuanSewa(): BelongsTo
    {
        return $this->belongsTo(PengajuanSewa::class, 'pengajuan_sewa_id');
    }
}