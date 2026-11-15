<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('admin.login');

    Route::middleware(['auth', 'can:access-admin'])->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('admin.logout');
    });
});
