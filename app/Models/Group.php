<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Group - Quản lý thông tin nhóm người dùng
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $owner_id
 * @property string|null $avatar
 * @property string $privacy
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $admins
 * @property-read int|null $admins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Folder> $folders
 * @property-read int|null $folders_count
 * @property-read string|null $avatar_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    use HasFactory;

    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'avatar',
        'privacy',
    ];

    /**
     * Lấy chủ sở hữu của nhóm
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Lấy tất cả các thành viên của nhóm
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Lấy chỉ các thành viên là admin
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->wherePivot('role', 'admin')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Kiểm tra xem người dùng có phải là thành viên của nhóm không
     */
    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Kiểm tra xem người dùng có phải là admin của nhóm không
     */
    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Kiểm tra xem người dùng có phải là chủ sở hữu của nhóm không
     */
    public function isOwner($userId)
    {
        return $this->owner_id == $userId;
    }

    /**
     * Lấy tất cả các thư mục được chia sẻ với nhóm này
     */
    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'group_folders')
            ->withPivot('shared_by', 'permission')
            ->withTimestamps();
    }

    /**
     * Lấy tất cả các file được chia sẻ với nhóm này
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'group_files')
            ->withPivot('shared_by', 'permission')
            ->withTimestamps();
    }

    /**
     * Lấy URL avatar của nhóm
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        // Sử dụng route để phục vụ avatar để kiểm soát truy cập tốt hơn và tránh lỗi 403
        try {
            return route('avatar.group', $this->id);
        } catch (\Exception $e) {
            // Fallback về storage URL nếu route thất bại
            if (strpos($this->avatar, 'storage/') === 0) {
                return asset($this->avatar);
            }
            return asset('storage/' . $this->avatar);
        }
    }
}
