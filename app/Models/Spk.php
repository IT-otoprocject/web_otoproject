<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Spk extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan (sesuai dengan nama tabel di database)
    protected $table = 'spks';

    // Kolom-kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'no_spk',
        'tanggal',
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
        'cancel_reason',
        'waktu_terbit_spk',
        'waktu_mulai_kerja',
        'durasi',
    ];

    // Format waktu secara otomatis
    protected $casts = [
        'waktu_terbit_spk' => 'datetime',
        'waktu_mulai_kerja' => 'datetime',
    ];

    // Relasi dengan model SpkItem
    public function items()
    {
        return $this->hasMany(SpkItem::class, 'spk_id'); // Sesuaikan foreign key jika berbeda
    }

    // Mutator untuk menyimpan waktu_mulai_kerja hanya jika dibutuhkan
    public function setWaktuMulaiKerjaAttribute($value)
    {
        if ($value) {
            $this->attributes['waktu_mulai_kerja'] = Carbon::parse($value)->setTimezone('Asia/Jakarta');
        }
    }

    // Accessor untuk menampilkan waktu di zona lokal
    public function getWaktuMulaiKerjaAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') : null;
    }

    // Accessor untuk format durasi (HH:MM:SS)
    public function getDurasiAttribute()
    {
        if (isset($this->attributes['waktu_terbit_spk']) && isset($this->attributes['waktu_mulai_kerja'])) {
            $waktuTerbit = Carbon::parse($this->attributes['waktu_terbit_spk'])->setTimezone('Asia/Jakarta');
            $waktuMulai = Carbon::parse($this->attributes['waktu_mulai_kerja'])->setTimezone('Asia/Jakarta');
            $durasiDetik = abs($waktuMulai->diffInSeconds($waktuTerbit));

            return gmdate('H:i:s', $durasiDetik);
        }

        return '00:00:00'; // Jika data tidak ada
    }

    protected static function booted()
    {
        static::creating(function ($spk) {
            if (empty($spk->waktu_terbit_spk)) {
                $spk->waktu_terbit_spk = now();
            }
        });
    }
}
