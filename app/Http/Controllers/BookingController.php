<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function __construct(protected BookingService $service) {}

    public function index(Request $request)
    {
        $data = Booking::with(['kost:id,name', 'kamar:id,number,kost_id', 'user:id,name,email', 'latestPayment'])
            ->when($request->filled('type'), fn ($q) => $q->where('booking_type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('payment'), fn ($q) => $q->whereHas('latestPayment', fn ($p) => $p->where('status', $request->payment)))
            ->latest()
            ->get();

        return view('pages.booking.index', [
            'data' => $data,
            'filterType' => $request->type,
            'filterStatus' => $request->status,
            'filterPayment' => $request->payment,
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['kost.lokasi', 'kamar.tipeKamar', 'user', 'payments']);

        return view('pages.booking.show', compact('booking'));
    }

    public function confirm(Request $request, Booking $booking)
    {
        return $this->handleAction(fn () => $this->service->confirm($booking), 'Booking dikonfirmasi.');
    }

    public function reject(Request $request, Booking $booking)
    {
        return $this->handleAction(fn () => $this->service->reject($booking), 'Booking ditolak & kamar dilepas.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        return $this->handleAction(fn () => $this->service->cancel($booking), 'Booking dibatalkan & kamar dilepas.');
    }

    public function activate(Request $request, Booking $booking)
    {
        return $this->handleAction(fn () => $this->service->activate($booking), 'Check-in berhasil. Kamar sekarang berstatus Occupied.');
    }

    public function complete(Request $request, Booking $booking)
    {
        return $this->handleAction(fn () => $this->service->complete($booking), 'Check-out berhasil. Kamar tersedia kembali.');
    }

    public function destroy(Booking $booking)
    {
        try {
            in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED, Booking::STATUS_EXPIRED], true)
                || throw new InvalidArgumentException('Hanya booking pending/cancelled/rejected/expired yang dapat dihapus.');

            $this->service->delete($booking->id);
        } catch (InvalidArgumentException $e) {
            if (request()->wantsJson()) {
                return ResponseHelper::error($e->getMessage());
            }

            return back()->with('error', $e->getMessage());
        }

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data booking berhasil dihapus!');
        }

        return redirect()->route('booking.index')->with('success', 'Data booking berhasil dihapus!');
    }

    private function handleAction(callable $action, string $successMessage)
    {
        try {
            $action();
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $successMessage);
    }
}
