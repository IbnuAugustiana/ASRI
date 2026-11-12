{{--
    Tag Open Graph & Twitter untuk pratinjau tautan (LinkedIn, WhatsApp, X).
    Dirender di server karena crawler LinkedIn tidak menjalankan JavaScript.
    $title dan $description dikirim sudah ter-escape (hasil @yield / e()).
--}}
@php
    $ogTitle = $title ?? 'ASRI | Rusun Jakarta';
    $ogDescription = $description ?? e('ASRI: cari unit rusun di Jakarta, ajukan sewa, unggah dokumen, dan bayar tagihan secara online.');
    $ogImage = rtrim(config('portfolio.site_url'), '/') . '/og-image.png';
    $ogImageAlt = 'Beranda ASRI: gedung rusun 3D dan judul "Pilih jendelamu di rusun Jakarta."';
@endphp
<meta property="og:type" content="website">
<meta property="og:site_name" content="ASRI">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{!! $ogTitle !!}">
<meta property="og:description" content="{!! $ogDescription !!}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:secure_url" content="{{ $ogImage }}">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="627">
<meta property="og:image:alt" content="{{ $ogImageAlt }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{!! $ogTitle !!}">
<meta name="twitter:description" content="{!! $ogDescription !!}">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $ogImageAlt }}">
