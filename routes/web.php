<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ScannerController;

// ============================================================
// AUTH ROUTES
// ============================================================
Auth::routes();

// Google OAuth
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('google.callback');

// OTP
Route::get('/otp',          [SocialiteController::class, 'otpForm'])  ->name('otp.form');
Route::post('/otp/verify',  [SocialiteController::class, 'otpVerify'])->name('otp.verify');

// Logout override
Route::post('/logout', [SocialiteController::class, 'logout'])->name('logout');

// ============================================================
// PUBLIC ROUTES - toko (tidak perlu login)
// ============================================================
Route::prefix('toko')->name('toko.')->group(function () {
    Route::get('/',              [TokoController::class, 'index'])        ->name('index');
    Route::get('/cari',          [TokoController::class, 'cariBuku'])     ->name('cari');
    Route::get('/by-kategori',   [TokoController::class, 'bukuByKategori'])->name('by-kategori');
    Route::post('/checkout',     [TokoController::class, 'checkout'])     ->name('checkout');
    Route::get('/status/{kode}', [TokoController::class, 'statusPesanan'])->name('status');
});

// Webhook Midtrans - CSRF exempt
Route::post('/toko/webhook', [TokoController::class, 'webhook'])
    ->name('toko.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ============================================================
// PROTECTED ROUTES - harus login
// ============================================================
Route::middleware(['auth', 'check.session'])->group(function () {

    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    // Kategori CRUD
    Route::resource('kategori', KategoriController::class)
        ->except(['show', 'create', 'edit']);

    // Tag Harga PDF - HARUS sebelum resource buku
    Route::post('/buku/tag-harga', [BukuController::class, 'tagHarga'])->name('buku.tag-harga');

    // Buku CRUD
    Route::resource('buku', BukuController::class)
        ->except(['show', 'create', 'edit']);

    // Laporan PDF
    Route::get('/pdf/laporan-buku',     [PdfController::class, 'laporanBuku'])    ->name('pdf.laporan-buku');
    Route::get('/pdf/laporan-kategori', [PdfController::class, 'laporanKategori'])->name('pdf.laporan-kategori');

    // Kasir
    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/',        [KasirController::class, 'index'])   ->name('index');
        Route::get('/cari',    [KasirController::class, 'cariBuku'])->name('cari');
        Route::post('/bayar',  [KasirController::class, 'bayar'])   ->name('bayar');
    });

    // Wilayah
    Route::prefix('wilayah')->name('wilayah.')->group(function () {
        Route::get('/',          [WilayahController::class, 'index'])    ->name('index');
        Route::get('/provinsi',  [WilayahController::class, 'provinsi']) ->name('provinsi');
        Route::get('/kota',      [WilayahController::class, 'kota'])     ->name('kota');
        Route::get('/kecamatan', [WilayahController::class, 'kecamatan'])->name('kecamatan');
        Route::get('/kelurahan', [WilayahController::class, 'kelurahan'])->name('kelurahan');
    });

    // Admin - pesanan masuk (toko)
    Route::get('/admin/pesanan', [TokoController::class, 'pesananAdmin'])->name('admin.pesanan');

    
    
    // Customer - SC3
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/',              [CustomerController::class, 'index'])       ->name('index');
        Route::get('/tambah1',       [CustomerController::class, 'tambah1Form']) ->name('tambah1');
        Route::post('/tambah1',      [CustomerController::class, 'tambah1Store'])->name('tambah1.store');
        Route::get('/tambah2',       [CustomerController::class, 'tambah2Form']) ->name('tambah2');
        Route::post('/tambah2',      [CustomerController::class, 'tambah2Store'])->name('tambah2.store');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])     ->name('destroy');
    });
 
    Route::prefix('scanner')->name('scanner.')->group(function () {
        Route::get('/',      [ScannerController::class, 'index'])    ->name('index');
        Route::get('/cari',  [ScannerController::class, 'cariBuku']) ->name('cari');
    });

});