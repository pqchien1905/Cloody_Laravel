<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Models\FileShare;
use App\Models\FolderShare;
use App\Models\Subscription;

/**
 * Controller - Xử lý trang quản trị của admin
 */
class AdminController extends Controller
{
    /**
     * Hiển thị dashboard quản trị với các thống kê tổng quan
     */
    public function index()
    {
        // Thống kê cơ bản
        $totalUsers = User::count();
        $totalFiles = File::count();
        $totalFolders = Folder::count();
        $totalFileShares = FileShare::count();
        $totalFolderShares = FolderShare::count();
        $storageUsed = File::sum('size');
        
        // Tính tổng giới hạn lưu trữ từ tất cả các gói đăng ký đang hoạt động
        // Lấy tổng storage_gb từ các subscriptions đang hoạt động và còn hiệu lực
        $totalStorageFromSubscriptions = Subscription::where('is_active', true)
            ->where('payment_status', 'paid')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->sum('storage_gb');
        
        // Đếm số người dùng có gói đăng ký đang hoạt động
        $usersWithActiveSubscription = Subscription::where('is_active', true)
            ->where('payment_status', 'paid')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->distinct('user_id')
            ->count('user_id');
        
        // Tính lưu trữ cho các người dùng không có gói đăng ký (mặc định 1GB mỗi người dùng)
        $usersWithoutSubscription = $totalUsers - $usersWithActiveSubscription;
        $defaultStorageGB = $usersWithoutSubscription * 1; // 1GB cho mỗi người dùng không có subscription
        
        // Tổng giới hạn lưu trữ
        $storageLimitGB = $totalStorageFromSubscriptions + $defaultStorageGB;

        // Thống kê file theo loại
        $byType = [
            'images' => File::where('mime_type', 'like', 'image%')->count(),
            'videos' => File::where('mime_type', 'like', 'video%')->count(),
            'audio'  => File::where('mime_type', 'like', 'audio%')->count(),
            'pdf'    => File::where('mime_type', 'like', '%pdf%')->count(),
            'docs'   => File::where(function($q){
                $q->where('mime_type', 'like', '%word%')
                  ->orWhere('mime_type', 'like', '%officedocument%');
            })->count(),
            'sheets' => File::where(function($q){
                $q->where('mime_type', 'like', '%excel%')
                  ->orWhere('mime_type', 'like', '%spreadsheet%');
            })->count(),
            'others' => 0,
        ];
        $counted = array_sum($byType);
        $byType['others'] = max(0, $totalFiles - $counted);

        return view('pages.admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalFiles' => $totalFiles,
            'totalFolders' => $totalFolders,
            'totalFileShares' => $totalFileShares,
            'totalFolderShares' => $totalFolderShares,
            'storageUsed' => $storageUsed,
            'storageLimitGB' => $storageLimitGB,
            'byType' => $byType,
        ]);
    }
}
