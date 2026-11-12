@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="asri-wrapper">
    <div class="asri-container">
        
        {{-- Flash Alert System --}}
        @if (session('success'))
        <div id="asri-alert" class="toast-alert alert-success">
            <i class="ri-checkbox-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if (session('error'))
        <div id="asri-alert" class="toast-alert alert-error">
            <i class="ri-error-warning-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Top Header Section --}}
        <div class="asri-header">
            <div class="header-text">
                <h1 class="page-title">Jadwal Survei</h1>
                <p class="page-subtitle">Pantau dan konfirmasi kunjungan calon penghuni rusun.</p>
            </div>
            
            {{-- Simplified Filter Form --}}
            <form action="{{ route('admin.jadwal.index') }}" method="GET" class="filter-form">
                <select name="status" class="asri-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>🔒 Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                </select>
                
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="asri-input-date" onchange="this.form.submit()">
                
                @if(request()->filled('status') || request()->filled('tanggal'))
                    <a href="{{ route('admin.jadwal.index') }}" class="btn-reset" title="Reset Filter">
                        <i class="ri-refresh-line"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- SECTION 1: QUICK AGENDA HARI INI --}}
        <div class="asri-card-today">
            <div class="today-header">
                <i class="ri-calendar-event-line"></i>
                <span>Agenda Terkonfirmasi Hari Ini</span>
            </div>
            @if($jadwalHariIni->isEmpty())
                <p class="empty-text-white">Tidak ada jadwal survei yang dikonfirmasi untuk hari ini.</p>
            @else
                <div class="today-list">
                    @foreach($jadwalHariIni as $hariIni)
                    @php
                        $uGedung = $hariIni->unit->gedung ?? ($hariIni->pengajuanSewa?->unit->gedung ?? 'Rusun');
                        $uWilayah = $hariIni->unit->wilayah ?? ($hariIni->pengajuanSewa?->unit->wilayah ?? '-');
                        $uNama = $hariIni->unit->nama_unit ?? ($hariIni->pengajuanSewa?->unit->nama_unit ?? 'Tidak Diketahui');
                    @endphp
                    <div class="today-row">
                        <span class="time-badge"><i class="ri-time-line"></i> {{ \Carbon\Carbon::parse($hariIni->tanggal_survei)->format('H:i') }} WIB</span>
                        <div class="today-info">
                            <strong>{{ $hariIni->user?->name ?? ($hariIni->pengajuanSewa?->user?->name ?? 'Data Pemohon Hilang') }}</strong>
                            <span>Unit: {{ $uNama }} ({{ $uGedung }} · {{ $uWilayah }})</span>
                        </div>
                        <form action="{{ route('admin.jadwal.selesai', $hariIni->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-today-done">Selesai</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 2: JADWAL MENDATANG (TERDEKAT) --}}
        <div class="asri-card-upcoming">
            <div class="upcoming-header">
                <i class="ri-time-grid-line"></i>
                <span>Jadwal Survei Mendatang (Urut Terdekat)</span>
            </div>
            @if($jadwalMendatang->isEmpty())
                <p class="empty-text-muted">Tidak ada jadwal survei terkonfirmasi untuk hari-hari berikutnya.</p>
            @else
                <div class="upcoming-grid">
                    @foreach($jadwalMendatang as $mendatang)
                    @php
                        $mGedung = $mendatang->unit->gedung ?? ($mendatang->pengajuanSewa?->unit->gedung ?? 'Rusun');
                        $mWilayah = $mendatang->unit->wilayah ?? ($mendatang->pengajuanSewa?->unit->wilayah ?? '-');
                        $mNama = $mendatang->unit->nama_unit ?? ($mendatang->pengajuanSewa?->unit->nama_unit ?? 'N/A');
                    @endphp
                    <div class="upcoming-item">
                        <div class="upcoming-date-box">
                            <span class="up-day">{{ \Carbon\Carbon::parse($mendatang->tanggal_survei)->translatedFormat('d') }}</span>
                            <span class="up-month">{{ \Carbon\Carbon::parse($mendatang->tanggal_survei)->translatedFormat('M') }}</span>
                        </div>
                        <div class="upcoming-info">
                            <strong class="up-name">{{ $mendatang->user?->name ?? ($mendatang->pengajuanSewa?->user?->name ?? 'User Tidak Ditemukan') }}</strong>
                            
                            {{-- Baris Blok / Unit --}}
                            <div class="up-details-unit">
                                <i class="ri-home-4-line"></i> <span>{{ $mNama }}</span>
                            </div>
                            
                            {{-- Baris Nama Rusun & Wilayah Lokasi --}}
                            <div class="up-details-loc">
                                <i class="ri-map-pin-line"></i> 
                                <span class="loc-text">{{ $mGedung }} · <strong class="loc-highlight">{{ $mWilayah }}</strong></span>
                            </div>

                            {{-- Baris Waktu Jam --}}
                            <div class="up-details-time">
                                <i class="ri-time-line"></i> <span>Pukul {{ \Carbon\Carbon::parse($mendatang->tanggal_survei)->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 3: DATA TABEL UTAMA --}}
        <div class="asri-table-card">
            <div class="table-title-area">
                <i class="ri-file-list-3-line"></i> Daftar Seluruh Log Pengajuan Jadwal
            </div>
            <div class="table-responsive">
                <table class="asri-table">
                    <thead>
                        <tr>
                            <th>Calon Penghuni</th>
                            <th>Unit Rusun</th>
                            <th>Waktu Kunjungan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($jadwals->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center text-muted py-8">Tidak ditemukan data jadwal survei.</td>
                            </tr>
                        @else
                            @foreach($jadwals as $jadwal)
                            @php
                                $tGedung = $jadwal->unit->gedung ?? ($jadwal->pengajuanSewa?->unit->gedung ?? 'Rusun');
                                $tWilayah = $jadwal->unit->wilayah ?? ($jadwal->pengajuanSewa?->unit->wilayah ?? '-');
                                $tNama = $jadwal->unit->nama_unit ?? ($jadwal->pengajuanSewa?->unit->nama_unit ?? 'N/A');
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <span class="u-name">{{ $jadwal->user?->name ?? ($jadwal->pengajuanSewa?->user?->name ?? 'User Tidak Ditemukan') }}</span>
                                        <span class="u-email">{{ $jadwal->user?->email ?? ($jadwal->pengajuanSewa?->user?->email ?? 'Hubungan data terputus') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="unit-table-cell">
                                        <span class="unit-pill">{{ $tNama }}</span>
                                        <span class="unit-desc-sub"><i class="ri-map-pin-line"></i> {{ $tGedung }} · {{ $tWilayah }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="time-cell">
                                        <span class="t-date">{{ \Carbon\Carbon::parse($jadwal->tanggal_survei)->translatedFormat('d M Y') }}</span>
                                        <span class="t-time">Pukul {{ \Carbon\Carbon::parse($jadwal->tanggal_survei)->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $jadwal->status }}">
                                        @if($jadwal->status == 'pending') Pending
                                        @elseif($jadwal->status == 'dikonfirmasi') Terkonfirmasi
                                        @elseif($jadwal->status == 'selesai') Selesai
                                        @else Dibatalkan
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        @if($jadwal->status == 'pending')
                                            <form action="{{ route('admin.jadwal.konfirmasi', $jadwal->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-action btn-approve" title="Konfirmasi"><i class="ri-check-line"></i></button>
                                            </form>
                                            <button type="button" class="btn-action btn-cancel" onclick="openCancelModal({{ $jadwal->id }})" title="Batalkan"><i class="ri-close-line"></i></button>
                                        @elseif($jadwal->status == 'dikonfirmasi')
                                            <form action="{{ route('admin.jadwal.selesai', $jadwal->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-action btn-done" title="Set Selesai"><i class="ri-checkbox-circle-line"></i> Selesai</button>
                                            </form>
                                        @else
                                            <span class="action-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="asri-pagination">
                {{ $jadwals->links() }}
            </div>
        </div>

    </div>
</div>

{{-- MODAL BOX PEMBATALAN --}}
<div id="cancel-modal" class="modal-backdrop">
    <div class="modal-box">
        <h3 class="modal-title">Batalkan Jadwal Kunjungan</h3>
        <p class="modal-desc">Berikan alasan pembatalan singkat untuk pemohon.</p>
        
        <form id="cancel-form" method="POST" action="">
            @csrf
            <textarea name="catatan" placeholder="Tulis alasan di sini..." rows="3" required class="asri-textarea"></textarea>
            <div class="modal-actions">
                <button type="button" onclick="closeCancelModal()" class="btn-modal-back">Kembali</button>
                <button type="submit" class="btn-modal-danger">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Reset & Base Theme Integration */
    .asri-wrapper { 
        background-color: #f8fafc; 
        min-height: 100vh; 
        padding: 24px; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        color: #334155;
    }
    .asri-wrapper * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
    .asri-container { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
    
    /* Header */
    .asri-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 4px;}
    .page-title { font-size: 22px; font-weight: 700; color: #141a46; margin: 0; }
    .page-subtitle { font-size: 13px; color: #64748b; margin: 4px 0 0 0; }
    
    .filter-form { display: flex; gap: 8px; align-items: center; }
    .asri-select, .asri-input-date { border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 8px; background: #fff; font-size: 13px; color: #334155; outline: none; transition: 0.2s; }
    .asri-select:focus, .asri-input-date:focus { border-color: #141a46; }
    .btn-reset { display: inline-flex; padding: 9px; background: #e2e8f0; color: #475569; border-radius: 8px; text-decoration: none; transition: 0.2s; }
    .btn-reset:hover { background: #cbd5e1; }

    /* Compact Card Agenda Hari Ini */
    .asri-card-today { background: #141a46; color: #fff; border-radius: 12px; padding: 18px 20px; }
    .today-header { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; margin-bottom: 12px; opacity: 0.9; }
    .empty-text-white { font-size: 13px; margin: 0; opacity: 0.7; }
    .today-list { display: flex; flex-direction: column; gap: 8px; }
    .today-row { display: flex; align-items: center; background: rgba(255,255,255,0.08); padding: 10px 14px; border-radius: 8px; justify-content: space-between; }
    .time-badge { font-size: 12px; font-weight: 600; background: #fff; color: #141a46; padding: 4px 8px; border-radius: 6px; }
    .today-info { flex: 1; margin: 0 16px; display: flex; flex-direction: column; gap: 2px; }
    .today-info strong { font-size: 14px; }
    .today-info span { font-size: 12px; opacity: 0.8; }
    .btn-today-done { background: #22c55e; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-today-done:hover { background: #16a34a; }

    /* SECTION 2: REWORKED JADWAL MENDATANG - PREMIUM CLEAN LOOK */
    .asri-card-upcoming { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    .upcoming-header { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 14px; color: #141a46; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;}
    .empty-text-muted { font-size: 13px; color: #94a3b8; margin: 0; }
    .upcoming-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
    
    .upcoming-item { 
        display: flex; 
        align-items: flex-start; 
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        padding: 14px; 
        border-radius: 12px; 
        gap: 14px; 
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.01);
    }
    .upcoming-item:hover {
        transform: translateY(-2px);
        border-color: #141a46;
        box-shadow: 0 6px 16px rgba(14,56,32,0.06);
    }
    
    .upcoming-date-box { 
        background: #f0fdf4; 
        color: #141a46; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        width: 50px; 
        height: 50px; 
        border-radius: 10px; 
        flex-shrink: 0; 
        border: 1px solid #dcfce7;
    }
    .up-day { font-size: 18px; font-weight: 800; line-height: 1; }
    .up-month { font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 3px; letter-spacing: 0.5px; }
    
    .upcoming-info { display: flex; flex-direction: column; gap: 4px; min-width: 0; width: 100%; }
    .up-name { font-size: 14px; color: #141a46; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
    
    .up-details-unit, .up-details-loc, .up-details-time { 
        font-size: 12px; 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        color: #475569;
    }
    .up-details-unit i, .up-details-loc i, .up-details-time i {
        font-size: 14px;
        flex-shrink: 0;
    }
    .up-details-unit span { font-weight: 600; color: #1e293b; }
    .up-details-loc { color: #64748b; }
    .loc-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .loc-highlight { color: #141a46; font-weight: 600; }
    .up-details-time { color: #2563eb; font-weight: 500; margin-top: 1px; }

    /* Minimalist Table Card */
    .asri-table-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 0 12px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .table-title-area { padding: 0 20px 14px 20px; font-size: 14px; font-weight: 600; color: #334155; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 6px; }
    .table-responsive { width: 100%; overflow-x: auto; }
    .asri-table { width: 100%; border-collapse: collapse; text-align: left; }
    .asri-table th { padding: 12px 20px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #e2e8f0; }
    .asri-table td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }
    .asri-table tr:hover td { background: #f8fafc; }
    
    /* Cell Components */
    .user-cell { display: flex; flex-direction: column; gap: 2px; }
    .u-name { font-weight: 600; color: #141a46; }
    .u-email { font-size: 12px; color: #94a3b8; }
    
    .unit-table-cell { display: flex; flex-direction: column; gap: 4px; }
    .unit-pill { background: #f0fdf4; color: #16a34a; font-weight: 600; padding: 2px 8px; border-radius: 6px; border: 1px solid #bbf7d0; font-size: 12px; display: inline-block; width: fit-content; }
    .unit-desc-sub { font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 3px; }
    
    .time-cell { display: flex; flex-direction: column; gap: 2px; }
    .t-date { font-weight: 500; color: #334155; }
    .t-time { font-size: 12px; color: #64748b; }
    
    /* Simplified Status Badges */
    .status-badge { display: inline-flex; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; }
    .status-pending { background: #fffbeb; color: #b45309; }
    .status-dikonfirmasi { background: #f0fdf4; color: #15803d; }
    .status-selesai { background: #f1f5f9; color: #475569; }
    .status-dibatalkan { background: #fef2f2; color: #b91c1c; }

    /* Action Buttons Group */
    .actions-group { display: flex; gap: 6px; align-items: center; }
    .btn-action { border: none; padding: 6px 10px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-approve { background: #141a46; color: #fff; }
    .btn-approve:hover { background: #154d2e; }
    .btn-cancel { background: #fff; color: #ef4444; border: 1px solid #fee2e2; }
    .btn-cancel:hover { background: #fef2f2; }
    .btn-done { background: #f1f5f9; color: #475569; }
    .btn-done:hover { background: #e2e8f0; }
    .action-muted { color: #cbd5e1; font-weight: bold; padding-left: 8px; }

    .asri-pagination { padding: 16px 20px 4px 20px; display: flex; justify-content: center; }
    .toast-alert { position: fixed; top: 20px; right: 20px; background: #fff; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 8px; z-index: 999; font-size: 13px; }
    .alert-success { border-left: 4px solid #22c55e; }
    .alert-error { border-left: 4px solid #ef4444; }

    /* Modal Styling */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); backdrop-filter: blur(1px); z-index: 9999; align-items: center; justify-content: center; padding: 16px; }
    .modal-box { background: #fff; padding: 24px; border-radius: 12px; max-width: 400px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .modal-title { margin: 0 0 6px 0; color: #b91c1c; font-size: 16px; font-weight: 700; }
    .modal-desc { font-size: 12px; color: #64748b; margin: 0 0 12px 0; }
    .asri-textarea { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 13px; outline: none; resize: none; }
    .asri-textarea:focus { border-color: #ef4444; }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; }
    .btn-modal-back { background: #f1f5f9; border: none; color: #475569; font-weight: 600; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; }
    .btn-modal-danger { background: #ef4444; border: none; color: #fff; font-weight: 600; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; }
</style>

<script>
    function openCancelModal(id) {
        const modal = document.getElementById('cancel-modal');
        const form = document.getElementById('cancel-form');
        form.action = `/admin/jadwal/${id}/batalkan`;
        modal.style.display = 'flex';
    }

    function closeCancelModal() {
        document.getElementById('cancel-modal').style.display = 'none';
    }

    setTimeout(() => {
        const alert = document.getElementById('asri-alert');
        if(alert) {
            alert.style.transition = '0.3s opacity';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }
    }, 3000);
</script>
@endsection