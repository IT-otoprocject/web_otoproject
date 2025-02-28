<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id', 'nama_barang', 'qty'
    ];
}
