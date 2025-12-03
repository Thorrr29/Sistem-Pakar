<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PenyakitGigiController;
use App\Http\Controllers\Admin\GejalaController;
use App\Http\Controllers\Admin\AturanController;
use App\Http\Controllers\Admin\KonsultasiController as AdminKonsultasiController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route untuk halaman publik (pasien), admin, dan fitur autentikasi.
|
*/

// Halaman landing / beranda.
Route::get('/', function () {
    return view('front.landing');
})->name('landing');

// Halaman tentang sistem pakar.
Route::get('/tentang', function () {
    return view('front.tentang');
})->name('tentang');

// Flow konsultasi untuk pasien (tanpa login).
Route::get('/konsultasi', [KonsultasiController::class, 'create'])->name('konsultasi.create');
Route::post('/konsultasi', [KonsultasiController::class, 'store'])->name('konsultasi.store');
Route::get('/konsultasi/{konsultasi}', [KonsultasiController::class, 'show'])->name('konsultasi.show');
Route::get('/konsultasi/{konsultasi}/pdf', [KonsultasiController::class, 'pdf'])->name('konsultasi.pdf');

// Dashboard default dari Laravel Breeze (opsional untuk user biasa).
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil user (dari Breeze).
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route untuk admin (hanya bisa diakses oleh user dengan role admin).
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('penyakit', PenyakitGigiController::class);
        Route::resource('gejala', GejalaController::class);
        Route::resource('aturan', AturanController::class);
        Route::resource('konsultasi', AdminKonsultasiController::class)->only(['index', 'show']);
    });

require __DIR__.'/auth.php';
