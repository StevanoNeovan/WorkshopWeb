<?php
// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table   = 'customers';
    protected $fillable = ['nama', 'email', 'no_hp', 'foto_blob', 'foto_path'];

    /**
     * Accessor: konversi blob ke base64 untuk ditampilkan di <img>
     */
    public function getFotoBlobBase64Attribute(): ?string
    {
        if (!$this->foto_blob) return null;
        return 'data:image/jpeg;base64,' . base64_encode($this->foto_blob);
    }
}