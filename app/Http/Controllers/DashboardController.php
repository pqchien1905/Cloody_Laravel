<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Lấy thống kê
        $totalFiles = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->count();
            
        $totalFolders = Folder::where('user_id', $userId)->count();
        
    // Số tệp được chia sẻ (tạm đặt là 0 cho đến khi thêm cột shared_token)
    $sharedFiles = 0;
            
        $storageUsed = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->sum('size');
            
        // Lấy các thư mục gần đây
        $recentFolders = Folder::where('user_id', $userId)
            ->withCount('files')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
            
        // Lấy các tệp gần đây
        $recentFiles = File::where('user_id', $userId)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Lấy các tài liệu (PDF, Word, Excel, PowerPoint)
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
            'recentFolders',
            'recentFiles',
            'documents'
        ));
    }
}
