@extends('layouts.app')
@section('title', 'Detail Tagihan')

@section('content')
<style>
.tag-wrap { padding: 1rem 0; font-family: inherit; }
.t-card { background: #fff; border: 0.5px solid rgba(0,0,0,0.12); border-radius: 12px; overflow: hidden; transition: border-color 0.2s; }
.t-card:hover { border-color: rgba(0,0,0,0.25); }
.t-card-header { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 0.5px solid rgba(0,0,0,0.1); background: #fafafa; }
.t-card-header .icon-wrap { width: 32px; height: 32px; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; }
.t-card-header .icon-wrap i { font-size: 15px; color: #666; }
.t-card-header span { font-size: 13px; font-weight: 500; color: #555; }
.t-card-body { padding: 18px; }
.info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 0.5px solid rgba(0,0,0,0.08); }
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 13px; color: #888; display: flex; align-items: center; gap: 6px; }
.info-label i { font-size: 14px; }
.info-value { font-size: 13px; color: #222; text-align: right; }
.amount-val { font-size: 18px; font-weight: 600; color: #185FA5; }
.avatar { width: 28px; height: 28px; border-radius: 50%; background: #dbeafe; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #1e40af; flex-shrink: 0; }
.badge-lunas { display: inline-flex; align-items: center; gap: 6px; background: #EAF3DE; color: #27500A; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 999px; }
.badge-pending { display: inline-flex; align-items: center; gap: 6px; background: #FAEEDA; color: #633806; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 999px; }
.badge-menunggak { display: inline-flex; align-items: center; gap: 6px; background: #FCEBEB; color: #501313; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 999px; }
.badge-default { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f0; color: #444; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 999px; }
.status-meta { font-size: 12px; color: #888; margin-top: 3px; }
.t-divider { height: 0.5px; background: rgba(0,0,0,0.08); margin: 14px -18px; }
.bukti-row { display: flex; align-items: center; justify-content: space-between; }
.bukti-label { font-size: 12px; color: #888; display: flex; align-items: center; gap: 6px; }
.btn-lihat { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; padding: 5px 14px; border-radius: 8px; border: 0.5px solid rgba(0,0,0,0.2); background: #f5f5f5; color: #333; cursor: pointer; transition: background 0.15s; text-decoration: none; }
.btn-lihat:hover { background: #e8e8e8; color: #111; text-decoration: none; }
.no-bukti { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #aaa; }
</style>

<div class="tag-wrap">
    <div class="t-card">
        <div class="t-card-header">
            <div class="icon-wrap">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span>Detail tagihan &middot; {{ $tagihan->periode }}</span>
        </div>
        <div class="t-card-body">

            {{-- Penghuni --}}
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-user"></i> Penghuni
                </span>
                <span class="info-value" style="display:flex;align-items:center;gap:8px;">
                    @php
                        $nama = $tagihan->user->name ?? 'Tidak diketahui';
                        $initials = collect(explode(' ', $nama))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
                    @endphp
                    <span class="avatar">{{ $initials }}</span>
                    <span style="font-weight:500;">{{ $nama }}</span>
                </span>
            </div>

            {{-- Unit --}}
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-building"></i> Unit
                </span>
                <span class="info-value">
                    {{ $tagihan->unit->nama_unit ?? 'Unit tidak ditemukan' }}
                </span>
            </div>

            {{-- Jumlah Tagihan --}}
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-money-bill-wave"></i> Jumlah tagihan
                </span>
                <span class="info-value amount-val">
                    Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                </span>
            </div>

            {{-- Jatuh Tempo --}}
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-calendar-alt"></i> Jatuh tempo
                </span>
                <span class="info-value">
                    {{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d M Y') : '-' }}
                </span>
            </div>

            {{-- Status Pembayaran --}}
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-shield-alt"></i> Status
                </span>
                <span class="info-value" style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                    @if($tagihan->status === 'lunas')
                        <span class="badge-lunas">
                            <i class="fas fa-check-circle"></i> Lunas
                        </span>
                        <span class="status-meta">
                            Dibayar: {{ $tagihan->tanggal_bayar ? $tagihan->tanggal_bayar->format('d M Y, H:i') : '-' }}
                        </span>

                    @elseif($tagihan->status === 'menunggu_verifikasi')
                        <span class="badge-pending">
                            <i class="fas fa-clock"></i> Menunggu verifikasi
                        </span>
                        <span class="status-meta">Sedang dalam proses konfirmasi</span>

                    @elseif($tagihan->status === 'menunggak')
                        <span class="badge-menunggak">
                            <i class="fas fa-exclamation-circle"></i> Menunggak
                        </span>
                        <span class="status-meta">
                            Lewat jatuh tempo: {{ $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('d M Y') : '-' }}
                        </span>

                    @else
                        <span class="badge-default">
                            <i class="fas fa-hourglass-half"></i>
                            {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}
                        </span>
                        <span class="status-meta">Status belum diperbarui</span>
                    @endif
        </div>
    </div>
</div>
@endsection