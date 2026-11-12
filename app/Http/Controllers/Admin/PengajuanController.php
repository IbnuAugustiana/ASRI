<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSewa;
use App\Models\KontrakSewa; 
use App\Models\Tagihan;
use App\Models\Notifikasi;
use App\Models\Unit;
use App\Models\User;
use App\Models\JadwalSurvei; 
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        // 1. DATA PENGAJUAN SEWA BARU
        $query = PengajuanSewa::with(['user', 'unit']);
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->whereLike('name', "%$search%")->orWhereLike('email', "%$search%"));
        }
        if ($request->filled('tanggal_dari')) { $query->whereDate('created_at', '>=', $request->tanggal_dari); }

        $pengajuans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $statusCounts = PengajuanSewa::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        // 2. DATA PERPANJANGAN KONTRAK PENDING
        $pengajuanPerpanjangan = KontrakSewa::with(['user', 'unit'])
            ->where('status_perpanjangan', 'menunggu_persetujuan')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. DATA PENGUNDURAN DIRI PENDING
        $pengajuanResign = KontrakSewa::with(['user', 'unit'])
            ->where('status_perpanjangan', 'mengundurkan_diri')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($kontrak) {
                $kontrak->rincian_resign = (object) [
                    'alasan'     => $kontrak->alasan_undur_diri ?: '-',
                    'tgl_keluar' => $kontrak->tgl_rencana_keluar?->format('d-m-Y') ?? '-',
                    'tgl_kunci'  => $kontrak->tgl_balik_kunci?->format('d-m-Y') ?? '-',
                    'berkas'     => $kontrak->berkas_undur_diri,
                ];
                return $kontrak;
            });

        return view('admin.pengajuan.index', compact('pengajuans', 'statusCounts', 'pengajuanPerpanjangan', 'pengajuanResign'));
    }

    /**
     * PROSES RESIGN (Fungsi Baru)
     */
    public function prosesResign(Request $request, $id)
{
    $kontrak = KontrakSewa::findOrFail($id);
    $keputusan = $request->status_keputusan; // 'disetujui' atau lainnya

    if ($keputusan === 'disetujui') {
        $kontrak->update(['status_perpanjangan' => 'selesai', 'status' => 'selesai']);
        $kontrak->unit->update(['status' => 'tersedia']);
        
        // Notifikasi ke Penghuni
        Notifikasi::create([
            'user_id' => $kontrak->user_id,
            'judul'   => '✅ Pengajuan Resign Disetujui',
            'pesan'   => 'Pengajuan pengunduran diri Anda untuk unit ' . $kontrak->unit->nama_unit . ' telah disetujui.',
            'tipe'    => 'success',
            'link'    => route('penghuni.kontrak.detail'),
        ]);
    } else {
        $kontrak->update(['status_perpanjangan' => 'aktif']);
    }

    return redirect()->route('admin.pengajuan.index')->with('success', 'Keputusan berhasil diproses.');
}

    public function prosesPerpanjangan(Request $request, $id, $aksi)
    {
        $kontrak = KontrakSewa::with(['user', 'unit'])->findOrFail($id);

        if ($aksi === 'setuju') {
            $durasiBulan = 12;
            if ($kontrak->catatan && preg_match('/(\d+)\s+bulan/', $kontrak->catatan, $matches)) {
                $durasiBulan = intval($matches[1]);
            }

            $tanggalMulaiPerpanjangan = Carbon::parse($kontrak->tanggal_selesai);
            $tanggalSelesaiBaru = $tanggalMulaiPerpanjangan->copy()->addMonths($durasiBulan);

            $kontrak->update([
                'status_perpanjangan' => 'disetujui',
                'tanggal_selesai'     => $tanggalSelesaiBaru,
                'status'              => 'aktif'
            ]);

            $periodeBaru = $tanggalMulaiPerpanjangan->format('Y-m');
            if (!Tagihan::where('user_id', $kontrak->user_id)->where('periode', $periodeBaru)->exists()) {
                Tagihan::create([
                    'user_id' => $kontrak->user_id, 'unit_id' => $kontrak->unit_id,
                    'jumlah' => $kontrak->harga_per_bulan * $durasiBulan,
                    'periode' => $periodeBaru, 'jatuh_tempo' => Carbon::now()->addDays(7),
                    'status' => 'belum_bayar',
                ]);
            }

            return redirect()->route('admin.pengajuan.index')->with('success', "Kontrak diperpanjang.");
        
        } elseif ($aksi === 'tolak') {
            $kontrak->update(['status_perpanjangan' => 'ditolak']);
            return redirect()->route('admin.pengajuan.index')->with('warning', "Permintaan perpanjangan ditolak.");
        }
        return redirect()->route('admin.pengajuan.index');
    }

    public function show($id) {
        $pengajuan = PengajuanSewa::with(['user', 'unit', 'dokumens', 'jadwalSurvei', 'kontrakSewa'])->findOrFail($id);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'         => 'required|in:pending,verifikasi_dokumen,jadwal_survei,diterima,ditolak,dibatalkan',
            'catatan_admin' => 'nullable|string|max:500',
            'tanggal_survei' => 'required_if:status,jadwal_survei|nullable|date|after:now',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit', 'jadwalSurvei')->findOrFail($id);
        $oldStatus = $pengajuan->status;

        $pengajuan->update([
            'status'         => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        if ($request->status === 'jadwal_survei') {
            if (!$pengajuan->jadwalSurvei) {
                JadwalSurvei::create([
                    'pengajuan_sewa_id' => $pengajuan->id,
                    'user_id'           => $pengajuan->user_id, 
                    'unit_id'           => $pengajuan->unit_id, 
                    'tanggal_survei'    => $request->tanggal_survei ?? now()->addDays(2), 
                    'status'            => 'pending', 
                ]);
            } else {
                if ($request->filled('tanggal_survei')) {
                    $pengajuan->jadwalSurvei->update(['tanggal_survei' => $request->tanggal_survei]);
                }
            }
        }

        $this->kirimNotifikasiStatus($pengajuan, $request->status);
        return back()->with('success', "Status pengajuan berhasil diubah dari {$oldStatus} menjadi {$request->status}.");
    }

    public function terima(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai'  => 'required|date',
            'harga_per_bulan'=> 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit')->findOrFail($id);

        if (!in_array($pengajuan->status, ['jadwal_survei', 'verifikasi_dokumen', 'pending'])) {
            return back()->with('error', 'Pengajuan tidak dapat diterima pada status ini.');
        }

        $tanggalMulai  = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = $tanggalMulai->copy()->addMonths($pengajuan->durasi_sewa);

        $pengajuan->update(['status' => 'diterima', 'tanggal_mulai' => $tanggalMulai]);

        $kontrak = KontrakSewa::create([
            'user_id'            => $pengajuan->user_id,
            'unit_id'            => $pengajuan->unit_id,
            'pengajuan_sewa_id'  => $pengajuan->id,
            'tanggal_mulai'      => $tanggalMulai,
            'tanggal_selesai'    => $tanggalSelesai,
            'harga_per_bulan'    => $request->harga_per_bulan,
            'status'             => 'aktif',
        ]);

        $pengajuan->unit->update(['status' => 'dihuni']);
        $pengajuan->user->update(['role' => 'penghuni']);

        Tagihan::create([
            'user_id'    => $pengajuan->user_id,
            'unit_id'    => $pengajuan->unit_id,
            'jumlah'     => $request->harga_per_bulan,
            'periode'    => $tanggalMulai->format('Y-m'),
            'jatuh_tempo'=> $tanggalMulai->copy()->addDays(7),
            'status'     => 'belum_bayar',
        ]);

        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'judul'   => '🎉 Pengajuan Sewa Diterima!',
            'pesan'   => "Selamat! Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda telah diterima. Kontrak berlaku mulai {$tanggalMulai->format('d M Y')}.",
            'tipe'    => 'success',
            'link'    => route('penghuni.dashboard'),
        ]);

        return back()->with('success', "Pengajuan diterima! Kontrak dan tagihan pertama telah dibuat.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:10|max:500',
        ]);

        $pengajuan = PengajuanSewa::with('user', 'unit')->findOrFail($id);
        $pengajuan->update(['status' => 'ditolak', 'catatan_admin' => $request->catatan_admin]);

        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'judul'   => 'Pengajuan Sewa Ditolak',
            'pesan'   => "Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda ditolak. Alasan: {$request->catatan_admin}",
            'tipe'    => 'danger',
            'link'    => route('calon.pengajuan.show', $pengajuan->id),
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak dan pengguna telah dinotifikasi.');
    }

    private function kirimNotifikasiStatus(PengajuanSewa $pengajuan, string $status): void
    {
        $messages = [
            'verifikasi_dokumen' => ['Dokumen Sedang Diverifikasi', "Dokumen pengajuan sewa Anda sedang dalam proses verifikasi oleh admin.", 'info'],
            'jadwal_survei'      => ['Jadwal Survei Dibuat', "Pengajuan Anda lolos verifikasi! Silakan pilih jadwal survei unit.", 'success'],
            'dibatalkan'         => ['Pengajuan Dibatalkan', "Pengajuan sewa unit {$pengajuan->unit->nama_unit} Anda telah dibatalkan.", 'warning'],
        ];

        if (isset($messages[$status])) {
            [$judul, $pesan, $tipe] = $messages[$status];
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'judul'   => $judul,
                'pesan'   => $pesan,
                'tipe'    => $tipe,
                'link'    => route('calon.pengajuan.show', $pengajuan->id),
            ]);
        }
    }
}