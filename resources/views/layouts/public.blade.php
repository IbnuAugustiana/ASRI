<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASRI | Rusun Jakarta')</title>
    <meta name="description" content="@yield('meta_description', 'ASRI: cari unit rusun di Jakarta, ajukan sewa, unggah dokumen, dan bayar tagihan secara online.')">
    <meta name="theme-color" content="#141A46">
    <meta property="og:title" content="@yield('title', 'ASRI | Rusun Jakarta')">
    <meta property="og:description" content="@yield('meta_description', 'Sistem manajemen rusun digital: pengajuan sewa, survei, tagihan, dan laporan kerusakan.')">
    <meta property="og:type" content="website">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <script>
        (function () {
            var d = document.documentElement;
            d.classList.add('js');
            try { if (!sessionStorage.getItem('asri-intro')) d.classList.add('has-loader'); } catch (e) {}
            // Pengaman: kalau JS animasi gagal dimuat, konten tetap tampil
            setTimeout(function () { d.classList.add('reveal-all'); }, 4500);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="site @yield('body_class')">
<a class="skip-link" href="#main">Lewati ke konten utama</a>

@stack('before_header')

<header class="nav" data-nav>
    <div class="nav__inner">
        <a href="{{ route('home') }}" class="brand" aria-label="ASRI, kembali ke beranda">
            <span class="brand__mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
            <span class="brand__name">ASRI</span>
        </a>

        <nav class="nav__links" aria-label="Menu utama">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('public.units') }}" @class(['is-active' => request()->routeIs('public.unit*')])>Unit</a>
            <a href="{{ route('public.lokasi') }}" @class(['is-active' => request()->routeIs('public.lokasi')])>Lokasi</a>
            <a href="{{ route('public.tur') }}" @class(['is-active' => request()->routeIs('public.tur')])>Tur 3D</a>
            <a href="{{ route('public.simulasi') }}" @class(['is-active' => request()->routeIs('public.simulasi')])>Simulasi</a>
            <a href="{{ route('public.news') }}" @class(['is-active' => request()->routeIs('public.news*')])>Berita</a>
            <div class="nav__more" data-more>
                <button type="button" @class(['nav__more-btn', 'is-active' => request()->routeIs('public.fasilitas', 'public.bandingkan', 'public.bantuan')]) aria-expanded="false" aria-haspopup="true">
                    Lainnya <i class="ri-arrow-down-s-line" aria-hidden="true"></i>
                </button>
                <div class="nav__menu" role="menu">
                    <a href="{{ route('public.fasilitas') }}" role="menuitem"><i class="ri-community-line" aria-hidden="true"></i><span><b>Fasilitas</b><small>Yang tersedia di tiap blok</small></span></a>
                    <a href="{{ route('public.bandingkan') }}" role="menuitem"><i class="ri-scales-3-line" aria-hidden="true"></i><span><b>Bandingkan unit</b><small>Sampai 3 unit sekaligus</small></span></a>
                    <a href="{{ route('public.bantuan') }}" role="menuitem"><i class="ri-question-answer-line" aria-hidden="true"></i><span><b>Bantuan</b><small>Pertanyaan yang sering diajukan</small></span></a>
                </div>
            </div>
        </nav>

        <div class="nav__actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn--leaf btn--sm" data-magnetic>
                    <i class="ri-dashboard-3-line" aria-hidden="true"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="nav__login">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn--leaf btn--sm" data-magnetic>Daftar</a>
            @endauth
            <button class="nav__toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu">
                <span></span><span></span>
            </button>
        </div>
    </div>

    <div class="nav__mobile" id="mobile-menu" hidden>
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('public.units') }}">Unit</a>
        <a href="{{ route('public.lokasi') }}">Peta lokasi</a>
        <a href="{{ route('public.tur') }}">Tur 3D unit</a>
        <a href="{{ route('public.simulasi') }}">Simulasi biaya</a>
        <a href="{{ route('public.bandingkan') }}">Bandingkan unit</a>
        <a href="{{ route('public.fasilitas') }}">Fasilitas</a>
        <a href="{{ route('public.news') }}">Berita</a>
        <a href="{{ route('public.bantuan') }}">Bantuan</a>
        @guest
            <a href="{{ route('login') }}">Masuk</a>
        @endguest
    </div>
</header>

<main id="main">
    @yield('content')
</main>

@php
    // Siluet kota untuk footer (deterministik agar tidak berubah tiap reload)
    mt_srand(17);
    $skyline = [];
    $x = 0;
    while ($x < 1440) {
        $bw = mt_rand(46, 120);
        $bh = mt_rand(50, 170);
        $wins = [];
        for ($wy = 14; $wy < $bh - 12; $wy += 16) {
            for ($wx = 10; $wx < $bw - 12; $wx += 14) {
                $r = mt_rand(0, 100);
                if ($r < 34) {
                    $wins[] = ['x' => $x + $wx, 'y' => 200 - $bh + $wy, 'c' => $r < 9 ? 'leaf' : 'lamp', 'd' => mt_rand(0, 60) / 10, 't' => mt_rand(30, 90) / 10];
                }
            }
        }
        $skyline[] = ['x' => $x, 'w' => $bw, 'h' => $bh, 'wins' => $wins];
        $x += $bw + mt_rand(2, 10);
    }
    mt_srand();
@endphp

<footer class="footer">
    <svg class="footer__skyline" viewBox="0 0 1440 200" preserveAspectRatio="xMidYMax slice" aria-hidden="true" focusable="false">
        @foreach ($skyline as $b)
            <rect class="sky-b" x="{{ $b['x'] }}" y="{{ 200 - $b['h'] }}" width="{{ $b['w'] }}" height="{{ $b['h'] }}" rx="3" />
            @foreach ($b['wins'] as $w)
                <rect class="sky-w sky-w--{{ $w['c'] }}" x="{{ $w['x'] }}" y="{{ $w['y'] }}" width="7" height="8" rx="1.5"
                      style="animation-delay: {{ $w['d'] }}s; animation-duration: {{ $w['t'] }}s" />
            @endforeach
        @endforeach
    </svg>

    <div class="wrap footer__grid">
        <div class="footer__brand">
            <a href="{{ route('home') }}" class="brand brand--footer" aria-label="ASRI, kembali ke beranda">
                <span class="brand__mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                <span class="brand__name">ASRI</span>
            </a>
            <p>Sistem pengelolaan rusun di Jakarta: pengajuan sewa, jadwal survei, tagihan, dan laporan kerusakan dalam satu tempat.</p>
        </div>
        <div>
            <h2 class="footer__title">Jelajahi</h2>
            <a href="{{ route('public.units') }}">Cari unit</a>
            <a href="{{ route('public.lokasi') }}">Peta lokasi</a>
            <a href="{{ route('public.tur') }}">Tur 3D unit</a>
            <a href="{{ route('public.simulasi') }}">Simulasi biaya</a>
            <a href="{{ route('public.fasilitas') }}">Fasilitas</a>
            <a href="{{ route('public.bantuan') }}">Bantuan</a>
        </div>
        <div>
            <h2 class="footer__title">Akun</h2>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('profile.edit') }}">Profil</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Buat akun</a>
            @endauth
        </div>
        <div>
            <h2 class="footer__title">Tentang proyek</h2>
            <p class="footer__note">Proyek portofolio dibangun dengan Laravel, Three.js, dan GSAP.</p>
            @if (config('portfolio.author'))
                <p class="footer__note">Dibuat oleh <strong>{{ config('portfolio.author') }}</strong></p>
            @endif
            <div class="footer__social">
                @if (config('portfolio.linkedin'))
                    <a href="{{ config('portfolio.linkedin') }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="ri-linkedin-box-fill"></i></a>
                @endif
                @if (config('portfolio.github'))
                    <a href="{{ config('portfolio.github') }}" target="_blank" rel="noopener" aria-label="GitHub"><i class="ri-github-fill"></i></a>
                @endif
            </div>
        </div>
    </div>
    <div class="wrap footer__bottom">
        <span>© {{ date('Y') }} ASRI</span>
        <span>Data unit dan penghuni adalah data contoh untuk demo.</span>
    </div>
</footer>

{{-- Notifikasi singkat dari redirect (mis. unit tidak tersedia) --}}
@foreach (['success' => 'ri-checkbox-circle-line', 'error' => 'ri-error-warning-line', 'info' => 'ri-information-line'] as $type => $icon)
    @if (session($type) && is_string(session($type)))
        <div class="toast toast--{{ $type }}" role="status" data-toast>
            <i class="{{ $icon }}" aria-hidden="true"></i>
            <span>{{ session($type) }}</span>
            <button type="button" class="toast__close" aria-label="Tutup" data-toast-close><i class="ri-close-line"></i></button>
        </div>
    @endif
@endforeach

{{-- Baki "bandingkan unit" (diisi oleh JS dari localStorage) --}}
<div class="compare-tray" data-compare-tray hidden>
    <div class="compare-tray__items" data-compare-items></div>
    <a href="{{ route('public.bandingkan') }}" class="btn btn--leaf btn--sm" data-compare-go><i class="ri-scales-3-line" aria-hidden="true"></i> Bandingkan</a>
    <button type="button" class="compare-tray__clear" data-compare-clear aria-label="Kosongkan perbandingan"><i class="ri-close-line"></i></button>
</div>

@stack('scripts')
</body>
</html>
