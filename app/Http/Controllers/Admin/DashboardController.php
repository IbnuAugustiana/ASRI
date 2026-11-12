<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use App\Models\PengajuanSewa;
use App\Models\Tagihan;
use App\Models\LaporanKerusakan;
use App\Models\KontrakSewa;
use App\Models\News;
use App\Support\Sql;
use Illuminate\Http\Request;

//
class DashboardController extends Controller
{
   public function index()
{
   
    $kontrakPerpanjanganCount = \App\Models\KontrakSewa::where('status_perpanjangan', 'menunggu_persetujuan')->count();

    $stats = [
        'total_unit'            => Unit::count(),
        'unit_tersedia'         => Unit::where('status', 'tersedia')->count(),
        'unit_dihuni'           => Unit::where('status', 'dihuni')->count(),
        'unit_maintenance'      => Unit::where('status', 'maintenance')->count(),
        'total_penghuni'        => User::where('role', 'penghuni')->count(),
        'total_calon'           => User::where('role', 'calon_penghuni')->count(),
        
        // UPDATE: Gabungkan pengajuan sewa baru pending + pengajuan perpanjangan kontrak pending
        'pengajuan_pending'     => PengajuanSewa::where('status', 'pending')->count() + $kontrakPerpanjanganCount,
        
        'pengajuan_verifikasi'  => PengajuanSewa::where('status', 'verifikasi_dokumen')->count(),
        'Tagihan_belum_bayar'   => Tagihan::where('status', 'belum_bayar')->count(),
        'Tagihan_menunggu'      => Tagihan::where('status', 'menunggu_verifikasi')->count(),
        'laporan_open'          => LaporanKerusakan::where('status', 'open')->count(),
        'laporan_in_progress'   => LaporanKerusakan::where('status', 'in_progress')->count(),
        'pendapatan_bulan_ini'  => Tagihan::where('status', 'lunas')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah'),
        'pendapatan_tahun_ini'  => Tagihan::where('status', 'lunas')
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah'),
    ];

        $pengajuanTerbaru = PengajuanSewa::with(['user', 'unit'])
            ->whereIn('status', ['pending', 'verifikasi_dokumen', 'jadwal_survei'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $laporanTerbaru = LaporanKerusakan::with(['user', 'unit'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $TagihanMenunggu = Tagihan::with(['user', 'unit'])
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Data grafik okupansi per wilayah
        $unitPerWilayah = Unit::selectRaw('wilayah, status, count(*) as total')
            ->groupBy('wilayah', 'status')
            ->get()
            ->groupBy('wilayah');

        // Pendapatan 6 bulan terakhir
        $pendapatan6Bulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $pendapatan6Bulan[] = [
                'label'  => $bulan->format('M Y'),
                'jumlah' => Tagihan::where('status', 'lunas')
                    ->whereMonth('tanggal_bayar', $bulan->month)
                    ->whereYear('tanggal_bayar', $bulan->year)
                    ->sum('jumlah'),
            ];
        }

        return view('admin.dashboard', compact(
            'stats', 'pengajuanTerbaru', 'laporanTerbaru',
            'TagihanMenunggu', 'unitPerWilayah', 'pendapatan6Bulan'
        ));
    }

    public function statistik()
    {
        // Ekspresi bulan dibuat lewat App\Support\Sql agar jalan di MySQL maupun PostgreSQL (Neon)
        $bulanBayar = Sql::yearMonth('tanggal_bayar');
        $pendapatanBulanan = Tagihan::where('status', 'lunas')
            ->selectRaw("{$bulanBayar} as bulan, SUM(jumlah) as total")
            ->whereYear('tanggal_bayar', now()->year)
            ->groupByRaw($bulanBayar)
            ->orderBy('bulan')
            ->get();

        $okupansiPerWilayah = Unit::selectRaw("wilayah, count(*) as total,
            SUM(CASE WHEN status = 'dihuni' THEN 1 ELSE 0 END) as dihuni,
            SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as tersedia")
            ->groupBy('wilayah')
            ->get();

        $bulanDibuat = Sql::yearMonth('created_at');
        $pengajuanPerBulan = PengajuanSewa::selectRaw(
            "{$bulanDibuat} as bulan,
             COUNT(*) as total,
             SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima,
             SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak"
        )
            ->whereYear('created_at', now()->year)
            ->groupByRaw($bulanDibuat)
            ->orderBy('bulan')
            ->get();

        $totalPenghuni = User::where('role', 'penghuni')->count();
        $totalUnit     = Unit::count();
        $tingkatOkupansi = $totalUnit > 0
            ? round(($totalPenghuni / $totalUnit) * 100, 1)
            : 0;

        return view('admin.statistik', compact(
            'pendapatanBulanan', 'okupansiPerWilayah',
            'pengajuanPerBulan', 'tingkatOkupansi'
        ));
    }
}