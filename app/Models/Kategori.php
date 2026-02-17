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
    | CASTING
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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