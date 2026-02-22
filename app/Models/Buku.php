<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buku extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLE & PRIMARY KEY
    |--------------------------------------------------------------------------
    */
    protected $table      = 'buku';
    protected $primaryKey = 'idbuku';

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
        'kode',
        'judul',
        'pengarang',
        'idkategori',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTING
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'idkategori' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Setiap buku milik satu kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Filter buku berdasarkan kategori.
     */
    public function scopeByKategori($query, int $idkategori)
    {
        return $query->where('idkategori', $idkategori);
    }

    /**
     * Urutkan buku berdasarkan kode A-Z.
     */
    public function scopeUrut($query)
    {
        return $query->orderBy('kode', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Kode buku selalu tampil uppercase.
     */
    public function getKodeAttribute(string $value): string
    {
        return strtoupper($value);
    }

    /**
     * Nama pengarang selalu huruf kapital di awal setiap kata.
     */
    public function getPengarangAttribute(string $value): string
    {
        return ucwords(strtolower($value));
    }
}