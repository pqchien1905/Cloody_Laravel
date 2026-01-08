<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminFoldersController extends Controller
{
    /**
     * Display a listing of all folders.
     */
    public function index(Request $request)
    {
        $query = Folder::with(['user', 'parent'])
            ->withCount(['files' => function ($q) {
                $q->where('is_trash', false);
            }])
            ->withCount('children');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo người dùng
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'trash') {
                $query->where('is_trash', true);
            } elseif ($request->status === 'active') {
                $query->where('is_trash', false);
            } elseif ($request->status === 'favorite') {
                $query->where('is_favorite', true);
            }
        }

        // Lọc theo quyền riêng tư
        if ($request->filled('privacy')) {
            if ($request->privacy === 'public') {
                $query->where('is_public', true);
            } elseif ($request->privacy === 'private') {
                $query->where('is_public', false);
            }
        }

        // Lọc theo thư mục gốc hoặc thư mục con
        if ($request->filled('level')) {
            if ($request->level === 'root') {
                $query->whereNull('parent_id');
            } elseif ($request->level === 'subfolder') {
                $query->whereNotNull('parent_id');
            }
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['name', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $folders = $query->paginate(20)->withQueryString();

        // Thống kê
        $stats = [
            'total' => Folder::count(),
            'active' => Folder::where('is_trash', false)->count(),
            'trash' => Folder::where('is_trash', true)->count(),
            'favorites' => Folder::where('is_favorite', true)->count(),
            'public' => Folder::where('is_public', true)->count(),
            'private' => Folder::where('is_public', false)->count(),
            'root' => Folder::whereNull('parent_id')->count(),
            'subfolders' => Folder::whereNotNull('parent_id')->count(),
        ];

        // Lấy danh sách users cho filter
        $users = User::orderBy('name')->get();

        return view('pages.admin.folders.index', compact('folders', 'stats', 'users'));
    }

    /**
     * Display the specified folder.
     */
    public function show(Folder $folder)
    {
        $folder->load(['user', 'parent', 'children' => function($query) {
            $query->where('is_trash', false)->withCount(['files' => function($q) {
                $q->where('is_trash', false);
            }]);
        }, 'files' => function($query) {
            $query->where('is_trash', false);
        }]);
        
        // Tính tổng dung lượng file trong folder
        $totalSize = $folder->files()->where('is_trash', false)->sum('size');
        
        return view('pages.admin.folders.show', compact('folder', 'totalSize'));
    }

    /**
     * View folder contents (Admin version - same as show).
     */
    public function view(Folder $folder)
    {
        return $this->show($folder);
    }

    /**
     * Download folder as ZIP (Admin version - no permission check).
     */
    public function download(Folder $folder)
    {
        try {
            // Tạo tên file ZIP
            $zipFileName = Str::slug($folder->name) . '_' . time() . '.zip';
            $zipFilePath = storage_path('app/temp/' . $zipFileName);
            
            // Đảm bảo thư mục temp tồn tại
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Tạo file ZIP
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return redirect()->back()->with('error', __('common.error_creating_zip'));
            }

            // Thêm file và thư mục con vào ZIP
            $this->addFolderToZip($zip, $folder, $folder->name);
            
            $zip->close();

            // Kiểm tra xem file ZIP có được tạo không
            if (!file_exists($zipFilePath)) {
                return redirect()->back()->with('error', __('common.error_creating_zip'));
            }

            // Trả về file ZIP để tải xuống và xóa sau khi tải xong
            return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('common.error_downloading_folder') . ': ' . $e->getMessage());
        }
    }

    /**
     * Recursively add folder contents to ZIP.
     */
    private function addFolderToZip(\ZipArchive $zip, Folder $folder, string $basePath)
    {
        // Thêm tất cả file trong folder
        foreach ($folder->files()->where('is_trash', false)->get() as $file) {
            $filePath = storage_path('app/public/' . $file->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $basePath . '/' . $file->original_name);
            }
        }

        // Thêm tất cả thư mục con (đệ quy)
        foreach ($folder->children()->where('is_trash', false)->get() as $childFolder) {
            $this->addFolderToZip($zip, $childFolder, $basePath . '/' . $childFolder->name);
        }
    }

    /**
     * Remove the specified folder from storage.
     */
    public function destroy(Folder $folder)
    {
        try {
            // Xóa tất cả file trong folder (nếu có)
            foreach ($folder->files as $file) {
                if ($file->path && Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
                $file->delete();
            }

            // Xóa tất cả thư mục con (recursive)
            $this->deleteFolderRecursive($folder);

            // Xóa record
            $folder->delete();

            return redirect()->route('admin.folders.index')
                ->with('status', __('common.folder_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('common.error_deleting_folder') . ': ' . $e->getMessage());
        }
    }

    /**
     * Recursively delete folder and its children.
     */
    private function deleteFolderRecursive(Folder $folder)
    {
        foreach ($folder->children as $child) {
            // Xóa file trong thư mục con
            foreach ($child->files as $file) {
                if ($file->path && Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
                $file->delete();
            }
            
            // Đệ quy xóa thư mục con
            $this->deleteFolderRecursive($child);
            
            // Xóa thư mục con
            $child->delete();
        }
    }
}

