<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed User
        DB::table('users')->insertOrIgnore([
            'name'       => 'Admin',
            'email'      => 'admin@koleksibuku.com',
            'password'   => Hash::make('password'),
        ]);

        // Seed Kategori
        $kategoris = [
            ['nama_kategori' => 'Novel'],
            ['nama_kategori' => 'Biografi'],
            ['nama_kategori' => 'Komik'],
        ];
        DB::table('kategori')->insertOrIgnore($kategoris);

        // Ambil ID kategori
        $novel    = DB::table('kategori')->where('nama_kategori', 'Novel')->value('idkategori');
        $biografi = DB::table('kategori')->where('nama_kategori', 'Biografi')->value('idkategori');

        // Seed Buku
        $bukus = [
            [
                'kode'        => 'NV-01',
                'judul'       => 'Home Sweet Loan',
                'pengarang'   => 'Almira Bastari',
                'idkategori'  => $novel,
            ],
            [
                'kode'        => 'BO-01',
                'judul'       => 'Mohammad Hatta, Untuk Negeriku',
                'pengarang'   => 'Taufik Abdullah',
                'idkategori'  => $biografi,
            ],
            [
                'kode'        => 'NV-02',
                'judul'       => 'Keajaiban Toko Kelontong Namiya',
                'pengarang'   => 'Keigo Higashino',
                'idkategori'  => $novel,
            ],
        ];
        DB::table('buku')->insertOrIgnore($bukus);
    }
}