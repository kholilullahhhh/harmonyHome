<?php

use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User API Routes (dilindungi token + permission RBAC menu user.index)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index'])->name('api.users.index')->middleware('check.permission:user.index');
        Route::get('/{id}', [UserApiController::class, 'show'])->name('api.users.show')->middleware('check.permission:user.index');
    });
});
