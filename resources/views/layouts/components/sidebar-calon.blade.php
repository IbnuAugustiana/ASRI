<div class="nav-section-label">Utama</div>

<a href="{{ route('calon.dashboard') }}" class="nav-item {{ request()->routeIs('calon.dashboard') ? 'active' : '' }}">
    <i class="ri-dashboard-line"></i> Dashboard
</a>

<div class="nav-section-label">Sewa</div>

<a href="{{ route('public.units') }}" class="nav-item {{ request()->routeIs('public.units') ? 'active' : '' }}">
    <i class="ri-search-line"></i> Cari Unit
</a>

<a href="{{ route('calon.pengajuan.index') }}" class="nav-item {{ request()->routeIs('calon.pengajuan.*') ? 'active' : '' }}">
    <i class="ri-file-list-3-line"></i> Pengajuan Saya
</a>

{{-- Menu Jadwal Survei Mandiri --}}
{{-- Menu ini sekarang selalu mengarah ke halaman index jadwal tanpa butuh ID pengajuan --}}
<a href="{{ route('calon.survei.index') }}" class="nav-item {{ request()->routeIs('calon.survei.*') ? 'active' : '' }}">
    <i class="ri-calendar-event-line"></i> Jadwal Survei
    
    @php
        // Menampilkan badge jumlah survei yang masih pending untuk user tersebut
        $pendingCount = auth()->user()->jadwalSurveis()->where('status', 'pending')->count();
    @endphp
    
    @if($pendingCount > 0)
        <span class="badge" style="background-color: #f1c40f; color: #000; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: auto;">
            {{ $pendingCount }}
        </span>
    @endif
</a>

<div class="nav-section-label">Informasi</div>

<a href="{{ route('notifikasi.index') }}" class="nav-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
    <i class="ri-notification-3-line"></i> Notifikasi
</a>

<a href="{{ route('public.news') }}" class="nav-item {{ request()->routeIs('public.news') ? 'active' : '' }}">
    <i class="ri-newspaper-line"></i> Berita
</a>