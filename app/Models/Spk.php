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
        'no_spk',
        'tanggal',
        'teknisi_1',
        'teknisi_2',
        'customer',
        'alamat',
        'no_hp',
        'jenis_mobil',
        'no_plat',
        'catatan',
        'status',
        'waktu_kerja',
        'catatan_kerja',
        'teknisi_selesai',
        'garage',
    ];
    

      // Definisikan relasi dengan model Item
      public function items()
      {
          return $this->hasMany(SpkItem::class, 'spk_id'); // Ganti 'spk_id' dengan nama kolom foreign key yang sesuai
      }
}
