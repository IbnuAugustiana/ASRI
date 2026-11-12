@php
    $statusLabel = ['tersedia' => 'Tersedia', 'dihuni' => 'Dihuni', 'maintenance' => 'Perawatan'][$unit->status] ?? ucfirst($unit->status);
@endphp
<div class="plate-cell">
    <article class="plate plate--{{ $unit->status }}" data-tilt="7">
        <a href="{{ route('public.unit.detail', $unit) }}" class="plate__link">
            <span class="sr-only">Lihat detail {{ $unit->nama_unit }}</span>
        </a>

        <div class="plate__visual plate__visual--photo">
            <img src="{{ $unit->foto_url }}" alt="Foto {{ $unit->nama_unit }}" loading="lazy" decoding="async">
            <span class="plate__mini" title="Posisi unit di gedung"><x-unit-iso :unit="$unit" /></span>
        </div>
        <button type="button" class="plate__compare" data-compare="{{ $unit->id }}" data-compare-label="{{ $unit->blok }}/{{ $unit->lantai }}.{{ $unit->no_kamar }}" aria-pressed="false">
            <i class="ri-scales-3-line" aria-hidden="true"></i><span>Bandingkan</span>
        </button>

        <div class="plate__code" aria-hidden="true">{{ $unit->blok }}<span>/</span>{{ $unit->lantai }}.{{ $unit->no_kamar }}</div>
        <span class="chip chip--glass chip--{{ $unit->status }} plate__chip">{{ $statusLabel }}</span>

        <div class="plate__body">
            <h3 class="plate__title">{{ $unit->gedung }}</h3>
            <p class="plate__meta">
                <span><i class="ri-map-pin-2-line" aria-hidden="true"></i>{{ $unit->wilayah }}</span>
                <span><i class="ri-stack-line" aria-hidden="true"></i>Lantai {{ $unit->lantai }}</span>
                @if ($unit->luas_m2)
                    <span><i class="ri-ruler-2-line" aria-hidden="true"></i>{{ $unit->luas_m2 }} m²</span>
                @endif
            </p>
            <div class="plate__foot">
                <div class="price">
                    <b>Rp {{ number_format($unit->harga_sewa, 0, ',', '.') }}</b>
                    <small>per bulan</small>
                </div>
                <span class="plate__go" aria-hidden="true"><i class="ri-arrow-right-up-line"></i></span>
            </div>
        </div>
        <span class="plate__glare" aria-hidden="true"></span>
    </article>
</div>
