<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MasterDivisi extends Model
{
    protected $table = 'master_divisi';
    
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Check if this divisi is being used by any user
     */
    public function isUsedByUsers()
    {
        return User::where('divisi', $this->kode)->exists();
    }

    /**
     * Get count of users using this divisi
     */
    public function getUsersCount()
    {
        return User::where('divisi', $this->kode)->count();
    }

    /**
     * Scope for active divisi only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
