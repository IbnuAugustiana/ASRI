@extends('layouts.public')
@section('title', 'Simulasi biaya sewa | ASRI')

@section('content')
<section class="page-hero page-hero--tight">
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Simulasi biaya sewa</h1>
        <p class="page-hero__lead" data-hero-in>Hitung perkiraan biaya bulanan dan biaya awal sebelum mengajukan sewa. Angka listrik dan air adalah perkiraan rata-rata.</p>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap calc" data-calc>
        <script type="application/json">@json($harga)</script>

        <form class="calc__form" onsubmit="return false">
            <div class="calc__group">
                <label class="calc__label" for="c-wilayah">Wilayah</label>
                <div class="field field--light">
                    <i class="ri-map-pin-2-line" aria-hidden="true"></i>
                    <select id="c-wilayah" data-calc-wilayah>
                        @foreach (array_keys($harga) as $w)
                            <option value="{{ $w }}">{{ $w }}</option>
                        @endforeach
                    </select>
                    <i class="ri-arrow-down-s-line field__caret" aria-hidden="true"></i>
                </div>
            </div>

            <div class="calc__group">
                <span class="calc__label">Tipe unit</span>
                <div class="calc__types" role="radiogroup">
                    <label class="type-card"><input type="radio" name="tipe" value="24" checked data-calc-tipe><span class="type-card__box"><i class="ri-home-4-line" aria-hidden="true"></i><b>Tipe 24</b><small>1 kamar tidur · 24 m²</small><em data-calc-price="24"></em></span></label>
                    <label class="type-card"><input type="radio" name="tipe" value="36" data-calc-tipe><span class="type-card__box"><i class="ri-home-8-line" aria-hidden="true"></i><b>Tipe 36</b><small>2 kamar tidur · 36 m²</small><em data-calc-price="36"></em></span></label>
                </div>
            </div>

            <div class="calc__group">
                <label class="calc__label" for="c-durasi">Durasi sewa <output data-calc-durasi-out>12 bulan</output></label>
                <input id="c-durasi" type="range" min="1" max="72" value="12" class="range" data-calc-durasi>
                <div class="range__marks"><span>1</span><span>24</span><span>48</span><span>72 bulan</span></div>
            </div>

            <div class="calc__row">
                <div class="calc__group">
                    <label class="calc__label" for="c-penghuni">Jumlah penghuni</label>
                    <div class="stepper"><button type="button" data-step="-1" aria-label="Kurangi">−</button><input id="c-penghuni" type="number" min="1" max="6" value="2" data-calc-penghuni><button type="button" data-step="1" aria-label="Tambah">+</button></div>
                </div>
                <div class="calc__group">
                    <label class="calc__label" for="c-listrik">Daya listrik</label>
                    <div class="field field--light">
                        <i class="ri-flashlight-line" aria-hidden="true"></i>
                        <select id="c-listrik" data-calc-listrik>
                            <option value="900">900 VA</option>
                            <option value="1300" selected>1.300 VA</option>
                            <option value="2200">2.200 VA</option>
                        </select>
                        <i class="ri-arrow-down-s-line field__caret" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </form>

        <div class="calc__result" data-tilt="3">
            <div class="calc__donut">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                    <circle cx="60" cy="60" r="48" class="donut__bg"/>
                    <circle cx="60" cy="60" r="48" class="donut__seg" stroke-dasharray="0 302" data-seg="sewa"/>
                    <circle cx="60" cy="60" r="48" class="donut__seg" stroke-dasharray="0 302" data-seg="listrik"/>
                    <circle cx="60" cy="60" r="48" class="donut__seg" stroke-dasharray="0 302" data-seg="air"/>
                    <circle cx="60" cy="60" r="48" class="donut__seg" stroke-dasharray="0 302" data-seg="ipl"/>
                </svg>
                <div class="calc__donut-center"><small>per bulan</small><b data-out="bulanan">Rp 0</b></div>
            </div>
            <ul class="calc__lines">
                <li><span class="dot dot--sewa"></span>Sewa unit<b data-out="sewa">Rp 0</b></li>
                <li><span class="dot dot--listrik"></span>Perkiraan listrik<b data-out="listrik">Rp 0</b></li>
                <li><span class="dot dot--air"></span>Perkiraan air<b data-out="air">Rp 0</b></li>
                <li><span class="dot dot--ipl"></span>Iuran kebersihan &amp; keamanan<b data-out="ipl">Rp 0</b></li>
            </ul>
            <div class="calc__box">
                <h2>Dibayar di awal</h2>
                <ul>
                    <li>Sewa bulan pertama<b data-out="pertama">Rp 0</b></li>
                    <li>Deposit (1 bulan sewa, dikembalikan)<b data-out="deposit">Rp 0</b></li>
                    <li>Biaya administrasi<b class="is-free">Gratis</b></li>
                </ul>
                <p class="calc__total">Total awal <b data-out="awal">Rp 0</b></p>
            </div>
            <p class="calc__contract">Total sewa selama <span data-out="durasi">12 bulan</span>: <b data-out="kontrak">Rp 0</b></p>
            <a href="{{ route('public.units') }}" class="btn btn--leaf btn--lg btn--block" data-calc-link data-base="{{ route('public.units') }}">Lihat unit yang cocok</a>
        </div>
    </div>
</section>
@endsection
