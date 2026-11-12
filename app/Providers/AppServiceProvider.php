<?php

namespace App\Providers;

use App\Models\Notifikasi;
use App\Support\TowerData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Di produksi (Vercel) semua URL dibuat https agar aset & form tidak terblokir (mixed content)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Notifikasi navbar: dihitung SEKALI per request (bukan per view/partial),
        // penting agar tidak membanjiri database cloud dengan query.
        View::composer('*', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $notifData = once(fn () => Notifikasi::where('user_id', Auth::id())
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get());

            $view->with([
                'navbarNotifications' => $notifData->take(5),
                'unreadCount'         => $notifData->count(),
            ]);
        });

        // Gedung 3D di panel kiri halaman login/daftar
        View::composer('components.auth-shell', function ($view) {
            $view->with('towerData', once(fn () => TowerData::payload(TowerData::all(), 'city')));
        });
    }
}
