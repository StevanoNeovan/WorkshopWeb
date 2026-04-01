<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;

// Auth routes (login, register, logout, dll)
Auth::routes();


use App\Http\Controllers\SocialiteController;

// Google OAuth
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])
    ->name('google.callback');

// OTP
Route::get('/otp', [SocialiteController::class, 'otpForm'])
    ->name('otp.form');

Route::post('/otp/verify', [SocialiteController::class, 'otpVerify'])
    ->name('otp.verify');

// Logout (override bawaan Laravel UI jika perlu)
Route::post('/logout', [SocialiteController::class, 'logout'])
    ->name('logout');
// Protected routes - hanya bisa diakses setelah login
// 'check.session' menyimpan data user ke session setelah login
Route::middleware(['auth', 'check.session'])->group(function () {

    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    // Kategori CRUD (index, store, update, destroy)
    Route::resource('kategori', KategoriController::class)
        ->except(['show', 'create', 'edit']);

    // Tag Harga PDF (POST karena kirim data checkbox + koordinat)
    Route::post('/buku/tag-harga', [BukuController::class, 'tagHarga'])->name('buku.tag-harga');

    // Buku CRUD (index, store, update, destroy)
    Route::resource('buku', BukuController::class)
        ->except(['show', 'create', 'edit']);


    // Laporan Buku - Landscape A4
    Route::get('/pdf/laporan-buku', [PdfController::class, 'laporanBuku'])
        ->name('pdf.laporan-buku');

    // Laporan Kategori - Portrait A4
    Route::get('/pdf/laporan-kategori', [PdfController::class, 'laporanKategori'])
        ->name('pdf.laporan-kategori');

});