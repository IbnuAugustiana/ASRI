@extends('layouts.public')
@section('title', $unit->nama_unit . ' | ASRI')

@php
    $statusLabel = ['tersedia' => 'Tersedia', 'dihuni' => 'Dihuni', 'maintenance' => 'Perawatan'][$unit->status] ?? ucfirst($unit->status);
@endphp

@section('content')
<section class="detail-hero">
    <div class="wrap">
        <a href="{{ route('public.units') }}" class="back-link"><i class="ri-arrow-left-line" aria-hidden="true"></i> Semua unit</a>
        <div class="detail-hero__head">
            <div>
                <p class="detail-hero__code" data-hero-in>{{ $unit->blok }}<span>/</span>{{ $unit->lantai }}.{{ $unit->no_kamar }}</p>
                <h1 class="detail-hero__title" data-split>{{ $unit->gedung }}</h1>
                <p class="detail-hero__addr" data-hero-in><i class="ri-map-pin-2-line" aria-hidden="true"></i> {{ $unit->alamat }}, {{ $unit->wilayah }}</p>
            </div>
            <span class="chip chip--glass chip--{{ $unit->status }} chip--lg" data-hero-in>{{ $statusLabel }}</span>
        </div>
    </div>
</section>

<section class="detail">
    <div class="wrap detail__grid">
        <div class="detail__main">
            <div class="gallery" data-gallery>
                @foreach ($unit->galeri as $i => $foto)
                    <button type="button" class="gallery__item {{ $i === 0 ? 'gallery__item--main' : '' }}" data-gallery-index="{{ $i }}" aria-label="Perbesar foto: {{ $foto['label'] }}">
                        <img src="{{ $foto['src'] }}" alt="{{ $foto['label'] }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                        <span class="gallery__label">{{ $foto['label'] }}</span>
                    </button>
                @endforeach
                <dialog class="lightbox" data-lightbox aria-label="Foto unit">
                    <img src="" alt="" data-lightbox-img>
                    <p class="lightbox__cap" data-lightbox-cap></p>
                    <button type="button" class="lightbox__btn lightbox__btn--prev" data-lightbox-prev aria-label="Foto sebelumnya"><i class="ri-arrow-left-s-line"></i></button>
                    <button type="button" class="lightbox__btn lightbox__btn--next" data-lightbox-next aria-label="Foto berikutnya"><i class="ri-arrow-right-s-line"></i></button>
                    <button type="button" class="lightbox__close" data-lightbox-close aria-label="Tutup"><i class="ri-close-line"></i></button>
                </dialog>
            </div>

            <div class="tower-view" data-tower>
                <script type="application/json">@json($towerData)</script>
                <div class="tower-tip" hidden></div>
                <p class="tower-view__hint"><i class="ri-drag-move-2-line" aria-hidden="true"></i> Seret untuk memutar. Unit ini ditandai di Blok {{ $unit->blok }}.</p>
            </div>

            <div class="specs" data-reveal-group>
                @foreach ([
                    ['ri-building-2-line', 'Blok', $unit->blok],
                    ['ri-stack-line', 'Lantai', $unit->lantai],
                    ['ri-door-line', 'Nomor', $unit->no_kamar],
                    ['ri-ruler-2-line', 'Luas', $unit->luas_m2 ? $unit->luas_m2 . ' m²' : 'Belum diisi'],
                ] as [$ic, $k, $v])
                    <div class="spec">
                        <i class="{{ $ic }}" aria-hidden="true"></i>
                        <span>{{ $k }}</span>
                        <b>{{ $v }}</b>
                    </div>
                @endforeach
            </div>

            <div class="detail__desc" data-reveal>
                <h2>Tentang unit ini</h2>
                <p>{{ $unit->deskripsi ?: 'Unit hunian di ' . $unit->gedung . ', ' . $unit->wilayah . '. Hubungi pengelola setelah mengajukan sewa untuk informasi fasilitas lengkap.' }}</p>
            </div>
        </div>

        <aside class="book" aria-label="Ajukan sewa">
            <div class="book__card" data-tilt="4">
                <div class="book__price">
                    <b>Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }}</b>
                    <span>per bulan</span>
                </div>
                <ul class="book__facts">
                    <li><i class="ri-calendar-check-line" aria-hidden="true"></i> Sewa 1 sampai 72 bulan</li>
                    <li><i class="ri-file-list-3-line" aria-hidden="true"></i> Syarat: KTP, KK, slip gaji</li>
                    <li><i class="ri-bank-card-line" aria-hidden="true"></i> Bayar tagihan lewat Midtrans</li>
                </ul>
                <div class="book__tools">
                    <a href="{{ route('public.tur', ['tipe' => $unit->luas_m2 >= 36 ? 36 : 24]) }}" class="book__tool"><i class="ri-box-3-line" aria-hidden="true"></i> Tur 3D tipe {{ $unit->luas_m2 >= 36 ? 36 : 24 }}</a>
                    <button type="button" class="book__tool" data-compare="{{ $unit->id }}" data-compare-label="{{ $unit->blok }}/{{ $unit->lantai }}.{{ $unit->no_kamar }}" aria-pressed="false"><i class="ri-scales-3-line" aria-hidden="true"></i> <span>Bandingkan</span></button>
                    <a href="{{ route('public.simulasi') }}" class="book__tool"><i class="ri-calculator-line" aria-hidden="true"></i> Simulasi biaya</a>
                </div>

                @if ($unit->status === 'tersedia')
                    @auth
                        @if (auth()->user()->isCalonPenghuni())
                            <a href="{{ route('calon.pengajuan.create', $unit) }}" class="btn btn--leaf btn--lg btn--block" data-magnetic>
                                <i class="ri-file-add-line" aria-hidden="true"></i> Ajukan sewa unit ini
                            </a>
                            <a href="{{ route('calon.survei.buat', $unit->id) }}" class="btn btn--outline btn--block">Jadwalkan survei dulu</a>
                        @else
                            <p class="book__note">Pengajuan sewa hanya bisa dilakukan dari akun calon penghuni.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn--leaf btn--lg btn--block" data-magnetic>Masuk untuk mengajukan</a>
                        <a href="{{ route('register') }}" class="btn btn--outline btn--block">Belum punya akun? Daftar</a>
                    @endauth
                @else
                    <p class="book__note book__note--muted">Unit ini sedang {{ strtolower($statusLabel) }}. Lihat unit serupa di bawah.</p>
                @endif
            </div>
        </aside>
    </div>
</section>

@if ($unitSerupa->count())
<section class="sec sec--mist sec--tight">
    <div class="wrap">
        <header class="sec-head">
            <h2 class="sec-title sec-title--sm" data-split>Unit kosong lain di {{ $unit->wilayah }}</h2>
        </header>
        <div class="plates" data-reveal-group>
            @foreach ($unitSerupa as $s)
                @include('public.partials.unit-plate', ['unit' => $s])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
