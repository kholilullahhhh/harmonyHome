<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Read-model aggregator untuk dashboard admin KostKu.
 * Semua angka dihitung via aggregate query; tidak ada query di Blade.
 */
class DashboardService
{
    /**
     * Ringkasan lengkap untuk dashboard admin.
     */
    public function adminOverview(): array
    {
        $now = now();

        return [
            'kost' => $this->kostStats(),
            'kamar' => $this->kamarStats(),
            'booking' => $this->bookingStats(),
            'pengguna' => $this->userStats(),
            'pendapatan' => $this->revenueStats($now),
            'chartBooking' => $this->bookingChart(12, $now),
            'perluTindakan' => $this->actionNeededStats($now),
            'bookingTerbaru' => $this->recentBookings(),
            'kamarPerhatian' => $this->kamarsNeedingAttention(),
            'aktivitas' => $this->recentActivities(),
        ];
    }

    /**
     * Payload chart untuk dikirim ke JavaScript (aman via JSON script tag).
     */
    public function chartPayload(array $overview): array
    {
        return [
            'kamar' => [
                'labels' => ['Available', 'Reserved', 'Occupied', 'Maintenance'],
                'series' => array_values(array_intersect_key(
                    $overview['kamar']['per_status'],
                    array_flip([Kamar::STATUS_AVAILABLE, Kamar::STATUS_RESERVED, Kamar::STATUS_OCCUPIED, Kamar::STATUS_MAINTENANCE])
                ) + array_fill_keys([Kamar::STATUS_AVAILABLE, Kamar::STATUS_RESERVED, Kamar::STATUS_OCCUPIED, Kamar::STATUS_MAINTENANCE], 0)),
            ],
            'booking' => $overview['chartBooking'],
        ];
    }

    private function kostStats(): array
    {
        return [
            'total' => Kost::count(),
        ];
    }

    private function kamarStats(): array
    {
        $perStatus = Kamar::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $perStatus->sum(),
            'tersedia' => (int) ($perStatus[Kamar::STATUS_AVAILABLE] ?? 0),
            'reserved' => (int) ($perStatus[Kamar::STATUS_RESERVED] ?? 0),
            'terisi' => (int) ($perStatus[Kamar::STATUS_OCCUPIED] ?? 0),
            'maintenance' => (int) ($perStatus[Kamar::STATUS_MAINTENANCE] ?? 0),
            'per_status' => $perStatus->map(fn ($v) => (int) $v)->all(),
        ];
    }

    private function bookingStats(): array
    {
        $perStatus = Booking::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $perStatus->sum(),
            'pending' => (int) ($perStatus[Booking::STATUS_PENDING] ?? 0),
            'confirmed' => (int) ($perStatus[Booking::STATUS_CONFIRMED] ?? 0),
            'active' => (int) ($perStatus[Booking::STATUS_ACTIVE] ?? 0),
            'completed' => (int) ($perStatus[Booking::STATUS_COMPLETED] ?? 0),
            'cancelled' => (int) ($perStatus[Booking::STATUS_CANCELLED] ?? 0),
            'rejected' => (int) ($perStatus[Booking::STATUS_REJECTED] ?? 0),
            'expired' => (int) ($perStatus[Booking::STATUS_EXPIRED] ?? 0),
        ];
    }

    private function userStats(): array
    {
        $byRole = fn (string $slug) => User::whereHas('role', fn ($q) => $q->where('slug', $slug))->count();

        return [
            'penyewa' => $byRole('penyewa'),
            'pemilik' => $byRole('pemilik'),
        ];
    }

    /**
     * Pendapatan = payment berstatus PAID (sudah lunas).
     * Booking pending/cancelled/rejected/expired TIDAK dihitung sebagai pendapatan.
     */
    private function revenueStats($now): array
    {
        $paid = Payment::query()->where('status', Payment::STATUS_PAID);

        $sumBetween = function ($from, $to) use ($paid) {
            return (clone $paid)->whereBetween('paid_at', [$from, $to])->sum('amount');
        };

        return [
            'hari_ini' => (int) $sumBetween($now->copy()->startOfDay(), $now->copy()->endOfDay()),
            'bulan_ini' => (int) $sumBetween($now->copy()->startOfMonth(), $now->copy()->endOfMonth()),
            'tahun_ini' => (int) $sumBetween($now->copy()->startOfYear(), $now->copy()->endOfYear()),
            'total' => (int) (clone $paid)->sum('amount'),
        ];
    }

    /**
     * Statistik booking per bulan (12 bulan terakhir termasuk bulan berjalan).
     * Grouping dilakukan di PHP agar portable lintas driver DB.
     */
    private function bookingChart(int $months, $now): array
    {
        $cutoff = $now->copy()->subMonths($months - 1)->startOfMonth();

        $rows = Booking::query()
            ->where('created_at', '>=', $cutoff)
            ->get(['created_at', 'status']);

        $labels = [];
        $keys = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = $now->copy()->subMonths($i)->format('Y-m');
            $keys[] = $key;
            $labels[] = $now->copy()->subMonths($i)->translatedFormat('M Y');
        }

        // Array native: increment pada elemen Collection overload tidak berfungsi.
        $buckets = [];
        foreach ($keys as $k) {
            $buckets[$k] = ['total' => 0, 'completed' => 0, 'cancelled' => 0];
        }

        foreach ($rows as $booking) {
            $key = $booking->created_at?->format('Y-m');

            if (! isset($buckets[$key])) {
                continue;
            }

            $buckets[$key]['total']++;

            if ($booking->status === Booking::STATUS_COMPLETED) {
                $buckets[$key]['completed']++;
            } elseif ($booking->status === Booking::STATUS_CANCELLED) {
                $buckets[$key]['cancelled']++;
            }
        }

        return [
            'labels' => $labels,
            'total' => array_values(array_column($buckets, 'total')),
            'completed' => array_values(array_column($buckets, 'completed')),
            'cancelled' => array_values(array_column($buckets, 'cancelled')),
        ];
    }

    private function actionNeededStats($now): array
    {
        // Pending yang tersisa < 4 jam sebelum batas 24 jam dianggap mendekati expiry.
        $expiringWindow = max(1, BookingService::PENDING_TTL_HOURS - 4);

        return [
            'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'menunggu_checkin' => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
            'mendekati_expiry' => Booking::where('status', Booking::STATUS_PENDING)
                ->where('created_at', '<=', $now->copy()->subHours($expiringWindow))
                ->count(),
        ];
    }

    /**
     * Booking terbaru dengan kolom eksplisit (access_token tidak ikut ke view).
     */
    private function recentBookings(): Collection
    {
        return Booking::query()
            ->with(['kost:id,name', 'kamar:id,number', 'user:id,name'])
            ->select(['id', 'booking_code', 'booking_type', 'guest_name', 'user_id', 'kost_id', 'kamar_id', 'total_amount', 'status', 'created_at'])
            ->latest()
            ->limit(8)
            ->get();
    }

    private function kamarsNeedingAttention(): Collection
    {
        return Kamar::query()
            ->with('kost:id,name')
            ->whereIn('status', [Kamar::STATUS_MAINTENANCE, Kamar::STATUS_RESERVED])
            ->select(['id', 'number', 'status', 'kost_id', 'updated_at'])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();
    }

    private function recentActivities(): Collection
    {
        return ActivityLog::query()
            ->with('user:id,name')
            ->select(['id', 'user_id', 'action', 'description', 'subject_type', 'created_at'])
            ->latest()
            ->limit(8)
            ->get();
    }
}
