@props(['unit', 'floors' => null])
@php
    // Gambar gedung isometrik; jendela unit ini menyala sesuai lantai & nomor kamarnya.
    $lantai = max(1, (int) $unit->lantai);
    $n      = $floors ?? max(5, min($lantai, 12));
    $hl     = min($lantai, $n);
    $cols   = 4;
    $col    = max(0, ((int) $unit->no_kamar - 1) % $cols);

    $w = 20; $h = 14; $d = 26;          // lebar kolom, tinggi lantai, kedalaman gedung
    $W = $cols * $w; $H = $n * $h;
    $c = 0.866; $s = 0.5;

    $pt   = fn ($x, $y, $z) => round(($x - $y) * $c, 2) . ',' . round(($x + $y) * $s - $z, 2);
    $poly = fn (array $pts) => implode(' ', array_map(fn ($q) => $pt(...$q), $pts));

    $front = $poly([[0, $d, 0], [$W, $d, 0], [$W, $d, $H], [0, $d, $H]]);
    $side  = $poly([[$W, 0, 0], [$W, $d, 0], [$W, $d, $H], [$W, 0, $H]]);
    $top   = $poly([[0, 0, $H], [$W, 0, $H], [$W, $d, $H], [0, $d, $H]]);
    $base  = $poly([[-6, -6, 0], [$W + 6, -6, 0], [$W + 6, $d + 6, 0], [-6, $d + 6, 0]]);

    // Posisi label lantai di kiri jendela yang menyala
    $tagZ = ($hl - 0.5) * $h;
    $tagX = round((0 - $d) * $c - 5, 2);
    $tagY = round((0 + $d) * $s - $tagZ + 2.5, 2);

    $minX = -$d * $c - 34; $maxX = $W * $c + 10;
    $minY = -$H - 16;      $maxY = ($W + $d) * $s + 12;
    $vb = round($minX, 1) . ' ' . round($minY, 1) . ' ' . round($maxX - $minX, 1) . ' ' . round($maxY - $minY, 1);
@endphp
<svg class="iso" viewBox="{{ $vb }}" role="img" aria-label="Posisi unit di gedung: lantai {{ $unit->lantai }}, nomor {{ $unit->no_kamar }}">
    <polygon class="iso__base" points="{{ $base }}" />
    <polygon class="iso__side" points="{{ $side }}" />
    <polygon class="iso__front" points="{{ $front }}" />
    <polygon class="iso__top" points="{{ $top }}" />

    {{-- Tandon air di atap --}}
    @php $tx = $W * 0.7; $ty = $d * 0.45; @endphp
    <polygon class="iso__tank" points="{{ $poly([[$tx - 5, $ty - 5, $H], [$tx + 5, $ty - 5, $H], [$tx + 5, $ty - 5, $H + 9], [$tx - 5, $ty - 5, $H + 9]]) }}" />
    <polygon class="iso__tank-top" points="{{ $poly([[$tx - 5, $ty - 5, $H + 9], [$tx + 5, $ty - 5, $H + 9], [$tx + 5, $ty + 5, $H + 9], [$tx - 5, $ty + 5, $H + 9]]) }}" />

    {{-- Jendela samping --}}
    @for ($f = 1; $f <= $n; $f++)
        @php $z0 = ($f - 1) * $h + 4; $z1 = $f * $h - 4; @endphp
        <polygon class="iso__win iso__win--side" points="{{ $poly([[$W, 6, $z0], [$W, $d - 6, $z0], [$W, $d - 6, $z1], [$W, 6, $z1]]) }}" />
    @endfor

    {{-- Jendela depan, satu per unit --}}
    @for ($f = 1; $f <= $n; $f++)
        @for ($k = 0; $k < $cols; $k++)
            @php
                $on = ($f === $hl && $k === $col);
                $x0 = $k * $w + 4; $x1 = ($k + 1) * $w - 4;
                $z0 = ($f - 1) * $h + 3; $z1 = $f * $h - 3;
            @endphp
            <polygon class="iso__win{{ $on ? ' iso__win--on' : ((($f * 7 + $k * 3) % 4 === 0) ? ' iso__win--lamp' : '') }}"
                     points="{{ $poly([[$x0, $d, $z0], [$x1, $d, $z0], [$x1, $d, $z1], [$x0, $d, $z1]]) }}" />
        @endfor
        @php $zb = ($f - 1) * $h; @endphp
        <polyline class="iso__ledge" points="{{ $poly([[0, $d, $zb], [$W, $d, $zb], [$W, 0, $zb]]) }}" />
    @endfor

    <text class="iso__tag" x="{{ $tagX }}" y="{{ $tagY }}" text-anchor="end">Lt {{ $unit->lantai }}</text>
</svg>
