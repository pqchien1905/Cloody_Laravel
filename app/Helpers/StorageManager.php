<?php

namespace App\Helpers;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Helper class - Quản lý dung lượng lưu trữ của hệ thống và người dùng
 */
class StorageManager
{
    /**
     * Định dạng ngày tháng theo múi giờ Việt Nam
     */
    public static function formatDateVN($date, string $format = 'd/m/Y H:i'): string
    {
        if (!$date) {
            return '';
        }
        
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $date->timezone('Asia/Ho_Chi_Minh')->format($format);
    }
    
    /**
     * Lấy dung lượng lưu trữ đã sử dụng của người dùng tính theo bytes
     */
    public static function getUserStorageUsed($userId): int
    {
        return File::where('user_id', $userId)
            ->where('is_trash', false)
            ->sum('size');
    }

    /**
     * Lấy dung lượng lưu trữ đã sử dụng của người dùng tính theo MB
     */
    public static function getUserStorageUsedMB($userId): float
    {
        return round(self::getUserStorageUsed($userId) / 1024 / 1024, 2);
    }

    /**
     * Lấy tổng dung lượng lưu trữ đã sử dụng tính theo bytes
     */
    public static function getTotalStorageUsed(): int
    {
        return File::where('is_trash', false)->sum('size');
    }

    /**
     * Lấy tổng dung lượng lưu trữ đã sử dụng tính theo GB
     */
    public static function getTotalStorageUsedGB(): float
    {
        return round(self::getTotalStorageUsed() / 1024 / 1024 / 1024, 2);
    }

    /**
     * Lấy giới hạn lưu trữ tối đa cho người dùng tính theo bytes
     */
    public static function getUserMaxStorage($userId = null): ?int
    {
        // Nếu có userId, lấy từ subscription
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $limitBytes = $user->getStorageLimitBytes();
                return $limitBytes > 0 ? $limitBytes : null;
            }
        }
        
        // Fallback về cấu hình
        $maxMB = config('cloody.storage.max_storage_per_user', 0);
        if ($maxMB <= 0) {
            return null; // Không giới hạn
        }
        return $maxMB * 1024 * 1024; // Chuyển đổi MB sang bytes
    }

    /**
     * Lấy giới hạn tổng dung lượng lưu trữ tối đa tính theo bytes
     */
    public static function getTotalMaxStorage(): ?int
    {
        $maxGB = config('cloody.storage.max_storage_total', 0);
        if ($maxGB <= 0) {
            return null; // Không giới hạn
        }
        return $maxGB * 1024 * 1024 * 1024; // Chuyển đổi GB sang bytes
    }

    /**
     * Kiểm tra xem người dùng có thể upload file với kích thước đã cho không
     */
    public static function canUserUpload($userId, int $fileSizeBytes): array
    {
        $maxStorage = self::getUserMaxStorage($userId);
        
        if ($maxStorage === null) {
            return ['allowed' => true, 'message' => ''];
        }

        $currentUsed = self::getUserStorageUsed($userId);
        $newTotal = $currentUsed + $fileSizeBytes;

        if ($newTotal > $maxStorage) {
            $usedGB = round($currentUsed / 1024 / 1024 / 1024, 2);
            $maxGB = round($maxStorage / 1024 / 1024 / 1024, 2);
            $fileMB = round($fileSizeBytes / 1024 / 1024, 2);
            
            return [
                'allowed' => false,
                'message' => "Đã vượt quá giới hạn lưu trữ. Bạn đã sử dụng {$usedGB} GB trong tổng số {$maxGB} GB. File này ({$fileMB} MB) sẽ vượt quá giới hạn của bạn.",
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Kiểm tra xem hệ thống có thể chấp nhận file mới không
     */
    public static function canSystemAccept(int $fileSizeBytes): array
    {
        $maxStorage = self::getTotalMaxStorage();
        
        if ($maxStorage === null) {
            return ['allowed' => true, 'message' => ''];
        }

        $currentUsed = self::getTotalStorageUsed();
        $newTotal = $currentUsed + $fileSizeBytes;

        if ($newTotal > $maxStorage) {
            $usedGB = round($currentUsed / 1024 / 1024 / 1024, 2);
            $maxGB = round($maxStorage / 1024 / 1024 / 1024, 2);
            
            return [
                'allowed' => false,
                'message' => "System storage limit exceeded. Used {$usedGB} GB of {$maxGB} GB.",
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Lấy thống kê lưu trữ cho người dùng
     */
    public static function getUserStorageStats($userId): array
    {
        $used = self::getUserStorageUsed($userId);
        $max = self::getUserMaxStorage();
        
        return [
            'used_bytes' => $used,
            'used_mb' => round($used / 1024 / 1024, 2),
            'max_bytes' => $max,
            'max_mb' => $max ? round($max / 1024 / 1024, 2) : null,
            'percentage' => $max ? round(($used / $max) * 100, 2) : null,
            'available_bytes' => $max ? max(0, $max - $used) : null,
            'available_mb' => $max ? round(max(0, $max - $used) / 1024 / 1024, 2) : null,
        ];
    }
}

