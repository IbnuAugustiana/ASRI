@extends('layouts.app')

@section('title', 'Daftar Pengajuan')
@section('page-title', 'Manajemen Pengajuan Sewa')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Statistik Ringkas (Status Counts) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        @php
            $statuses = [
                'pending' => ['label' => 'Menunggu', 'icon' => 'ri-time-line', 'color' => '#f59e0b'],
                'verifikasi_dokumen' => ['label' => 'Verifikasi', 'icon' => 'ri-file-search-line', 'color' => '#3b82f6'],
                'jadwal_survei' => ['label' => 'Survei', 'icon' => 'ri-map-pin-user-line', 'color' => '#8b5cf6'],
                'diterima' => ['label' => 'Diterima', 'icon' => 'ri-checkbox-circle-line', 'color' => 'var(--green-600)'],
            ];
        @endphp

        @foreach($statuses as $key => $val)
        <div class="card" style="padding: 15px; border-bottom: 3px solid {{ $val['color'] }};">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">{{ $val['label'] }}</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--green-900);">{{ $statusCounts[$key] ?? 0 }}</div>
                </div>
                <i class="{{ $val['icon'] }}" style="font-size: 32px; color: {{ $val['color'] }}; opacity: 0.3;"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- NAVIGASI TAB MENU --}}
    <div style="display: flex; gap: 10px; border-bottom: 2px solid #e0e0e0; padding-bottom: 2px;">
        <button id="btn-tab-sewa" onclick="switchTab('sewa')" style="padding: 10px 20px; font-weight: 700; font-size: 14px; border: none; background: none; border-bottom: 3px solid var(--green-600); color: var(--green-900); cursor: pointer; transition: all 0.2s;">
            Sewa Unit Baru ({{ $pengajuans->total() }})
        </button>
        <button id="btn-tab-perpanjang" onclick="switchTab('perpanjang')" style="padding: 10px 20px; font-weight: 700; font-size: 14px; border: none; background: none; border-bottom: 3px solid transparent; color: #666; cursor: pointer; transition: all 0.2s;">
            Perpanjangan Kontrak <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">{{ $pengajuanPerpanjangan->count() }} Pending</span>
        </button>
        <button id="btn-tab-resign" onclick="switchTab('resign')" style="padding: 10px 20px; font-weight: 700; font-size: 14px; border: none; background: none; border-bottom: 3px solid transparent; color: #666; cursor: pointer; transition: all 0.2s;">
            Pengunduran Diri <span style="background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">{{ $pengajuanResign->count() }} Proses</span>
        </button>
    </div>

    {{-- KONTEN TAB 1: SEWA UNIT BARU --}}
    <div id="konten-tab-sewa" style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Filter & Search --}}
        <div class="card">
            <form action="{{ route('admin.pengajuan.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end;">
                <div class="form-group">
                    <label class="form-label">Cari Nama / Email</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Ketik nama penghuni...">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $val)
                            <option value="{{ $key }}" @selected(request('status') == $key)>{{ $val['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 15px;"><i class="ri-search-line"></i></button>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary" style="padding: 10px 15px;"><i class="ri-refresh-line"></i></a>
                </div>
            </form>
        </div>

        {{-- Tabel Pengajuan Sewa Baru --}}
        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead style="background: var(--green-50); border-bottom: 1px solid var(--green-100);">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: var(--green-900);">ID</th>
                        <th style="padding: 15px; text-align: left; color: var(--green-900);">Pemohon</th>
                        <th style="padding: 15px; text-align: left; color: var(--green-900);">Unit</th>
                        <th style="padding: 15px; text-align: center; color: var(--green-900);">Durasi</th>
                        <th style="padding: 15px; text-align: left; color: var(--green-900);">Status</th>
                        <th style="padding: 15px; text-align: left; color: var(--green-900);">Tgl Masuk</th>
                        <th style="padding: 15px; text-align: center; color: var(--green-900);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $p)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">#{{ $p->id }}</td>
                        <td style="padding: 15px;">
                            <div style="font-weight: 700; color: var(--green-900);">{{ $p->user->name }}</div>
                            <div style="font-size: 11px; color: #666;">{{ $p->user->email }}</div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="font-weight: 600;">{{ $p->unit->nama_unit }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center;">{{ $p->durasi_sewa }} Bln</td>
                        <td style="padding: 15px;">
                            <span class="badge" style="
                                @if($p->status == 'pending') background: #fef3c7; color: #92400e;
                                @elseif($p->status == 'diterima') background: var(--green-100); color: var(--green-800);
                                @elseif($p->status == 'ditolak') background: #fee2e2; color: #991b1b;
                                @else background: #e0e7ff; color: #3730a3; @endif
                                padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
                            ">
                                {{ strtoupper(str_replace('_', ' ', $p->status)) }}
                            </span>
                        </td>
                        <td style="padding: 15px; font-size: 12px; color: #666;">
                            {{ $p->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                Detail <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #666;">
                            <i class="ri-inbox-line" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            Belum ada data pengajuan sewa baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Pagination --}}
            @if($pengajuans->hasPages())
            <div style="padding: 15px; background: #fcfcfc; border-top: 1px solid #eee;">
                {{ $pengajuans->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- KONTEN TAB 2: PERPANJANGAN KONTRAK PENDING --}}
    <div id="konten-tab-perpanjang" style="display: none; flex-direction: column; gap: 24px;">
        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead style="background: #fee2e2; border-bottom: 1px solid #fca5a5;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #991b1b;">Penghuni</th>
                        <th style="padding: 15px; text-align: left; color: #991b1b;">Unit/Kamar</th>
                        <th style="padding: 15px; text-align: center; color: #991b1b;">Masa Kontrak Selesai</th>
                        <th style="padding: 15px; text-align: center; color: #991b1b;">Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanPerpanjangan as $kp)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            <div style="font-weight: 700; color: #991b1b;">{{ $kp->user->name }}</div>
                            <div style="font-size: 11px; color: #666;">{{ $kp->user->email }}</div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="font-weight: 600;">{{ $kp->unit->nama_unit }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center; color: #dc2626; font-weight: 700;">
                            {{ $kp->tanggal_selesai ? $kp->tanggal_selesai->format('d M Y') : '-' }}
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <form action="{{ route('admin.kontrak.perpanjang.proses', [$kp->id, 'setuju']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="background: var(--green-600); padding: 6px 14px; font-size: 12px;" onclick="return confirm('Setujui perpanjangan kontrak ini? Kontrak otomatis bertambah sesuai request.')">
                                        <i class="ri-checkbox-circle-line"></i> Setujui
                                    </button>
                                </form>

                                <form action="{{ route('admin.kontrak.perpanjang.proses', [$kp->id, 'tolak']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" style="background: #dc2626; padding: 6px 14px; font-size: 12px;" onclick="return confirm('Tolak perpanjangan kontrak untuk penghuni ini?')">
                                        <i class="ri-close-circle-line"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: #666;">
                            <i class="ri-checkbox-circle-line" style="font-size: 40px; display: block; margin-bottom: 10px; color: var(--green-600); opacity: 0.5;"></i>
                            Bersih! Tidak ada pengajuan perpanjangan kontrak yang pending.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- KONTEN TAB 3: PENGUNDURAN DIRI PENDING --}}
    <div id="konten-tab-resign" style="display: none; flex-direction: column; gap: 24px;">
        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead style="background: #fef3c7; border-bottom: 1px solid #fcd34d;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #92400e;">Penghuni</th>
                        <th style="padding: 15px; text-align: left; color: #92400e;">Unit / Kamar</th>
                        <th style="padding: 15px; text-align: center; color: #92400e;">Rencana Tanggal Keluar</th>
                        <th style="padding: 15px; text-align: center; color: #92400e;">Status Operasional</th>
                        <th style="padding: 15px; text-align: center; color: #92400e;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanResign as $kr)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px;">
                            <div style="font-weight: 700; color: #92400e;">{{ $kr->user->name }}</div>
                            <div style="font-size: 11px; color: #666;">{{ $kr->user->email }}</div>
                        </td>
                        <td style="padding: 15px;">
                            <span style="font-weight: 600;">{{ $kr->unit->nama_unit }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center; font-weight: 600; color: #b45309;">
                            {{ $kr->rincian_resign->tgl_keluar }}
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #fde68a;">
                                🛑 MENUNGGU VERIFIKASI
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            {{-- Inject JSON data agar dibaca JS modal pop-out --}}
                            <button type="button" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; background: #d97706; color:white; border:none;" onclick="openAdminResignModal({{ json_encode($kr) }})">
                                <i class="ri-file-list-3-line"></i> Periksa Berkas
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: #666;">
                            <i class="ri-shield-check-line" style="font-size: 40px; display: block; margin-bottom: 10px; color: #d97706; opacity: 0.5;"></i>
                            Tidak ada pengajuan pengunduran diri yang perlu diproses.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL BOX POP-OUT: VERIFIKASI RESIGN --}}
<div id="adminResignModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div class="card" style="width: 100%; max-width: 550px; background: white; padding: 0; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        
        <div style="background: #fdf2f8; padding: 15px 20px; border-bottom: 1px solid #fbcfe8; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; font-weight: 700; color: #9d174d; font-size: 16px;"><i class="ri-file-search-line"></i> Lembar Verifikasi Keluar Hunian</h5>
            <button type="button" onclick="closeAdminResignModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #9d174d;">&times;</button>
        </div>
        
        <form id="formAksiAdminResign" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div style="padding: 20px; display: flex; flex-direction: column; gap: 15px; max-height: 75vh; overflow-y: auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #fafafa; padding: 12px; border-radius: 6px; font-size: 13px;">
                    <p style="margin: 0;"><strong>Nama Penghuni:</strong><br><span id="md-nama">-</span></p>
                    <p style="margin: 0;"><strong>Unit Kamar:</strong><br><span id="md-unit">-</span></p>
                    <p style="margin: 0;"><strong>Rencana Keluar:</strong><br><span id="md-tgl-keluar" style="color: #dc2626; font-weight: 700;">-</span></p>
                    <p style="margin: 0;"><strong>Pengembalian Kunci:</strong><br><span id="md-tgl-kunci">-</span></p>
                </div>

                <div>
                    <label style="font-weight: 700; font-size: 13px; display: block; margin-bottom: 5px; color: #444;">Alasan Pengunduran Diri</label>
                    <blockquote id="md-alasan" style="background: #f3f4f6; padding: 10px; margin: 0; border-left: 4px solid #9ca3af; font-style: italic; font-size: 13px; border-radius: 0 4px 4px 0;">-</blockquote>
                </div>

                <div>
                    <label style="font-weight: 700; font-size: 13px; display: block; margin-bottom: 5px; color: #444;">Dokumen Bukti Fisik / Formulir</label>
                    <div id="md-wrapper-berkas">
                        {{-- Link download disuntik via Javascript --}}
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 5px 0;">

                <div>
                    <label style="font-weight: 700; font-size: 13px; display: block; margin-bottom: 5px; color: #444;">Tindakan Keputusan Admin <span style="color:red;">*</span></label>
                    <select name="status_keputusan" id="status_keputusan" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 14px;" required onchange="toggleCatatanTolak()">
                        <option value="disetujui">✔ Setujui & Selesaikan Kontrak (Kosongkan Unit)</option>
                        <option value="ditolak">❌ Tolak Pengajuan (Kembalikan ke Kontrak Aktif)</option>
                    </select>
                </div>

                <div id="wrapper-alasan-tolak" style="display: none;">
                    <label style="font-weight: 700; font-size: 13px; display: block; margin-bottom: 5px; color: #444;">Alasan Penolakan Berkas <span style="color:red;">*</span></label>
                    <textarea name="catatan_admin" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 13px; min-height: 80px;" placeholder="Tulis alasan penolakan jika dokumen tidak valid atau belum lengkap..."></textarea>
                </div>
            </div>

            <div style="background: #f9fafb; padding: 12px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeAdminResignModal()" style="padding: 8px 16px; font-size: 13px; background: #6b7280; color: white; border: none;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; background: #d97706; color: white; border: none; font-weight: 600;">Simpan Keputusan</button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT LOGIC SWITCH TAB & MODAL HANDLER --}}
@push('scripts')
<script>
    function switchTab(jenis) {
        const tabSewa = document.getElementById('konten-tab-sewa');
        const tabPerpanjang = document.getElementById('konten-tab-perpanjang');
        const tabResign = document.getElementById('konten-tab-resign');
        
        const btnSewa = document.getElementById('btn-tab-sewa');
        const btnPerpanjang = document.getElementById('btn-tab-perpanjang');
        const btnResign = document.getElementById('btn-tab-resign');

        // Sembunyikan semua tab & normalkan button
        tabSewa.style.display = 'none';
        tabPerpanjang.style.display = 'none';
        tabResign.style.display = 'none';
        
        btnSewa.style.borderBottom = '3px solid transparent';
        btnSewa.style.color = '#666';
        btnPerpanjang.style.borderBottom = '3px solid transparent';
        btnPerpanjang.style.color = '#666';
        btnResign.style.borderBottom = '3px solid transparent';
        btnResign.style.color = '#666';

        // Aktifkan tab terpilih
        if (jenis === 'sewa') {
            tabSewa.style.display = 'flex';
            btnSewa.style.borderBottom = '3px solid var(--green-600)';
            btnSewa.style.color = 'var(--green-900)';
        } else if (jenis === 'perpanjang') {
            tabPerpanjang.style.display = 'flex';
            btnPerpanjang.style.borderBottom = '3px solid #dc2626';
            btnPerpanjang.style.color = '#991b1b';
        } else if (jenis === 'resign') {
            tabResign.style.display = 'flex';
            btnResign.style.borderBottom = '3px solid #d97706';
            btnResign.style.color = '#92400e';
        }
    }

    // Modal Operasional Pengunduran Diri
    function openAdminResignModal(data) {
        document.getElementById('md-nama').innerText = data.user.name;
        document.getElementById('md-unit').innerText = data.unit.nama_unit;
        document.getElementById('md-tgl-keluar').innerText = data.rincian_resign.tgl_keluar;
        document.getElementById('md-tgl-kunci').innerText = data.rincian_resign.tgl_kunci;
        document.getElementById('md-alasan').innerText = data.rincian_resign.alasan;

        // Atur action routing penanganan submit keputusan (arahkan ke route backend proses keputusan)
      
        document.getElementById('formAksiAdminResign').action = "{{ route('admin.kontrak.resign.proses', ':id') }}".replace(':id', data.id);

        // Render file attachment pendukung formulir
        const wrapperBerkas = document.getElementById('md-wrapper-berkas');
        if (data.rincian_resign.berkas) {
            wrapperBerkas.innerHTML = `
                <a href="/storage/${data.rincian_resign.berkas}" target="_blank" style="display: inline-block; padding: 8px 12px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">
                    <i class="ri-file-download-line"></i> Lihat / Unduh Dokumen Formulir Resign
                </a>
            `;
        } else {
            wrapperBerkas.innerHTML = `<span style="color: #dc2626; font-size: 13px; font-weight:600;"><i class="ri-error-warning-line"></i> Berkas tidak diunggah / tidak ada</span>`;
        }

        document.getElementById('adminResignModal').style.display = 'flex';
    }

    function closeAdminResignModal() {
        document.getElementById('adminResignModal').style.display = 'none';
    }

    function toggleCatatanTolak() {
        const status = document.getElementById('status_keputusan').value;
        const wrapper = document.getElementById('wrapper-alasan-tolak');
        const textarea = wrapper.querySelector('textarea');
        
        if (status === 'ditolak') {
            wrapper.style.display = 'block';
            textarea.setAttribute('required', 'required');
        } else {
            wrapper.style.display = 'none';
            textarea.removeAttribute('required');
        }
    }
</script>
@endpush
@endsection