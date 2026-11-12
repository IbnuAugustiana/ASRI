<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\KontrakSewa; // Ditambahkan untuk menangani model kontrak
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class TagihanController extends Controller
{
    public function __construct()
    {
        // PERBAIKAN: Membaca konfigurasi terpusat melalui config() agar aman dari cache data .env
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $userId = Auth::id();

        // FIXED: Menggunakan paginate() agar fungsi $tagihans->links() di blade bekerja sempurna
        $tagihans = Tagihan::where('user_id', $userId)
            ->orderBy('jatuh_tempo', 'desc')
            ->paginate(10); 

        $tagihanSegera = Tagihan::where('user_id', $userId)
            ->where('status', 'belum_bayar')
            ->orderBy('jatuh_tempo', 'asc')
            ->first();

        $summary = [
            'total_belum_bayar' => Tagihan::where('user_id', $userId)
                ->where('status', 'belum_bayar')
                ->sum('jumlah'),
                
            'total_lunas' => Tagihan::where('user_id', $userId)
                ->where('status', 'lunas')
                ->count(),
                
            'tagihan_terlambat' => Tagihan::where('user_id', $userId)
                ->where('status', 'belum_bayar')
                ->where('jatuh_tempo', '<', now()->startOfDay())
                ->count(),
        ];

        return view('penghuni.tagihan.index', compact('tagihans', 'summary', 'tagihanSegera'));
    }

    public function show($id)
    {
        $tagihan = Tagihan::where('user_id', Auth::id())->findOrFail($id);
        $snapToken = null;

        if ($tagihan->status === 'belum_bayar') {
            // FIXED SINKRONISASI: Menggunakan created_at agar order_id konsisten saat dicek oleh Admin/Penghuni
            $orderId = 'ASRI-' . $tagihan->id . '-' . strtotime($tagihan->created_at);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $tagihan->jumlah,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'expiry' => [
                    'start' => date("Y-m-d H:i:s O"),
                    'unit' => 'days',
                    'duration' => 1
                ]
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                // Jika ada error lain dari Midtrans, balikkan ke null agar view tidak crash
                $snapToken = null;
            }
        }

        return view('penghuni.tagihan.show', compact('tagihan', 'snapToken'));
    }

    public function periksaStatusPembayaran($id)
    {
        $tagihan = Tagihan::where('user_id', Auth::id())->findOrFail($id);
        $orderId = 'ASRI-' . $tagihan->id . '-' . strtotime($tagihan->created_at);

        try {
            /** @var \stdClass $statusMidtrans */
            $statusMidtrans = Transaction::status($orderId);
            $transactionStatus = $statusMidtrans->transaction_status;

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $tagihan->update([
                    'status' => 'lunas',
                    'tanggal_bayar' => now(),
                    'catatan' => 'Lunas otomatis via verifikasi API Midtrans oleh Penghuni.'
                ]);

                return redirect()->back()->with('success', 'Pembayaran Anda berhasil diverifikasi! Status tagihan kini LUNAS.');
            } elseif ($transactionStatus === 'pending') {
                return redirect()->back()->with('info', 'Transaksi Anda masih pending. Silakan selesaikan pembayaran di Midtrans.');
            } else {
                $tagihan->update(['status' => 'belum_bayar']);
                return redirect()->back()->with('error', 'Transaksi gagal, expired, atau dibatalkan di Midtrans.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Belum ada rekaman transaksi atau gagal terhubung ke Midtrans.');
        }
    }

    // TAMBAHAN FUNGSI BARU UNTUK RIWAYAT
    public function riwayatTransaksi()
    {
        $userId = Auth::id();

        $riwayats = Tagihan::where('user_id', $userId)
            ->where('status', 'lunas')
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(10);

        return view('penghuni.tagihan.riwayat', compact('riwayats'));
    }

    /**
     * BARU: Menangani Pengajuan Perpanjangan Kontrak dari Sisi Penghuni
     */
    public function ajukanPerpanjangan(Request $request, $id)
    {
        $request->validate([
            'durasi' => 'required|integer|min:1|max:24'
        ]);

        // Proteksi: Pastikan kontrak yang mau diperpanjang memang milik penghuni yang sedang login
        $kontrak = KontrakSewa::where('user_id', Auth::id())->findOrFail($id);

        // Update status perpanjangan dan simpan request durasi bulan ke kolom catatan
        $kontrak->update([
            'status_perpanjangan' => 'menunggu_persetujuan',
            'catatan' => 'Request perpanjangan: ' . $request->durasi . ' bulan'
        ]);

        return redirect()->back()->with('success', 'Pengajuan perpanjangan sewa sebesar ' . $request->durasi . ' bulan berhasil dikirim! Menunggu persetujuan Admin.');
    }
}