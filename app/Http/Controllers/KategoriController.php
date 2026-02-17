<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan daftar kategori beserta jumlah buku (withCount).
     */
    public function index()
    {
        $kategoris = Kategori::withCount('buku')->orderBy('nama_kategori')->get();

        return view('kategori.index', compact('kategoris'));
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                'unique:kategori,nama_kategori',
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah terdaftar.',
            'nama_kategori.max'      => 'Nama kategori maksimal 100 karakter.',
        ]);

        Kategori::create([
            'nama_kategori' => ucfirst(trim($request->nama_kategori)),
        ]);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori "' . ucfirst(trim($request->nama_kategori)) . '" berhasil ditambahkan.');
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                'unique:kategori,nama_kategori,' . $id . ',idkategori',
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah terdaftar.',
        ]);

        $kategori->update([
            'nama_kategori' => ucfirst(trim($request->nama_kategori)),
        ]);

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori (buku di dalamnya ikut terhapus karena cascade).
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $nama     = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()
            ->route('kategori.index')
            ->with('success', 'Kategori "' . $nama . '" berhasil dihapus.');
    }
}