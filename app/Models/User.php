<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level', // Pastikan level ada di sini
        'divisi', // Tambahkan divisi
        'garage', // Tambahkan garage agar bisa mass assignment
        'system_access', // Tambahkan system_access
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'system_access' => 'array', // Cast JSON ke array
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            Log::info('User created in database', ['user' => $user]);
        });
    }

    /**
     * Check if user has access to a specific module/page
     *
     * @param string $module
     * @return bool
     */
    public function hasAccess($module)
    {
        // Admin always has access to everything
        if ($this->level === 'admin') {
            return true;
        }
        
        if (!$this->system_access || !is_array($this->system_access)) {
            return false;
        }

        return in_array($module, $this->system_access);
    }

    /**
     * Check if user has any of the specified accesses
     *
     * @param array $modules
     * @return bool
     */
    public function hasAnyAccess(array $modules)
    {
        // Admin always has access to everything
        if ($this->level === 'admin') {
            return true;
        }
        
        if (!$this->system_access || !is_array($this->system_access)) {
            return false;
        }

        return !empty(array_intersect($modules, $this->system_access));
    }

    /**
     * Check if user has all specified accesses
     *
     * @param array $modules
     * @return bool
     */
    public function hasAllAccess(array $modules)
    {
        // Admin always has access to everything
        if ($this->level === 'admin') {
            return true;
        }
        
        if (!$this->system_access || !is_array($this->system_access)) {
            return false;
        }

        return empty(array_diff($modules, $this->system_access));
    }

    /**
     * Get user's accessible modules
     *
     * @return array
     */
    public function getAccessibleModules()
    {
        // Admin has access to all modules
        if ($this->level === 'admin') {
            return ['dashboard', 'spk_garage', 'pr', 'reports', 'users', 'settings', 'master_location'];
        }
        
        return $this->system_access ?? [];
    }

    /**
     * Add access to user
     *
     * @param string|array $modules
     * @return void
     */
    public function addAccess($modules)
    {
        $modules = is_array($modules) ? $modules : [$modules];
        $currentAccess = $this->system_access ?? [];
        
        $this->system_access = array_unique(array_merge($currentAccess, $modules));
        $this->save();
    }

    /**
     * Remove access from user
     *
     * @param string|array $modules
     * @return void
     */
    public function removeAccess($modules)
    {
        $modules = is_array($modules) ? $modules : [$modules];
        $currentAccess = $this->system_access ?? [];
        
        $this->system_access = array_diff($currentAccess, $modules);
        $this->save();
    }

    /**
     * Set user access (replace all existing access)
     *
     * @param array $modules
     * @return void
     */
    public function setAccess(array $modules)
    {
        $this->system_access = array_unique($modules);
        $this->save();
    }

    /**
     * Check if user is admin (has all access or specific admin level)
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->level === 'admin' || $this->hasAccess('admin');
    }

    /**
     * Get default access based on user level
     *
     * @return array
     */
    public function getDefaultAccessByLevel()
    {
        return match($this->level) {
            'admin' => ['spk_garage', 'pr', 'dashboard', 'reports', 'users', 'settings', 'master_location'],
            'manager' => ['spk_garage', 'pr', 'dashboard', 'reports', 'master_location'],
            'kasir' => ['spk_garage', 'dashboard'],
            'mekanik' => ['spk_garage', 'dashboard'],
            'pr_user' => ['pr', 'dashboard'],
            default => ['dashboard']
        };
    }
}
