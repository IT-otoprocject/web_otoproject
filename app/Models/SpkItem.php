<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id', 'nama_barang', 'qty', 'is_new' // Tambahkan 'is_new'
    ];

    // Definisikan relasi dengan model Spk
    public function spk()
    {
        return $this->belongsTo(Spk::class, 'spk_id'); // Ganti 'spk_id' dengan nama kolom foreign key yang sesuai
    }
}
