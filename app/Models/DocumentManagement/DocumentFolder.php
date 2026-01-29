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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}
