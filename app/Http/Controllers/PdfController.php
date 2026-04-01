<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Laporan Daftar Buku - Landscape A4
     * Cocok landscape karena banyak kolom (no, kode, judul, pengarang, kategori)
     */
    public function laporanBuku()
    {
        $bukus     = Buku::with('kategori')->orderBy('kode')->get();
        $tanggal   = now()->translatedFormat('d F Y');
        $dicetak   = auth()->user()->name;

        $pdf = Pdf::loadView('pdf.laporan-buku', compact('bukus', 'tanggal', 'dicetak'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-koleksi-buku-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Laporan Daftar Kategori - Portrait A4
     * Portrait cukup karena kolom sedikit, tapi ada ringkasan jumlah buku per kategori
     */
    public function laporanKategori()
    {
        $kategoris = Kategori::withCount('buku')->orderBy('nama_kategori')->get();
        $tanggal   = now()->translatedFormat('d F Y');
        $dicetak   = auth()->user()->name;
        $totalBuku = $kategoris->sum('buku_count');

        $pdf = Pdf::loadView('pdf.laporan-kategori', compact('kategoris', 'tanggal', 'dicetak', 'totalBuku'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kategori-buku-' . now()->format('Ymd') . '.pdf');
    }
}