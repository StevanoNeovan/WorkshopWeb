<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buku extends Model
{
    protected $table      = 'buku';
    protected $primaryKey = 'idbuku';

    // Gunakan kolom TIMESTAMP sebagai created_at, tidak ada updated_at
    const CREATED_AT = 'TIMESTAMP';
    const UPDATED_AT = null;

    protected $fillable = [
        'kode',
        'judul',
        'pengarang',
        'idkategori',
        'harga',
    ];

    protected $casts = [
        'idkategori' => 'integer',
        'harga'      => 'integer',
        'TIMESTAMP'  => 'datetime',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }

    public function scopeByKategori($query, int $idkategori)
    {
        return $query->where('idkategori', $idkategori);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('kode', 'asc');
    }

    public function getKodeAttribute(string $value): string
    {
        return strtoupper($value);
    }

    public function getPengarangAttribute(string $value): string
    {
        return ucwords(strtolower($value));
    }

    /**
     * Format harga ke Rupiah
     */
    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}