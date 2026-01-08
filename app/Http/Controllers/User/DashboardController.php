<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller - Xử lý trang dashboard chính của người dùng
 */
class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Lấy thống kê tổng số file
        $totalFiles = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->count();
            
        // Lấy thống kê tổng số thư mục
        $totalFolders = Folder::where('user_id', $userId)->count();
        
        // Số tệp được chia sẻ (tạm đặt là 0 cho đến khi thêm cột shared_token)
        $sharedFiles = 0;
            
        // Tính tổng dung lượng lưu trữ đã sử dụng
        $storageUsed = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->sum('size');
        
        // Lấy giới hạn lưu trữ từ gói đăng ký của người dùng
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $storageLimit = $user->getStorageLimitGB();
            
        // Lấy các thư mục gần đây (4 thư mục mới nhất)
        $recentFolders = Folder::where('user_id', $userId)
            ->withCount('files')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
            
        // Lấy các tệp gần đây (5 file mới nhất)
        $recentFiles = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Lấy các tài liệu (PDF, Word, Excel, PowerPoint) gần đây
        $documents = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->whereIn('mime_type', [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
            
        return view('pages.dashboard', compact(
            'totalFiles',
            'totalFolders', 
            'sharedFiles',
            'storageUsed',
            'storageLimit',
            'recentFolders',
            'recentFiles',
            'documents'
        ));
    }
}
