<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class CekBookingController extends Controller
{
    public function __construct(protected BookingService $service) {}

    public function index()
    {
        return view('pages.front.cek-booking');
    }

    public function check(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:30'],
            'contact' => ['required', 'string', 'max:100'],
        ], [
            'booking_code.required' => 'Kode booking wajib diisi.',
            'contact.required' => 'Email atau nomor HP wajib diisi.',
        ]);

        $booking = $this->service->verifyTracking($request->booking_code, $request->contact);

        if (! $booking) {
            return back()
                ->withInput($request->only('booking_code'))
                ->with('error', 'Booking tidak ditemukan. Periksa kembali kode booking dan email/nomor HP Anda.');
        }

        return redirect()->route('guest.booking.status', $booking->access_token);
    }
}
