<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kost;

class FrontKamarController extends Controller
{
    public function show(string $slug, Kamar $kamar)
    {
        $kost = Kost::active()
            ->with(['lokasi', 'fasilitas'])
            ->withCount(['kamar as kamar_available_count' => fn ($q) => $q->where('status', Kamar::STATUS_AVAILABLE)])
            ->where('slug', $slug)
            ->firstOrFail();

        // Kamar harus benar-benar milik kost pada URL (cegah probing ID lintas kost)
        abort_unless($kamar->kost_id === $kost->id, 404);

        $kamar->load(['tipeKamar', 'fasilitas']);

        return view('pages.front.kamar-detail', compact('kost', 'kamar'));
    }
}
