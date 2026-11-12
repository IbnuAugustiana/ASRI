@extends('layouts.public')
@section('title', 'Peta lokasi rusun | ASRI')

@section('content')
<section class="page-hero page-hero--tight">
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Peta lokasi rusun</h1>
        <p class="page-hero__lead" data-hero-in>Lima kompleks di lima wilayah Jakarta. Tinggi balok menunjukkan jumlah unit, angka hijau menunjukkan unit yang masih kosong.</p>
    </div>
</section>

<section class="sec sec--mist sec--tight map-sec">
    <div class="wrap map-layout">
        <div class="map" data-map>
            <script type="application/json">@json($wilayah)</script>
            <div class="map__scene">
                <div class="map__sea"><span>Teluk Jakarta</span></div>
                @foreach ($wilayah as $i => $w)
                    @php $h = 34 + round($w['total'] / $maxTotal * 96); @endphp
                    <button type="button" class="map__tile {{ $i === 0 ? 'is-active' : '' }}" data-tile="{{ $i }}"
                            style="--c: {{ $w['col'] }}; --r: {{ $w['row'] }}; --h: {{ $h }}px"
                            aria-label="{{ $w['nama'] }}: {{ $w['tersedia'] }} unit kosong dari {{ $w['total'] }}">
                        <span class="map__top"><b>{{ str_replace('Jakarta ', '', $w['nama']) }}</b></span>
                        <span class="map__front"></span>
                        <span class="map__side"></span>
                        <span class="map__pin"><i>{{ $w['tersedia'] }}</i></span>
                    </button>
                @endforeach
            </div>
            <p class="map__hint"><i class="ri-cursor-line" aria-hidden="true"></i> Klik balok wilayah untuk melihat gedungnya</p>
        </div>

        <aside class="map-panel" data-map-panel aria-live="polite">
            <p class="map-panel__kicker">Wilayah terpilih</p>
            <h2 class="map-panel__title" data-p="nama">{{ $wilayah[0]['nama'] }}</h2>
            <p class="map-panel__note" data-p="catatan">{{ $wilayah[0]['catatan'] }}</p>
            <div class="map-panel__stats">
                <div><b data-p="tersedia">{{ $wilayah[0]['tersedia'] }}</b><span>unit kosong</span></div>
                <div><b data-p="total">{{ $wilayah[0]['total'] }}</b><span>total unit</span></div>
                <div><b data-p="harga">Rp {{ number_format($wilayah[0]['harga_min'], 0, ',', '.') }}</b><span>harga mulai</span></div>
            </div>
            <h3 class="map-panel__sub">Gedung di wilayah ini</h3>
            <ul class="map-panel__list" data-p="gedung">
                @foreach ($wilayah[0]['gedung'] as $g)
                    <li><span class="map-panel__blok">{{ $g['blok'] }}</span><div><b>{{ $g['nama'] }}</b><small>{{ $g['alamat'] }}</small></div><em>{{ $g['tersedia'] }}/{{ $g['total'] }}</em></li>
                @endforeach
            </ul>
            <a href="{{ route('public.units', ['wilayah' => $wilayah[0]['nama'], 'tersedia' => 1]) }}" class="btn btn--leaf btn--block" data-p="link" data-base="{{ route('public.units') }}">
                Lihat unit kosong di wilayah ini
            </a>
        </aside>
    </div>
</section>
@endsection
