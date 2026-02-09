<?php

namespace App\Models\DocumentManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
        'is_private',
        'department',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get all documents in this folder
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    /**
     * Get active documents only
     */
    public function activeDocuments()
    {
        return $this->hasMany(Document::class, 'folder_id')->whereNull('deleted_at');
    }

    /**
     * Scope to get only active folders
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order folders by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get document count for this folder
     */
    public function getDocumentCountAttribute()
    {
        return $this->documents()->count();
    }

    /**
     * Check if user can manage this folder (CRUD operations)
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function canUserManage($user)
    {
        // If department is 'all', only dokumen_manajemen_admin can manage
        if ($this->department === 'all') {
            return $user->hasAccess('dokumen_manajemen_admin');
        }
        
        // If has specific department
        if ($this->department && $this->department !== 'all') {
            // User must be from that department AND be a Manager
            $isSameDepartment = $user->divisi && $user->divisi === $this->department;
            $isManager = $user->level && strtolower($user->level) === 'manager';
            
            return $isSameDepartment && $isManager;
        }

        // If no department set, only dokumen_manajemen_admin can manage
        return $user->hasAccess('dokumen_manajemen_admin');
    }

    /**
     * Check if user can view this folder
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function canUserView($user)
    {
        // Admin always can view
        if ($user->hasAccess('dokumen_manajemen_admin')) {
            return true;
        }

        // If folder has no department set, everyone can view
        if (!$this->department || $this->department === '') {
            return true;
        }

        // If department is 'all', everyone can view
        if ($this->department === 'all') {
            return true;
        }

        // If private and has specific department
        if ($this->is_private && $this->department) {
            // Only users from that department can view
            return $user->divisi && $user->divisi === $this->department;
        }

        // If not private but has specific department
        // Everyone can view, but only that department can CRUD (handled in canUserManage)
        return true;
    }
}
