<?php

namespace Database\Seeders;

use App\Models\JadwalSurvei;
use App\Models\KontrakSewa;
use App\Models\LaporanKerusakan;
use App\Models\News;
use App\Models\Notifikasi;
use App\Models\PengajuanSewa;
use App\Models\Tagihan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data demo untuk versi portofolio. Semua nama, NIK, nomor HP, dan alamat FIKTIF.
 *
 * Akun demo:
 *   Admin          admin123@gmail.com  / Admin123
 *   Penghuni       penghuni@asri.test  / Penghuni123
 *   Calon penghuni calon@asri.test     / Calon123
 */
class DemoSeeder extends Seeder
{
    private const BLOCKS = [
        'A' => ['Rusun Asri Kemayoran', 'Jakarta Pusat', 'Jl. Asri Raya No. 1, Kemayoran', 200000],
        'B' => ['Rusun Asri Pulogadung', 'Jakarta Timur', 'Jl. Asri Raya No. 2, Pulogadung', 0],
        'C' => ['Rusun Asri Pasar Minggu', 'Jakarta Selatan', 'Jl. Asri Raya No. 3, Pasar Minggu', 100000],
        'D' => ['Rusun Asri Marunda', 'Jakarta Utara', 'Jl. Asri Raya No. 4, Cilincing', 0],
        'E' => ['Rusun Asri Cengkareng', 'Jakarta Barat', 'Jl. Asri Raya No. 5, Cengkareng', 50000],
    ];

    private const NAMES = [
        'Ahmad Fauzi', 'Siti Rahmawati', 'Budi Hartono', 'Dewi Lestari', 'Agus Setiawan', 'Rina Wulandari', 'Hendra Gunawan',
        'Maya Sari', 'Rizky Ramadhan', 'Fitri Handayani', 'Yusuf Maulana', 'Nur Aisyah', 'Bambang Susilo', 'Indah Permata',
        'Fajar Nugroho', 'Lina Marlina', 'Doni Saputra', 'Ratna Dewi', 'Arif Hidayat', 'Wulan Sari', 'Taufik Hidayat',
        'Yuni Astuti', 'Eko Prasetyo', 'Sri Mulyani', 'Andi Wijaya', 'Putri Ayu', 'Hadi Santoso', 'Novi Anggraini',
        'Iwan Kurniawan', 'Mega Puspita', 'Rudi Hermawan', 'Tari Oktaviani', 'Joko Susanto', 'Desi Ratnasari', 'Ilham Akbar',
        'Sari Purnama', 'Wahyu Pratama', 'Nina Kartini', 'Gilang Saputra', 'Ayu Lestari', 'Bayu Aji',
    ];

    public function run(): void
    {
        $now = Carbon::now();
        $sharedHash = Hash::make(Str::random(24)); // penghuni fiktif tidak bisa login

        /* ---------- Akun demo ---------- */
        $admin = User::create([
            'name' => 'Admin ASRI', 'email' => 'admin123@gmail.com', 'password' => 'Admin123',
            'role' => 'admin', 'no_hp' => '081200000001', 'is_active' => true,
        ]);
        $demoPenghuni = User::create([
            'name' => 'Rina Kartika', 'email' => 'penghuni@asri.test', 'password' => 'Penghuni123',
            'role' => 'penghuni', 'no_hp' => '081234567801', 'nik' => '3171020101900001', 'is_active' => true,
        ]);
        $demoCalon = User::create([
            'name' => 'Dimas Pratama', 'email' => 'calon@asri.test', 'password' => 'Calon123',
            'role' => 'calon_penghuni', 'no_hp' => '081234567802', 'is_active' => true,
        ]);

        /* ---------- Unit: 5 blok x 5 lantai x 4 kamar ---------- */
        $units = [];
        foreach (self::BLOCKS as $blok => [$gedung, $wilayah, $alamat, $plus]) {
            for ($lt = 1; $lt <= 5; $lt++) {
                for ($no = 1; $no <= 4; $no++) {
                    $besar = $lt >= 4;
                    $seed = ord($blok) * 31 + $lt * 7 + $no * 13;
                    $status = match (true) {
                        $seed % 23 === 0 => 'maintenance',
                        $no === 4 || $seed % 3 === 0 => 'dihuni',
                        default => 'tersedia',
                    };
                    $units[] = Unit::create([
                        'blok' => $blok,
                        'lantai' => $lt,
                        'no_kamar' => str_pad((string) $no, 2, '0', STR_PAD_LEFT),
                        'gedung' => $gedung,
                        'alamat' => $alamat,
                        'wilayah' => $wilayah,
                        'luas_m2' => $besar ? 36 : 24,
                        'harga_sewa' => ($besar ? 1200000 : 800000) + $plus,
                        'status' => $status,
                        'deskripsi' => $besar
                            ? 'Unit tipe 36 dengan dua kamar tidur, dapur, kamar mandi dalam, dan balkon jemur. Cocok untuk keluarga kecil.'
                            : 'Unit tipe 24 dengan satu kamar tidur, dapur kecil, kamar mandi dalam, dan balkon. Listrik 1.300 VA dan air PAM.',
                    ]);
                }
            }
        }
        $units = collect($units);

        /* ---------- Penghuni demo (Rina) di Blok C lantai 3 no. 02 ---------- */
        $rinaUnit = $units->first(fn ($u) => $u->blok === 'C' && $u->lantai === 3 && $u->no_kamar === '02');
        $rinaUnit->update(['status' => 'dihuni']);
        $this->kontrakLengkap($demoPenghuni, $rinaUnit, $now->copy()->subMonths(5)->startOfMonth(), 12, $admin, true);

        LaporanKerusakan::create([
            'user_id' => $demoPenghuni->id, 'unit_id' => $rinaUnit->id, 'kategori' => 'plumbing',
            'judul' => 'Keran wastafel dapur bocor', 'status' => 'in_progress',
            'deskripsi' => 'Air terus menetes dari pangkal keran wastafel dapur sejak dua hari lalu, lantai jadi basah.',
            'respon_admin' => 'Teknisi dijadwalkan datang Kamis pukul 10.00.',
            'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDay(),
        ]);
        LaporanKerusakan::create([
            'user_id' => $demoPenghuni->id, 'unit_id' => $rinaUnit->id, 'kategori' => 'listrik',
            'judul' => 'Lampu kamar mandi mati', 'status' => 'resolved',
            'deskripsi' => 'Lampu kamar mandi tidak menyala walaupun sudah diganti bohlam baru. Kemungkinan fitting rusak.',
            'respon_admin' => 'Fitting lampu sudah diganti teknisi.', 'resolved_at' => $now->copy()->subDays(20),
            'created_at' => $now->copy()->subDays(24), 'updated_at' => $now->copy()->subDays(20),
        ]);
        foreach ([
            ['Tagihan Baru Tersedia', 'Tagihan sewa bulan ' . $now->format('Y-m') . ' sudah terbit. Jatuh tempo tanggal 10.', 'warning', route('penghuni.tagihan.index', [], false)],
            ['Update Laporan Kerusakan', 'Laporan "Keran wastafel dapur bocor" sedang ditangani teknisi.', 'info', null],
            ['Pengumuman Kerja Bakti', 'Kerja bakti lingkungan diadakan Minggu pagi pukul 07.00 di halaman blok.', 'info', route('public.news', [], false)],
        ] as [$judul, $pesan, $tipe, $link]) {
            Notifikasi::create(['user_id' => $demoPenghuni->id, 'judul' => $judul, 'pesan' => $pesan, 'tipe' => $tipe, 'link' => $link, 'is_read' => false]);
        }

        /* ---------- Penghuni fiktif untuk semua unit "dihuni" ---------- */
        $names = collect(self::NAMES);
        $i = 0;
        foreach ($units->where('status', 'dihuni')->where('id', '!=', $rinaUnit->id) as $unit) {
            $name = $names[$i % $names->count()];
            $user = User::create([
                'name' => $name,
                'email' => Str::slug($name, '.') . ($i >= $names->count() ? $i : '') . '@contoh.test',
                'password' => $sharedHash,
                'role' => 'penghuni',
                'no_hp' => '0812' . str_pad((string) (3400000 + $i * 137), 8, '0', STR_PAD_LEFT),
                'nik' => '3171' . str_pad((string) (100000000000 + $i * 7919), 12, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);
            $mulai = $now->copy()->subMonths(2 + ($i % 9))->startOfMonth();
            $this->kontrakLengkap($user, $unit, $mulai, [6, 12, 24][$i % 3], $admin, false);
            $i++;
        }

        // Beberapa laporan kerusakan dari penghuni lain
        $laporan = [
            ['listrik', 'Stop kontak ruang tamu berasap', 'open', 'Muncul bau gosong dari stop kontak dekat TV saat charger dicolok.'],
            ['fasilitas', 'Lift blok macet di lantai 3', 'in_progress', 'Pintu lift tidak mau menutup sejak pagi, penghuni lansia kesulitan naik.'],
            ['struktur', 'Retak di dinding balkon', 'open', 'Ada retakan memanjang sekitar 40 cm di dinding balkon setelah hujan deras.'],
            ['plumbing', 'Saluran air kamar mandi mampet', 'resolved', 'Air menggenang di kamar mandi dan lambat sekali turun.'],
        ];
        $penghuniLain = KontrakSewa::with('user')->where('user_id', '!=', $demoPenghuni->id)->take(count($laporan))->get();
        foreach ($penghuniLain as $k => $kontrak) {
            [$kat, $judul, $status, $desk] = $laporan[$k];
            LaporanKerusakan::create([
                'user_id' => $kontrak->user_id, 'unit_id' => $kontrak->unit_id, 'kategori' => $kat, 'judul' => $judul,
                'deskripsi' => $desk, 'status' => $status,
                'respon_admin' => $status === 'open' ? null : 'Sudah dicek teknisi pengelola.',
                'resolved_at' => $status === 'resolved' ? $now->copy()->subDays(3) : null,
                'created_at' => $now->copy()->subDays(6 - $k), 'updated_at' => $now->copy()->subDays(5 - $k),
            ]);
        }

        /* ---------- Calon penghuni demo (Dimas) ---------- */
        $tersedia = $units->where('status', 'tersedia')->values();
        $dimasUnit = $tersedia->first(fn ($u) => $u->blok === 'A');
        $pengajuanDimas = PengajuanSewa::create([
            'user_id' => $demoCalon->id, 'unit_id' => $dimasUnit->id, 'nik' => '3171020202950002', 'no_hp' => $demoCalon->no_hp,
            'durasi_sewa' => 12, 'tanggal_mulai' => $now->copy()->addMonth()->startOfMonth(), 'status' => 'verifikasi_dokumen',
            'submitted_at' => $now->copy()->subDays(3), 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDay(),
        ]);
        JadwalSurvei::create([
            'user_id' => $demoCalon->id, 'unit_id' => $tersedia->first(fn ($u) => $u->blok === 'B')->id, 'pengajuan_sewa_id' => null,
            'tanggal_survei' => $now->copy()->addDays(2)->setTime(10, 0), 'status' => 'dikonfirmasi',
        ]);
        foreach ([
            ['Pengajuan Berhasil', 'Pengajuan sewa unit ' . $dimasUnit->nama_unit . ' berhasil dikirim.', 'info', route('calon.pengajuan.show', $pengajuanDimas->id, false)],
            ['Dokumen Sedang Diverifikasi', 'Admin sedang memeriksa KTP, KK, dan slip gaji kamu.', 'info', route('calon.pengajuan.show', $pengajuanDimas->id, false)],
            ['Jadwal Survei Dikonfirmasi', 'Survei unit di Blok B dikonfirmasi untuk ' . $now->copy()->addDays(2)->translatedFormat('d F Y') . ' pukul 10.00.', 'success', route('calon.survei.index', [], false)],
        ] as [$judul, $pesan, $tipe, $link]) {
            Notifikasi::create(['user_id' => $demoCalon->id, 'judul' => $judul, 'pesan' => $pesan, 'tipe' => $tipe, 'link' => $link, 'is_read' => false]);
        }

        // Calon penghuni lain dengan pengajuan menunggu verifikasi admin
        foreach (['Galih Permana', 'Nadia Safitri', 'Reza Mahendra'] as $k => $name) {
            $calon = User::create([
                'name' => $name, 'email' => Str::slug($name, '.') . '@contoh.test', 'password' => $sharedHash,
                'role' => 'calon_penghuni', 'no_hp' => '08135500' . str_pad((string) ($k + 11), 4, '0', STR_PAD_LEFT), 'is_active' => true,
            ]);
            $unit = $tersedia[5 + $k * 7];
            PengajuanSewa::create([
                'user_id' => $calon->id, 'unit_id' => $unit->id, 'nik' => '317103030' . str_pad((string) ($k + 1), 7, '0', STR_PAD_LEFT),
                'no_hp' => $calon->no_hp, 'durasi_sewa' => [6, 12, 24][$k], 'tanggal_mulai' => $now->copy()->addMonth()->startOfMonth(),
                'status' => ['pending', 'pending', 'jadwal_survei'][$k], 'submitted_at' => $now->copy()->subHours(5 + $k * 20),
                'created_at' => $now->copy()->subHours(5 + $k * 20), 'updated_at' => $now->copy()->subHours(4 + $k * 20),
            ]);
            JadwalSurvei::create([
                'user_id' => $calon->id, 'unit_id' => $unit->id, 'pengajuan_sewa_id' => null,
                'tanggal_survei' => $now->copy()->addDays($k)->setTime(9 + $k * 2, 0), 'status' => $k === 0 ? 'dikonfirmasi' : 'pending',
            ]);
        }

        /* ---------- Berita ---------- */
        $berita = [
            ['pengumuman', 'Jadwal pemadaman air terjadwal di Blok B dan C', 2,
                '<p>Sehubungan dengan pembersihan tandon utama, aliran air di <strong>Blok B dan Blok C</strong> akan dihentikan sementara pada hari Sabtu pukul 08.00 sampai 14.00 WIB.</p><p>Penghuni diimbau menampung air secukupnya sebelum jadwal tersebut. Pengelola menyiapkan tandon cadangan di halaman tiap blok.</p><ul><li>Blok B: 08.00 – 11.00 WIB</li><li>Blok C: 11.00 – 14.00 WIB</li></ul><p>Terima kasih atas pengertiannya.</p>'],
            ['kegiatan', 'Kerja bakti dan lomba kebersihan antarblok', 6,
                '<p>Dalam rangka menyambut musim hujan, pengelola mengadakan <strong>kerja bakti serentak</strong> di seluruh blok pada hari Minggu pukul 07.00 WIB.</p><p>Blok dengan lingkungan terbersih akan mendapat hadiah perbaikan taman bermain. Peralatan kebersihan disediakan pengelola, penghuni cukup membawa semangat.</p>'],
            ['info_penting', 'Pembayaran tagihan sewa kini bisa lewat Midtrans', 12,
                '<p>Mulai bulan ini, tagihan sewa bisa dibayar langsung dari menu <strong>Tagihan Saya</strong> menggunakan transfer bank virtual account, e-wallet, atau QRIS melalui Midtrans.</p><p>Status tagihan akan berubah menjadi <em>Lunas</em> setelah pembayaran terverifikasi. Simpan bukti pembayaran sampai status berubah.</p>'],
            ['promo', 'Gratis biaya administrasi untuk pendaftar bulan ini', 18,
                '<p>Calon penghuni yang mengajukan sewa bulan ini <strong>dibebaskan dari biaya administrasi</strong> pendaftaran. Promo berlaku untuk semua unit tipe 24 dan tipe 36 di lima wilayah.</p><p>Ajukan sewa lewat menu Cari Unit, lalu unggah KTP, KK, dan slip gaji untuk mempercepat verifikasi.</p>'],
        ];
        foreach ($berita as [$kategori, $judul, $hariLalu, $konten]) {
            News::create([
                'user_id' => $admin->id, 'judul' => $judul, 'slug' => Str::slug($judul) . '-' . Str::lower(Str::random(5)),
                'kategori' => $kategori, 'konten' => $konten, 'is_published' => true,
                'published_at' => $now->copy()->subDays($hariLalu), 'created_at' => $now->copy()->subDays($hariLalu),
            ]);
        }

        Notifikasi::create([
            'user_id' => $admin->id, 'judul' => 'Pengajuan Sewa Baru', 'tipe' => 'info', 'is_read' => false,
            'pesan' => 'Ada 2 pengajuan sewa baru yang menunggu verifikasi.', 'link' => route('admin.pengajuan.index', [], false),
        ]);
    }

    /**
     * Pengajuan diterima + kontrak aktif + riwayat tagihan bulanan untuk satu penghuni.
     */
    private function kontrakLengkap(User $user, Unit $unit, Carbon $mulai, int $durasi, User $admin, bool $demo): void
    {
        $pengajuan = PengajuanSewa::create([
            'user_id' => $user->id, 'unit_id' => $unit->id, 'nik' => $user->nik ?: '3171000000000000', 'no_hp' => $user->no_hp,
            'durasi_sewa' => $durasi, 'tanggal_mulai' => $mulai, 'status' => 'diterima', 'catatan_admin' => 'Berkas lengkap dan sesuai.',
            'submitted_at' => $mulai->copy()->subWeeks(3), 'created_at' => $mulai->copy()->subWeeks(3), 'updated_at' => $mulai->copy()->subWeek(),
        ]);
        KontrakSewa::create([
            'user_id' => $user->id, 'unit_id' => $unit->id, 'pengajuan_sewa_id' => $pengajuan->id,
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $mulai->copy()->addMonths($durasi),
            'harga_per_bulan' => $unit->harga_sewa, 'status' => 'aktif', 'status_perpanjangan' => 'aktif',
        ]);

        $bulanIni = Carbon::now()->startOfMonth();
        for ($p = $mulai->copy(); $p->lte($bulanIni); $p->addMonth()) {
            $isCurrent = $p->equalTo($bulanIni);
            $lunas = ! $isCurrent || (! $demo && $user->id % 3 === 0);
            Tagihan::create([
                'user_id' => $user->id, 'unit_id' => $unit->id, 'jumlah' => $unit->harga_sewa, 'periode' => $p->format('Y-m'),
                'jatuh_tempo' => $p->copy()->day(10), 'status' => $lunas ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $lunas ? $p->copy()->day(min(9, 3 + $user->id % 6))->setTime(10, 15) : null,
                'catatan' => $lunas ? 'Lunas via Midtrans.' : null,
                'created_at' => $p->copy()->day(1), 'updated_at' => $p->copy()->day(5),
            ]);
        }
    }
}
