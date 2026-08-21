<?php

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardService;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

it('super-admin dapat membuka dashboard admin', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->first();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
});

it('admin dapat membuka dashboard admin', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
});

it('penyewa mendapat dashboard member tanpa error', function () {
    $this->seed(DatabaseSeeder::class);

    $penyewa = User::whereHas('role', fn ($q) => $q->where('slug', 'penyewa'))->first();

    $this->actingAs($penyewa)->get(route('dashboard'))->assertOk();
});

it('tamu diarahkan ke login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('dashboard tidak error saat database kosong', function () {
    // Roles default dibuat oleh migration; cukup buat user admin tanpa data domain.
    $role = Role::where('slug', 'super-admin')->first() ?? Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
    $admin = User::create([
        'name' => 'Admin Kosong',
        'email' => 'kosong@mail.com',
        'password' => 'password',
        'role_id' => $role->id,
    ]);

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Belum ada booking.')
        ->assertSee('Belum ada kamar terdaftar.');
});

it('menampilkan booking terbaru dengan kode dan status yang benar', function () {
    $this->seed(DatabaseSeeder::class);

    $booking = Booking::query()->latest()->first();
    $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'super-admin'))->first();

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee($booking->booking_code)
        ->assertSee(ucfirst($booking->status));
});

it('statistik kamar dihitung dari database', function () {
    $this->seed(DatabaseSeeder::class);

    $expected = Kamar::selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status');
    $overview = app(DashboardService::class)->adminOverview();

    expect($overview['kamar']['total'])->toBe((int) $expected->sum())
        ->and($overview['kamar']['tersedia'])->toBe((int) ($expected['available'] ?? 0))
        ->and($overview['kamar']['terisi'])->toBe((int) ($expected['occupied'] ?? 0))
        ->and($overview['kamar']['reserved'])->toBe((int) ($expected['reserved'] ?? 0))
        ->and($overview['kamar']['maintenance'])->toBe((int) ($expected['maintenance'] ?? 0));
});

it('pendapatan hanya menghitung payment lunas', function () {
    $this->seed(DatabaseSeeder::class);

    $expectedTotal = (int) Payment::where('status', Payment::STATUS_PAID)->sum('amount');
    $overview = app(DashboardService::class)->adminOverview();

    expect($overview['pendapatan']['total'])->toBe($expectedTotal);
});

it('chart payload berisi struktur yang valid', function () {
    $this->seed(DatabaseSeeder::class);

    $service = app(DashboardService::class);
    $payload = $service->chartPayload($service->adminOverview());

    expect(count($payload['kamar']['labels']))->toBe(4)
        ->and(count($payload['kamar']['series']))->toBe(4)
        ->and(count($payload['booking']['labels']))->toBe(12)
        ->and(count($payload['booking']['total']))->toBe(12);
});
