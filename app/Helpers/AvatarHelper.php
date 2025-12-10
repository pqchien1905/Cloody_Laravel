<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Helper class - Xử lý các chức năng liên quan đến avatar người dùng
 */
class AvatarHelper
{
    /**
     * Lấy URL avatar cho người dùng
     */
    public static function getAvatarUrl($avatarPath): ?string
    {
        if (!$avatarPath) {
            return null;
        }

        // Nếu đường dẫn đã bao gồm 'storage/', trả về như vậy
        if (strpos($avatarPath, 'storage/') === 0) {
            return asset($avatarPath);
        }

        // Nếu đường dẫn chỉ là 'avatars/filename.jpg', thêm 'storage/'
        if (strpos($avatarPath, 'avatars/') === 0) {
            return asset('storage/' . $avatarPath);
        }

        // Mặc định: giả định đây là đường dẫn storage
        return asset('storage/' . $avatarPath);
    }

    /**
     * Lấy URL avatar mặc định
     */
    public static function getDefaultAvatar(): string
    {
        return asset('assets/images/user/1.jpg');
    }

    /**
     * Kiểm tra xem file avatar có tồn tại không
     */
    public static function avatarExists($avatarPath): bool
    {
        if (!$avatarPath) {
            return false;
        }

        return Storage::disk('public')->exists($avatarPath);
    }
}

