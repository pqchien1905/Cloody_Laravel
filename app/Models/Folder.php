<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Folder - Quản lý thông tin thư mục trong hệ thống
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $color
 * @property string|null $description
 * @property bool $is_trash
 * @property bool $is_public
 * @property bool $is_favorite
 * @property \Illuminate\Support\Carbon|null $trashed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read Folder|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FolderShare> $shares
 * @property-read int|null $shares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $subfolders
 * @property-read int|null $subfolders_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereIsTrash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereTrashedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereUserId($value)
 * @mixin \Eloquent
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
     * Lấy các thư mục con (alias để phù hợp với view).
     */
    public function subfolders(): HasMany
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
     * Lấy tất cả các lượt chia sẻ của thư mục này.
     */
    public function shares(): HasMany
    {
        return $this->hasMany(FolderShare::class);
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
