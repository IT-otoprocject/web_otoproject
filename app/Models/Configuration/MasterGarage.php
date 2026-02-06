<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MasterGarage extends Model
{
    protected $table = 'master_garage';
    
    protected $fillable = [
        'kode',
        'nama',
        'lokasi',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Check if this garage is being used by any user
     */
    public function isUsedByUsers()
    {
        return User::where('garage', $this->kode)->exists();
    }

    /**
     * Get count of users using this garage
     */
    public function getUsersCount()
    {
        return User::where('garage', $this->kode)->count();
    }

    /**
     * Scope for active garages only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
