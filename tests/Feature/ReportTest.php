<?php

use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->seed(DatabaseSeeder::class);
});

$laporanRoutes = fn () => [
    route('laporan.booking.index'),
    route('laporan.pembayaran.index'),
    route('laporan.pendapatan.index'),
];

it('admin dapat membuka semua halaman laporan', function () use ($laporanRoutes) {
    $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

    $this->actingAs($admin);

    foreach ($laporanRoutes() as $url) {
        $this->get($url)->assertOk();
    }
});

it('pemilik dapat melihat laporan', function () use ($laporanRoutes) {
    $pemilik = User::whereHas('role', fn ($q) => $q->where('slug', 'pemilik'))->first();

    $this->actingAs($pemilik);

    foreach ($laporanRoutes() as $url) {
        $this->get($url)->assertOk();
    }
});

it('penyewa dan user tidak dapat mengakses laporan', function () use ($laporanRoutes) {
    foreach (['penyewa', 'user'] as $roleSlug) {
        $user = User::whereHas('role', fn ($q) => $q->where('slug', $roleSlug))->first();

        $this->actingAs($user);

        foreach ($laporanRoutes() as $url) {
            $this->get($url)->assertForbidden();
        }
    }
});

it('tamu diarahkan ke login saat membuka laporan', function () use ($laporanRoutes) {
    foreach ($laporanRoutes() as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }
});

it('halaman laporan booking menampilkan data dan filter bekerja', function () {
    $admin = User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

    $this->actingAs($admin)
        ->get(route('laporan.booking.index'))
        ->assertOk()
        ->assertSee('BK-');

    // Filter status yang tidak punya data tetap 200 dengan empty state
    $this->actingAs($admin)
        ->get(route('laporan.booking.index', ['status' => 'expired', 'date_from' => now()->subYear()->toDateString(), 'date_to' => now()->toDateString()]))
        ->assertOk();
});
