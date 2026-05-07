<?php
// == app/Models/PenjualanDetail.php ==

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table      = 'penjualan_detail';
    protected $primaryKey = 'idpenjualan_detail';
    public    $timestamps = false;

    protected $fillable = ['id_penjualan', 'idbuku', 'jumlah', 'subtotal'];

    protected $casts = [
        'jumlah'   => 'integer',
        'subtotal' => 'integer',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'idbuku', 'idbuku');
    }
}