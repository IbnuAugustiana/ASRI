<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PengajuanSewa;
use App\Models\KontrakSewa; // Import Model KontrakSewa jika ingin memantau perpanjangan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(): View
    {
        // 1. Ambil data notifikasi milik user/admin yang sedang login
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // HAPUS ATAU KOMENTARI PROSES OTOMATIS UPDATE DI SINI
        // Supaya highlight warna hijau penanda notifikasi baru tidak langsung hilang saat di-refresh

        // 2. Khusus akun Admin: Ambil data pengajuan pending
        $pengajuanPending = null;
        if (Auth::user()->role === 'admin') {
            $pengajuanPending = PengajuanSewa::with(['user', 'unit'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('notifikasi.index', compact('notifikasis', 'pengajuanPending'));
    }

    // Mengubah return redirect untuk form fallback non-ajax jika tombol "Tandai dibaca" diklik biasa
    public function markRead(string|int $id)
    {
        $notif = Notifikasi::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Notifikasi berhasil dibaca.');
    }

    public function markAllRead(): RedirectResponse
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        Notifikasi::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        Notifikasi::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }

    public function countUnread(): JsonResponse
    {
        $count = Notifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }
}