<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan daftar buku beserta relasi kategori.
     */
    public function index()
    {
        $bukus     = Buku::with('kategori')->orderBy('kode')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.index', compact('bukus', 'kategoris'));
    }

    /**
     * Simpan buku baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode'       => ['required', 'string', 'max:20', 'unique:buku,kode'],
            'judul'      => ['required', 'string', 'max:500'],
            'pengarang'  => ['required', 'string', 'max:200'],
            'idkategori' => ['required', 'exists:kategori,idkategori'],
        ], [
            'kode.required'       => 'Kode buku wajib diisi.',
            'kode.unique'         => 'Kode buku sudah digunakan.',
            'kode.max'            => 'Kode buku maksimal 20 karakter.',
            'judul.required'      => 'Judul buku wajib diisi.',
            'pengarang.required'  => 'Nama pengarang wajib diisi.',
            'idkategori.required' => 'Kategori wajib dipilih.',
            'idkategori.exists'   => 'Kategori tidak valid.',
        ]);

        Buku::create([
            'kode'       => strtoupper(trim($request->kode)),
            'judul'      => trim($request->judul),
            'pengarang'  => trim($request->pengarang),
            'idkategori' => $request->idkategori,
        ]);

        return redirect()
            ->route('buku.index')
            ->with('success', 'Buku "' . trim($request->judul) . '" berhasil ditambahkan.');
    }

    /**
     * Update buku.
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'kode'       => ['required', 'string', 'max:20', 'unique:buku,kode,' . $id . ',idbuku'],
            'judul'      => ['required', 'string', 'max:500'],
            'pengarang'  => ['required', 'string', 'max:200'],
            'idkategori' => ['required', 'exists:kategori,idkategori'],
        ], [
            'kode.unique'         => 'Kode buku sudah digunakan.',
            'idkategori.required' => 'Kategori wajib dipilih.',
        ]);

        $buku->update([
            'kode'       => strtoupper(trim($request->kode)),
            'judul'      => trim($request->judul),
            'pengarang'  => trim($request->pengarang),
            'idkategori' => $request->idkategori,
        ]);

        return redirect()
            ->route('buku.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Hapus buku.
     */
    public function destroy($id)
    {
        $buku  = Buku::findOrFail($id);
        $judul = $buku->judul;
        $buku->delete();

        return redirect()
            ->route('buku.index')
            ->with('success', 'Buku "' . $judul . '" berhasil dihapus.');
    }
}