<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#141A46">
    <title>@yield('title', 'Dashboard') | ASRI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
@php
    $me = auth()->user();
    $avatarUrl = ($me->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($me->foto_profil))
        ? Storage::url($me->foto_profil) : null;
    $roleLabel = $me->isAdmin() ? 'Admin' : ($me->isPenghuni() ? 'Penghuni' : 'Calon penghuni');
@endphp
<body class="app">
    <div class="app__bg" aria-hidden="true"><i></i><i></i><i></i></div>

    @include('layouts.components.sidebar', ['avatarUrl' => $avatarUrl, 'roleLabel' => $roleLabel])
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <div class="main-content">
        <header class="topbar">
            <button class="topbar__toggle" type="button" data-sidebar-toggle aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                <i class="ri-menu-2-line" aria-hidden="true"></i>
            </button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>

            <div class="topbar-right">
                <a href="{{ route('home') }}" class="topbar__site"><i class="ri-global-line" aria-hidden="true"></i><span>Lihat situs</span></a>

                <a href="{{ route('notifikasi.index') }}" class="notif-btn" aria-label="Notifikasi{{ ($unreadCount ?? 0) ? ', ' . $unreadCount . ' belum dibaca' : '' }}">
                    <i class="ri-notification-3-line" aria-hidden="true"></i>
                    @if (($unreadCount ?? 0) > 0)
                        <span class="count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>

                <div class="user-menu" data-user-menu>
                    <button type="button" class="user-menu__btn" aria-haspopup="true" aria-expanded="false">
                        <span class="user-menu__avatar">
                            @if ($avatarUrl)<img src="{{ $avatarUrl }}" alt="">@else{{ strtoupper(substr($me->name, 0, 1)) }}@endif
                        </span>
                        <span class="user-menu__name">{{ $me->name }}</span>
                        <i class="ri-arrow-down-s-line" aria-hidden="true"></i>
                    </button>
                    <div class="user-menu__panel" role="menu">
                        <a href="{{ route('profile.edit') }}" role="menuitem"><i class="ri-user-settings-line" aria-hidden="true"></i> Profil saya</a>
                        <a href="{{ route('notifikasi.index') }}" role="menuitem"><i class="ri-notification-3-line" aria-hidden="true"></i> Notifikasi</a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"><i class="ri-logout-box-r-line" aria-hidden="true"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-body" id="konten">
            @if (session('success') && is_string(session('success')))
                <div class="alert alert-success" role="status"><i class="ri-checkbox-circle-line" aria-hidden="true"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error" role="alert"><i class="ri-error-warning-line" aria-hidden="true"></i> {{ session('error') }}</div>
            @endif
            @if (session('info'))
                <div class="alert alert-info" role="status"><i class="ri-information-line" aria-hidden="true"></i> {{ session('info') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-info" role="status"><i class="ri-alert-line" aria-hidden="true"></i> {{ session('warning') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
