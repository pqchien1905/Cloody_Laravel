<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'color',
        'description',
        'is_favorite',
        'is_trash',
        'is_public',
        'trashed_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_trash' => 'boolean',
        'is_public' => 'boolean',
        'trashed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the folder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent folder.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Get child folders.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Get files in this folder.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Scope to get only non-trashed folders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_trash', false);
    }

    /**
     * Scope to get root folders (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
