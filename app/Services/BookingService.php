<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Payment;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BookingService extends BaseService
{
    public const PENDING_TTL_HOURS = 24;

    private const CODE_ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    public function __construct(BookingRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Buat booking GUEST (tanpa akun). Harga selalu dihitung server-side.
     *
     * @throws InvalidArgumentException
     */
    public function createGuest(array $data, Kamar $kamar): Booking
    {
        return $this->createBooking($data, Booking::TYPE_GUEST, null, $kamar);
    }

    /**
     * Buat booking MEMBER (user login).
     *
     * @throws InvalidArgumentException
     */
    public function createMember(array $data, $user, Kamar $kamar): Booking
    {
        return $this->createBooking($data, Booking::TYPE_MEMBER, $user, $kamar);
    }

    private function createBooking(array $data, string $type, $user, Kamar $routeKamar): Booking
    {
        return DB::transaction(function () use ($data, $type, $user, $routeKamar) {
            $kamar = Kamar::with('kost')->lockForUpdate()->findOrFail($routeKamar->id);

            if ($kamar->status !== Kamar::STATUS_AVAILABLE) {
                throw new InvalidArgumentException('Kamar ini baru saja dipesan orang lain atau sedang tidak tersedia.');
            }
            if ($kamar->kost->status !== 'active') {
                throw new InvalidArgumentException('Kost ini sedang tidak menerima pemesanan.');
            }

            $duration = (int) $data['duration_months'];
            $pricePerMonth = (int) $kamar->price_monthly;
            $subtotal = $pricePerMonth * $duration;
            $discount = 0;
            $additionalFee = 0;
            $total = max(0, $subtotal - $discount + $additionalFee);

            $startDate = $data['start_date'];
            $endDate = date('Y-m-d', strtotime($startDate." +{$duration} months"));

            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'user_id' => $user?->id,
                'booking_type' => $type,
                'guest_name' => $data['guest_name'] ?? null,
                'guest_email' => $data['guest_email'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,
                'guest_identity_number' => $data['guest_identity_number'] ?? null,
                'guest_gender' => $data['guest_gender'] ?? null,
                'guest_birth_date' => $data['guest_birth_date'] ?? null,
                'guest_address' => $data['guest_address'] ?? null,
                'kost_id' => $kamar->kost_id,
                'kamar_id' => $kamar->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration_months' => $duration,
                'price_per_month' => $pricePerMonth,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'additional_fee' => $additionalFee,
                'total_amount' => $total,
                'status' => Booking::STATUS_PENDING,
                'notes' => $data['notes'] ?? null,
                'access_token' => Str::random(40),
            ]);

            Payment::create([
                'invoice_no' => $this->generateInvoiceNo(),
                'booking_id' => $booking->id,
                'user_id' => $user?->id,
                'amount' => $total,
                'method' => 'transfer',
                'status' => Payment::STATUS_PENDING,
                'expired_at' => now()->addHours(self::PENDING_TTL_HOURS),
            ]);

            $kamar->update(['status' => Kamar::STATUS_RESERVED]);

            $this->sendConfirmationEmail($booking);

            return $booking;
        });
    }

    // ------------------------------------------------------------------
    // Transisi status admin
    // ------------------------------------------------------------------

    public function confirm(Booking $booking): Booking
    {
        return $this->transition($booking, Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED);
    }

    public function reject(Booking $booking): Booking
    {
        $booking = $this->transition($booking, Booking::STATUS_PENDING, Booking::STATUS_REJECTED);
        $this->releaseRoom($booking);

        return $booking;
    }

    public function cancel(Booking $booking): Booking
    {
        in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)
            || throw new InvalidArgumentException("Booking berstatus {$booking->status} tidak dapat dibatalkan.");

        $booking = $this->transition($booking, $booking->status, Booking::STATUS_CANCELLED);
        $this->releaseRoom($booking);

        return $booking;
    }

    public function activate(Booking $booking): Booking
    {
        $booking = $this->transition($booking, Booking::STATUS_CONFIRMED, Booking::STATUS_ACTIVE);

        $booking->kamar()->update(['status' => Kamar::STATUS_OCCUPIED]);

        return $booking;
    }

    public function complete(Booking $booking): Booking
    {
        $booking = $this->transition($booking, Booking::STATUS_ACTIVE, Booking::STATUS_COMPLETED);

        $booking->kamar()->update(['status' => Kamar::STATUS_AVAILABLE]);

        return $booking;
    }

    private function transition(Booking $booking, string $from, string $to): Booking
    {
        $booking->status === $from
            || throw new InvalidArgumentException("Transisi tidak valid: booking berstatus {$booking->status}, harus {$from}.");

        $booking->update(['status' => $to]);

        return $booking->refresh();
    }

    private function releaseRoom(Booking $booking): void
    {
        $booking->kamar()
            ->where('status', Kamar::STATUS_RESERVED)
            ->update(['status' => Kamar::STATUS_AVAILABLE]);
    }

    // ------------------------------------------------------------------
    // Pembayaran
    // ------------------------------------------------------------------

    public function markPaymentPaid(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            if ($payment->status === Payment::STATUS_PAID) {
                throw new InvalidArgumentException('Invoice ini sudah lunas.');
            }
            if ($payment->status !== Payment::STATUS_PENDING) {
                throw new InvalidArgumentException("Pembayaran berstatus {$payment->status} tidak dapat ditandai lunas.");
            }

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            $booking = $payment->booking;
            if ($booking && $booking->status === Booking::STATUS_PENDING) {
                $booking->update(['status' => Booking::STATUS_CONFIRMED]);
            }

            return $payment->refresh();
        });
    }

    // ------------------------------------------------------------------
    // Expiry: pending melewati batas waktu → expired + kamar dilepas
    // ------------------------------------------------------------------

    public function expireStale(): int
    {
        return DB::transaction(function () {
            $stale = Booking::with('kamar')
                ->where('status', Booking::STATUS_PENDING)
                ->where('created_at', '<', now()->subHours(self::PENDING_TTL_HOURS))
                ->get();

            foreach ($stale as $booking) {
                $booking->update(['status' => Booking::STATUS_EXPIRED]);
                $booking->payments()->where('status', Payment::STATUS_PENDING)->update([
                    'status' => Payment::STATUS_EXPIRED,
                ]);
                $this->releaseRoom($booking);
            }

            return $stale->count();
        });
    }

    // ------------------------------------------------------------------
    // Akses aman tanpa login
    // ------------------------------------------------------------------

    public function findByAccessToken(string $token): ?Booking
    {
        return Booking::with(['kost.lokasi', 'kamar.tipeKamar', 'latestPayment'])
            ->where('access_token', $token)
            ->first();
    }

    /**
     * Verifikasi tracking: kode booking + email ATAU nomor HP.
     */
    public function verifyTracking(string $code, string $contact): ?Booking
    {
        $code = strtoupper(trim($code));
        $contact = strtolower(trim($contact));

        $query = Booking::with(['kost.lokasi', 'kamar.tipeKamar', 'latestPayment'])
            ->where('booking_code', $code)
            ->where(function ($q) use ($contact) {
                $q->whereRaw('LOWER(guest_email) = ?', [$contact])
                    ->orWhereRaw('LOWER(guest_phone) = ?', [$contact])
                    ->orWhereHas('user', fn ($u) => $u
                        ->whereRaw('LOWER(email) = ?', [$contact])
                        ->orWhereRaw('LOWER(phone) = ?', [$contact]));
            });

        return $query->first();
    }

    // ------------------------------------------------------------------
    // Generator kode publik (unik & sulit ditebak, bukan auto-increment)
    // ------------------------------------------------------------------

    private function generateBookingCode(): string
    {
        do {
            $code = 'BK-'.now()->format('Ymd').'-'.$this->randomBlock(5);
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    private function generateInvoiceNo(): string
    {
        do {
            $no = 'INV-'.now()->format('Ym').'-'.$this->randomBlock(5);
        } while (Payment::where('invoice_no', $no)->exists());

        return $no;
    }

    private function randomBlock(int $length): string
    {
        $chars = '';
        $max = strlen(self::CODE_ALPHABET) - 1;

        for ($i = 0; $i < $length; $i++) {
            $chars .= self::CODE_ALPHABET[random_int(0, $max)];
        }

        return $chars;
    }

    private function sendConfirmationEmail(Booking $booking): void
    {
        $email = $booking->customerEmail();

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new \App\Mail\BookingCreatedMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email booking: '.$e->getMessage(), [
                'booking_code' => $booking->booking_code,
            ]);
        }
    }
}
