<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $totalSize = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->sum('size');
        
        $storageUsedGB = $totalSize / (1024 * 1024 * 1024);
        
        // Lấy storage limit từ subscription của user
        $storageLimit = $user->getStorageLimitGB();
        $storagePercent = $storageLimit > 0 ? min(($storageUsedGB / $storageLimit) * 100, 100) : 0;
        
        $images = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->where('mime_type', 'like', 'image%')
            ->sum('size');
        
        $videos = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->where('mime_type', 'like', 'video%')
            ->sum('size');
        
        $documents = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->where(function($q) {
                $q->where('mime_type', 'like', '%pdf%')
                  ->orWhere('mime_type', 'like', '%word%')
                  ->orWhere('mime_type', 'like', '%document%')
                  ->orWhere('mime_type', 'like', '%excel%')
                  ->orWhere('mime_type', 'like', '%spreadsheet%');
            })
            ->sum('size');
        
        $totalFiles = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->count();
        
        $totalFolders = Folder::where('user_id', $user->id)
            ->where('is_trash', false)
            ->count();
        
        $recentFiles = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        $recentFolders = Folder::where('user_id', $user->id)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        
        return view('pages.user.profile', [
            'user' => $user,
            'storageUsedGB' => $storageUsedGB,
            'storageLimit' => $storageLimit,
            'storagePercent' => $storagePercent,
            'imagesGB' => $images / (1024 * 1024 * 1024),
            'videosGB' => $videos / (1024 * 1024 * 1024),
            'documentsGB' => $documents / (1024 * 1024 * 1024),
            'totalFiles' => $totalFiles,
            'totalFolders' => $totalFolders,
            'recentFiles' => $recentFiles,
            'recentFolders' => $recentFolders,
        ]);
    }
}
