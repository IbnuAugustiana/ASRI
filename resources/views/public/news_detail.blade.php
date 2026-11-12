@extends('layouts.public')
@section('title', $article->judul . ' | ASRI')
@section('meta_description', \App\Support\Text::excerpt($article->konten, 150))

@push('styles')
    {{-- Kelas perataan/ukuran teks dari editor Quill --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="read-progress" aria-hidden="true"><span data-progress></span></div>

<section class="article-hero">
    <div class="wrap article-hero__inner">
        <a href="{{ route('public.news') }}" class="back-link"><i class="ri-arrow-left-line" aria-hidden="true"></i> Semua berita</a>
        <div class="article-hero__meta" data-hero-in>
            <span class="chip chip--glass">{{ $article->label_kategori }}</span>
            <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d F Y') }}</time>
            @if ($article->user)
                <span>oleh {{ $article->user->name }}</span>
            @endif
        </div>
        <h1 class="article-hero__title" data-split>{{ $article->judul }}</h1>
    </div>
</section>

<section class="article">
    <div class="wrap article__grid">
        <article class="article__main">
            <div class="article__cover" data-tilt="4">
                @include('public.partials.news-media', ['item' => $article])
            </div>
            <div class="ql-editor prose">
                {!! $article->konten !!}
            </div>
        </article>

        <aside class="article__side">
            @if ($related->count())
                <div class="side-card">
                    <h2 class="side-card__title">Berita lainnya</h2>
                    @foreach ($related as $r)
                        <a href="{{ route('public.news.detail', $r->slug) }}" class="side-news">
                            <span class="side-news__thumb">@include('public.partials.news-media', ['item' => $r])</span>
                            <span>
                                <b>{{ Str::limit($r->judul, 64) }}</b>
                                <small>{{ $r->published_at->translatedFormat('d M Y') }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="side-card side-card--ink" data-tilt="5">
                <h2 class="side-card__title">Sedang cari unit?</h2>
                <p>Lihat unit yang masih kosong dan ajukan sewa secara online.</p>
                <a href="{{ route('public.units', ['tersedia' => 1]) }}" class="btn btn--leaf btn--block">Lihat unit kosong</a>
            </div>
        </aside>
    </div>
</section>
@endsection
