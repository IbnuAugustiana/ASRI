<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans API Credentials
    |--------------------------------------------------------------------------
    |
    | Di sini konfigurasi kredensial Midtrans diambil langsung dari file .env
    | untuk menjaga keamanan data Production maupun Sandbox (Development).
    |
    */

    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Environment & Settings
    |--------------------------------------------------------------------------
    |
    | - is_production : Set ke false untuk mode Sandbox, true untuk Production.
    | - is_sanitized  : Mengubah karakter khusus pada string input menjadi aman.
    | - is_3ds        : Mengaktifkan fitur 3D Secure untuk transaksi kartu kredit.
    |
    */

    'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
    
    'is_sanitized' => true,
    
    'is_3ds' => true,
];