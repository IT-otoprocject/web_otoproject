<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id', 'nama_barang', 'qty', 'is_new', 'mekanik_id', 'waktu_pengerjaan_barang', 'sku', 'waktu_sebelumnya', 'selisih_waktu'
    ];

    // Definisikan relasi dengan model Spk
    public function spk()
    {
        return $this->belongsTo(Spk::class, 'spk_id'); // Ganti 'spk_id' dengan nama kolom foreign key yang sesuai
    }

    public function mekanik()
    {
        return $this->belongsTo(User::class, 'mekanik_id');
    }
}
