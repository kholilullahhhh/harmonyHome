<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BookingService;

class InvoiceController extends Controller
{
    public function __construct(protected BookingService $service) {}

    public function show(string $token)
    {
        $booking = $this->service->findByAccessToken($token);

        if (! $booking) {
            abort(404);
        }

        $booking->load(['kost.lokasi', 'kamar.tipeKamar', 'latestPayment']);

        return view('pages.front.invoice', compact('booking'));
    }
}
