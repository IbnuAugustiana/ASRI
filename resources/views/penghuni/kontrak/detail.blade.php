@extends('layouts.app')

{{-- Mengirimkan judul halaman ke navbar atas di layouts.app --}}
@section('title', 'Detail Kontrak Sewa')

@section('content')
<div class="kontrak-wrapper">
    <div class="container-kontrak">
        
        <div class="header-page-info">
            <p>Kelola dan pantau masa aktif hunian Anda di Rusunawa dengan mudah.</p>
        </div>

        @if(!$kontrak)
            <div class="alert-custom alert-danger-custom" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; border: 1px solid #fca5a5;">
                <i class="ri-error-warning-line"></i> Anda belum memiliki kontrak sewa yang aktif saat ini.
            </div>
        @else
            {{-- Alert Flash Session jika backend mengirimkan pesan sukses/gagal --}}
            @if(session('success'))
                <div style="background: #eef5ed; color: #2d5a27; padding: 12px 16px; border-radius: 8px; border: 1px solid #bcd4bc; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
                    <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card-kontrak">
                
                <div class="card-kontrak-header">
                    <div class="unit-info">
                        <span class="label-unit">Unit Hunian Anda</span>
                        <h3>{{ $kontrak->unit->gedung ?? 'Nama Rusunawa' }}</h3>
                        <p class="unit-detail-text">
                            <span><i class="ri-map-pin-line"></i> {{ $kontrak->unit->wilayah ?? 'Lokasi' }}</span> • 
                            <span>Blok {{ $kontrak->unit->blok ?? '-' }}</span> • 
                            <span>No. {{ $kontrak->unit->nama_unit ?? '-' }}</span>
                        </p>
                    </div>
                   <div class="status-badge">
    @if($kontrak->status_perpanjangan == 'menunggu_persetujuan')
        <span class="badge-status" style="background: #fff3cd; color: #856404;">⏳ Menunggu Persetujuan Perpanjangan</span>
    @elseif($kontrak->status_perpanjangan == 'mengundurkan_diri')
        <span class="badge-status" style="background: #fee2e2; color: #991b1b;">🛑 Pengajuan Resign Sedang Diverifikasi</span>
    @elseif($kontrak->status_perpanjangan == 'selesai')
        <span class="badge-status" style="background: #d1fae5; color: #065f46;">✅ Kontrak Berakhir (Selesai)</span>
    @else
        <span class="badge-status" style="background: #dbeafe; color: #1e40af;">🟢 Kontrak Aktif</span>
    @endif
</div>

@if($kontrak->status_perpanjangan == 'ditolak')
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px solid #fecaca;">
        <strong>Informasi Admin:</strong> Pengajuan Anda ditolak. Silakan hubungi admin atau cek notifikasi untuk detailnya.
    </div>
@endif
</div>

@if($kontrak->status_perpanjangan == 'ditolak')
    <div class="alert alert-danger" style="margin-top: 15px;">
        <strong>Pengajuan Ditolak:</strong> {{ $kontrak->catatan_admin ?? 'Silakan hubungi pengelola untuk informasi lebih lanjut.' }}
    </div>
@endif
                    </div>
                </div>
                
                <hr class="divider">
                
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Tanggal Mulai Sewa</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($kontrak->tanggal_mulai)->format('d M Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Kontrak Berakhir</span>
                        <span class="info-value font-deadline">{{ \Carbon\Carbon::parse($kontrak->tanggal_selesai)->format('d M Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Biaya Sewa Bulanan</span>
                        <span class="info-value price-tag">Rp {{ number_format($kontrak->harga_per_bulan, 0, ',', '.') }} <small>/ bulan</small></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sisa Masa Sewa</span>
                        <span class="badge-countdown {{ $sisaHari <= 7 ? 'countdown-danger' : ($sisaHari <= 30 ? 'countdown-warning' : 'countdown-success') }}">
                            {{ $sisaHari > 0 ? floor($sisaHari) . ' Hari Lagi' : 'Kontrak Habis' }}
                        </span>
                    </div>
                </div>

                {{-- SISI AKSI UTAMA (GRID DUA SUB-FITUR) --}}
                <div class="action-section-grid">
                    
                    {{-- KOLOM KIRI: FORM PERPANJANGAN --}}
                    <div class="action-section">
                        <div class="form-header-title" style="margin-bottom: 12px;">
                            <h5 style="margin: 0; color: var(--primary-green); font-size: 0.95rem; font-weight: 700;"><i class="ri-refresh-line"></i> Perpanjang Kontrak</h5>
                            <p style="margin: 3px 0 0 0; font-size: 0.8rem; color: #718096;">Ajukan penambahan masa sewa unit rusun Anda.</p>
                        </div>

                        <form action="{{ route('penghuni.kontrak.perpanjang', $kontrak->id) }}" method="POST" class="form-perpanjangan" id="formPerpanjangan">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label-custom">Durasi Perpanjangan</label>
                                <select class="form-control-custom" name="durasi" required {{ $kontrak->status_perpanjangan == 'mengundurkan_diri' ? 'disabled' : '' }}>
                                    @for ($i = 1; $i <= 24; $i++)
                                        @if ($i == 12)
                                            <option value="12" selected>12 Bulan (1 Tahun) - Rekomendasi</option>
                                        @elseif ($i == 24)
                                            <option value="24">24 Bulan (2 Tahun)</option>
                                        @else
                                            <option value="{{ $i }}">{{ $i }} Bulan</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="form-check-custom mb-3">
                                <input type="checkbox" id="setujuSyarat" required {{ $kontrak->status_perpanjangan == 'mengundurkan_diri' ? 'disabled' : '' }}>
                                <label for="setujuSyarat">Saya menyetujui seluruh syarat dan ketentuan perpanjangan sewa Rusunawa.</label>
                            </div>

                            <button type="submit" class="btn-submit-perpanjangan" {{ $kontrak->status_perpanjangan == 'mengundurkan_diri' ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                <i class="ri-refresh-line"></i> Kirim Pengajuan Perpanjangan
                            </button>
                        </form>
                    </div>

                    {{-- KOLOM KANAN: OPSI MENGUNDURKAN DIRI --}}
                    <div class="action-section resign-box">
                        <div class="resign-info">
                            <h5><i class="ri-logout-box-r-line"></i> Mengundurkan Diri</h5>
                            <p>Ingin berhenti berlangganan sewa hunian rusunawa sebelum atau saat masa kontrak berakhir?</p>
                        </div>
                        
                        @if($kontrak->status_perpanjangan == 'mengundurkan_diri')
                            <div style="background: #fff5f5; color: #e53e3e; padding: 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-align: center; border: 1px solid #fed7d7;">
                                <i class="ri-error-warning-line"></i> Berkas pengunduran diri Anda telah dikirim dan sedang ditinjau oleh Admin.
                            </div>
                        @else
                            {{-- Tombol pemicu Modal Pop-out --}}
                            <button type="button" class="btn-submit-resign" onclick="openResignModal()">
                                <i class="ri-error-warning-line"></i> Ajukan Keluar / Berhenti Sewa
                            </button>
                        @endif
                    </div>

                </div>

            </div>
        @endif
    </div>
</div>

{{-- POP-OUT MODAL KONFIRMASI MENGUNDURKAN DIRI --}}
@if($kontrak && $kontrak->status_perpanjangan != 'mengundurkan_diri')
<div id="resignModal" class="modal-custom-backdrop" style="display: none;">
    <div class="modal-custom-content">
        <div class="modal-custom-header">
            <h5><i class="ri-error-warning-line"></i> Formulir Pengunduran Diri</h5>
            <button type="button" class="btn-close-modal" onclick="closeResignModal()">&times;</button>
        </div>
        
        <form action="{{ route('penghuni.kontrak.resign', $kontrak->id) }}" method="POST" enctype="multipart/form-data" id="formResignSewa">
            @csrf
            <div class="modal-custom-body">
                <p style="font-size: 0.82rem; color: #666; margin-bottom: 15px; line-height: 1.4;">Silakan lengkapi berkas dan rincian di bawah ini untuk mengajukan permohonan keluar resmi dari hunian.</p>
                
                {{-- Input 1: Alasan --}}
                <div class="form-group mb-3">
                    <label class="form-label-custom">Alasan Mengundurkan Diri <span style="color:red;">*</span></label>
                    <textarea class="form-control-custom" name="alasan_keluar" rows="3" placeholder="Contoh: Pindah lokasi kerja, beralih ke rumah pribadi, dll..." required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="mb-3">
                    {{-- Input 2: Tanggal Keluar --}}
                    <div class="form-group">
                        <label class="form-label-custom">Tanggal Keluar <span style="color:red;">*</span></label>
                        <input type="date" class="form-control-custom" name="tanggal_keluar" required>
                    </div>
                    {{-- Input 3: Tanggal Pengembalian Kunci --}}
                    <div class="form-group">
                        <label class="form-label-custom">Pengembalian Kunci <span style="color:red;">*</span></label>
                        <input type="date" class="form-control-custom" name="tanggal_kembali_kunci" required>
                    </div>
                </div>

                {{-- Input 4: Upload Dokumen Formulir --}}
                <div class="form-group mb-3">
                    <label class="form-label-custom">Upload Form Pengunduran Diri (.pdf/.jpg/.png) <span style="color:red;">*</span></label>
                    <input type="file" class="form-control-custom" name="dokumen_pengunduran" accept=".pdf,.jpg,.jpeg,.png" required style="padding: 5px 10px;">
                    <small style="color: #718096; font-size: 0.75rem; display: block; margin-top: 4px;">Pastikan dokumen sudah diisi dan ditandatangani.</small>
                </div>
            </div>
            
            <div class="modal-custom-footer">
                <button type="button" class="btn-secondary-modal" onclick="closeResignModal()">Batal</button>
                {{-- PERBAIKAN DI SINI: Ditambahkan type="submit" eksplisit & ganti warna background manual agar tidak bentrok style class --}}
                <button type="submit" style="width: auto; padding: 8px 20px; margin: 0; background-color: #dc2626; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 0.92rem; cursor: pointer;">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // Validasi Submit Form Perpanjangan
    document.getElementById('formPerpanjangan')?.addEventListener('submit', function(e) {
        if(!confirm('Apakah Anda yakin data sudah benar dan ingin mengajukan perpanjangan kontrak sekarang?')) {
            e.preventDefault();
        }
    });

    // Kontrol Handler Pop-out Modal Resign
    function openResignModal() {
        document.getElementById('resignModal').style.display = 'flex';
    }

    function closeResignModal() {
        document.getElementById('resignModal').style.display = 'none';
    }

    // Menutup modal otomatis jika mengeklik area background hitam samar
    window.onclick = function(event) {
        const modal = document.getElementById('resignModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Validasi Submit Keluar Kontrak
    document.getElementById('formResignSewa')?.addEventListener('submit', function(e) {
        if(!confirm('Apakah Anda yakin formulir pengunduran diri sudah lengkap dan siap diajukan ke Admin?')) {
            e.preventDefault();
        }
    });
</script>

<style>
    :root {
        --primary-green: #2b3480;
        --light-green: #eef1fb;
        --accent-green: #0e8f5b;
        --cream-bg: #f8f9fe;
        --cream-dark: #e8ebf9;
        --text-dark: #151a3d;
    }

    .kontrak-wrapper {
        padding-left: 25px; 
        width: 100%;
        box-sizing: border-box;
    }

    .container-kontrak {
        max-width: 950px;
        margin: 10px 0 35px 0;
    }

    .header-page-info p {
        color: #667a66;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .card-kontrak {
        background: #ffffff;
        border: 1px solid var(--cream-dark);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 6px 20px rgba(45, 90, 39, 0.02);
    }

    .card-kontrak-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .label-unit {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #889988;
        display: block;
        margin-bottom: 2px;
    }

    .unit-info h3 {
        color: var(--primary-green);
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .unit-detail-text {
        margin: 4px 0 0 0;
        font-size: 0.88rem;
        color: #556655;
    }
    
    .unit-detail-text span {
        display: inline-block;
    }

    .divider {
        border: 0;
        border-top: 1px solid var(--cream-dark);
        margin: 20px 0;
        opacity: 0.8;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .info-item {
        background: var(--cream-bg);
        padding: 14px;
        border-radius: 10px;
        border-left: 4px solid var(--accent-green);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-label {
        font-size: 0.8rem;
        color: #778877;
        margin-bottom: 4px;
    }

    .info-value {
        font-weight: 600;
        font-size: 1.05rem;
    }

    .font-deadline {
        color: #c0392b;
    }

    .price-tag {
        color: var(--primary-green);
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-block;
    }
    .status-waiting { background: #fff3cd; color: #856404; }
    .status-active { background: #e2f0fd; color: #004085; }
    .status-resign { background: #fee2e2; color: #991b1b; }

    .badge-countdown {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 0.82rem;
        text-align: center;
        display: inline-block;
        width: max-content;
    }
    .countdown-danger { background-color: #dc3545; color: white; }
    .countdown-warning { background-color: #ffc107; color: #212529; }
    .countdown-success { background-color: var(--accent-green); color: white; }

    /* GRID UNTUK AKSI SEWA DAN RESIGN */
    .action-section-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .action-section-grid {
            grid-template-columns: 1fr;
        }
    }

    .action-section {
        background: var(--light-green);
        padding: 20px;
        border-radius: 10px;
        border: 1px dashed #bcd4bc;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .resign-box {
        background: #faf5f5;
        border: 1px dashed #e2b6b6;
    }

    .resign-info h5 {
        margin: 0 0 6px 0;
        color: #991b1b;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .resign-info p {
        margin: 0 0 15px 0;
        font-size: 0.82rem;
        color: #718096;
        line-height: 1.4;
    }

    .form-label-custom {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        font-size: 0.85rem;
        color: var(--text-dark);
    }

    .form-control-custom {
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ccd6cc;
        background-color: #fff;
        font-size: 0.88rem;
        color: var(--text-dark);
        box-sizing: border-box;
    }

    .form-check-custom {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        font-size: 0.82rem;
        margin-top: 12px;
        color: #445544;
        cursor: pointer;
    }
    .form-check-custom input {
        margin-top: 2px;
        cursor: pointer;
    }

    .btn-submit-perpanjangan {
        width: 100%;
        background-color: var(--primary-green);
        color: white;
        padding: 11px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 10px;
    }
    .btn-submit-perpanjangan:hover { background-color: var(--accent-green); }

    .btn-submit-resign {
        width: 100%;
        background-color: #dc2626;
        color: white;
        padding: 11px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-submit-resign:hover { background-color: #991b1b; }

    /* STYLE MODAL POP-OUT CUSTOM */
    .modal-custom-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        padding: 15px;
        box-sizing: border-box;
    }

    .modal-custom-content {
        background: #ffffff;
        width: 100%;
        max-width: 500px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        animation: slideDown 0.3s ease-out;
        overflow: hidden;
    }

    .modal-custom-header {
        background: #fafafa;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-custom-header h5 {
        margin: 0;
        color: #991b1b;
        font-size: 1rem;
        font-weight: 700;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #aaa;
    }
    .btn-close-modal:hover { color: #333; }

    .modal-custom-body { padding: 20px; }

    .modal-custom-footer {
        background: #fafafa;
        padding: 12px 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-secondary-modal {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.88rem;
        cursor: pointer;
    }
    .btn-secondary-modal:hover { background: #cbd5e0; }

    .mb-2 { margin-bottom: 0.5rem; }
    .mb-3 { margin-bottom: 1rem; }

    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endsection