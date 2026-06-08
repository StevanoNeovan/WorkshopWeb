<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BukuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bukus     = Buku::with('kategori')->orderBy('kode')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.index', compact('bukus', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'       => ['required', 'string', 'max:20', 'unique:buku,kode'],
            'judul'      => ['required', 'string', 'max:500'],
            'pengarang'  => ['required', 'string', 'max:200'],
            'idkategori' => ['required', 'exists:kategori,idkategori'],
            'harga'      => ['required', 'integer', 'min:0'],
        ], [
            'kode.required'       => 'Kode buku wajib diisi.',
            'kode.unique'         => 'Kode buku sudah digunakan.',
            'judul.required'      => 'Judul buku wajib diisi.',
            'pengarang.required'  => 'Nama pengarang wajib diisi.',
            'idkategori.required' => 'Kategori wajib dipilih.',
            'harga.required'      => 'Harga wajib diisi.',
            'harga.integer'       => 'Harga harus berupa angka.',
            'harga.min'           => 'Harga tidak boleh negatif.',
        ]);

        Buku::create([
            'kode'       => strtoupper(trim($request->kode)),
            'judul'      => trim($request->judul),
            'pengarang'  => trim($request->pengarang),
            'idkategori' => $request->idkategori,
            'harga'      => $request->harga,
        ]);

        return redirect()->route('buku.index')
            ->with('success', 'Buku "' . trim($request->judul) . '" berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'kode'       => ['required', 'string', 'max:20', 'unique:buku,kode,' . $id . ',idbuku'],
            'judul'      => ['required', 'string', 'max:500'],
            'pengarang'  => ['required', 'string', 'max:200'],
            'idkategori' => ['required', 'exists:kategori,idkategori'],
            'harga'      => ['required', 'integer', 'min:0'],
        ], [
            'kode.unique'         => 'Kode buku sudah digunakan.',
            'idkategori.required' => 'Kategori wajib dipilih.',
            'harga.required'      => 'Harga wajib diisi.',
        ]);

        $buku->update([
            'kode'       => strtoupper(trim($request->kode)),
            'judul'      => trim($request->judul),
            'pengarang'  => trim($request->pengarang),
            'idkategori' => $request->idkategori,
            'harga'      => $request->harga,
        ]);

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku  = Buku::findOrFail($id);
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Buku "' . $judul . '" berhasil dihapus.');
    }

// Ganti method tagHarga() di app/Http/Controllers/BukuController.php
// use statement di atas class:
// use Picqer\Barcode\BarcodeGeneratorPNG;
 
public function tagHarga(Request $request)
{
    $request->validate([
        'buku_ids'   => ['required', 'array', 'min:1'],
        'buku_ids.*' => ['exists:buku,idbuku'],
        'start_x'    => ['required', 'integer', 'min:1', 'max:5'],
        'start_y'    => ['required', 'integer', 'min:1', 'max:8'],
    ], [
        'buku_ids.required' => 'Pilih minimal 1 buku.',
    ]);
 
    $bukus      = Buku::with('kategori')->whereIn('idbuku', $request->buku_ids)->get();
    $startX     = (int) $request->start_x;
    $startY     = (int) $request->start_y;
    $startIndex = ($startY - 1) * 5 + ($startX - 1);
 
    $generator = new BarcodeGeneratorPNG();
 
    /**
     * Helper: generate barcode PNG base64 dari kode buku
     * Barcode dibuat dari kolom 'kode' (misal: 001, 002, NV-01)
     */
    $makeBarcode = function (string $kode) use ($generator): string {
        return base64_encode(
            $generator->getBarcode(
                strtoupper($kode),
                BarcodeGeneratorPNG::TYPE_CODE_128,
                1,   // width factor per bar
                35   // height px
            )
        );
    };
 
    $labels   = array_fill(0, 40, null);
    $bukuList = $bukus->values()->toArray();
 
    for ($i = 0; $i < count($bukuList); $i++) {
        $pos = $startIndex + $i;
        if ($pos < 40) {
            $buku = $bukuList[$i];
            $labels[$pos] = array_merge($buku, [
                'barcode_png' => $makeBarcode($buku['kode']),
            ]);
        }
    }
 
    // Overflow ke halaman berikutnya
    $pages    = [];
    $pages[]  = $labels;
    $overflow = [];
 
    for ($i = 0; $i < count($bukuList); $i++) {
        if (($startIndex + $i) >= 40) {
            $buku       = $bukuList[$i];
            $overflow[] = array_merge($buku, [
                'barcode_png' => $makeBarcode($buku['kode']),
            ]);
        }
    }
 
    foreach (array_chunk($overflow, 40) as $chunk) {
        $page = array_fill(0, 40, null);
        foreach ($chunk as $idx => $b) {
            $page[$idx] = $b;
        }
        $pages[] = $page;
    }
 
    $pdf = Pdf::loadView('pdf.tag-harga', compact('pages'))
        ->setPaper([0, 0, 595.28, 841.89], 'portrait')
        ->setOption('dpi', 150)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);
 
    return $pdf->download('tag-harga-buku-' . now()->format('Ymd') . '.pdf');
}
}