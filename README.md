# ASRI — Sistem Pengelolaan Rusun Jakarta

Aplikasi web untuk mengelola rumah susun (rusun): calon penghuni mencari unit dan mengajukan sewa secara online, penghuni membayar tagihan dan melaporkan kerusakan, dan admin mengelola semuanya dari satu dashboard.

Halaman depannya menampilkan **gedung rusun 3D interaktif yang dibangun dari data unit asli**: setiap jendela adalah satu unit, hijau berarti kosong, kuning berarti dihuni. Jendela bisa diklik untuk membuka detail unitnya.

## Fitur

**Calon penghuni**
- Cari unit berdasarkan wilayah, harga, dan ketersediaan
- Ajukan sewa dengan unggah KTP, KK, dan slip gaji
- Jadwalkan survei unit, pantau status pengajuan

**Penghuni**
- Bayar tagihan bulanan lewat Midtrans (sandbox)
- Laporkan kerusakan dan pantau penanganannya
- Ajukan perpanjangan kontrak atau pengunduran diri

**Admin**
- Kelola unit, pengajuan, kontrak, tagihan, jadwal survei, dan berita
- Statistik pendapatan dan okupansi per wilayah
- Notifikasi otomatis ke penghuni di setiap perubahan status

## Teknologi

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Database | PostgreSQL (Neon, serverless) — juga jalan di MySQL untuk lokal |
| Frontend | Blade, Tailwind CSS 4, Vite |
| 3D & animasi | Three.js (gedung 3D, bloom, bayangan), GSAP + ScrollTrigger, Lenis |
| Pembayaran | Midtrans Snap |
| Hosting | Vercel Container Runtime (FrankenPHP) |

## Akun demo

| Peran | Email | Password |
|---|---|---|
| Admin | `admin123@gmail.com` | `Admin123` |
| Penghuni | `penghuni@asri.test` | `Penghuni123` |
| Calon penghuni | `calon@asri.test` | `Calon123` |

Semua nama, NIK, dan alamat di data demo adalah fiktif.

## Menjalankan di komputer sendiri

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed     # mengisi data demo
npm run build
php artisan serve
```

## Deploy ke Vercel + Neon

1. Buat database di [Neon](https://neon.tech), lalu jalankan migrasi dan data demo dari komputer lokal dengan kredensial Neon:
   `php artisan migrate:fresh --seed --force`
2. Isi Environment Variables di Vercel (lihat daftar di `Dockerfile.vercel` dan `.env.example`): `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, dan akun demo.
3. Deploy: `npx vercel deploy --prod`. Vercel membangun `Dockerfile.vercel` (FrankenPHP + aset Vite) dan menjalankannya di region Singapura (`sin1`).

Catatan: filesystem Vercel hanya-baca, jadi file unggahan disimpan sementara di `/tmp` dan hilang saat instance di-recycle. Untuk penyimpanan permanen, gunakan object storage yang kompatibel dengan S3.
