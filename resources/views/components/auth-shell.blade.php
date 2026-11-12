@props([
    'title' => 'Masuk',
    'heading' => 'Rusun Jakarta, dari pilih unit sampai bayar sewa.',
    'lead' => 'Satu akun untuk mengajukan sewa, memantau survei, membayar tagihan, dan melapor kerusakan.',
])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#141A46">
    <title>{{ $title }} | ASRI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        (function () {
            var d = document.documentElement;
            d.classList.add('js');
            setTimeout(function () { d.classList.add('reveal-all'); }, 4500);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site site--auth">
<a class="skip-link" href="#auth-form">Lewati ke formulir</a>

<div class="auth">
    <aside class="auth__art" aria-label="Tentang ASRI">
        <div class="auth__stage" data-tower>
            <script type="application/json">@json($towerData ?? ['mode' => 'city', 'units' => []])</script>
            <div class="tower-tip" hidden></div>
        </div>
        <div class="auth__art-inner">
            <a href="{{ route('home') }}" class="brand" aria-label="ASRI, kembali ke beranda">
                <span class="brand__mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                <span class="brand__name">ASRI</span>
            </a>
            <div class="auth__pitch">
                <h2 class="auth__heading" data-split>{{ $heading }}</h2>
                <p class="auth__lead" data-hero-in>{{ $lead }}</p>
                <ul class="auth__points" data-hero-in>
                    <li><i class="ri-building-4-line" aria-hidden="true"></i> Jendela hijau di gedung = unit yang masih kosong</li>
                    <li><i class="ri-bank-card-line" aria-hidden="true"></i> Tagihan dibayar online lewat Midtrans</li>
                    <li><i class="ri-tools-line" aria-hidden="true"></i> Laporan kerusakan dipantau sampai selesai</li>
                </ul>
            </div>
        </div>
    </aside>

    <main class="auth__main" id="auth-form">
        <a href="{{ route('home') }}" class="brand brand--dark auth__mobile-brand" aria-label="ASRI, kembali ke beranda">
            <span class="brand__mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
            <span class="brand__name">ASRI</span>
        </a>
        <div class="auth__card" data-hero-in>
            {{ $slot }}
        </div>
        <a href="{{ route('home') }}" class="auth__back"><i class="ri-arrow-left-line" aria-hidden="true"></i> Kembali ke beranda</a>
    </main>
</div>
</body>
</html>
