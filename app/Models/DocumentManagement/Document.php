<?php

namespace App\Models\DocumentManagement;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folder_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'last_downloaded_at' => 'datetime',
    ];

    /**
     * Get the folder that owns the document
     */
    public function folder()
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    /**
     * Get the user who uploaded this document
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    /**
     * Delete file from storage when document is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if ($document->isForceDeleting()) {
                // Permanently delete file from storage
                if (Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
            }
        });
    }
}
