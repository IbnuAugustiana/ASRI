{{-- resources/views/penghuni/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Penghuni')
@section('page-title', 'Dashboard Saya')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Lora:wght@500;600&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap');

    :root {
        --g50:  #effbf5;
        --g100: #d7f6e7;
        --g200: #b2ecd0;
        --g400: #2ed48a;
        --g500: #14b370;
        --g600: #0e8f5b;
        --g700: #2b3480;
        --g800: #141a46;
        
        /* Tone hijau baru untuk unit card (lebih redup/soft & premium) */
        --g-card-start: #1f2766;
        --g-card-end: #3a2f84;

        --cr50: #f8f9fe;
        --cr100:#f2f4fb;
        --cr200:#e3e7f4;
        --cr400:#bcc4ec;
        --warn-bg: #fffbeb;
        --warn-border: #fde68a;
        --warn-text: #92400e;
        --danger-bg: #fef2f2;
        --danger-border: #fecaca;
        --danger-text: #991b1b;
        --tx: #151a3d;
        --tx2: #4a5078;
        --tx3: #7a80a6;
        --border: #e3e7f4;
        --card-shadow: 0 1px 3px rgba(20,26,70,.07), 0 4px 18px rgba(20,26,70,.07);
        --r-sm: 8px;
        --r-md: 12px;
        --r-lg: 18px;
        --r-xl: 24px;
        
        /* Variabel lebar maksimal halaman baru */
        --max-width-layout: 1400px;
    }

    .db-wrap {
        font-family: 'DM Sans', sans-serif;
        background: var(--cr50);
        min-height: 100vh;
        padding: 1.5rem 2rem 4rem;
        color: var(--tx);
    }

    /* ── greeting ── */
    .db-greeting {
        max-width: var(--max-width-layout);
        margin: 0 auto 1.75rem;
    }
    .db-greeting .hello {
        font-size: 13px;
        color: var(--tx3);
        font-weight: 500;
        margin-bottom: 2px;
    }
    .db-greeting h1 {
        font-family: 'DM Sans', sans-serif;
        font-size: 1.85rem;
        font-weight: 600;
        color: var(--g800);
        margin: 0 0 .2rem;
        letter-spacing: -0.02em;
    }
    .db-greeting .sub {
        font-size: 13px;
        color: var(--tx2);
    }

    /* ── alert banner ── */
    .alert-banner {
        max-width: var(--max-width-layout);
        margin: 0 auto 1.5rem;
        border-radius: var(--r-md);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .alert-banner.danger {
        background: var(--danger-bg);
        border: 1px solid var(--danger-border);
    }
    .alert-banner.warn {
        background: var(--warn-bg);
        border: 1px solid var(--warn-border);
    }
    .alert-banner .al-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .al-icon {
        width: 38px; height: 38px;
        border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }
    .danger .al-icon { background: #fee2e2; }
    .warn   .al-icon { background: #fef3c7; }
    .al-title { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
    .danger .al-title { color: var(--danger-text); }
    .warn   .al-title { color: var(--warn-text); }
    .al-sub  { font-size: 12px; }
    .danger .al-sub { color: #b91c1c; }
    .warn   .al-sub { color: #78350f; }
    .btn-pay {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--g600); color: #fff;
        text-decoration: none; font-size: 13px; font-weight: 600;
        padding: 8px 16px; border-radius: var(--r-sm);
        white-space: nowrap; transition: background .2s;
        flex-shrink: 0;
        border: none;
        cursor: pointer;
    }
    .btn-pay:hover { background: var(--g700); color: #fff; }

    /* ── hero unit card ── */
    .hero-grid {
        max-width: var(--max-width-layout);
        margin: 0 auto 1.5rem;
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 16px;
    }
    @media (max-width: 768px) { .hero-grid { grid-template-columns: 1fr; } }

    .unit-card {
        background: linear-gradient(140deg, var(--g-card-start) 0%, var(--g-card-end) 100%);
        border-radius: var(--r-xl);
        padding: 26px 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .unit-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
    }
    .unit-card::after {
        content: '';
        position: absolute;
        bottom: -30px; left: 60px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.03);
    }
    .unit-label {
        font-size: 11px; font-weight: 600; letter-spacing: .1em;
        text-transform: uppercase; opacity: .7; margin-bottom: 8px;
    }
    .unit-name {
        font-family: 'Lora', serif;
        font-size: 2.25rem; font-weight: 600; margin: 0 0 6px;
        line-height: 1.1;
    }
    .unit-sub {
        font-size: 14px; opacity: .8; margin-bottom: 24px;
    }
    .unit-meta {
        display: flex; gap: 32px; flex-wrap: wrap;
    }
    .unit-meta-item .meta-label {
        font-size: 10px; font-weight: 600; opacity: .6;
        text-transform: uppercase; letter-spacing: .07em; margin-bottom: 4px;
    }
    .unit-meta-item .meta-val {
        font-size: 14px; font-weight: 600;
    }
    .unit-location {
        margin-top: 20px; padding-top: 16px;
        border-top: 1px solid rgba(255,255,255,.15);
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; opacity: .75;
    }

    /* ── countdown card ── */
    .countdown-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        padding: 24px;
        display: flex; flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--card-shadow);
    }
    .cd-label { font-size: 12px; font-weight: 600; color: var(--tx3); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 8px; }
    .cd-days { font-family: 'Lora', serif; font-size: 3.25rem; font-weight: 600; color: var(--g700); line-height: 1; }
    .cd-unit { font-size: 13px; color: var(--tx2); margin-top: 2px; }
    .progress-bar-wrap { margin-top: 16px; }
    .progress-bar-track {
        background: var(--g100); border-radius: 50px; height: 7px;
        overflow: hidden; margin-bottom: 6px;
    }
    .progress-bar-fill {
        height: 100%; border-radius: 50px;
        background: linear-gradient(90deg, var(--g400), var(--g600));
        transition: width 1s ease;
    }
    .progress-labels {
        display: flex; justify-content: space-between;
        font-size: 11px; color: var(--tx3);
    }
    .cd-warning {
        margin-top: 12px;
        background: #fef3c7; border: 1px solid var(--warn-border);
        border-radius: var(--r-sm); padding: 7px 10px;
        font-size: 11px; font-weight: 600; color: var(--warn-text);
        display: flex; align-items: center; gap: 6px;
    }
    .btn-renew {
        width: 100%; background: var(--g600); color: white; border: none; 
        padding: 10px; border-radius: var(--r-sm); font-size: 12px; 
        font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 12px;
        display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .btn-renew:hover { background: var(--g700); }

    /* ── stats row ── */
    .stats-row {
        max-width: var(--max-width-layout);
        margin: 0 auto 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }
    .stat-tile {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 18px 20px;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
        transition: transform .15s, box-shadow .15s;
    }
    .stat-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(20,26,70,.11); }
    .stat-tile .st-accent {
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: var(--r-lg) var(--r-lg) 0 0;
    }
    .accent-green  { background: var(--g500); }
    .accent-amber  { background: #d97706; }
    .accent-cream  { background: var(--cr400); }
    .accent-blue   { background: #3b82f6; }
    .st-icon {
        width: 38px; height: 38px;
        border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; margin-bottom: 12px;
    }
    .icon-green  { background: var(--g50);    color: var(--g600); }
    .icon-amber  { background: #fef3c7;        color: #b45309; }
    .icon-cream  { background: var(--cr100);   color: #8c7340; }
    .icon-blue   { background: #eff6ff;        color: #1d4ed8; }
    .st-val  { font-size: 1.45rem; font-weight: 600; color: var(--tx); line-height: 1.1; }
    .st-lbl  { font-size: 11px; color: var(--tx3); font-weight: 500; margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; }

    /* ── main grid ── */
    .main-grid {
        max-width: var(--max-width-layout);
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 768px) { .main-grid { grid-template-columns: 1fr; } }

    /* ── panel card ── */
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }
    .panel-head {
        padding: 16px 20px 14px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .panel-head .ph-left {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--tx);
    }
    .ph-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--g500); flex-shrink: 0;
    }
    .ph-dot.orange { background: #f59e0b; }
    .panel-head a {
        font-size: 12px; color: var(--g600); text-decoration: none; font-weight: 500;
        display: flex; align-items: center; gap: 3px;
        transition: color .2s;
    }
    .panel-head a:hover { color: var(--g700); }

    /* ── tagihan rows ── */
    .tagihan-row {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--cr100);
        transition: background .15s;
        text-decoration: none;
        color: inherit;
    }
    .tagihan-row:last-child { border-bottom: none; }
    .tagihan-row:hover { background: var(--g50); }
    .tr-month {
        width: 44px; height: 44px; border-radius: var(--r-sm);
        background: var(--g50); border: 1px solid var(--g100);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .tr-month .m { font-size: 9px; font-weight: 700; color: var(--g600); text-transform: uppercase; line-height: 1; }
    .tr-month .d { font-size: 16px; font-weight: 700; color: var(--g800); line-height: 1.1; }
    .tr-body { flex: 1; min-width: 0; }
    .tr-body .tr-name { font-size: 13px; font-weight: 600; color: var(--tx); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tr-body .tr-due  { font-size: 11px; color: var(--tx3); margin-top: 2px; }
    .tr-right { text-align: right; flex-shrink: 0; }
    .tr-right .tr-amount { font-size: 13px; font-weight: 700; color: var(--g700); }
    .tr-right .badge {
        display: inline-block; font-size: 10px; font-weight: 600;
        padding: 2px 8px; border-radius: 50px; margin-top: 3px;
    }
    .badge-ok     { background: var(--g100); color: var(--g700); }
    .badge-warn   { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    /* ── laporan rows ── */
    .laporan-row {
        padding: 14px 20px;
        border-bottom: 1px solid var(--cr100);
        transition: background .15s;
    }
    .laporan-row:last-child { border-bottom: none; }
    .laporan-row:hover { background: var(--g50); }
    .lr-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 4px; }
    .lr-title { font-size: 13px; font-weight: 600; color: var(--tx); }
    .status-pill {
        font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 50px; white-space: nowrap; flex-shrink: 0;
    }
    .pill-blue   { background: #dbeafe; color: #1e40af; }
    .pill-orange { background: #ffedd5; color: #9a3412; }
    .pill-red    { background: #fee2e2; color: #991b1b; }
    .lr-meta { font-size: 11px; color: var(--tx3); }

    /* ── empty state ── */
    .empty-panel {
        padding: 3rem 1.5rem; text-align: center;
    }
    .empty-panel .ep-icon {
        width: 52px; height: 52px; border-radius: 50%;
        background: var(--g50); border: 1px solid var(--g100);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin: 0 auto 12px;
        color: var(--g400);
    }
    .empty-panel p { font-size: 13px; color: var(--tx3); margin: 0 0 14px; }
    .btn-sm-green {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--g50); color: var(--g700);
        border: 1px solid var(--g200); border-radius: var(--r-sm);
        padding: 7px 14px; font-size: 13px; font-weight: 500;
        text-decoration: none; transition: background .2s;
    }
    .btn-sm-green:hover { background: var(--g100); color: var(--g700); }

    /* ── notifikasi dot ── */
    .notif-dot {
        display: inline-flex; align-items: center; justify-content: center;
        background: #ef4444; color: #fff; font-size: 10px; font-weight: 700;
        width: 18px; height: 18px; border-radius: 50%; margin-left: 4px;
    }

    /* ── no-contract ── */
    .no-contract {
        max-width: var(--max-width-layout); margin: 0 auto 1.75rem;
        background: #fff; border: 1px solid var(--border);
        border-radius: var(--r-xl); padding: 3rem 2rem;
        text-align: center; box-shadow: var(--card-shadow);
    }
    .no-contract .nc-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--g50); border: 1px solid var(--g100);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; margin: 0 auto 1rem; color: var(--g400);
    }
    .no-contract h3 { font-family: 'DM Sans', sans-serif; font-size: 1.25rem; color: var(--tx); margin: 0 0 .4rem; font-weight: 600; }
    .no-contract p  { font-size: 14px; color: var(--tx3); margin: 0; }
</style>
@endpush

@section('content')
<div class="db-wrap">

    {{-- Alert Flash Session Success / Error --}}
    @if(session('success'))
        <div style="max-width: var(--max-width-layout); margin: 0 auto 1.5rem; background: #ecfdf5; color: #065f46; padding: 12px 18px; border-radius: var(--r-md); border: 1px solid #a7f3d0; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="max-width: var(--max-width-layout); margin: 0 auto 1.5rem; background: #fef2f2; color: #991b1b; padding: 12px 18px; border-radius: var(--r-md); border: 1px solid #fecaca; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Greeting --}}
    <div class="db-greeting">
        <div class="hello">Selamat datang kembali 👋</div>
        <h1>{{ Auth::user()->name }}</h1>
        <div class="sub">{{ now()->translatedFormat('l, d F Y') }}
            @if($notifikasiTerbaru->count() > 0)
            &nbsp;·&nbsp;<span style="color:var(--g600);font-weight:500;">{{ $notifikasiTerbaru->count() }} notifikasi belum dibaca</span>
            @endif
        </div>
    </div>

    @if($kontrakAktif)

    {{-- Alert Banner --}}
    @if(isset($tagihanBelumBayar) && $tagihanBelumBayar)
        @php $isLate = $tagihanBelumBayar->jatuh_tempo->isPast(); @endphp
        <div class="alert-banner {{ $isLate ? 'danger' : 'warn' }}">
            <div class="al-left">
                <div class="al-icon">{{ $isLate ? '🚨' : '📋' }}</div>
                <div>
                    <div class="al-title">
                        {{ $isLate ? 'Tagihan Melewati Jatuh Tempo!' : 'Ada Tagihan Menunggu Pembayaran' }}
                    </div>
                    <div class="al-sub">
                        Periode {{ $tagihanBelumBayar->periode }} ·
                        Rp {{ number_format($tagihanBelumBayar->jumlah, 0, ',', '.') }} ·
                        Jatuh tempo {{ $tagihanBelumBayar->jatuh_tempo->translatedFormat('d F Y') }}
                        @if($isLate)
                        · <strong>Terlambat {{ (int) $tagihanBelumBayar->jatuh_tempo->diffInDays(now()) }} hari</strong>
                        @else
                        · {{ $tagihanBelumBayar->jatuh_tempo->diffForHumans() }}
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('penghuni.tagihan.show', $tagihanBelumBayar->id) }}" class="btn-pay">
                <i class="ri-bank-card-line"></i> Bayar Sekarang
            </a>
        </div>
    @endif

    {{-- Hero: Unit + Countdown --}}
    <div class="hero-grid">
        {{-- Unit Card --}}
        <div class="unit-card">
            <div>
                <div class="unit-label">Unit Hunian Anda</div>
                <div class="unit-name">{{ $kontrakAktif->unit->nama_unit }}</div>
                <div class="unit-sub">{{ $kontrakAktif->unit->gedung ?? 'Gedung —' }}</div>
            </div>
            <div>
                <div class="unit-meta">
                    <div class="unit-meta-item">
                        <div class="meta-label">Mulai Kontrak</div>
                        <div class="meta-val">{{ $kontrakAktif->tanggal_mulai->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="unit-meta-item">
                        <div class="meta-label">Selesai Kontrak</div>
                        <div class="meta-val">{{ $kontrakAktif->tanggal_selesai->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="unit-meta-item">
                        <div class="meta-label">Harga / Bulan</div>
                        <div class="meta-val">Rp {{ number_format($kontrakAktif->harga_per_bulan, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="unit-location">
                    <i class="ri-map-pin-line"></i>
                    {{ $kontrakAktif->unit->wilayah ?? 'Lokasi tidak tersedia' }}
                </div>
            </div>
        </div>

        {{-- Countdown Card --}}
        @if(isset($infoKontrak))
        <div class="countdown-card">
            <div>
                <div class="cd-label">Sisa Masa Kontrak</div>
                <div class="cd-days">{{ (int) $infoKontrak['sisa_hari'] }}</div>
                <div class="cd-unit">hari lagi</div>
            </div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" style="width: {{ $infoKontrak['progress'] }}%"></div>
                </div>
                <div class="progress-labels">
                    <span>Mulai</span>
                    <span>{{ (int) $infoKontrak['progress'] }}% berlalu</span>
                    <span>Selesai</span>
                </div>
                
                {{-- Logic Tombol & Peringatan Perpanjangan Sewa --}}
                @if($infoKontrak['hampir_habis'])
                    <div class="cd-warning">
                        ⚠️ Kontrak hampir berakhir, segera lakukan perpanjangan.
                    </div>
                    
                    @if(($kontrakAktif->status_perpanjangan ?? '') === 'menunggu_persetujuan')
                        <div style="margin-top: 12px; background: #eff6ff; color: #1e40af; border: 1px dashed #bfdbfe; padding: 10px; border-radius: var(--r-sm); font-size: 11px; text-align: center; font-weight: 600;">
                            ⏳ Pengajuan perpanjangan sedang ditinjau
                        </div>
                    @else
                        <form action="{{ route('penghuni.kontrak.perpanjang', $kontrakAktif->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan perpanjangan sewa untuk unit ini?')">
                            @csrf
                            <button type="submit" class="btn-renew">
                                <i class="ri-refresh-line"></i> Ajukan Perpanjang Sewa
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="stats-row">
        <div class="stat-tile">
            <div class="st-accent accent-green"></div>
            <div class="st-icon icon-green"><i class="ri-money-dollar-circle-line"></i></div>
            {{-- Menggunakan key total_bayar yang valid dengan fallback --}}
            <div class="st-val">Rp {{ number_format(($statsPersonal['total_bayar'] ?? 0) / 1000000, 1, ',', '.') }}jt</div>
            <div class="st-lbl">Total Dibayar</div>
        </div>
        <div class="stat-tile">
            <div class="st-accent accent-amber"></div>
            <div class="st-icon icon-amber"><i class="ri-file-warning-line"></i></div>
            <div class="st-val">{{ $tagihanBelumLunas ?? 0 }}</div>
            <div class="st-lbl">Tagihan Belum Lunas</div>
        </div>
        <div class="stat-tile">
            <div class="st-accent accent-cream"></div>
            <div class="st-icon icon-cream"><i class="ri-time-line"></i></div>
            <div class="st-val">{{ number_format($statsPersonal['lama_menghuni'] ?? 0, 1, ',', '.') }} bln</div>
            <div class="st-lbl">Lama Menghuni</div>
        </div>
        <div class="stat-tile">
            <div class="st-accent accent-blue"></div>
            <div class="st-icon icon-blue"><i class="ri-tools-line"></i></div>
            <div class="st-val">{{ $statsPersonal['total_laporan'] ?? 0 }}</div>
            <div class="st-lbl">Total Laporan</div>
        </div>
    </div>

    {{-- Main Grid: Tagihan + Laporan --}}
    <div class="main-grid">

        {{-- Tagihan Panel --}}
        <div class="panel">
            <div class="panel-head">
                <div class="ph-left">
                    <span class="ph-dot"></span>
                    Tagihan Saya
                    @if(($tagihanBelumLunas ?? 0) > 0)
                    <span class="notif-dot">{{ $tagihanBelumLunas }}</span>
                    @endif
                </div>
                <a href="{{ route('penghuni.tagihan.index') }}">Lihat semua →</a>
            </div>

            @forelse($tagihanTerbaru as $t)
            <a href="{{ route('penghuni.tagihan.show', $t->id) }}" class="tagihan-row">
                <div class="tr-month">
                    <div class="m">{{ $t->jatuh_tempo->translatedFormat('M') }}</div>
                    <div class="d">{{ $t->jatuh_tempo->format('d') }}</div>
                </div>
                <div class="tr-body">
                    <div class="tr-name">{{ $t->periode }}</div>
                    <div class="tr-due">
                        Jatuh tempo {{ $t->jatuh_tempo->translatedFormat('d M Y') }}
                        @if($t->jatuh_tempo->isPast() && $t->status === 'belum_bayar')
                        · <span style="color:#e53e3e;font-weight:600;">{{ (int) $t->jatuh_tempo->diffInDays(now()) }}h terlambat</span>
                        @endif
                    </div>
                </div>
                <div class="tr-right">
                    <div class="tr-amount">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</div>
                    @if($t->status === 'lunas')
                        <span class="badge badge-ok">Lunas</span>
                    @elseif($t->status === 'menunggu_verifikasi')
                        <span class="badge badge-warn">Menunggu</span>
                    @else
                        <span class="badge badge-danger">Belum Bayar</span>
                    @endif
                </div>
            </a>
            @empty
            <div class="empty-panel">
                <div class="ep-icon"><i class="ri-bill-line"></i></div>
                <p>Belum ada tagihan tercatat</p>
            </div>
            @endforelse
        </div>

        {{-- Laporan Panel --}}
        <div class="panel">
            <div class="panel-head">
                <div class="ph-left">
                    <span class="ph-dot orange"></span>
                    Laporan Kerusakan
                </div>
                <a href="{{ route('penghuni.laporan.create') }}">+ Lapor →</a>
            </div>

            @forelse($laporanAktif as $l)
            <div class="laporan-row">
                <div class="lr-top">
                    <div class="lr-title">{{ Str::limit($l->judul, 50) }}</div>
                    @if($l->status === 'in_progress')
                        <span class="status-pill pill-blue">Ditangani</span>
                    @elseif($l->status === 'open')
                        <span class="status-pill pill-orange">Open</span>
                    @else
                        <span class="status-pill pill-red">{{ ucfirst($l->status) }}</span>
                    @endif
                </div>
                <div class="lr-meta">
                    {{ ucfirst($l->kategori) }} · {{ $l->created_at->diffForHumans() }}
                </div>
            </div>
            @empty
            <div class="empty-panel">
                <div class="ep-icon"><i class="ri-tools-line"></i></div>
                <p>Tidak ada laporan aktif saat ini</p>
                <a href="{{ route('penghuni.laporan.create') }}" class="btn-sm-green">
                    <i class="ri-add-line"></i> Buat Laporan
                </a>
            </div>
            @endforelse
        </div>

    </div>{{-- .main-grid --}}

    @else
    {{-- No contract state --}}
    <div class="no-contract">
        <div class="nc-icon"><i class="ri-home-line"></i></div>
        <h3>Belum Ada Kontrak Aktif</h3>
        <p>Anda belum memiliki kontrak sewa yang aktif. Silakan hubungi pengelola untuk informasi lebih lanjut.</p>
    </div>
    @endif

</div>
@endsection