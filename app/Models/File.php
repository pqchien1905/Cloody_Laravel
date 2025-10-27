<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'original_name',
        'path',
        'mime_type',
        'extension',
        'size',
        'is_favorite',
        'is_trash',
        'trashed_at',
        'description',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_trash' => 'boolean',
        'trashed_at' => 'datetime',
        'size' => 'integer',
    ];

    /**
     * Get the user that owns the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the folder that contains the file.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get all shares for this file.
     */
    public function shares(): HasMany
    {
        return $this->hasMany(FileShare::class);
    }

    /**
     * Get human readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to get only non-trashed files.
     */
    public function scopeActive($query)
    {
        return $query->where('is_trash', false);
    }

    /**
     * Scope to get only trashed files.
     */
    public function scopeTrashed($query)
    {
        return $query->where('is_trash', true);
    }

    /**
     * Scope to get favorite files.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
}
