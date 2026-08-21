<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Payment;
use App\Services\BookingService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $data = Payment::with(['booking:id,booking_code,booking_type,guest_name,user_id,kost_id,kamar_id', 'booking.kost:id,name', 'booking.kamar:id,number'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return view('pages.payment.index', ['data' => $data, 'filterStatus' => $request->status]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.kost.lokasi', 'booking.kamar.tipeKamar', 'booking.user']);

        return view('pages.payment.show', compact('payment'));
    }

    public function markPaid(Request $request, Payment $payment)
    {
        try {
            $this->bookingService->markPaymentPaid($payment);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran '.$payment->invoice_no.' ditandai lunas. Booking dikonfirmasi.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === Payment::STATUS_PAID) {
            return back()->with('error', 'Pembayaran lunas tidak dapat dihapus.');
        }

        $payment->delete();

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data pembayaran berhasil dihapus!');
        }

        return redirect()->route('payment.index')->with('success', 'Data pembayaran berhasil dihapus!');
    }
}
