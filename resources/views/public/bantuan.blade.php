@extends('layouts.public')
@section('title', 'Bantuan | ASRI')

@php
    $faq = [
        'Pendaftaran' => [
            ['Siapa yang boleh mendaftar?', 'Warga yang memiliki KTP dan belum memiliki hunian sendiri. Daftar sebagai calon penghuni dengan nama, email, dan nomor WhatsApp.'],
            ['Dokumen apa saja yang perlu disiapkan?', 'Foto KTP, Kartu Keluarga, dan slip gaji atau bukti penghasilan. Format JPG, PNG, atau PDF, maksimal 2 MB per berkas.'],
            ['Berapa lama proses verifikasi?', 'Biasanya 2 sampai 3 hari kerja. Status pengajuan bisa dipantau di dashboard, dan kamu mendapat notifikasi setiap ada perubahan.'],
            ['Apakah wajib survei unit dulu?', 'Tidak wajib, tapi disarankan. Jadwal survei bisa dipilih Senin sampai Sabtu pukul 08.00 sampai 15.00.'],
        ],
        'Pembayaran' => [
            ['Kapan tagihan sewa terbit?', 'Setiap tanggal 1, dengan jatuh tempo tanggal 10 di bulan yang sama.'],
            ['Metode pembayaran apa yang tersedia?', 'Virtual account bank, e-wallet, dan QRIS melalui Midtrans, langsung dari menu Tagihan Saya.'],
            ['Apakah deposit dikembalikan?', 'Ya. Deposit sebesar satu bulan sewa dikembalikan saat kontrak selesai, setelah dipotong kerusakan jika ada.'],
        ],
        'Selama tinggal' => [
            ['Bagaimana melaporkan kerusakan?', 'Buka menu Laporan Kerusakan, pilih kategori, tulis deskripsi, dan lampirkan foto. Teknisi akan menjadwalkan perbaikan.'],
            ['Bisakah memperpanjang kontrak?', 'Bisa. Ajukan perpanjangan dari menu Detail Sewa sebelum kontrak berakhir, lalu tunggu persetujuan admin.'],
            ['Bagaimana jika ingin keluar sebelum kontrak habis?', 'Ajukan pengunduran diri dari menu Detail Sewa beserta alasan, rencana tanggal keluar, dan tanggal pengembalian kunci.'],
        ],
    ];
@endphp

@section('content')
<section class="page-hero page-hero--tight">
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Ada yang bisa dibantu?</h1>
        <p class="page-hero__lead" data-hero-in>Jawaban untuk pertanyaan yang paling sering diajukan calon penghuni dan penghuni.</p>
        <div class="faq-search" data-hero-in>
            <i class="ri-search-line" aria-hidden="true"></i>
            <label for="faq-q" class="sr-only">Cari pertanyaan</label>
            <input id="faq-q" type="search" placeholder="Cari, misalnya: deposit, survei, kerusakan" data-faq-search>
        </div>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap faq-layout">
        <nav class="faq-cats" aria-label="Kategori">
            @foreach (array_keys($faq) as $i => $kat)
                <a href="#faq-{{ $i }}" class="faq-cats__link"><span>{{ $i + 1 }}</span>{{ $kat }}</a>
            @endforeach
        </nav>
        <div class="faq" data-faq>
            @foreach ($faq as $kat => $items)
                <section class="faq__group" id="faq-{{ $loop->index }}" data-reveal>
                    <h2 class="faq__title">{{ $kat }}</h2>
                    @foreach ($items as [$q, $a])
                        <details class="faq__item" data-faq-item>
                            <summary><span>{{ $q }}</span><i class="ri-add-line" aria-hidden="true"></i></summary>
                            <div class="faq__answer"><p>{{ $a }}</p></div>
                        </details>
                    @endforeach
                </section>
            @endforeach
            <p class="faq__empty" data-faq-empty hidden>Tidak ada pertanyaan yang cocok. Coba kata kunci lain.</p>
        </div>
    </div>
    <div class="wrap">
        <div class="help-cards" data-reveal-group>
            <a href="{{ route('public.simulasi') }}" class="help-card" data-tilt="5"><i class="ri-calculator-line"></i><b>Simulasi biaya</b><span>Hitung biaya bulanan dan biaya awal.</span></a>
            <a href="{{ route('public.tur') }}" class="help-card" data-tilt="5"><i class="ri-box-3-line"></i><b>Tur 3D unit</b><span>Lihat tata ruang tipe 24 dan 36.</span></a>
            <a href="{{ route('register') }}" class="help-card help-card--ink" data-tilt="5"><i class="ri-user-add-line"></i><b>Buat akun</b><span>Mulai ajukan sewa unit pilihanmu.</span></a>
        </div>
    </div>
</section>
@endsection
