<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakSewa extends Model
{
    // Tambahkan kolom baru ke fillable agar bisa diisi (mass assignment)
    protected $fillable = [
        'user_id', 
        'unit_id', 
        'pengajuan_sewa_id', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'harga_per_bulan', 
        'status', 
        'dokumen_kontrak',
        'status_perpanjangan',
        'alasan_undur_diri',    // Kolom baru
        'tgl_rencana_keluar',  // Kolom baru
        'tgl_balik_kunci',     // Kolom baru
        'berkas_undur_diri'    // Kolom baru
    ];

    // Tambahkan casting agar Laravel otomatis mengubah string tanggal dari database 
    // menjadi objek Carbon (agar bisa dipanggil ->format() di Blade)
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tgl_rencana_keluar' => 'date', // Penting untuk format tanggal
        'tgl_balik_kunci' => 'date',    // Penting untuk format tanggal
        'harga_per_bulan' => 'decimal:2'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function pengajuanSewa() { return $this->belongsTo(PengajuanSewa::class); }
}