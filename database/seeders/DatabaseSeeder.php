<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Isi database dengan data demo portofolio.
     * Jalankan pada database kosong: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}
