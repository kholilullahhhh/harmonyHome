<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;

use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\FrontKostController;
use App\Http\Controllers\Front\FrontKamarController;
use App\Http\Controllers\Front\GuestBookingController;
use App\Http\Controllers\Front\CekBookingController;
use App\Http\Controllers\Front\InvoiceController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('tentang', [PageController::class, 'tentang'])->name('front.tentang');
Route::get('kontak', [PageController::class, 'kontak'])->name('front.kontak');
Route::get('cara-kerja', [PageController::class, 'caraKerja'])->name('front.cara-kerja');

Route::get('kost', [FrontKostController::class, 'index'])->name('front.kost.index');
Route::get('kost/{slug}', [FrontKostController::class, 'show'])->name('front.kost.show');
Route::get('kost/{slug}/kamar/{kamar}', [FrontKamarController::class, 'show'])->name('front.kamar.show');

// Guest booking flow (tanpa login)
Route::prefix('booking/guest')->name('guest.booking.')->group(function () {
    Route::get('checkout/{kamar}', [GuestBookingController::class, 'checkout'])->name('checkout');
    Route::post('store/{kamar}', [GuestBookingController::class, 'store'])->name('store');
    Route::get('success/{token}', [GuestBookingController::class, 'success'])->name('success');
    Route::get('status/{token}', [GuestBookingController::class, 'status'])->name('status');
});

// Tracking & invoice via verifikasi/token
Route::get('cek-booking', [CekBookingController::class, 'index'])->name('cek-booking.index');
Route::post('cek-booking', [CekBookingController::class, 'check'])->name('cek-booking.check');
Route::get('invoice/{token}', [InvoiceController::class, 'show'])->name('invoice.show');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Member dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Personal Profile (member)
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA (/admin/*) — nama route dipertahankan agar RBAC & menu tetap valid
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        // User CRUD routes
        Route::resource('user', UserController::class)->middleware('check.permission:user.index');

        // Role & Menu Management
        Route::resource('role', \App\Http\Controllers\RoleController::class)->middleware('check.permission:role.index');
        Route::resource('menu', \App\Http\Controllers\MenuController::class)->middleware('check.permission:menu.index');
        Route::get('permission', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission.index')->middleware('check.permission:permission.index');
        Route::put('permission', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update')->middleware('check.permission:permission.index');

        // Products CRUD routes
        Route::get('products/export/excel', [ProductsController::class, 'exportExcel'])->name('products.export.excel')->middleware('check.permission:products.index');
        Route::get('products/export/pdf', [ProductsController::class, 'exportPdf'])->name('products.export.pdf')->middleware('check.permission:products.index');
        Route::post('products/import/excel', [ProductsController::class, 'importExcel'])->name('products.import.excel')->middleware('check.permission:products.index');
        Route::resource('products', ProductsController::class)->middleware('check.permission:products.index');

        // Activity Log
        Route::get('activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/data', [\App\Http\Controllers\ActivityLogController::class, 'getData'])->name('activity-log.data');
        Route::get('activity-log/statistics', [\App\Http\Controllers\ActivityLogController::class, 'statistics'])->name('activity-log.statistics');

        // Website Settings
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index')->middleware('check.permission:settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update')->middleware('check.permission:settings.index');
        Route::get('settings/clear-cache', [\App\Http\Controllers\SettingController::class, 'clearCache'])->name('settings.clear-cache')->middleware('check.permission:settings.index');

        // Impersonate Features
        Route::get('impersonate/start/{id}', [\App\Http\Controllers\ImpersonateController::class, 'start'])->name('impersonate.start');
        Route::get('impersonate/stop', [\App\Http\Controllers\ImpersonateController::class, 'stop'])->name('impersonate.stop');

        // System Status & Backup
        Route::get('system/health', [\App\Http\Controllers\SystemController::class, 'health'])->name('system.health')->middleware('check.permission:system.health');
        Route::get('system/backup', [\App\Http\Controllers\SystemController::class, 'backup'])->name('system.backup')->middleware('check.permission:system.health');

        // KostKu domain
        Route::resource('lokasi', \App\Http\Controllers\LokasiController::class)->middleware('check.permission:lokasi.index');
        Route::resource('tipe-kamar', \App\Http\Controllers\TipeKamarController::class)->middleware('check.permission:tipe-kamar.index');
        Route::resource('fasilitas', \App\Http\Controllers\FasilitasController::class)->middleware('check.permission:fasilitas.index');
        Route::resource('kost', \App\Http\Controllers\KostController::class)->middleware('check.permission:kost.index');
        Route::resource('kamar', \App\Http\Controllers\KamarController::class)->middleware('check.permission:kamar.index');

        Route::resource('booking', \App\Http\Controllers\BookingController::class)
            ->only(['index', 'show', 'destroy'])
            ->middleware('check.permission:booking.index');
        Route::post('booking/{booking}/confirm', [\App\Http\Controllers\BookingController::class, 'confirm'])->name('booking.confirm')->middleware('check.permission:booking.index');
        Route::post('booking/{booking}/reject', [\App\Http\Controllers\BookingController::class, 'reject'])->name('booking.reject')->middleware('check.permission:booking.index');
        Route::post('booking/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])->name('booking.cancel')->middleware('check.permission:booking.index');
        Route::post('booking/{booking}/activate', [\App\Http\Controllers\BookingController::class, 'activate'])->name('booking.activate')->middleware('check.permission:booking.index');
        Route::post('booking/{booking}/complete', [\App\Http\Controllers\BookingController::class, 'complete'])->name('booking.complete')->middleware('check.permission:booking.index');

        Route::resource('payment', \App\Http\Controllers\PaymentController::class)
            ->only(['index', 'show'])
            ->middleware('check.permission:payment.index');
        Route::post('payment/{payment}/mark-paid', [\App\Http\Controllers\PaymentController::class, 'markPaid'])->name('payment.mark-paid')->middleware('check.permission:payment.index');

        // Laporan
        Route::get('laporan/booking', [\App\Http\Controllers\ReportController::class, 'booking'])->name('laporan.booking.index')->middleware('check.permission:laporan.booking');
        Route::get('laporan/pembayaran', [\App\Http\Controllers\ReportController::class, 'pembayaran'])->name('laporan.pembayaran.index')->middleware('check.permission:laporan.pembayaran');
        Route::get('laporan/pendapatan', [\App\Http\Controllers\ReportController::class, 'pendapatan'])->name('laporan.pendapatan.index')->middleware('check.permission:laporan.pendapatan');
    });
});
