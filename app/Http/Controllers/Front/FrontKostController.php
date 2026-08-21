<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Kost;
use App\Models\Lokasi;
use App\Models\TipeKamar;
use Illuminate\Http\Request;

class FrontKostController extends Controller
{
    public function index(Request $request)
    {
        $q = Kost::query()
            ->active()
            ->with('lokasi')
            ->withMin('kamar', 'price_monthly')
            ->withCount(['kamar as kamar_available_count' => fn ($qr) => $qr->where('status', 'available')])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->q.'%';
                $query->where(fn ($w) => $w
                    ->where('name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('kelurahan', 'like', $term)
                    ->orWhere('kecamatan', 'like', $term)
                    ->orWhereHas('lokasi', fn ($l) => $l->where('name', 'like', $term)));
            })
            ->when($request->filled('lokasi_id'), fn ($query) => $query->where('lokasi_id', $request->lokasi_id))
            ->when($request->filled('min_price') || $request->filled('max_price'), function ($query) use ($request) {
                $query->whereHas('kamar', function ($k) use ($request) {
                    $k->where('status', 'available')
                        ->when($request->filled('min_price'), fn ($kk) => $kk->where('price_monthly', '>=', (int) $request->min_price))
                        ->when($request->filled('max_price'), fn ($kk) => $kk->where('price_monthly', '<=', (int) $request->max_price));
                });
            })
            ->when($request->filled('tipe_kamar_id'), fn ($query) => $query->whereHas('kamar', fn ($k) => $k
                ->where('tipe_kamar_id', $request->tipe_kamar_id)
                ->where('status', 'available')))
            ->when($request->filled('fasilitas'), fn ($query) => $query->whereHas('fasilitas', fn ($f) => $f
                ->whereIn('fasilitas.id', (array) $request->fasilitas)))
            ->when($request->filled('only_available'), fn ($query) => $query->whereHas('kamar', fn ($k) => $k
                ->where('status', \App\Models\Kamar::STATUS_AVAILABLE)));

        match ($request->sort) {
            'price_asc' => $q->orderBy('kamar_min_price_monthly'),
            'price_desc' => $q->orderByDesc('kamar_min_price_monthly'),
            default => $q->latest(),
        };

        $kosts = $q->paginate(9)->withQueryString();

        return view('pages.front.kost-index', [
            'kosts' => $kosts,
            'lokasiList' => Lokasi::orderBy('name')->get(['id', 'name']),
            'tipeList' => TipeKamar::orderBy('name')->get(['id', 'name']),
            'fasilitasList' => Fasilitas::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(string $slug)
    {
        $kost = Kost::active()
            ->with(['lokasi', 'fasilitas'])
            ->withCount(['kamar as kamar_available_count' => fn ($q) => $q->where('status', 'available')])
            ->where('slug', $slug)
            ->firstOrFail();

        $kamars = $kost->kamar()->with(['tipeKamar', 'fasilitas'])->orderBy('number')->get();
        $reviews = $kost->approvedReviews()->with('user:id,name')->latest()->take(6)->get();
        $avgRating = round((float) $kost->approvedReviews()->avg('rating'), 1);

        return view('pages.front.kost-detail', compact('kost', 'kamars', 'reviews', 'avgRating'));
    }
}
