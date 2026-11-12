<aside class="sidebar" id="sidebar" aria-label="Menu dashboard">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
        <span class="brand__mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span>
            <span class="brand">ASRI</span>
            <span class="tagline" style="display:block">Pengelolaan rusun</span>
        </span>
    </a>

    <div class="sidebar__scroll" data-lenis-prevent>
        <div class="sidebar-user">
            <div class="avatar">
                @if (!empty($avatarUrl))
                    <img src="{{ $avatarUrl }}" alt="">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role-badge">{{ $roleLabel ?? '' }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if (auth()->user()->isAdmin())
                @include('layouts.components.sidebar-admin')
            @elseif (auth()->user()->isPenghuni())
                @include('layouts.components.sidebar-penghuni')
            @else
                @include('layouts.components.sidebar-calon')
            @endif
        </nav>
    </div>

    <div class="sidebar__city" aria-hidden="true">
        <span style="height:60%"></span><span style="height:90%"></span><span style="height:45%"></span><span style="height:75%"></span><span style="height:100%"></span><span style="height:55%"></span><span style="height:80%"></span>
    </div>
    <div class="sidebar__foot">
        <a href="{{ route('profile.edit') }}" class="sidebar__link"><i class="ri-user-settings-line" aria-hidden="true"></i> Profil saya</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar__link sidebar__link--out"><i class="ri-logout-box-r-line" aria-hidden="true"></i> Keluar</button>
        </form>
    </div>
</aside>
