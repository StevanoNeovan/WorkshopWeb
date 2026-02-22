<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;

// Auth routes (login, register, logout, dll)
Auth::routes();

// Protected routes - hanya bisa diakses setelah login
// 'check.session' menyimpan data user ke session setelah login
Route::middleware(['auth', 'check.session'])->group(function () {

    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    // Kategori CRUD (index, store, update, destroy)
    Route::resource('kategori', KategoriController::class)
        ->except(['show', 'create', 'edit']);
    
    // Buku CRUD (index, store, update, destroy)
    Route::resource('buku', BukuController::class)
        ->except(['show', 'create', 'edit']);

});