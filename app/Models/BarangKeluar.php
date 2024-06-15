<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barangkeluar'; // Assuming your table name is 'barang_keluar'

    protected $fillable = [
        'tgl_keluar',
        'qty_keluar',
        'barang_id',
    ];

    // Define the relationship with the Barang model
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}