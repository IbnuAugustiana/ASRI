@extends('layouts.public')
@section('title', 'Fasilitas rusun | ASRI')

@php
    $fasilitas = [
        ['ri-shield-check-line', 'Keamanan 24 jam', 'Satpam bergiliran, CCTV di lobi dan tangga, serta akses kartu ke lantai hunian.', 'Setiap hari, 24 jam'],
        ['ri-store-2-line', 'Kios & koperasi', 'Kebutuhan pokok, galon, dan gas tersedia di lantai dasar dengan harga koperasi.', '06.00 – 22.00'],
        ['ri-parking-box-line', 'Parkir motor', 'Area parkir beratap dengan stiker penghuni. Satu motor per unit tanpa biaya tambahan.', 'Setiap hari'],
        ['ri-plant-line', 'Taman & RPTRA', 'Ruang terbuka hijau dan taman bermain anak di setiap kompleks.', '05.00 – 21.00'],
        ['ri-building-4-line', 'Mushola', 'Mushola di lantai dasar setiap blok, lengkap dengan tempat wudu.', 'Setiap waktu salat'],
        ['ri-bus-2-line', 'Shuttle TransJakarta', 'Halte dan rute feeder dekat kompleks, terhubung ke koridor utama.', '05.00 – 22.00'],
        ['ri-tools-line', 'Teknisi bangunan', 'Laporkan kerusakan dari dashboard, teknisi datang sesuai jadwal.', 'Senin – Sabtu'],
        ['ri-wifi-line', 'Wi-Fi area bersama', 'Internet gratis di lobi, taman, dan ruang serbaguna.', 'Setiap hari'],
    ];
@endphp

@section('content')
<section class="page-hero page-hero--tight">
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Fasilitas di setiap blok</h1>
        <p class="page-hero__lead" data-hero-in>Semua fasilitas berikut sudah termasuk dalam iuran bulanan. Arahkan kursor atau ketuk kartu untuk melihat detailnya.</p>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap">
        <div class="flips" data-reveal-group>
            @foreach ($fasilitas as $i => [$icon, $nama, $desk, $jam])
                <div class="plate-cell">
                    <button type="button" class="flip" data-flip aria-label="{{ $nama }}: {{ $desk }}">
                        <span class="flip__inner">
                            <span class="flip__face flip__face--front">
                                <span class="flip__icon flip__icon--{{ $i % 4 }}"><i class="{{ $icon }}" aria-hidden="true"></i></span>
                                <b>{{ $nama }}</b>
                                <small><i class="ri-time-line" aria-hidden="true"></i> {{ $jam }}</small>
                            </span>
                            <span class="flip__face flip__face--back">
                                <b>{{ $nama }}</b>
                                <span>{{ $desk }}</span>
                                <small><i class="ri-time-line" aria-hidden="true"></i> {{ $jam }}</small>
                            </span>
                        </span>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="rules">
            <div class="rules__copy">
                <h2 class="sec-title sec-title--sm" data-split>Aturan hunian singkat</h2>
                <p class="sec-lead">Supaya lingkungan tetap nyaman untuk semua penghuni.</p>
            </div>
            <ol class="rules__list" data-reveal-group>
                <li><b>Jam tenang 22.00 – 05.00</b><span>Hindari suara keras dan pekerjaan renovasi di jam ini.</span></li>
                <li><b>Tamu menginap lapor pengelola</b><span>Tamu yang menginap lebih dari satu malam dicatat di pos keamanan.</span></li>
                <li><b>Sampah dipilah</b><span>Buang sampah organik dan anorganik di tempat terpisah di tiap lantai.</span></li>
                <li><b>Tagihan dibayar sebelum tanggal 10</b><span>Lewat dari tanggal itu, tagihan ditandai terlambat di dashboard.</span></li>
            </ol>
        </div>
    </div>
</section>
@endsection
