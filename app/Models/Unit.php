<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'blok', 'lantai', 'no_kamar', 'gedung', 'alamat',
        'deskripsi', 'gambar', 'harga_sewa', 'status', 'wilayah', 'luas_m2',
    ];

    protected $casts = [
        'harga_sewa' => 'decimal:2',
        'luas_m2' => 'integer',
    ];

    public function pengajuanSewas() { return $this->hasMany(PengajuanSewa::class); }
    public function kontrakSewas() { return $this->hasMany(KontrakSewa::class); }
    public function tagihans() { return $this->hasMany(Tagihan::class); }
    public function laporanKerusakans() { return $this->hasMany(LaporanKerusakan::class); }

    public function penghuni()
    {
        return $this->hasOneThrough(User::class, KontrakSewa::class,
            'unit_id', 'id', 'id', 'user_id');
    }

    public function scopeTersedia($query) { return $query->where('status', 'tersedia'); }
    public function scopeWilayah($query, $wilayah) { return $query->where('wilayah', $wilayah); }

    public function getNamaUnitAttribute(): string
    {
        return "Blok {$this->blok} - Lt.{$this->lantai} No.{$this->no_kamar}";
    }

    /*
    |--------------------------------------------------------------------------
    | FOTO UNIT
    | Foto stok (Unsplash License) di public/images/units. Dibagi bergiliran
    | per unit supaya kartu yang bersebelahan selalu berbeda. Foto yang
    | diunggah admin (kolom "gambar") selalu didahulukan.
    |--------------------------------------------------------------------------
    */
    private const FOTO_INTERIOR = [
        'liv-1', 'bed-1', 'kit-1', 'liv-2', 'bed-2', 'kit-2', 'liv-3', 'bed-3', 'kit-3', 'liv-4',
        'bed-4', 'kit-4', 'liv-5', 'bed-5', 'kit-5', 'liv-6', 'bed-6', 'kit-6', 'liv-7', 'liv-8',
    ];
    private const FOTO_GEDUNG = ['A' => 'ext-a', 'B' => 'ext-b', 'C' => 'ext-c', 'D' => 'ext-d', 'E' => 'ext-e'];

    private static function foto(string $nama): string
    {
        return asset("images/units/{$nama}.jpg");
    }

    public function getPunyaUnggahanAttribute(): bool
    {
        return $this->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->gambar);
    }

    /** Foto utama untuk kartu & daftar. */
    public function getFotoUrlAttribute(): string
    {
        if ($this->punya_unggahan) {
            return \Illuminate\Support\Facades\Storage::url($this->gambar);
        }

        return self::foto(self::FOTO_INTERIOR[((int) $this->id * 7) % count(self::FOTO_INTERIOR)]);
    }

    /** Foto eksterior gedung sesuai blok. */
    public function getFotoGedungUrlAttribute(): string
    {
        return self::foto(self::FOTO_GEDUNG[$this->blok] ?? (((int) $this->id % 2) ? 'ext-f' : 'ext-g'));
    }

    /** Galeri halaman detail: foto utama, gedung, ruang tamu, kamar, dapur (tanpa duplikat). */
    public function getGaleriAttribute(): array
    {
        $i = (int) $this->id;
        $items = [
            ['src' => $this->foto_url, 'label' => 'Foto utama'],
            ['src' => $this->foto_gedung_url, 'label' => $this->gedung],
            ['src' => self::foto('liv-' . (($i % 8) + 1)), 'label' => 'Ruang tamu'],
            ['src' => self::foto('bed-' . ((($i + 2) % 6) + 1)), 'label' => 'Kamar tidur'],
            ['src' => self::foto('kit-' . ((($i + 4) % 6) + 1)), 'label' => 'Dapur'],
        ];

        return collect($items)->unique('src')->values()->all();
    }
}
