{{-- resources/views/penghuni/tagihan/riwayat.blade.php --}}

@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@push('styles')
<style>
    :root {
        --green-50:  #f0f7f0;
        --green-100: #d6ead5;
        --green-200: #aed4ac;
        --green-400: #5fa65c;
        --green-600: #2d7a2b;
        --green-700: #1e5c1c;
        --green-800: #154014;
        --text-main: #151a3d;
        --text-sub:  #4a5078;
        --text-muted:#7a80a6;
        --border:    #e3e7f4;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 24px; /* Sudut lengkung besar sesuai contoh gambar */
        --shadow-card: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    /* Card Utama Berwarna Putih dengan Ujung Melengkung Sempurna */
    .main-white-card {
        background: #ffffff;
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(0, 0, 0, 0.01);
        min-height: 75vh;
        margin-bottom: 2rem;
    }

    /* ── Header ── */
    .riwayat-header {
        width: 100%;
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .riwayat-header .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--green-600);
        text-decoration: none;
        font-weight: 500;
        margin-bottom: .5rem;
        transition: color .2s;
    }
    .riwayat-header .back-link:hover { color: var(--green-700); }
    .riwayat-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--green-800);
        margin: 0 0 .25rem;
        line-height: 1.2;
    }
    .riwayat-header p.subtitle {
        font-size: 14px;
        color: var(--text-sub);
        margin: 0;
    }
    .total-badge {
        background: var(--green-600);
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 50px;
        white-space: nowrap;
        align-self: flex-start;
        margin-top: .25rem;
    }

    /* ── Summary bar ── */
    .summary-bar {
        width: 100%;
        margin-bottom: 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1.2rem 1.5rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--green-400);
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }
    .stat-card .label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: .4rem;
    }
    .stat-card .value {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--green-700);
        line-height: 1;
    }

    /* ── Filter bar ── */
    .filter-bar {
        width: 100%;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-bar .search-wrap {
        position: relative;
        flex: 1;
        min-width: 280px;
    }
    .filter-bar .search-wrap svg {
        position: absolute;
        left: 14px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }
    .filter-bar input[type="text"] {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 14px;
        background: #fbfcfb;
        color: var(--text-main);
        transition: border .2s, box-shadow .2s;
    }
    .filter-bar input[type="text"]:focus {
        outline: none;
        border-color: var(--green-400);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(95,166,92,.08);
    }
    .filter-bar select {
        padding: 10px 36px 10px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 14px;
        background: #fbfcfb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a9478' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 12px center;
        -webkit-appearance: none;
        color: var(--text-main);
        cursor: pointer;
        transition: border .2s;
    }
    .filter-bar select:focus { outline: none; border-color: var(--green-400); background-color: #fff; }

    /* ── Timeline ── */
    .timeline {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .timeline-month {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 1rem 0 .25rem;
        border-top: 1px dashed var(--border);
    }
    .timeline-month:first-child { border-top: none; }

    /* Baris Daftar Riwayat */
    .trx-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        transition: box-shadow .2s, transform .15s;
        text-decoration: none;
        color: inherit;
    }
    .trx-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
        border-color: var(--green-200);
    }

    .trx-left-group {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex: 1;
        min-width: 0;
    }
    .trx-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        background: var(--green-50);
        border: 1px solid var(--green-100);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--green-600);
    }
    .trx-icon svg { width: 20px; height: 20px; }

    .trx-body {
        flex: 1;
        min-width: 0;
    }
    .trx-body .trx-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 4px;
    }
    .trx-body .trx-meta {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        align-items: center;
    }
    .trx-body .trx-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Badge Dinamis Midtrans Payment Type */
    .midtrans-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #eef7ff;
        color: #0d6efd;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .trx-right {
        text-align: right;
        flex-shrink: 0;
    }
    .trx-right .amount {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--green-700);
        white-space: nowrap;
    }
    .trx-right .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 50px;
        margin-top: 6px;
        background: #eaf5e9;
        color: var(--green-700);
    }
    .trx-right .badge-status svg { width: 12px; height: 12px; }

    /* ── Empty state ── */
    .empty-state {
        width: 100%;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-state .empty-icon {
        width: 64px; height: 64px;
        background: var(--green-50);
        border: 1px solid var(--green-100);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        color: var(--green-400);
    }
    .empty-state h3 { font-size: 1.25rem; color: var(--text-main); margin: 0 0 .5rem; }
    .empty-state p { font-size: 14px; color: var(--text-muted); margin: 0 0 1.5rem; }
    
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--green-600);
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 22px;
        border-radius: var(--radius-sm);
        transition: background .2s;
    }
    .btn-primary:hover { background: var(--green-700); }

    /* ── Pagination ── */
    .pagination-wrap {
        width: 100%;
        margin: 2.5rem auto 0;
        display: flex;
        justify-content: center;
    }
    .pagination-wrap .pagination {
        display: flex; gap: 6px; list-style: none; padding: 0; margin: 0;
    }
    .pagination-wrap .pagination li a,
    .pagination-wrap .pagination li span {
        display: flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; padding: 0 10px; font-size: 13px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        background: #fff; color: var(--text-sub); text-decoration: none;
    }
    .pagination-wrap .pagination li.active span {
        background: var(--green-600); border-color: var(--green-600); color: #fff;
    }

    /* ── Flash Messages ── */
    .flash {
        width: 100%; margin-bottom: 1.5rem; padding: .9rem 1.2rem;
        border-radius: var(--radius-md); font-size: 14px; display: flex; align-items: center; gap: 10px;
    }
    .flash.success { background: var(--green-50); border: 1px solid var(--green-200); color: var(--green-700); }

    @media (max-width: 768px) {
        .main-white-card { padding: 1.25rem; }
        .riwayat-header h1 { font-size: 1.5rem; }
        .trx-card { padding: 1rem; gap: 1rem; }
        .trx-icon { display: none; }
    }
</style>
@endpush

@section('content')
{{-- Memakai container fluid default bawaan layout biar dapet background krem luarnya --}}
<div class="container-fluid px-4 py-4">

    {{-- INI DIA: Card putih besar berujung tumpul (rounded) rapi seperti contoh gambar --}}
    <div class="main-white-card">

        {{-- Flash Session Alert --}}
        @if(session('success'))
        <div class="flash success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Header Bagian Dalam Card --}}
        <div class="riwayat-header">
            <div>
                <a href="{{ route('penghuni.tagihan.index') }}" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali ke Daftar Tagihan
                </a>
                <h1>Riwayat Transaksi</h1>
                <p class="subtitle">Daftar seluruh pembayaran tagihan hunian Anda yang telah lunas.</p>
            </div>
            <span class="total-badge">{{ $riwayats->total() }} Transaksi</span>
        </div>

        @if($riwayats->total() > 0)
        {{-- Tiga Kotak Mini Summary --}}
        <div class="summary-bar">
            <div class="stat-card">
                <div class="label">Total Lunas</div>
                <div class="value">{{ $riwayats->total() }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Dibayar</div>
                <div class="value">Rp {{ number_format($riwayats->sum('jumlah'), 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Terakhir Bayar</div>
                <div class="value" style="font-size:1.15rem;">
                    {{ $riwayats->first() ? \Carbon\Carbon::parse($riwayats->first()->tanggal_bayar)->translatedFormat('d M Y') : '-' }}
                </div>
            </div>
        </div>

        {{-- Baris Filter Pencarian --}}
        <div class="filter-bar">
            <div class="search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama tagihan..." />
            </div>
            <select id="filterBulan">
                <option value="">Semua Bulan</option>
                @foreach($riwayats->groupBy(fn($t) => \Carbon\Carbon::parse($t->tanggal_bayar)->format('Y-m'))->keys() as $ym)
                <option value="{{ $ym }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->translatedFormat('F Y') }}</option>
                @endforeach
            </select>
        </div>

        {{-- Alur Timeline Pembayaran --}}
        <div class="timeline" id="transactionList">
            @php
                $grouped = $riwayats->groupBy(fn($t) => \Carbon\Carbon::parse($t->tanggal_bayar)->format('Y-m'));
            @endphp

            @foreach($grouped as $ym => $items)
            <div class="timeline-month" data-month="{{ $ym }}">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->translatedFormat('F Y') }}
            </div>

            @foreach($items as $item)
            @php
                // Logika Judul Kustom: Merubah "Tagihan #7" jadi "Pembayaran Sewa Hunian"
                $titleLower = strtolower($item->nama_tagihan ?? $item->keterangan ?? '');
                if (str_contains($titleLower, 'tagihan') || empty($titleLower)) {
                    $displayTitle = 'Pembayaran Sewa Hunian';
                } else {
                    $displayTitle = $item->nama_tagihan ?? $item->keterangan;
                }

                // Ambil info metode pembayaran langsung dari API Midtrans secara dinamis
                $paymentMethod = $item->payment_type ? strtoupper(str_replace('_', ' ', $item->payment_type)) : 'MIDTRANS API';
            @endphp
            
            <a href="{{ route('penghuni.tagihan.show', $item->id) }}" class="trx-card" data-title="{{ strtolower($displayTitle) }}" data-month="{{ $ym }}">
                <div class="trx-left-group">
                    <div class="trx-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>

                    <div class="trx-body">
                        <div class="trx-title">
                            {{ $displayTitle }}
                        </div>
                        <div class="trx-meta">
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d M Y') }}
                            </span>
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('H:i') }} WIB
                            </span>
                            <span class="midtrans-badge">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M12 22V2M12 2l-4 4M12 2l4 4"/></svg>
                                Lunas otomatis via {{ $paymentMethod }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="trx-right">
                    <div class="amount">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                    <div class="badge-status">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Berhasil
                    </div>
                </div>
            </a>
            @endforeach
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($riwayats->hasPages())
        <div class="pagination-wrap">
            {{ $riwayats->links() }}
        </div>
        @endif

        @else
        {{-- Tampilan Kosong --}}
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
            </div>
            <h3>Belum Ada Riwayat Transaksi</h3>
            <p>Transaksi yang sudah lunas akan muncul di sini setelah pembayaran berhasil dikonfirmasi.</p>
            <a href="{{ route('penghuni.tagihan.index') }}" class="btn-primary"> Lihat Tagihan </a>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput  = document.getElementById('searchInput');
    const filterBulan  = document.getElementById('filterBulan');
    const cards        = document.querySelectorAll('.trx-card');
    const monthHeaders = document.querySelectorAll('.timeline-month');

    function applyFilter() {
        const q   = (searchInput ? searchInput.value.toLowerCase() : '');
        const bln = (filterBulan ? filterBulan.value : '');

        cards.forEach(card => {
            const matchQ   = !q   || card.dataset.title.includes(q);
            const matchBln = !bln || card.dataset.month === bln;
            card.style.display = (matchQ && matchBln) ? '' : 'none';
        });

        monthHeaders.forEach(header => {
            const month = header.dataset.month;
            const visible = [...cards].some(c => c.dataset.month === month && c.style.display !== 'none');
            header.style.display = visible ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (filterBulan) filterBulan.addEventListener('change', applyFilter);
</script>
@endpush