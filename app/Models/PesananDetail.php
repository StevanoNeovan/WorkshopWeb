<?php
// app/Models/PesananDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table    = 'pesanan_detail';
    public    $timestamps = false;
    protected $fillable = ['pesanan_id', 'idbuku', 'harga', 'jumlah', 'subtotal'];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'idbuku', 'idbuku');
    }
}