@extends('layouts.public')
@section('title', 'Tur 3D unit | ASRI')
@section('body_class', 'page-light-top')

@section('content')
<section class="tour" data-tour data-price24="{{ $hargaMin[24] }}" data-price36="{{ $hargaMin[36] }}">
    <div class="tour__stage" data-tour-stage>
        <div class="tour__labels" data-tour-labels></div>
    </div>

    <div class="tour__ui">
        <div class="tour__head">
            <h1 class="tour__title" data-split>Tur 3D unit</h1>
            <p class="tour__lead" data-hero-in>Lihat tata ruang unit sebelum survei. Seret untuk memutar, gulir untuk memperbesar, klik nama ruangan untuk mendekat.</p>
        </div>

        <div class="tour__panel" data-hero-in>
            <div class="seg" role="radiogroup" aria-label="Pilih tipe unit">
                <button type="button" class="seg__btn is-active" role="radio" aria-checked="true" data-tour-type="24">Tipe 24 <small>1 kamar</small></button>
                <button type="button" class="seg__btn" role="radio" aria-checked="false" data-tour-type="36">Tipe 36 <small>2 kamar</small></button>
            </div>

            <div class="tour__rooms" data-tour-rooms aria-label="Ruangan"></div>

            <div class="tour__toggles">
                <label class="switch switch--dark"><input type="checkbox" data-tour-night><span class="switch__track" aria-hidden="true"><span></span></span><span>Suasana malam</span></label>
                <label class="switch switch--dark"><input type="checkbox" data-tour-spin checked><span class="switch__track" aria-hidden="true"><span></span></span><span>Putar otomatis</span></label>
            </div>

            <div class="tour__info">
                <div>
                    <b data-tour-price>Rp {{ number_format($hargaMin[24], 0, ',', '.') }}</b>
                    <span>mulai per bulan · <span data-tour-count>{{ $tipe[24] }}</span> unit kosong</span>
                </div>
                <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="btn btn--leaf btn--sm" data-magnetic>Cari unit</a>
            </div>
        </div>
    </div>
    <script type="application/json" data-tour-counts>@json($tipe)</script>
</section>
@endsection
