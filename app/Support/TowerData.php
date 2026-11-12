<?php

namespace App\Support;

use App\Models\Unit;
use Illuminate\Support\Collection;

/**
 * Data ringkas untuk scene Three.js (resources/js/site/tower.js).
 * Setiap unit = satu modul gedung; warna jendela mengikuti status unit.
 */
class TowerData
{
    public const COLUMNS = ['id', 'blok', 'lantai', 'no_kamar', 'gedung', 'status', 'wilayah', 'harga_sewa'];

    public static function all(): Collection
    {
        return Unit::orderBy('blok')->orderBy('lantai')->orderBy('no_kamar')->get(self::COLUMNS);
    }

    public static function payload(Collection $units, string $mode = 'city', ?int $focus = null): array
    {
        return [
            'mode'  => $mode,
            'focus' => $focus,
            'units' => $units->map(fn (Unit $u) => [
                'id' => $u->id,
                'b'  => $u->blok,
                'l'  => (int) $u->lantai,
                'n'  => $u->no_kamar,
                's'  => $u->status,
                'h'  => (int) $u->harga_sewa,
                'w'  => $u->wilayah,
                'g'  => $u->gedung,
                'u'  => route('public.unit.detail', $u),
            ])->values()->all(),
        ];
    }
}
