<?php
// == app/Models/Penjualan.php ==

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table      = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public    $timestamps = false;

    protected $fillable = ['id_user', 'total'];

    protected $casts = [
        'timestamp' => 'datetime',
        'total'     => 'integer',
    ];

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null;

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}