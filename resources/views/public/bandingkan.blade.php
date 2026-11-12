@extends('layouts.public')
@section('title', 'Bandingkan unit | ASRI')

@section('content')
<section class="page-hero page-hero--tight">
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Bandingkan unit</h1>
        <p class="page-hero__lead" data-hero-in>Pilih sampai tiga unit lewat tombol “Bandingkan” di kartu unit, lalu lihat perbedaannya berdampingan.</p>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap">
        @if ($units->isEmpty())
            <div class="empty empty--wide">
                <i class="ri-scales-3-line" aria-hidden="true"></i>
                <h3>Belum ada unit yang dipilih</h3>
                <p>Tekan tombol “Bandingkan” di kartu unit. Unit pilihan akan muncul di baki bawah layar.</p>
                <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="btn btn--ink">Pilih dari daftar unit</a>
            </div>
            <header class="sec-head sec-head--sm">
                <h2 class="sec-title sec-title--sm">Atau mulai dari unit termurah</h2>
            </header>
            <div class="plates" data-reveal-group>
                @foreach ($saran as $unit)
                    @include('public.partials.unit-plate', ['unit' => $unit])
                @endforeach
            </div>
        @else
            @php
                $minHarga = $units->min('harga_sewa');
                $maxLuas = $units->max('luas_m2');
                $statusLabel = ['tersedia' => 'Tersedia', 'dihuni' => 'Dihuni', 'maintenance' => 'Perawatan'];
            @endphp
            <div class="compare" style="--n: {{ $units->count() }}" data-reveal-group>
                @foreach ($units as $u)
                    <article class="compare__col" data-tilt="4">
                        <button type="button" class="compare__remove" data-compare-remove="{{ $u->id }}" aria-label="Hapus {{ $u->nama_unit }} dari perbandingan"><i class="ri-close-line"></i></button>
                        <div class="compare__photo"><img src="{{ $u->foto_url }}" alt="Foto {{ $u->nama_unit }}"></div>
                        <p class="compare__code">{{ $u->blok }}<span>/</span>{{ $u->lantai }}.{{ $u->no_kamar }}</p>
                        <h2 class="compare__name">{{ $u->gedung }}</h2>
                        <div class="compare__badges">
                            @if ($u->harga_sewa == $minHarga && $units->count() > 1)<span class="chip chip--best"><i class="ri-price-tag-3-line"></i> Termurah</span>@endif
                            @if ($u->luas_m2 && $u->luas_m2 == $maxLuas && $units->count() > 1)<span class="chip chip--best chip--alt"><i class="ri-ruler-2-line"></i> Terluas</span>@endif
                        </div>
                        <dl class="compare__list">
                            <div><dt>Harga / bulan</dt><dd>Rp {{ number_format($u->harga_sewa, 0, ',', '.') }}</dd></div>
                            <div><dt>Luas</dt><dd>{{ $u->luas_m2 ? $u->luas_m2 . ' m²' : '-' }}</dd></div>
                            <div><dt>Harga per m²</dt><dd>{{ $u->luas_m2 ? 'Rp ' . number_format($u->harga_sewa / $u->luas_m2, 0, ',', '.') : '-' }}</dd></div>
                            <div><dt>Wilayah</dt><dd>{{ $u->wilayah }}</dd></div>
                            <div><dt>Lantai</dt><dd>{{ $u->lantai }}</dd></div>
                            <div><dt>Kamar tidur</dt><dd>{{ $u->luas_m2 >= 36 ? '2' : '1' }}</dd></div>
                            <div><dt>Status</dt><dd>{{ $statusLabel[$u->status] ?? $u->status }}</dd></div>
                            <div><dt>Sewa 12 bulan</dt><dd>Rp {{ number_format($u->harga_sewa * 12, 0, ',', '.') }}</dd></div>
                        </dl>
                        <a href="{{ route('public.unit.detail', $u) }}" class="btn btn--ink btn--block">Lihat detail</a>
                    </article>
                @endforeach
                @if ($units->count() < 3)
                    <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="compare__add">
                        <i class="ri-add-line" aria-hidden="true"></i>
                        <b>Tambah unit</b>
                        <span>Pilih dari daftar unit</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection
