<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Generate PDF tag harga untuk label kertas TnJ No. 108
     * Layout: 5 kolom x 8 baris = 40 label per halaman
     * User memilih buku via checkbox dan input koordinat X,Y awal
     */
    public function tagHarga(Request $request)
    {
        $request->validate([
            'buku_ids' => ['required', 'array', 'min:1'],
            'buku_ids.*' => ['exists:buku,idbuku'],
            'start_x'  => ['required', 'integer', 'min:1', 'max:5'],
            'start_y'  => ['required', 'integer', 'min:1', 'max:8'],
        ], [
            'buku_ids.required' => 'Pilih minimal 1 buku.',
            'start_x.required'  => 'Koordinat X wajib diisi.',
            'start_y.required'  => 'Koordinat Y wajib diisi.',
        ]);

        $bukus   = Buku::with('kategori')->whereIn('idbuku', $request->buku_ids)->get();
        $startX  = (int) $request->start_x; // kolom 1-5
        $startY  = (int) $request->start_y; // baris 1-8

        // Buat array 40 slot (index 0-39), isi null dulu
        // Posisi = (baris-1)*5 + (kolom-1)
        $startIndex = ($startY - 1) * 5 + ($startX - 1);

        // Bangun array label: null untuk slot kosong, data buku untuk slot terisi
        $labels = array_fill(0, 40, null);
        $bukuList = $bukus->values()->toArray();

        for ($i = 0; $i < count($bukuList); $i++) {
            $pos = $startIndex + $i;
            if ($pos < 40) {
                $labels[$pos] = $bukuList[$i];
            }
        }

        // Jika buku melebihi sisa slot halaman pertama, buat halaman tambahan
        $pages   = [];
        $pages[] = $labels;

        $overflow = [];
        for ($i = 0; $i < count($bukuList); $i++) {
            $pos = $startIndex + $i;
            if ($pos >= 40) {
                $overflow[] = $bukuList[$i];
            }
        }

        // Halaman berikutnya: mulai dari slot 0
        $chunkSize = 40;
        foreach (array_chunk($overflow, $chunkSize) as $chunk) {
            $page = array_fill(0, 40, null);
            foreach ($chunk as $idx => $b) {
                $page[$idx] = $b;
            }
            $pages[] = $page;
        }

        $pdf = Pdf::loadView('pdf.tag-harga', compact('pages'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait') // A4
            ->setOption('dpi', 150)
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download('tag-harga-buku-' . now()->format('Ymd') . '.pdf');
    }
}