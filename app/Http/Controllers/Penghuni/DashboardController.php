<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\KontrakSewa;
use App\Models\Tagihan;
use App\Models\LaporanKerusakan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama penghuni.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kontrak aktif
        $kontrakAktif = KontrakSewa::with('unit')
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        // Tagihan belum bayar
        $tagihanBelumBayar = Tagihan::where('user_id', $user->id)
            ->where('status', 'belum_bayar')
            ->orderBy('jatuh_tempo')
            ->first();

        // Count semua tagihan belum lunas
        $tagihanBelumLunas = Tagihan::where('user_id', $user->id)
            ->whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])
            ->count();

        // Tagihan terbaru
        $tagihanTerbaru = Tagihan::where('user_id', $user->id)
            ->orderBy('jatuh_tempo', 'desc')
            ->take(4)
            ->get();

        // Laporan kerusakan aktif
        $laporanAktif = LaporanKerusakan::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Notifikasi belum dibaca
        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        // Kalkulasi Info kontrak
        $infoKontrak = null;
        if ($kontrakAktif) {
            $sisaHari   = now()->diffInDays($kontrakAktif->tanggal_selesai, false);
            $totalHari   = $kontrakAktif->tanggal_mulai->diffInDays($kontrakAktif->tanggal_selesai);
            
            $progressPct = $totalHari > 0
                ? min(100, round((($totalHari - $sisaHari) / $totalHari) * 100))
                : 0;

            $infoKontrak = [
                'sisa_hari'    => max(0, $sisaHari),
                'progress'     => $progressPct,
                'hampir_habis' => $sisaHari <= 30 && $sisaHari > 0,
            ];
        }

        // Statistik pribadi penghuni
        $statsPersonal = [
            'total_bayar'   => Tagihan::where('user_id', $user->id)->where('status', 'lunas')->sum('jumlah'),
            'total_laporan' => LaporanKerusakan::where('user_id', $user->id)->count(),
            'lama_menghuni' => $kontrakAktif ? $kontrakAktif->tanggal_mulai->diffInMonths(now()) : 0,
        ];

        return view('penghuni.dashboard', compact(
            'kontrakAktif', 'tagihanBelumBayar', 'tagihanBelumLunas',
            'tagihanTerbaru', 'laporanAktif', 'notifikasiTerbaru',
            'infoKontrak', 'statsPersonal'
        ));
    }

    /**
     * Menampilkan halaman detail sewa dan tombol perpanjangan.
     */
    public function detailKontrak(): View
{
    $user = Auth::user();

    // Kontrak terbaru milik penghuni (status_perpanjangan bisa NULL untuk kontrak baru)
    $kontrak = KontrakSewa::with(['unit'])
        ->where('user_id', $user->id)
        ->latest()
        ->first();

    $sisaHari = 0;
    if ($kontrak && $kontrak->tanggal_selesai) {
        $sisaHari = now()->diffInDays(Carbon::parse($kontrak->tanggal_selesai), false);
    }

    return view('penghuni.kontrak.detail', compact('kontrak', 'sisaHari'));
}

    /**
     * Memproses pengajuan perpanjangan sewa dari penghuni.
     */
    public function perpanjangKontrak(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();

        // Cari kontrak sewa aktif milik user bersangkutan
        $kontrak = KontrakSewa::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Cegah duplikasi pengajuan
        if ($kontrak->status_perpanjangan === 'menunggu_persetujuan') {
            return redirect()->back()->with('error', 'Anda sudah mengajukan perpanjangan sewa sebelumnya. Mohon tunggu konfirmasi admin.');
        }

        // Update status pengajuan perpanjangan sewa
        $kontrak->update([
            'status_perpanjangan' => 'menunggu_persetujuan'
        ]);

        // Notifikasi ke Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul'   => '⏳ Pengajuan Perpanjangan Kontrak',
                'pesan'   => "Penghuni " . $user->name . " mengajukan perpanjangan kontrak untuk unit " . $kontrak->unit->nama_unit . ".",
                'tipe'    => 'info',
                'link'    => route('admin.pengajuan.index'),
            ]);
        }

        return redirect()->back()->with('success', 'Permintaan perpanjangan sewa berhasil dikirim! Pengelola akan segera memeriksa berkas Anda.');
    }

    /**
     * HANYA MENAMBAHKAN METHOD INI:
     * Memproses pengajuan pengunduran diri sewa (resign) dari penghuni.
     */
    public function ajukanResign(Request $request, $id): RedirectResponse
    {
        // 1. Validasi input form dari modal pop-out
        $request->validate([
            'alasan_keluar'          => 'required|string|max:500',
            'tanggal_keluar'         => 'required|date|after_or_equal:today',
            'tanggal_kembali_kunci'  => 'required|date|after_or_equal:tanggal_keluar',
            'dokumen_pengunduran'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        $user = Auth::user();

        // Proteksi kepemilikan kontrak sewa aktif milik user bersangkutan
        $kontrak = KontrakSewa::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // 2. Proses upload berkas fisik ke folder storage/app/public/dokumen_resign
        $pathDokumen = null;
        if ($request->hasFile('dokumen_pengunduran')) {
            $pathDokumen = $request->file('dokumen_pengunduran')->store('dokumen_resign', 'public');
        }

        // 3. Simpan rincian ke kolom khusus (sebelumnya ditulis ke kolom "catatan" yang tidak ada → data hilang)
        $kontrak->update([
            'status_perpanjangan' => 'mengundurkan_diri',
            'alasan_undur_diri'   => $request->alasan_keluar,
            'tgl_rencana_keluar'  => $request->tanggal_keluar,
            'tgl_balik_kunci'     => $request->tanggal_kembali_kunci,
            'berkas_undur_diri'   => $pathDokumen,
        ]);

        // 5. Kirim Notifikasi ke Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul'   => '🛑 Pengajuan Keluar Hunian',
                'pesan'   => "Penghuni " . $user->name . " mengajukan pengunduran diri (keluar sewa) dari unit " . ($kontrak->unit->nama_unit ?? '-') . ".",
                'tipe'    => 'info',
                'link'    => route('admin.penghuni.index'),
            ]);
        }

        return redirect()->back()->with('success', 'Permohonan pengunduran diri berhasil diajukan! Formulir dan jadwal Anda sudah terekam di sistem.');
    }
}