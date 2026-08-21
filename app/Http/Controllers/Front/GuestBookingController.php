<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuestBookingRequest;
use App\Models\Kamar;
use App\Services\BookingService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class GuestBookingController extends Controller
{
    public function __construct(protected BookingService $service) {}

    public function checkout(Kamar $kamar)
    {
        $kamar->load(['kost.lokasi', 'tipeKamar', 'fasilitas']);

        if ($kamar->status !== Kamar::STATUS_AVAILABLE || $kamar->kost->status !== 'active') {
            return redirect()
                ->route('front.kost.show', $kamar->kost->slug)
                ->with('error', 'Kamar ini sedang tidak tersedia untuk dipesan.');
        }

        return view('pages.front.checkout', compact('kamar'));
    }

    public function store(GuestBookingRequest $request, Kamar $kamar)
    {
        $data = $request->validated();

        // Harga & kamar selalu dari database; field harga dari request diabaikan totalan.
        try {
            if (auth()->check()) {
                $user = auth()->user();
                $data['guest_name'] = $user->name;
                $data['guest_email'] = $user->email;
                $data['guest_phone'] = $user->phone ?: $data['guest_phone'];
                $booking = $this->service->createMember($data, $user, $kamar);
            } else {
                $booking = $this->service->createGuest($data, $kamar);
            }
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('guest.booking.success', $booking->access_token)
            ->with('success', 'Booking berhasil dibuat!');
    }

    public function success(string $token)
    {
        $booking = $this->service->findByAccessToken($token);

        if (! $booking) {
            abort(404);
        }

        return view('pages.front.booking-success', compact('booking'));
    }

    public function status(Request $request, string $token)
    {
        $booking = $this->service->findByAccessToken($token);

        if (! $booking) {
            abort(404);
        }

        return view('pages.front.booking-status', compact('booking'));
    }
}
