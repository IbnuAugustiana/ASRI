<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

/**
 * Halaman fitur publik tambahan: peta lokasi, tur 3D, simulasi biaya,
 * bandingkan unit, fasilitas, dan bantuan.
 */
class FiturController extends Controller
{
    /** Posisi wilayah di peta (kolom, baris) mengikuti letak geografis Jakarta. */
    private const PETA = [
        'Jakarta Utara'   => [1, 0, 'Pesisir utara, dekat pelabuhan dan kawasan industri.'],
        'Jakarta Barat'   => [0, 1, 'Kawasan padat dengan akses tol bandara dan pusat grosir.'],
        'Jakarta Pusat'   => [1, 1, 'Pusat kota: perkantoran, Monas, dan stasiun utama.'],
        'Jakarta Timur'   => [2, 1, 'Wilayah terluas, banyak taman dan kawasan industri.'],
        'Jakarta Selatan' => [1, 2, 'Hunian hijau dengan akses MRT dan pusat bisnis.'],
    ];

    public function lokasi()
    {
        $units = Unit::get(['id', 'blok', 'lantai', 'gedung', 'alamat', 'wilayah', 'status', 'harga_sewa', 'luas_m2']);

        $wilayah = collect(self::PETA)->map(function ($meta, $nama) use ($units) {
            $list = $units->where('wilayah', $nama);
            return [
                'nama'      => $nama,
                'col'       => $meta[0],
                'row'       => $meta[1],
                'catatan'   => $meta[2],
                'total'     => $list->count(),
                'tersedia'  => $list->where('status', 'tersedia')->count(),
                'harga_min' => (int) $list->min('harga_sewa'),
                'gedung'    => $list->groupBy('gedung')->map(fn ($g, $nama) => [
                    'nama'     => $nama,
                    'alamat'   => $g->first()->alamat,
                    'blok'     => $g->first()->blok,
                    'total'    => $g->count(),
                    'tersedia' => $g->where('status', 'tersedia')->count(),
                ])->values()->all(),
            ];
        })->values();

        $maxTotal = max(1, $wilayah->max('total'));

        return view('public.lokasi', compact('wilayah', 'maxTotal'));
    }

    public function tur()
    {
        $tipe = [
            24 => Unit::where('luas_m2', 24)->where('status', 'tersedia')->count(),
            36 => Unit::where('luas_m2', 36)->where('status', 'tersedia')->count(),
        ];
        $hargaMin = [
            24 => (int) Unit::where('luas_m2', 24)->min('harga_sewa'),
            36 => (int) Unit::where('luas_m2', 36)->min('harga_sewa'),
        ];

        return view('public.tur', compact('tipe', 'hargaMin'));
    }

    public function simulasi()
    {
        // Harga termurah per wilayah & tipe, dipakai kalkulator di sisi browser
        $harga = Unit::selectRaw('wilayah, luas_m2, MIN(harga_sewa) as harga, COUNT(*) as total')
            ->groupBy('wilayah', 'luas_m2')
            ->get()
            ->groupBy('wilayah')
            ->map(fn ($rows) => $rows->mapWithKeys(fn ($r) => [(int) $r->luas_m2 => (int) $r->harga]))
            ->all();

        return view('public.simulasi', compact('harga'));
    }

    public function bandingkan(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) $id)->filter()->unique()->take(3)->values();

        $units = $ids->isEmpty()
            ? collect()
            : Unit::whereIn('id', $ids)->get()->sortBy(fn ($u) => $ids->search($u->id))->values();

        $saran = Unit::where('status', 'tersedia')->orderBy('harga_sewa')->take(3)->get();

        return view('public.bandingkan', compact('units', 'saran'));
    }

    public function fasilitas()
    {
        return view('public.fasilitas');
    }

    public function bantuan()
    {
        return view('public.bantuan');
    }
}
