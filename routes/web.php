<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::redirect('/home', '/dashboard'); // tujuan default middleware guest di Laravel 10

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absensi pegawai
    Route::post('/absen/masuk', [AbsenController::class, 'masuk'])->name('absen.masuk');
    Route::post('/absen/pulang', [AbsenController::class, 'pulang'])->name('absen.pulang');
    Route::post('/absen/izin', [AbsenController::class, 'izin'])->name('absen.izin');
    Route::get('/absen/riwayat', [AbsenController::class, 'riwayat'])->name('absen.riwayat');

    // Khusus admin
    Route::middleware(AdminOnly::class)->prefix('admin')->name('admin.')->group(function () {
        Route::resource('pegawai', PegawaiController::class)->except('show');
        Route::get('rekap', [RekapController::class, 'index'])->name('rekap');
        Route::get('rekap/export', [RekapController::class, 'export'])->name('rekap.export');
    });
});
