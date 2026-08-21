<?php

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Payment;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->kamar = Kamar::where('status', 'available')->first();
});

// ----------------------------------------------------------------
// Akses publik tanpa login
// ----------------------------------------------------------------

it('guest dapat membuka halaman publik tanpa login', function () {
    $kost = \App\Models\Kost::active()->first();

    $this->get('/')->assertOk();
    $this->get('/kost')->assertOk();
    $this->get("/kost/{$kost->slug}")->assertOk();

    $kamar = Kamar::where('kost_id', $kost->id)->first();
    $this->get("/kost/{$kost->slug}/kamar/{$kamar->id}")->assertOk();

    $this->get('/tentang')->assertOk();
    $this->get('/kontak')->assertOk();
    $this->get('/cara-kerja')->assertOk();
    $this->get('/cek-booking')->assertOk();
});

it('guest dapat search dan filter kost', function () {
    $this->get('/kost?q=a&min_price=0&max_price=999999999&sort=price_asc')->assertOk();
});

// ----------------------------------------------------------------
// Booking guest
// ----------------------------------------------------------------

function dataGuest(): array
{
    return [
        'guest_name' => 'Budi Tamu',
        'guest_email' => 'budi.tamu@mail.com',
        'guest_phone' => '081298765432',
        'guest_identity_number' => '7312345678900001',
        'start_date' => now()->addDays(2)->toDateString(),
        'duration_months' => 3,
        // percobaan manipulasi harga — harus diabaikan server
        'price_per_month' => 1000,
        'total_amount' => 3000,
        'subtotal' => 1,
    ];
}

it('guest dapat booking tanpa login dan harga dihitung server-side', function () {
    $response = $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest());

    $booking = Booking::where('guest_email', 'budi.tamu@mail.com')->first();

    expect($booking)->not->toBeNull()
        ->and($booking->booking_type)->toBe('guest')
        ->and($booking->user_id)->toBeNull()
        ->and($booking->total_amount)->toBe($this->kamar->price_monthly * 3)
        ->and($booking->subtotal)->toBe($this->kamar->price_monthly * 3)
        ->and($booking->access_token)->toHaveLength(40)
        ->and($booking->booking_code)->toStartWith('BK-');

    $response->assertRedirect(route('guest.booking.success', $booking->access_token));
    expect($this->kamar->fresh()->status)->toBe(Kamar::STATUS_RESERVED);
});

it('double booking pada kamar yang sama ditolak', function () {
    $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest())->assertRedirect();

    $response = $this->post("/booking/guest/store/{$this->kamar->id}", [
        ...dataGuest(),
        'guest_email' => 'penumpang.kedua@mail.com',
    ]);

    $response->assertSessionHas('error'); // kamar sudah reserved → ditolak server-side
    expect(Booking::where('kamar_id', $this->kamar->id)->count())->toBe(1);
});

it('guest tidak dapat mengakses area member dan admin', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->get('/admin/kost')->assertRedirect(route('login'));
    $this->get('/profile')->assertRedirect(route('login'));
});

it('invoice hanya dapat diakses dengan token yang benar', function () {
    $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest());
    $booking = Booking::where('guest_email', 'budi.tamu@mail.com')->first();

    $this->get("/invoice/{$booking->access_token}")->assertOk();
    $this->get('/invoice/token-ngawur-123')->assertNotFound();
    $this->get("/invoice/{$booking->id}")->assertNotFound();
});

it('cek-booking memverifikasi kode dan kontak', function () {
    $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest());
    $booking = Booking::where('guest_email', 'budi.tamu@mail.com')->first();

    // kombinasi benar
    $this->post('/cek-booking', [
        'booking_code' => $booking->booking_code,
        'contact' => 'budi.tamu@mail.com',
    ])->assertRedirect(route('guest.booking.status', $booking->access_token));

    // email salah → tidak boleh tampil
    $this->post('/cek-booking', [
        'booking_code' => $booking->booking_code,
        'contact' => 'orang.lain@mail.com',
    ])->assertSessionHas('error');

    // kode salah
    $this->post('/cek-booking', [
        'booking_code' => 'BK-XXXXXXX-XXXXX',
        'contact' => 'budi.tamu@mail.com',
    ])->assertSessionHas('error');
});

it('member yang login mendapat booking_type member', function () {
    $user = \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', 'penyewa'))->first();

    $this->actingAs($user)
        ->post("/booking/guest/store/{$this->kamar->id}", dataGuest());

    $booking = Booking::where('kamar_id', $this->kamar->id)->first();
    expect($booking->booking_type)->toBe('member')
        ->and($booking->user_id)->toBe($user->id);
});

it('payment dibuat otomatis pending dan mark-paid mengonfirmasi booking', function () {
    $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest());
    $booking = Booking::where('guest_email', 'budi.tamu@mail.com')->first();
    $payment = $booking->latestPayment;

    expect($payment->status)->toBe(Payment::STATUS_PENDING)
        ->and($payment->user_id)->toBeNull()
        ->and($payment->invoice_no)->toStartWith('INV-');

    app(App\Services\BookingService::class)->markPaymentPaid($payment);

    expect($payment->fresh()->status)->toBe(Payment::STATUS_PAID)
        ->and($booking->fresh()->status)->toBe(Booking::STATUS_CONFIRMED);
});

it('booking expired melepas kamar', function () {
    $this->post("/booking/guest/store/{$this->kamar->id}", dataGuest());
    $booking = Booking::where('guest_email', 'budi.tamu@mail.com')->first();

    // paksa melewati batas waktu
    $booking->update(['created_at' => now()->subHours(25)]);

    $count = app(App\Services\BookingService::class)->expireStale();

    expect($count)->toBeGreaterThanOrEqual(1)
        ->and($booking->fresh()->status)->toBe(Booking::STATUS_EXPIRED)
        ->and($this->kamar->fresh()->status)->toBe(Kamar::STATUS_AVAILABLE);
});
