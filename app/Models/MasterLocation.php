<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'company',
        'address',
        'phone',
        'email',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseRequests()
    {
        return $this->hasMany(\App\Models\Access_PR\Purchase_Request\PurchaseRequest::class, 'location_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByName($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Accessors & Mutators
     */
    public function getFullLocationAttribute()
    {
        return "{$this->name} ({$this->code}) - {$this->company}";
    }

    /**
     * Static methods
     */
    public static function getActiveLocations()
    {
        return static::active()->orderBy('code')->get();
    }

    public static function getLocationOptions()
    {
        return static::active()
            ->orderBy('code')
            ->get()
            ->pluck('full_location', 'id')
            ->toArray();
    }
}
