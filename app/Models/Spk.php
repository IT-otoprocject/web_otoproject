<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan yang ada di database
    protected $table = 'spks';
    

    protected $fillable = [
        'no_spk', 'tanggal', 'teknisi_1', 'teknisi_2', 'customer', 'alamat', 'no_hp', 'jenis_mobil', 'no_plat', 'catatan'
    ];
}
