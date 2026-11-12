@extends('layouts.public')
@section('title', 'Cari unit rusun | ASRI')

@section('content')

<section class="page-hero">
    <div class="floaters" aria-hidden="true">
        @foreach ([[8, 22, 70, 0], [84, 18, 54, 1.5], [70, 70, 40, 3], [20, 78, 34, 2.2], [92, 62, 26, 4]] as [$fx, $fy, $fs, $fd])
            <div class="floater" style="--x: {{ $fx }}%; --y: {{ $fy }}%; --s: {{ $fs }}px; --d: {{ $fd }}s">
                <i></i><i></i><i></i><i></i><i></i><i></i>
            </div>
        @endforeach
    </div>

    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Cari unit rusun</h1>
        <p class="page-hero__lead" data-hero-in>Saring berdasarkan wilayah dan harga. Hasil diurutkan per blok dan lantai.</p>

        <form method="GET" action="{{ route('public.units') }}" class="filter" data-hero-in role="search">
            <div class="field field--grow">
                <label for="f-search" class="sr-only">Kata kunci</label>
                <i class="ri-search-line" aria-hidden="true"></i>
                <input id="f-search" type="search" name="search" value="{{ request('search') }}" placeholder="Nama gedung, blok, atau alamat">
            </div>
            <div class="field">
                <label for="f-wilayah" class="sr-only">Wilayah</label>
                <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                <select id="f-wilayah" name="wilayah">
                    <option value="">Semua wilayah</option>
                    @foreach ($wilayahs as $w)
                        <option value="{{ $w }}" @selected(request('wilayah') == $w)>{{ $w }}</option>
                    @endforeach
                </select>
                <i class="ri-arrow-down-s-line field__caret" aria-hidden="true"></i>
            </div>
            <div class="field">
                <label for="f-harga" class="sr-only">Harga maksimal per bulan</label>
                <i class="ri-wallet-3-line" aria-hidden="true"></i>
                <input id="f-harga" type="number" name="harga_max" min="0" step="50000" value="{{ request('harga_max') }}" placeholder="Harga maks. / bulan">
            </div>
            <label class="switch">
                <input type="checkbox" name="tersedia" value="1" @checked(request('tersedia'))>
                <span class="switch__track" aria-hidden="true"><span></span></span>
                <span>Hanya yang kosong</span>
            </label>
            <button type="submit" class="btn btn--leaf filter__submit">
                <i class="ri-search-2-line" aria-hidden="true"></i> Cari
            </button>
        </form>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap">
        <div class="results-bar">
            <p class="results-bar__count">
                <b>{{ $units->total() }}</b> unit ditemukan
                @if (request('wilayah')) di {{ request('wilayah') }} @endif
            </p>
            @if (request()->anyFilled(['search', 'wilayah', 'harga_max', 'tersedia']))
                <a href="{{ route('public.units') }}" class="link-pill link-pill--sm">
                    <i class="ri-close-line" aria-hidden="true"></i> Hapus filter
                </a>
            @endif
        </div>

        @if ($units->isEmpty())
            <div class="empty">
                <i class="ri-search-eye-line" aria-hidden="true"></i>
                <h3>Tidak ada unit yang cocok</h3>
                <p>Coba naikkan harga maksimal atau pilih wilayah lain.</p>
                <a href="{{ route('public.units') }}" class="btn btn--ink">Tampilkan semua unit</a>
            </div>
        @else
            <div class="plates" data-reveal-group>
                @foreach ($units as $unit)
                    @include('public.partials.unit-plate', ['unit' => $unit])
                @endforeach
            </div>

            {{ $units->withQueryString()->links('partials.pagination') }}
        @endif
    </div>
</section>
@endsection
