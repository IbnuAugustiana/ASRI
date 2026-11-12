@php
    $hasCover = $item->gambar_cover && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->gambar_cover);
    $kat = $item->kategori ?? 'info_penting';
@endphp
<div class="news-media news-media--{{ $kat }}">
    @if ($hasCover)
        <img src="{{ Storage::url($item->gambar_cover) }}" alt="" loading="lazy">
    @else
        <div class="news-media__art" aria-hidden="true">
            <span class="news-media__word">{{ $item->label_kategori }}</span>
            <span class="news-media__cube"><i></i><i></i><i></i></span>
        </div>
    @endif
</div>
