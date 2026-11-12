@extends('layouts.public')
@section('title', 'Berita | ASRI')

@section('content')
<section class="page-hero page-hero--news">
    <div class="floaters" aria-hidden="true">
        @foreach ([[10, 30, 56, 0], [86, 24, 44, 1.2], [76, 74, 30, 2.6]] as [$fx, $fy, $fs, $fd])
            <div class="floater" style="--x: {{ $fx }}%; --y: {{ $fy }}%; --s: {{ $fs }}px; --d: {{ $fd }}s">
                <i></i><i></i><i></i><i></i><i></i><i></i>
            </div>
        @endforeach
    </div>
    <div class="wrap page-hero__inner">
        <h1 class="page-hero__title" data-split>Kabar dari pengelola</h1>
        <p class="page-hero__lead" data-hero-in>Pengumuman, kegiatan, dan info penting seputar rusun.</p>

        <form method="GET" action="{{ route('public.news') }}" class="filter filter--news" data-hero-in role="search">
            <div class="field field--grow">
                <label for="n-search" class="sr-only">Cari berita</label>
                <i class="ri-search-line" aria-hidden="true"></i>
                <input id="n-search" type="search" name="search" value="{{ request('search') }}" placeholder="Judul atau tanggal (dd-mm-yyyy)">
            </div>
            <div class="field">
                <label for="n-kat" class="sr-only">Kategori</label>
                <i class="ri-price-tag-3-line" aria-hidden="true"></i>
                <select id="n-kat" name="kategori" onchange="this.form.submit()">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriList as $key => $label)
                        <option value="{{ $key }}" @selected(request('kategori') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <i class="ri-arrow-down-s-line field__caret" aria-hidden="true"></i>
            </div>
            <div class="field">
                <label for="n-sort" class="sr-only">Urutkan</label>
                <i class="ri-sort-desc" aria-hidden="true"></i>
                <select id="n-sort" name="sort" onchange="this.form.submit()">
                    <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru dulu</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Terlama dulu</option>
                </select>
                <i class="ri-arrow-down-s-line field__caret" aria-hidden="true"></i>
            </div>
            <button type="submit" class="btn btn--leaf filter__submit"><i class="ri-search-2-line" aria-hidden="true"></i> Cari</button>
        </form>
    </div>
</section>

<section class="sec sec--mist sec--tight">
    <div class="wrap">
        <div class="results-bar">
            <p class="results-bar__count"><b>{{ $news->total() }}</b> berita</p>
            @if (request()->hasAny(['search', 'kategori', 'sort']))
                <a href="{{ route('public.news') }}" class="link-pill link-pill--sm"><i class="ri-close-line" aria-hidden="true"></i> Hapus filter</a>
            @endif
        </div>

        @if ($news->isEmpty())
            <div class="empty">
                <i class="ri-newspaper-line" aria-hidden="true"></i>
                <h3>Tidak ada berita yang cocok</h3>
                <p>Coba kata kunci lain atau pilih semua kategori.</p>
                <a href="{{ route('public.news') }}" class="btn btn--ink">Tampilkan semua berita</a>
            </div>
        @else
            <div class="news-list" data-reveal-group>
                @foreach ($news as $n)
                    <div class="plate-cell">
                        <a href="{{ route('public.news.detail', $n->slug) }}" class="news-card" data-tilt="6">
                            @include('public.partials.news-media', ['item' => $n])
                            <div class="news-card__body">
                                <div class="news-card__meta">
                                    <span class="chip chip--soft">{{ $n->label_kategori }}</span>
                                    <time datetime="{{ $n->published_at->toDateString() }}">{{ $n->published_at->translatedFormat('d F Y') }}</time>
                                </div>
                                <h2 class="news-card__title">{{ $n->judul }}</h2>
                                <p class="news-card__excerpt">{{ Str::limit(strip_tags($n->konten), 110) }}</p>
                            </div>
                            <span class="plate__glare" aria-hidden="true"></span>
                        </a>
                    </div>
                @endforeach
            </div>
            {{ $news->withQueryString()->links('partials.pagination') }}
        @endif
    </div>
</section>
@endsection
