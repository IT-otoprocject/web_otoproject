<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MasterUserLevel extends Model
{
    protected $table = 'master_user_level';
    
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
     * Check if this level is being used by any user
     */
    public function isUsedByUsers()
    {
        return User::where('level', $this->kode)->exists();
    }

    /**
     * Get count of users using this level
     */
    public function getUsersCount()
    {
        return User::where('level', $this->kode)->count();
    }

    /**
     * Scope for active levels only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
