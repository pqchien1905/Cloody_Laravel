<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Group - Quản lý thông tin nhóm người dùng
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
