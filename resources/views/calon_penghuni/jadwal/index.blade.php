@extends('layouts.app')

@section('title', 'Cari Unit Rusun')

@section('content')
{{-- Bagian Header & Filter (Hijau Gelap) --}}
<div style="background:var(--green-900); padding: 60px 5%; margin: -25px -25px 30px -25px; border-radius: 0 0 30px 30px;">
    <div class="container" style="padding:0">        
        <form method="GET" style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:12px; align-items:end;">
            <div>
                <label style="font-size:12px; color:rgba(255,255,255,0.75); font-weight:600; display:block; margin-bottom:6px">Kata Kunci</label>
                <input type="text" name="search" placeholder="Gedung, blok, alamat..." value="{{ request('search') }}" style="width:100%; padding:12px 16px; border-radius:10px; border:none; font-size:14px; background:white;">
            </div>
            <div>
                <label style="font-size:12px; color:rgba(255,255,255,0.75); font-weight:600; display:block; margin-bottom:6px">Wilayah</label>
                <select name="wilayah" style="width:100%; padding:12px 16px; border-radius:10px; border:none; font-size:14px; background:white; appearance: none;">
                    <option value="">Semua Wilayah</option>
                    @foreach($wilayahs as $w)
                    <option value="{{ $w }}" {{ request('wilayah')==$w?'selected':'' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px; color:rgba(255,255,255,0.75); font-weight:600; display:block; margin-bottom:6px">Harga Maks. (Rp)</label>
                <input type="number" name="harga_max" placeholder="Contoh: 1500000" value="{{ request('harga_max') }}" style="width:100%; padding:12px 16px; border-radius:10px; border:none; font-size:14px; background:white;">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" style="background:var(--green-400); color:white; border:none; padding:12px 25px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; transition: 0.3s;" onmouseover="this.style.background='var(--green-500)'" onmouseout="this.style.background='var(--green-400)'">
                    <i class="ri-search-line"></i> Cari
                </button>
            </div>
        </form>

        @if(request()->anyFilled(['search','wilayah','harga_max']))
        <div style="margin-top:15px">
            <a href="{{ route('calon.survei.index') }}" style="color:rgba(255,255,255,0.6); font-size:13px; text-decoration:none">× Reset filter</a>
            <span style="color:rgba(255,255,255,0.5); font-size:13px; margin-left:12px">{{ $units->total() }} unit tersedia untuk survei</span>
        </div>
        @endif
    </div>
</div>

{{-- Section: Jadwal Survei Saya --}}
<div style="padding: 0 5% 30px 5%;">
    <div class="container" style="padding:0">
        @if($riwayatSurvei->isNotEmpty())
        <div style="background: #f0fdf4; border: 1px solid var(--green-200); border-radius: 15px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                <h6 style="font-weight: 800; color: var(--green-900); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-calendar-todo-fill" style="color: var(--green-600); font-size: 20px;"></i> 
                    Jadwal Survei Saya
                </h6>
                <span style="font-size: 11px; background: var(--green-600); color: white; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                    {{ $riwayatSurvei->count() }} Pengajuan
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                @foreach($riwayatSurvei as $survei)
                <div style="background: white; border-radius: 12px; padding: 15px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: 0.3s;">
                    <div style="background: var(--green-50); color: var(--green-600); width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                        <i class="ri-home-gear-line"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        {{-- Nama Unit / Blok --}}
                        <div style="font-size: 13px; font-weight: 700; color: var(--green-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $survei->unit->nama_unit ?? 'Unit Dihapus' }}
                        </div>
                        
                        {{-- Nama Rusun / Gedung & Wilayah Lokasi --}}
                        <div style="font-size: 11px; color: #475569; font-weight: 500; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 4px;">
                            <i class="ri-map-pin-line" style="color: var(--green-600);"></i> 
                            <span>
                                {{ $survei->unit->gedung ?? ($survei->pengajuanSewa?->unit->gedung ?? 'Rusun') }} · 
                                <strong style="color: var(--green-700);">{{ $survei->unit->wilayah ?? ($survei->pengajuanSewa?->unit->wilayah ?? '-') }}</strong>
                            </span>
                        </div>
                        
                        {{-- Waktu Survei --}}
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                            <i class="ri-time-line"></i> {{ $survei->tanggal_survei->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    <div style="text-align: right; align-self: flex-start;">
                        @php
                            $statusConfig = match($survei->status) {
                                'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Pending'],
                                'dikonfirmasi' => ['bg' => '#dcfce7', 'text' => '#166534', 'label' => 'Ok'],
                                'selesai' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Selesai'],
                                'dibatalkan' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Batal'],
                                default => ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => $survei->status]
                            };
                        @endphp
                        <span style="font-size: 9px; font-weight: 800; color: {{ $statusConfig['text'] }}; background: {{ $statusConfig['bg'] }}; padding: 4px 8px; border-radius: 6px; text-transform: uppercase; border: 1px solid rgba(0,0,0,0.05);">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 15px; padding: 20px; text-align: center; color: #64748b; font-size: 13px;">
            <i class="ri-information-line"></i> Anda belum memiliki jadwal survei. Pilih unit di bawah untuk memulai.
        </div>
        @endif
    </div>
</div>

{{-- Grid Katalog --}}
<div style="padding:10px 5% 40px 5%">
    <div class="container" style="padding:0">
        <h5 style="font-weight: 800; color: var(--green-900); margin-bottom: 25px;">Katalog Unit Tersedia</h5>
        
        @if($units->isEmpty())
        <div style="text-align:center; padding:80px; background:white; border-radius:20px; border:1px solid #eee;">
            <i class="ri-search-line" style="font-size:48px; color:#ccc; display:block; margin-bottom:15px"></i>
            <h5 style="font-weight:700; color:var(--green-900)">Unit Tidak Ditemukan</h5>
            <p class="text-muted">Maaf, kami tidak menemukan unit yang sesuai dengan kriteria Anda.</p>
        </div>
        @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:25px; margin-bottom:32px">
            @foreach($units as $unit)
            <div class="unit-card-katalog">
                {{-- Foto Unit --}}
                <div class="card-image-wrapper">
                    @if($unit->gambar)
                        <img src="{{ Storage::url($unit->gambar) }}" class="img-fluid rounded-t-lg shadow-sm" style="width:100%; height:200px; object-fit:cover">
                    @else
                        <div class="no-image-placeholder">
                            <i class="ri-building-line"></i>
                        </div>
                    @endif
                    
                    <div class="badge-status">
                        <span class="badge-custom">Tersedia</span>
                    </div>
                </div>

                {{-- Konten Info --}}
                <div style="padding:20px">
                    <div style="font-size:11px; font-weight:800; color:var(--green-600); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px">{{ $unit->wilayah }}</div>
                    <h5 style="font-weight:700; color:var(--green-900); margin-bottom:5px">{{ $unit->nama_unit }}</h5>
                    <p style="font-size:13px; color:#777; margin-bottom:20px">
                        <i class="ri-map-pin-line"></i> {{ $unit->gedung }} · {{ $unit->luas_m2 }}m²
                    </p>

                    <div style="display:flex; align-items:center; justify-content:space-between; padding-top:15px; border-top:1px solid #f0f0f0;">
                        <div>
                            <div style="font-size:18px; font-weight:800; color:var(--green-700)">Rp {{ number_format($unit->harga_sewa,0,',','.') }}</div>
                            <div style="font-size:11px; color:#999">per bulan</div>
                        </div>
                        <a href="{{ route('calon.survei.buat', $unit->id) }}" class="btn-survei-action">
                            Jadwal Survei
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $units->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .unit-card-katalog {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #efefef;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
    }

    .unit-card-katalog:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(30,124,70,0.1);
        border-color: var(--green-200);
    }

    .card-image-wrapper {
        position: relative;
        background: #f8faf9;
    }

    .no-image-placeholder {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        color: var(--green-200);
    }

    .badge-status {
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .badge-custom {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(4px);
        color: #166534;
        font-size: 11px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .btn-survei-action {
        background: var(--green-600);
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-survei-action:hover {
        background: var(--green-800);
        color: white;
        box-shadow: 0 5px 15px rgba(30,124,70,0.3);
    }
</style>
@endsection