<?php

/*
|--------------------------------------------------------------------------
| Info Portofolio
|--------------------------------------------------------------------------
|
| Ditampilkan di footer situs publik. Isi lewat .env (lokal) atau
| Environment Variables di Vercel. Kosongkan jika tidak ingin ditampilkan.
|
*/

return [
    'author'   => env('PORTFOLIO_AUTHOR'),
    'linkedin' => env('PORTFOLIO_LINKEDIN'),
    'github'   => env('PORTFOLIO_GITHUB'),

    /*
    | Tombol "coba akun demo" di halaman login (muncul hanya jika diisi).
    */
    'demo_accounts' => array_values(array_filter([
        ['label' => 'Admin', 'icon' => 'ri-shield-user-line', 'email' => env('DEMO_ADMIN_EMAIL'), 'password' => env('DEMO_ADMIN_PASSWORD')],
        ['label' => 'Penghuni', 'icon' => 'ri-home-heart-line', 'email' => env('DEMO_PENGHUNI_EMAIL'), 'password' => env('DEMO_PENGHUNI_PASSWORD')],
        ['label' => 'Calon penghuni', 'icon' => 'ri-user-search-line', 'email' => env('DEMO_CALON_EMAIL'), 'password' => env('DEMO_CALON_PASSWORD')],
    ], fn ($a) => $a['email'] && $a['password'])),
];
