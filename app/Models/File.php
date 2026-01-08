<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model File - Quản lý thông tin file trong hệ thống
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $folder_id
 * @property string $name
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property string $extension
 * @property int $size
 * @property bool $is_favorite
 * @property bool $is_trash
 * @property \Illuminate\Support\Carbon|null $trashed_at
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Folder|null $folder
 * @property-read string $formatted_size
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FileShare> $shares
 * @property-read int|null $shares_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File favorites()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File trashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereIsTrash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereTrashedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUserId($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    /**
     * Các thuộc tính có thể gán hàng loạt
     */
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

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'is_trash' => 'boolean',
        'trashed_at' => 'datetime',
        'size' => 'integer',
    ];

    /**
     * Lấy người dùng sở hữu file này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thư mục chứa file này.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Lấy tất cả các lượt chia sẻ của file này.
     */
    public function shares(): HasMany
    {
        return $this->hasMany(FileShare::class);
    }

    /**
     * Lấy tất cả các nhóm mà file này được chia sẻ với.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_files')
            ->withPivot('shared_by', 'permission')
            ->withTimestamps();
    }

    /**
     * Lấy kích thước file dạng dễ đọc (B, KB, MB, GB, TB).
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
     * Scope để lấy chỉ các file chưa bị xóa (không ở thùng rác).
     */
    public function scopeActive($query)
    {
        return $query->where('is_trash', false);
    }

    /**
     * Scope để lấy chỉ các file đã bị xóa (đang ở thùng rác).
     */
    public function scopeTrashed($query)
    {
        return $query->where('is_trash', true);
    }

    /**
     * Scope để lấy các file yêu thích.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
}
