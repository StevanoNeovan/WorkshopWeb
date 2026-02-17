<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Simpan data user ke session setelah login (requirement poin c-iii)
        if (!session()->has('user_data')) {
            session([
                'user_data' => [
                    'id'    => auth()->user()->id,
                    'name'  => auth()->user()->name,
                    'email' => auth()->user()->email,
                ]
            ]);
        }

        $totalKategori = Kategori::count();
        $totalBuku     = Buku::count();

        return view('dashboard', compact('totalKategori', 'totalBuku'));
    }
}