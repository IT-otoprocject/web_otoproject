<?php

namespace App\Models\Access_PR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class PrCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'approval_rules',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'approval_rules' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
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
        return $this->hasMany(\App\Models\Access_PR\Purchase_Request\PurchaseRequest::class, 'category_id');
    }

    // Get available approval levels for category configuration
    public static function getAvailableApprovalLevels()
    {
        return [
            'dept_head' => 'Department Head',
            'ga' => 'GA',
            'finance_dept' => 'Finance Department (FAT Manager/SPV)',
            'ceo' => 'CEO',
            'cfo' => 'CFO',
        ];
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
