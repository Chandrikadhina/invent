<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class BarangMasuk extends Model
{
    protected $table = 'barangmasuk'; // Assuming the table name is 'barang_masuk'

    protected $fillable = [
        'tgl_masuk',
        'qty_masuk',
        'barang_id',
    ];

    // Define the relationship with the Barang model
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}