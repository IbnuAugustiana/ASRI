@extends('layouts.public')
@section('title', 'ASRI | Rusun Jakarta, dari pilih unit sampai bayar sewa')
@section('body_class', 'page-home')

@push('before_header')
    <div class="loader" id="loader" aria-hidden="true">
        <div class="loader__windows"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
        <span class="loader__word">ASRI</span>
    </div>
@endpush

@section('content')

{{-- ============ HERO: gedung 3D dari data unit ============ --}}
<section class="hero" data-hero>
    <div class="hero__stage" data-tower>
        <script type="application/json">@json($towerData)</script>
        <div class="tower-tip" hidden></div>
    </div>

    <div class="wrap hero__content">
        <p class="hero__kicker" data-hero-in>
            <span class="pulse-dot" aria-hidden="true"></span>
            {{ $stats['tersedia'] }} unit kosong di {{ $stats['wilayah'] }} wilayah Jakarta
        </p>
        <h1 class="hero__title" data-split>Pilih jendelamu di rusun Jakarta.</h1>
        <p class="hero__lead" data-hero-in>
            Lihat unit yang masih kosong, ajukan sewa, unggah dokumen, dan bayar tagihan dari satu tempat.
        </p>
        <div class="hero__cta" data-hero-in>
            <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="btn btn--leaf btn--lg" data-magnetic>
                <i class="ri-search-eye-line" aria-hidden="true"></i> Lihat unit kosong
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn--glass btn--lg" data-magnetic>Buat akun</a>
            @endguest
        </div>

        <ul class="legend" data-hero-in aria-label="Arti warna jendela">
            <li><span class="swatch swatch--leaf" aria-hidden="true"></span><b data-count="{{ $stats['tersedia'] }}">{{ $stats['tersedia'] }}</b> tersedia</li>
            <li><span class="swatch swatch--lamp" aria-hidden="true"></span><b data-count="{{ $stats['dihuni'] }}">{{ $stats['dihuni'] }}</b> dihuni</li>
            <li><span class="swatch swatch--dim" aria-hidden="true"></span><b data-count="{{ $stats['maintenance'] }}">{{ $stats['maintenance'] }}</b> perawatan</li>
        </ul>
        <p class="hero__hint" data-hero-in>
            <i class="ri-cursor-line" aria-hidden="true"></i>
            Arahkan kursor ke jendela hijau, lalu klik untuk membuka unitnya. Seret untuk memutar gedung.
        </p>
    </div>

    <a href="#unit" class="hero__scroll" aria-label="Gulir ke daftar unit"><span></span></a>
</section>

{{-- ============ Pita berjalan ============ --}}
<div class="ribbons" aria-label="Unit kosong per wilayah">
    <div class="ribbon ribbon--ink">
        <div class="ribbon__skew" data-skew>
            <div class="ribbon__track ribbon__track--reverse" aria-hidden="true">
                @foreach (range(1, 2) as $copy)
                    @foreach (['Pengajuan sewa online', 'Unggah KTP, KK, slip gaji', 'Jadwal survei unit', 'Bayar lewat Midtrans', 'Laporan kerusakan', 'Perpanjangan kontrak'] as $f)
                        <span class="ribbon__item">{{ $f }}</span><span class="ribbon__sep">✦</span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    <div class="ribbon ribbon--leaf">
        <div class="ribbon__skew" data-skew>
            <div class="ribbon__track">
                @foreach (range(1, 2) as $copy)
                    @foreach ($wilayahStats as $w)
                        <span class="ribbon__item" @if ($copy === 2) aria-hidden="true" @endif>
                            {{ $w['wilayah'] }} <small>{{ $w['tersedia'] }} unit kosong</small>
                        </span>
                        <span class="ribbon__sep" aria-hidden="true">✦</span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ============ Unit tersedia ============ --}}
<section class="sec sec--mist" id="unit">
    <div class="wrap">
        <header class="sec-head">
            <div>
                <h2 class="sec-title" data-split>Unit yang masih kosong</h2>
                <p class="sec-lead">Unit di bawah bisa langsung kamu ajukan. Kotak hijau di gambar menunjukkan posisi unit di gedungnya.</p>
            </div>
            <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="link-pill" data-magnetic>
                Lihat semua unit <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </header>

        <div class="plates" data-reveal-group>
            @forelse ($unitTersedia as $unit)
                @include('public.partials.unit-plate', ['unit' => $unit])
            @empty
                <div class="empty">
                    <i class="ri-building-4-line" aria-hidden="true"></i>
                    <h3>Semua unit sedang terisi</h3>
                    <p>Cek lagi nanti, atau lihat semua unit untuk mengetahui kapan unit tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============ Jelajahi fitur ============ --}}
<section class="sec sec--paper features" id="fitur">
    <div class="wrap">
        <header class="sec-head">
            <div>
                <h2 class="sec-title" data-split>Lebih dari daftar unit</h2>
                <p class="sec-lead">Alat bantu untuk memilih hunian dengan yakin, dari melihat peta sampai menghitung biaya.</p>
            </div>
        </header>
        <div class="bento" data-reveal-group>
            <a href="{{ route('public.tur') }}" class="bento__item bento__item--tour" data-tilt="5">
                <span class="bento__art bento__art--room" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                <span class="bento__icon"><i class="ri-box-3-line" aria-hidden="true"></i></span>
                <b>Tur 3D unit</b>
                <span>Putar dan jelajahi tata ruang tipe 24 dan 36 langsung di browser.</span>
            </a>
            <a href="{{ route('public.lokasi') }}" class="bento__item bento__item--map" data-tilt="5">
                <span class="bento__art bento__art--map" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
                <span class="bento__icon"><i class="ri-map-2-line" aria-hidden="true"></i></span>
                <b>Peta lokasi</b>
                <span>Lihat sebaran kompleks di lima wilayah Jakarta.</span>
            </a>
            <a href="{{ route('public.simulasi') }}" class="bento__item" data-tilt="5">
                <span class="bento__icon"><i class="ri-calculator-line" aria-hidden="true"></i></span>
                <b>Simulasi biaya</b>
                <span>Hitung sewa, listrik, air, dan biaya awal.</span>
            </a>
            <a href="{{ route('public.bandingkan') }}" class="bento__item" data-tilt="5">
                <span class="bento__icon"><i class="ri-scales-3-line" aria-hidden="true"></i></span>
                <b>Bandingkan unit</b>
                <span>Taruh tiga unit berdampingan.</span>
            </a>
            <a href="{{ route('public.fasilitas') }}" class="bento__item" data-tilt="5">
                <span class="bento__icon"><i class="ri-community-line" aria-hidden="true"></i></span>
                <b>Fasilitas</b>
                <span>Keamanan, taman, kios, dan lainnya.</span>
            </a>
            <a href="{{ route('public.bantuan') }}" class="bento__item" data-tilt="5">
                <span class="bento__icon"><i class="ri-question-answer-line" aria-hidden="true"></i></span>
                <b>Bantuan</b>
                <span>Jawaban pertanyaan yang sering diajukan.</span>
            </a>
        </div>
    </div>
</section>

{{-- ============ Alur daftar: tangga 3D ============ --}}
<section class="steps" id="alur" data-steps>
    <div class="steps__glow" aria-hidden="true"></div>
    <div class="wrap steps__inner">
        <div class="steps__copy">
            <h2 class="sec-title sec-title--light" data-split>Dari daftar sampai pegang kunci</h2>
            <p class="sec-lead sec-lead--light">Empat langkah, semuanya online. Status pengajuanmu bisa dipantau dari dashboard.</p>
            <ol class="steps__list">
                <li class="steps__item">
                    <span class="steps__num">1</span>
                    <div><h3>Buat akun</h3><p>Daftar sebagai calon penghuni dengan nama, email, dan nomor HP.</p></div>
                </li>
                <li class="steps__item">
                    <span class="steps__num">2</span>
                    <div><h3>Pilih unit</h3><p>Cari unit kosong berdasarkan wilayah, harga, dan luas, lalu jadwalkan survei.</p></div>
                </li>
                <li class="steps__item">
                    <span class="steps__num">3</span>
                    <div><h3>Ajukan sewa</h3><p>Isi NIK dan durasi sewa, lalu unggah KTP, KK, dan slip gaji.</p></div>
                </li>
                <li class="steps__item">
                    <span class="steps__num">4</span>
                    <div><h3>Kontrak aktif</h3><p>Admin memverifikasi berkas. Setelah disetujui, kontrak dan tagihan pertama terbit otomatis.</p></div>
                </li>
            </ol>
        </div>

        <div class="stairs" aria-hidden="true">
            <div class="stairs__scene">
                @foreach (range(0, 3) as $i)
                    <div class="stair" style="--i: {{ $i }}">
                        <div class="stair__top"><span>{{ $i + 1 }}</span></div>
                        <div class="stair__front"></div>
                        <div class="stair__side"></div>
                    </div>
                @endforeach
                <div class="stairs__key">
                    <div class="cube">
                        <i class="cube__f cube__f--front"></i><i class="cube__f cube__f--back"></i>
                        <i class="cube__f cube__f--left"></i><i class="cube__f cube__f--right"></i>
                        <i class="cube__f cube__f--top"></i><i class="cube__f cube__f--bottom"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ Tiga peran: prisma 3D ============ --}}
<section class="sec sec--paper roles" id="peran" data-roles>
    <div class="wrap roles__inner">
        <div class="roles__copy">
            <h2 class="sec-title" data-split>Satu sistem untuk tiga peran</h2>
            <p class="sec-lead">ASRI melayani semua pihak di rusun: yang sedang mencari unit, yang sudah tinggal, dan pengelolanya.</p>
            <div class="roles__tabs" role="tablist" aria-label="Pilih peran">
                <button class="roles__tab" role="tab" id="role-tab-0" aria-controls="role-face-0" aria-selected="true" data-role="0">
                    <i class="ri-user-search-line" aria-hidden="true"></i> Calon penghuni
                </button>
                <button class="roles__tab" role="tab" id="role-tab-1" aria-controls="role-face-1" aria-selected="false" tabindex="-1" data-role="1">
                    <i class="ri-home-heart-line" aria-hidden="true"></i> Penghuni
                </button>
                <button class="roles__tab" role="tab" id="role-tab-2" aria-controls="role-face-2" aria-selected="false" tabindex="-1" data-role="2">
                    <i class="ri-shield-user-line" aria-hidden="true"></i> Admin
                </button>
            </div>
        </div>

        <div class="prism-wrap">
            <div class="prism" data-prism>
                @php
                    $roles = [
                        ['ri-user-search-line', 'Calon penghuni', 'Mencari dan mengajukan unit', ['Cari dan bandingkan unit kosong', 'Ajukan sewa dan unggah dokumen', 'Pilih jadwal survei unit', 'Pantau status pengajuan']],
                        ['ri-home-heart-line', 'Penghuni', 'Mengurus hunian sehari-hari', ['Bayar tagihan online lewat Midtrans', 'Laporkan kerusakan dengan foto', 'Ajukan perpanjangan kontrak', 'Lihat riwayat pembayaran']],
                        ['ri-shield-user-line', 'Admin', 'Mengelola seluruh rusun', ['Kelola unit, harga, dan status', 'Verifikasi pengajuan dan dokumen', 'Terbitkan tagihan, cek pembayaran', 'Pantau statistik okupansi']],
                    ];
                @endphp
                @foreach ($roles as $i => [$icon, $title, $sub, $items])
                    <div class="prism__face prism__face--{{ $i }}" style="--f: {{ $i }}" id="role-face-{{ $i }}" role="tabpanel" aria-labelledby="role-tab-{{ $i }}" @if ($i) aria-hidden="true" @endif>
                        <div class="prism__head">
                            <span class="prism__icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
                            <div>
                                <h3>{{ $title }}</h3>
                                <p>{{ $sub }}</p>
                            </div>
                        </div>
                        <ul class="prism__list">
                            @foreach ($items as $it)
                                <li><i class="ri-check-line" aria-hidden="true"></i>{{ $it }}</li>
                            @endforeach
                        </ul>
                        <div class="prism__mock" aria-hidden="true">
                            <span style="--w: 72%"></span><span style="--w: 48%"></span><span style="--w: 86%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============ Berita ============ --}}
@if ($beritaTerbaru->count())
<section class="sec sec--mist">
    <div class="wrap">
        <header class="sec-head">
            <div>
                <h2 class="sec-title" data-split>Kabar dari pengelola</h2>
                <p class="sec-lead">Pengumuman, kegiatan, dan info penting untuk penghuni rusun.</p>
            </div>
            <a href="{{ route('public.news') }}" class="link-pill" data-magnetic>
                Semua berita <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </header>

        <div class="news-home" data-reveal-group>
            @foreach ($beritaTerbaru as $i => $n)
                <div class="plate-cell">
                    <a href="{{ route('public.news.detail', $n->slug) }}" class="news-card {{ $i === 0 ? 'news-card--feature' : '' }}" data-tilt="5">
                        @include('public.partials.news-media', ['item' => $n])
                        <div class="news-card__body">
                            <div class="news-card__meta">
                                <span class="chip chip--soft">{{ $n->label_kategori }}</span>
                                <time datetime="{{ $n->published_at->toDateString() }}">{{ $n->published_at->translatedFormat('d F Y') }}</time>
                            </div>
                            <h3 class="news-card__title">{{ $n->judul }}</h3>
                            @if ($i === 0)
                                <p class="news-card__excerpt">{{ \App\Support\Text::excerpt($n->konten, 160) }}</p>
                            @endif
                        </div>
                        <span class="plate__glare" aria-hidden="true"></span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ Ajakan ============ --}}
<section class="cta" id="mulai">
    <div class="wrap">
        <div class="cta__box" data-tilt="3">
            <div class="cta__num" aria-hidden="true" data-count="{{ $stats['tersedia'] }}">{{ $stats['tersedia'] }}</div>
            <div class="cta__copy">
                <h2 class="cta__title"><span class="sr-only">{{ $stats['tersedia'] }} </span>unit menunggu penghuni baru.</h2>
                <p>Buat akun gratis untuk mengajukan sewa. Semua prosesnya bisa kamu pantau dari dashboard.</p>
                <div class="cta__actions">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn--leaf btn--lg" data-magnetic>Buat akun</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn--leaf btn--lg" data-magnetic>Buka dashboard</a>
                    @endguest
                    <a href="{{ route('public.units') }}" class="btn btn--glass btn--lg">Lihat semua unit</a>
                </div>
            </div>
            <div class="cta__cubes" aria-hidden="true"><i></i><i></i><i></i></div>
        </div>
    </div>
</section>

@endsection
