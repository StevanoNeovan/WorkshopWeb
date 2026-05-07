<?php
// app/Models/Pesanan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table   = 'pesanan';
    protected $fillable = [
        'guest_id', 'kode_pesanan', 'total', 'status_bayar',
        'snap_token', 'payment_type', 'va_number',
        'midtrans_order_id', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total'   => 'integer',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'pesanan_id');
    }

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'ORD-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}