<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $penyewa = User::whereHas('role', fn ($q) => $q->where('slug', 'penyewa'))->orderBy('id')->get();
        $kamarTersedia = Kamar::with('kost')->where('status', 'available')->get();

        // [jumlah, status booking, status payment]
        $rencana = [
            [6, Booking::STATUS_ACTIVE, Payment::STATUS_PAID],
            [6, Booking::STATUS_COMPLETED, Payment::STATUS_PAID],
            [4, Booking::STATUS_PENDING, Payment::STATUS_PENDING],
            [3, Booking::STATUS_CONFIRMED, Payment::STATUS_PAID],
            [2, Booking::STATUS_CANCELLED, Payment::STATUS_CANCELLED],
            [1, Booking::STATUS_REJECTED, Payment::STATUS_FAILED],
        ];

        $kamarIndex = 0;
        $seq = 0;

        foreach ($rencana as [$jumlah, $statusBooking, $statusPayment]) {
            for ($i = 0; $i < $jumlah; $i++) {
                if (! isset($kamarTersedia[$kamarIndex])) {
                    break;
                }
                /** @var Kamar $kamar */
                $kamar = $kamarTersedia[$kamarIndex++];
                $penyewaUser = $penyewa[$seq % $penyewa->count()];
                $durasi = rand(1, 12);
                $seq++;

                // Rentang tanggal sesuai status
                [$mulai, $selesai] = match ($statusBooking) {
                    Booking::STATUS_ACTIVE => [now()->subMonths(rand(0, 2))->startOfMonth(), now()->addMonths(rand(1, 6))],
                    Booking::STATUS_COMPLETED => [now()->subMonths(rand(8, 14)), now()->subMonths(rand(2, 7))],
                    Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED => [now()->addDays(rand(3, 30)), null],
                    default => [now()->subDays(rand(5, 40)), null],
                };
                $selesai ??= $mulai->copy()->addMonths($durasi);
                $durasi = max(1, (int) round($mulai->diffInMonths($selesai)));

                DB::transaction(function () use (&$seq, $penyewaUser, $kamar, $durasi, $mulai, $selesai, $statusBooking, $statusPayment) {
                    $booking = Booking::create([
                        'booking_code' => sprintf('BK-%s-%04d', now()->format('Ym'), ++$this->counter),
                        'user_id' => $penyewaUser->id,
                        'booking_type' => Booking::TYPE_MEMBER,
                        'kost_id' => $kamar->kost_id,
                        'kamar_id' => $kamar->id,
                        'start_date' => $mulai,
                        'end_date' => $selesai,
                        'duration_months' => $durasi,
                        'price_per_month' => $kamar->price_monthly,
                        'subtotal' => $kamar->price_monthly * $durasi,
                        'total_amount' => $kamar->price_monthly * $durasi,
                        'status' => $statusBooking,
                        'notes' => fake()->boolean(30) ? 'Mohon konfirmasi jam kedatangan.' : null,
                        'access_token' => \Illuminate\Support\Str::random(40),
                    ]);

                    $dibayar = in_array($statusPayment, [Payment::STATUS_PAID], true);
                    Payment::create([
                        'invoice_no' => sprintf('INV-%s-%04d', now()->format('Ym'), $this->counter),
                        'booking_id' => $booking->id,
                        'user_id' => $penyewaUser->id,
                        'amount' => $booking->total_amount,
                        'method' => $dibayar ? 'transfer' : null,
                        'status' => $statusPayment,
                        'paid_at' => $dibayar ? $mulai->copy()->subDays(rand(1, 5)) : null,
                        'expired_at' => in_array($statusPayment, [Payment::STATUS_PENDING, Payment::STATUS_EXPIRED], true)
                            ? now()->addDay()
                            : null,
                    ]);

                    // Sinkronkan status kamar: aktif = occupied, menunggu bayar/konfirmasi = reserved
                    if ($statusBooking === Booking::STATUS_ACTIVE) {
                        $kamar->update(['status' => Kamar::STATUS_OCCUPIED]);
                    } elseif (in_array($statusBooking, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
                        $kamar->update(['status' => Kamar::STATUS_RESERVED]);
                    }
                });
            }
        }

        // ------------------------------------------------------------------
        // Booking GUEST (tanpa akun)
        // ------------------------------------------------------------------
        $guestData = [
            ['Muhammad Rizki', 'rizki.guest@mail.com', '081234567001', Booking::STATUS_ACTIVE, Payment::STATUS_PAID],
            ['Siti Nurhaliza', 'siti.guest@mail.com', '081234567002', Booking::STATUS_PENDING, Payment::STATUS_PENDING],
            ['Andi Saputra', 'andi.guest@mail.com', '081234567003', Booking::STATUS_CONFIRMED, Payment::STATUS_PAID],
            ['Dewi Lestari', 'dewi.guest@mail.com', '081234567004', Booking::STATUS_EXPIRED, Payment::STATUS_EXPIRED],
        ];

        foreach ($guestData as [$nama, $email, $hp, $statusBooking, $statusPayment]) {
            $kamar = Kamar::with('kost')->where('status', 'available')->inRandomOrder()->first();
            if (! $kamar) {
                break;
            }
            $durasi = rand(1, 6);
            $mulai = now()->addDays(rand(1, 14));

            DB::transaction(function () use ($nama, $email, $hp, $kamar, $durasi, $mulai, $statusBooking, $statusPayment) {
                $booking = Booking::create([
                    'booking_code' => sprintf('BK-%s-%04d', now()->format('Ym'), ++$this->counter),
                    'user_id' => null,
                    'booking_type' => Booking::TYPE_GUEST,
                    'guest_name' => $nama,
                    'guest_email' => $email,
                    'guest_phone' => $hp,
                    'guest_identity_number' => (string) random_int(1000000000000000, 9999999999999999),
                    'guest_gender' => fake()->randomElement(['L', 'P']),
                    'guest_address' => fake()->address(),
                    'kost_id' => $kamar->kost_id,
                    'kamar_id' => $kamar->id,
                    'start_date' => $mulai,
                    'end_date' => $mulai->copy()->addMonths($durasi),
                    'duration_months' => $durasi,
                    'price_per_month' => $kamar->price_monthly,
                    'subtotal' => $kamar->price_monthly * $durasi,
                    'total_amount' => $kamar->price_monthly * $durasi,
                    'status' => $statusBooking,
                    'access_token' => \Illuminate\Support\Str::random(40),
                ]);

                $dibayar = $statusPayment === Payment::STATUS_PAID;
                Payment::create([
                    'invoice_no' => sprintf('INV-%s-%04d', now()->format('Ym'), $this->counter),
                    'booking_id' => $booking->id,
                    'user_id' => null,
                    'amount' => $booking->total_amount,
                    'method' => $dibayar ? 'transfer' : null,
                    'status' => $statusPayment,
                    'paid_at' => $dibayar ? now()->subDays(rand(0, 2)) : null,
                    'expired_at' => in_array($statusPayment, [Payment::STATUS_PENDING, Payment::STATUS_EXPIRED], true)
                        ? now()->subHours($statusPayment === Payment::STATUS_EXPIRED ? 2 : -24)
                        : null,
                ]);

                if ($statusBooking === Booking::STATUS_ACTIVE) {
                    $kamar->update(['status' => Kamar::STATUS_OCCUPIED]);
                } elseif ($statusBooking === Booking::STATUS_PENDING || $statusBooking === Booking::STATUS_CONFIRMED) {
                    $kamar->update(['status' => Kamar::STATUS_RESERVED]);
                }
            });
        }

        // Review dari booking member yang sudah dibayar (completed + active)
        $dibayar = Booking::with(['user', 'kost'])
            ->where('booking_type', Booking::TYPE_MEMBER)
            ->whereNotNull('user_id')
            ->whereIn('status', [Booking::STATUS_COMPLETED, Booking::STATUS_ACTIVE])
            ->get();

        $komentar = [
            'Lingkungan kost nyaman dan bersih, penghuni ramah.',
            'Lokasi strategis, dekat kampus dan minimarket.',
            'Pemilik responsif, perbaikan cepat ditangani.',
            'WiFi kadang lambat di kamar paling belakang.',
            'Kamar sesuai foto, fasilitas lengkap.',
            'Parkir luas, keamanan cukup baik.',
            'Harga sepadan dengan fasilitas yang didapat.',
            'Air kadang kecil di jam sibuk sore.',
            'Suasana tenang, cocok untuk fokus belajar.',
            'Dapur bersih dan bisa dipakai masak.',
            'Kamar mandi dalam selalu bersih.',
            'AC dingin, tidur jadi nyaman.',
        ];

        foreach ($dibayar as $n => $booking) {
            Review::updateOrCreate(
                ['user_id' => $booking->user_id, 'kost_id' => $booking->kost_id],
                [
                    'booking_id' => $booking->id,
                    'rating' => rand(3, 5),
                    'comment' => $komentar[$n % count($komentar)],
                    'is_approved' => $n % 5 !== 4, // sebagian kecil menunggu moderasi
                ]
            );
        }

        // Review tambahan dari penyewa yang pernah tinggal sebelumnya
        $semuaPenyewa = User::whereHas('role', fn ($q) => $q->where('slug', 'penyewa'))->get();
        $target = 22;
        $percobaan = 0;
        while (Review::count() < $target && $percobaan++ < 100) {
            $user = $semuaPenyewa->random();
            $kostId = DB::table('kosts')->inRandomOrder()->value('id');
            if (! $kostId || Review::where('user_id', $user->id)->where('kost_id', $kostId)->exists()) {
                continue;
            }
            Review::create([
                'user_id' => $user->id,
                'kost_id' => $kostId,
                'rating' => rand(3, 5),
                'comment' => $komentar[array_rand($komentar)],
                'is_approved' => fake()->boolean(85),
            ]);
        }

        $this->command->info('Seeded '.Booking::count().' booking, '.Payment::count().' payment, '.Review::count().' review.');
    }

    private int $counter = 0;
}
