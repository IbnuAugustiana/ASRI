<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KontrakSewa;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Transaction;

class TagihanController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index(Request $request)
    {
        $query = Tagihan::with(['user', 'unit']);

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'menunggu') $status = 'menunggu_verifikasi';
            $query->where('status', $status);
        }
        
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->whereLike('name', "%$search%"));
        }

        $tagihans = $query->orderBy('jatuh_tempo', 'desc')->paginate(15)->withQueryString();

        $summary = [
            'total_belum_bayar'         => Tagihan::where('status', 'belum_bayar')->sum('jumlah'),
            'total_menunggu_verifikasi' => Tagihan::where('status', 'menunggu_verifikasi')->count(),
            'total_lunas_bulan_ini'     => Tagihan::where('status', 'lunas')
                ->whereMonth('tanggal_bayar', now()->month)->sum('jumlah'),
        ];

        return view('admin.tagihan.index', compact('tagihans', 'summary'));
    }

    public function show($id)
    {
        // Memastikan relasi user dan unit dipanggil dengan with()
        $tagihan = Tagihan::with(['user', 'unit'])->findOrFail($id);
        
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function create()
    {
        $penghunisAktif = KontrakSewa::with(['user', 'unit'])
            ->where('status', 'aktif')
            ->get();
        return view('admin.tagihan.create', compact('penghunisAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'unit_id'     => 'required|exists:units,id',
            'jumlah'      => 'required|numeric|min:0',
            'periode'     => 'required|string|regex:/^\d{4}-\d{2}$/',
            'jatuh_tempo' => 'required|date',
            'catatan'     => 'nullable|string|max:255',
        ]);

        if (Tagihan::where(['user_id' => $request->user_id, 'unit_id' => $request->unit_id, 'periode' => $request->periode])->exists()) {
            return back()->with('error', 'Tagihan untuk periode ini sudah ada.')->withInput();
        }

        $tagihan = Tagihan::create($request->only(['user_id', 'unit_id', 'jumlah', 'periode', 'jatuh_tempo', 'catatan']));

        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => 'Tagihan Baru Tersedia',
            'pesan'   => "Tagihan sewa bulan {$tagihan->periode} sebesar Rp " . number_format($tagihan->jumlah, 0, ',', '.') . " telah diterbitkan.",
            'tipe'    => 'warning',
            'link'    => route('penghuni.tagihan.show', $tagihan->id),
        ]);

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dibuat!');
    }

    public function verifikasi($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if (!in_array($tagihan->status, ['menunggu', 'menunggu_verifikasi'])) {
            return back()->with('error', 'Tagihan tidak dalam status menunggu verifikasi.');
        }

        $tagihan->update(['status' => 'lunas', 'tanggal_bayar' => now()]);

        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => '✅ Pembayaran Terverifikasi',
            'pesan'   => "Pembayaran tagihan bulan {$tagihan->periode} Anda telah diverifikasi manual oleh Admin.",
            'tipe'    => 'success',
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function tolakBayar(Request $request, $id)
    {
        $request->validate(['catatan_penolakan' => 'required|string|min:5|max:255']);

        $tagihan = Tagihan::findOrFail($id);

        $tagihan->update([
            'status'        => 'belum_bayar',
            'bukti_bayar'   => null,
            'tanggal_bayar' => null,
            'catatan'       => $request->catatan_penolakan,
        ]);

        Notifikasi::create([
            'user_id' => $tagihan->user_id,
            'judul'   => '❌ Bukti Pembayaran Ditolak',
            'pesan'   => "Bukti pembayaran ditolak. Alasan: {$request->catatan_penolakan}.",
            'tipe'    => 'danger',
            'link'    => route('penghuni.tagihan.show', $tagihan->id),
        ]);

        return back()->with('success', 'Pembayaran ditolak dan penghuni telah diberitahu.');
    }

    public function periksaStatusPembayaranAdmin($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $orderId = 'ASRI-' . $tagihan->id . '-' . strtotime($tagihan->created_at);

        try {
            $statusMidtrans = Transaction::status($orderId);
            $transactionStatus = $statusMidtrans['transaction_status'];

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $tagihan->update(['status' => 'lunas', 'tanggal_bayar' => now()]);
                return back()->with('success', 'Status Midtrans: SETTLEMENT. Lunas.');
            } 
            
            return back()->with('info', 'Status Midtrans: ' . $transactionStatus);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    public function generateBulanan()
    {
        $kontrakAktif = KontrakSewa::where('status', 'aktif')->where('tanggal_selesai', '>=', now())->get();
        $periode = now()->format('Y-m');
        $generated = 0;

        foreach ($kontrakAktif as $kontrak) {
            if (!Tagihan::where(['user_id' => $kontrak->user_id, 'unit_id' => $kontrak->unit_id, 'periode' => $periode])->exists()) {
                Tagihan::create([
                    'user_id'     => $kontrak->user_id,
                    'unit_id'     => $kontrak->unit_id,
                    'jumlah'      => $kontrak->harga_per_bulan,
                    'periode'     => $periode,
                    'jatuh_tempo' => now()->endOfMonth(),
                    'status'      => 'belum_bayar',
                ]);
                $generated++;
            }
        }
        return redirect()->back()->with('success', "$generated tagihan berhasil dibuat.");
    }
}