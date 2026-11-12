<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Support\TowerData;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $units = TowerData::all();

        $stats = [
            'total_unit'     => $units->count(),
            'tersedia'       => $units->where('status', 'tersedia')->count(),
            'dihuni'         => $units->where('status', 'dihuni')->count(),
            'maintenance'    => $units->where('status', 'maintenance')->count(),
            'total_penghuni' => \App\Models\User::where('role', 'penghuni')->count(),
            'wilayah'        => $units->pluck('wilayah')->unique()->count(),
        ];

        // Jumlah unit kosong per wilayah (untuk pita berjalan di beranda)
        $wilayahStats = $units->groupBy('wilayah')
            ->map(fn ($group, $wilayah) => [
                'wilayah'  => $wilayah,
                'tersedia' => $group->where('status', 'tersedia')->count(),
            ])
            ->sortByDesc('tersedia')
            ->values();

        $unitTersedia  = Unit::where('status', 'tersedia')->orderBy('id')->take(6)->get();
        $beritaTerbaru = News::published()->with('user')->orderBy('published_at', 'desc')->take(3)->get();

        // Data gedung 3D di hero: setiap unit = satu modul, warna jendela = status
        $towerData = TowerData::payload($units, 'city');

        return view('public.home', compact('unitTersedia', 'beritaTerbaru', 'stats', 'wilayahStats', 'towerData'));
    }

    public function units(Request $request)
    {
        $query = Unit::query();

        if ($request->filled('wilayah')) {
            $query->where('wilayah', $request->wilayah);
        }
        if ($request->has('tersedia')) {
            $query->where('status', 'tersedia');
        }
        if ($request->filled('harga_min')) {
            $query->where('harga_sewa', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga_sewa', '<=', $request->harga_max);
        }
        if ($request->filled('luas_min')) {
            $query->where('luas_m2', '>=', $request->luas_min);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereLike('gedung', "%$search%")
                  ->orWhereLike('blok', "%$search%")
                  ->orWhereLike('alamat', "%$search%")
                  ->orWhereLike('deskripsi', "%$search%");
            });
        }

        $units     = $query->paginate(12)->appends($request->query());
        $wilayahs  = Unit::distinct()->pluck('wilayah')->sort();
        $totalHasil = $query->count();

        return view('public.units', compact('units', 'wilayahs', 'totalHasil'));
    }

    public function unitDetail(Unit $unit)
    {
        // Unit serupa pada wilayah yang sama
        $unitSerupa = Unit::where('wilayah', $unit->wilayah)
            ->where('id', '!=', $unit->id)
            ->where('status', 'tersedia')
            ->take(3)
            ->get();

        // Gedung 3D untuk blok unit ini, dengan unit yang dibuka disorot
        $blokUnits = Unit::where('blok', $unit->blok)
            ->orderBy('lantai')->orderBy('no_kamar')
            ->get(TowerData::COLUMNS);
        $towerData = TowerData::payload($blokUnits, 'focus', $unit->id);

        return view('public.unit_detail', compact('unit', 'unitSerupa', 'towerData'));
    }
public function news(Request $request)
{
    $query = News::published();

    // Search: judul atau tanggal (format: dd-mm-yyyy atau yyyy-mm-dd)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereLike('judul', "%$search%")
              ->orWhereRaw(\App\Support\Sql::dateText('published_at', 'dmy') . ' like ?', ["%$search%"])
              ->orWhereRaw(\App\Support\Sql::dateText('published_at', 'ymd') . ' like ?', ["%$search%"]);
        });
    }

    // Filter kategori
    if ($request->filled('kategori') && array_key_exists($request->kategori, News::KATEGORI)) {
        $query->where('kategori', $request->kategori);
    }

    // Sort urutan
    $sort = $request->get('sort', 'terbaru');
    if ($sort === 'terlama') {
        $query->orderBy('published_at', 'asc');
    } else {
        $query->orderBy('published_at', 'desc');
    }

    $news = $query->paginate(9)->withQueryString();
    $kategoriList = News::KATEGORI;

    return view('public.news', compact('news', 'kategoriList'));
}

    public function newsDetail($slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();
        $related = News::published()
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('public.news_detail', compact('article', 'related'));
    }
}
