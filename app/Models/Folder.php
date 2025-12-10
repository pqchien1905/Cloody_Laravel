<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Folder - Quản lý thông tin thư mục trong hệ thống
 */
class Folder extends Model
{
    /**
     * Các thuộc tính có thể gán hàng loạt
     */
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

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'is_trash' => 'boolean',
        'is_public' => 'boolean',
        'trashed_at' => 'datetime',
    ];

    /**
     * Lấy người dùng sở hữu thư mục này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thư mục cha.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Lấy các thư mục con.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Lấy các file trong thư mục này.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Lấy tất cả các nhóm mà thư mục này được chia sẻ với.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_folders')
            ->withPivot('shared_by', 'permission')
            ->withTimestamps();
    }

    /**
     * Scope để lấy chỉ các thư mục chưa bị xóa (không ở thùng rác).
     */
    public function scopeActive($query)
    {
        return $query->where('is_trash', false);
    }

    /**
     * Scope để lấy các thư mục gốc (không có thư mục cha).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
