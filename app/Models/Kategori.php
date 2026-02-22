<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLE & PRIMARY KEY
    |--------------------------------------------------------------------------
    */
    protected $table      = 'kategori';
    protected $primaryKey = 'idkategori';

    /**
     * Matikan auto timestamps karena tabel tidak punya
     * kolom created_at & updated_at.
     */
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNABLE
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'nama_kategori',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Satu kategori memiliki banyak buku.
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Urutkan berdasarkan nama kategori A-Z.
     */
    public function scopeUrut($query)
    {
        return $query->orderBy('nama_kategori', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Nama kategori selalu huruf kapital di awal kata.
     */
    public function getNamaKategoriAttribute(string $value): string
    {
        return ucfirst($value);
    }
}