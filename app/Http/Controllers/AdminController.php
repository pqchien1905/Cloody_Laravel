<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Models\FileShare;
use App\Models\FolderShare;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalFiles = File::count();
        $totalFolders = Folder::count();
        $totalFileShares = FileShare::count();
        $totalFolderShares = FolderShare::count();
        $storageUsed = File::sum('size');

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
            'byType' => $byType,
        ]);
    }
}
