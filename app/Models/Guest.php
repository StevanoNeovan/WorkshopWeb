<?php
// app/Models/Guest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table   = 'guests';
    protected $fillable = ['kode_guest', 'nama', 'phone'];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'guest_id');
    }

    /**
     * Generate kode guest berikutnya: Guest_0000001, Guest_0000002, dst.
     */
    public static function generateKode(): string
    {
        $last = self::orderByDesc('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'Guest_' . str_pad($next, 7, '0', STR_PAD_LEFT);
    }
}