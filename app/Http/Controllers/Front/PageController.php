<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use App\Models\Review;

class PageController extends Controller
{
    public function home()
    {
        $featured = Kost::query()
            ->active()
            ->with('lokasi')
            ->withMin('kamar', 'price_monthly')
            ->withCount(['kamar' => fn ($q) => $q->where('status', 'available')])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'kost' => Kost::active()->count(),
            'kamar' => \App\Models\Kamar::where('status', 'available')->count(),
            'lokasi' => \App\Models\Lokasi::count(),
        ];

        return view('pages.front.home', compact('featured', 'stats'));
    }

    public function tentang()
    {
        return view('pages.front.tentang');
    }

    public function kontak()
    {
        return view('pages.front.kontak');
    }

    public function caraKerja()
    {
        return view('pages.front.cara-kerja');
    }
}
