<?php
// app/Http/Controllers/ScannerController.php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** GET /scanner - Halaman scanner barcode */
    public function index()
    {
        return view('scanner.index');
    }

    /**
     * AJAX GET /scanner/cari?kode=001
     * Cari buku berdasarkan kode hasil scan barcode
     */
    public function cariBuku(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode', '')));

        if (empty($kode)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode tidak boleh kosong.',
            ], 400);
        }

        $buku = Buku::with('kategori')->where('kode', $kode)->first();

        if (!$buku) {
            return response()->json([
                'status'  => 'error',
                'message' => "Buku dengan kode \"{$kode}\" tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'idbuku'    => $buku->idbuku,
                'kode'      => $buku->kode,
                'judul'     => $buku->judul,
                'pengarang' => $buku->pengarang,
                'kategori'  => $buku->kategori->nama_kategori ?? '-',
                'harga'     => $buku->harga,
                'harga_fmt' => 'Rp ' . number_format($buku->harga, 0, ',', '.'),
            ],
        ]);
    }
}