<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    private const BOOKING_STATUSES = [
        Booking::STATUS_PENDING,
        Booking::STATUS_CONFIRMED,
        Booking::STATUS_ACTIVE,
        Booking::STATUS_COMPLETED,
        Booking::STATUS_CANCELLED,
        Booking::STATUS_REJECTED,
        Booking::STATUS_EXPIRED,
    ];

    public function booking(Request $request)
    {
        [$from, $to] = $this->range($request);

        $base = $this->scopedBookings()->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);

        $data = (clone $base)
            ->with(['kost:id,name,user_id', 'kamar:id,number', 'user:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $ringkasan = collect(self::BOOKING_STATUSES)
            ->mapWithKeys(fn ($status) => [$status => (clone $base)->where('status', $status)->count()]);

        $nilaiAktif = (clone $base)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_ACTIVE])
            ->sum('total_amount');

        return view('pages.laporan.booking', [
            'data' => $data,
            'ringkasan' => $ringkasan,
            'nilaiAktif' => $nilaiAktif,
            'from' => $from,
            'to' => $to,
            'filterStatus' => $request->status,
        ]);
    }

    public function pembayaran(Request $request)
    {
        [$from, $to] = $this->range($request);

        $base = $this->scopedPayments()->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);

        $data = (clone $base)
            ->with(['booking:id,booking_code,booking_type,guest_name,user_id,kost_id,kamar_id', 'booking.kost:id,name', 'booking.kamar:id,number', 'booking.user:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $ringkasan = collect([Payment::STATUS_PAID, Payment::STATUS_PENDING, Payment::STATUS_FAILED, Payment::STATUS_EXPIRED])
            ->mapWithKeys(function ($status) use ($base) {
                $q = (clone $base)->where('status', $status);

                return [$status => ['count' => $q->count(), 'total' => $q->sum('amount')]];
            });

        return view('pages.laporan.pembayaran', [
            'data' => $data,
            'ringkasan' => $ringkasan,
            'from' => $from,
            'to' => $to,
            'filterStatus' => $request->status,
        ]);
    }

    public function pendapatan(Request $request)
    {
        [$from, $to] = $this->range($request);

        $paid = $this->scopedPayments()
            ->where('status', Payment::STATUS_PAID)
            ->whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])
            ->with('booking:id,kost_id', 'booking.kost:id,name')
            ->orderBy('paid_at')
            ->get();

        // Grouping di sisi PHP agar portable lintas driver DB (SQLite/MySQL).
        $perBulan = $paid
            ->groupBy(fn (Payment $p) => $p->paid_at->format('Y-m'))
            ->map(fn (Collection $rows) => (object) [
                'bulan' => $rows->first()->paid_at->translatedFormat('F Y'),
                'jumlah' => $rows->count(),
                'total' => $rows->sum('amount'),
            ])
            ->sortKeys();

        $perKost = $paid
            ->groupBy(fn (Payment $p) => $p->booking->kost->name ?? '-')
            ->map(fn (Collection $rows, $kost) => (object) [
                'kost' => $kost,
                'jumlah' => $rows->count(),
                'total' => $rows->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('pages.laporan.pendapatan', [
            'perBulan' => $perBulan,
            'perKost' => $perKost,
            'totalPendapatan' => $paid->sum('amount'),
            'jumlahTransaksi' => $paid->count(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    /**
     * Rentang tanggal filter; default awal bulan berjalan s.d. hari ini.
     * Input invalid dari request diabaikan (fallback default).
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function range(Request $request): array
    {
        $from = $request->date('date_from') ?? now()->startOfMonth();
        $to = $request->date('date_to') ?? now();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [CarbonImmutable::parse($from), CarbonImmutable::parse($to)];
    }

    /**
     * Pemilik hanya melihat data kost miliknya; admin & super-admin melihat semua.
     */
    private function isStaff(): bool
    {
        $roleSlug = auth()->user()?->role?->slug;

        return in_array($roleSlug, ['super-admin', 'admin'], true);
    }

    private function scopedBookings()
    {
        $query = Booking::query();

        if (! $this->isStaff()) {
            $query->whereHas('kost', fn ($q) => $q->where('user_id', auth()->id()));
        }

        return $query;
    }

    private function scopedPayments()
    {
        $query = Payment::query();

        if (! $this->isStaff()) {
            $query->whereHas('booking.kost', fn ($q) => $q->where('user_id', auth()->id()));
        }

        return $query;
    }
}
