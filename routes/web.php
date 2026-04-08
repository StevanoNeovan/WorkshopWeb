<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\WilayahController;
    

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

use App\Http\Controllers\TokoController;
 
// ============================================================
// PUBLIC ROUTES - tidak perlu login (untuk guest/customer)
// ============================================================
Route::prefix('toko')->name('toko.')->group(function () {
    Route::get('/',                [TokoController::class, 'index'])        ->name('index');
    Route::get('/cari',            [TokoController::class, 'cariBuku'])     ->name('cari');
    Route::get('/by-kategori',     [TokoController::class, 'bukuByKategori'])->name('by-kategori');
    Route::post('/checkout',       [TokoController::class, 'checkout'])     ->name('checkout');
    Route::get('/status/{kode}',   [TokoController::class, 'statusPesanan'])->name('status');
});
 
// Webhook Midtrans - CSRF exempt!
Route::post('/toko/webhook', [TokoController::class, 'webhook'])
    ->name('toko.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/',           [KasirController::class, 'index'])    ->name('index');
        Route::get('/cari',       [KasirController::class, 'cariBuku']) ->name('cari');
        Route::post('/bayar',     [KasirController::class, 'bayar'])    ->name('bayar');
    });
 
    Route::prefix('wilayah')->name('wilayah.')->group(function () {
        Route::get('/',           [WilayahController::class, 'index'])     ->name('index');
        Route::get('/provinsi',   [WilayahController::class, 'provinsi'])  ->name('provinsi');
        Route::get('/kota',       [WilayahController::class, 'kota'])      ->name('kota');
        Route::get('/kecamatan',  [WilayahController::class, 'kecamatan']) ->name('kecamatan');
        Route::get('/kelurahan',  [WilayahController::class, 'kelurahan']) ->name('kelurahan');
    });
});

