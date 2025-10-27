<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display all files page.
     */
    public function index(Request $request)
    {
        $query = File::with(['user', 'folder'])
            ->where('user_id', Auth::id() ?? 1)
            ->active();

    // Lọc theo thư mục
        if ($request->has('folder_id') && $request->folder_id) {
            $query->where('folder_id', $request->folder_id);
        }

    // Lọc theo loại
        if ($request->has('type') && $request->type) {
            switch ($request->type) {
                case 'documents':
                    $query->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']);
                    break;
                case 'images':
                    $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                    break;
                case 'videos':
                    $query->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
                    break;
                case 'audio':
                    $query->whereIn('extension', ['mp3', 'wav', 'ogg', 'wma']);
                    break;
            }
        }

    // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%');
            });
        }

    // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $files = $query->paginate(20);
        $folders = Folder::where('user_id', Auth::id() ?? 1)->active()->root()->get();

    // Thống kê
        $stats = [
            'total' => File::where('user_id', Auth::id() ?? 1)->active()->count(),
            'size' => File::where('user_id', Auth::id() ?? 1)->active()->sum('size'),
            'folders' => Folder::where('user_id', Auth::id() ?? 1)->active()->count(),
            'favorites' => File::where('user_id', Auth::id() ?? 1)->where('is_favorite', true)->count(),
        ];

        return view('pages.files', compact('files', 'folders', 'stats'));
    }

    /**
     * Display shared files and folders.
     */
    public function shared(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $user = Auth::user();
    $tab = $request->get('tab', 'with-me'); // with-me | by-me
        
    // Lấy các chia sẻ file và thư mục liên quan tới người dùng
        if ($tab === 'with-me') {
            // Các file được chia sẻ VỚI tôi (tôi là người nhận)
            $fileShares = \App\Models\FileShare::with(['file.user', 'file.folder', 'sharedBy', 'sharedWith'])
                ->where('shared_with', $userId)
                ->get()
                ->map(function($share) {
                    $share->type = 'file';
                    return $share;
                });
            
            // Các thư mục được chia sẻ VỚI tôi
            $folderShares = \App\Models\FolderShare::with(['folder.user', 'sharedBy', 'sharedWith'])
                ->where('shared_with', $userId)
                ->get()
                ->map(function($share) {
                    $share->type = 'folder';
                    return $share;
                });
        } else {
            // Các file tôi chia sẻ CHO người khác
            $fileShares = \App\Models\FileShare::with(['file.user', 'file.folder', 'sharedWith'])
                ->where('shared_by', $userId)
                ->get()
                ->map(function($share) {
                    $share->type = 'file';
                    return $share;
                });
            
            // Các thư mục tôi chia sẻ CHO người khác
            $folderShares = \App\Models\FolderShare::with(['folder.user', 'sharedWith'])
                ->where('shared_by', $userId)
                ->get()
                ->map(function($share) {
                    $share->type = 'folder';
                    return $share;
                });
        }

    // Gộp và sắp xếp theo created_at
        $shares = $fileShares->concat($folderShares)->sortByDesc('created_at');
        
    // Phân trang thủ công
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $total = $shares->count();
        $shares = $shares->forPage($currentPage, $perPage)->values();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $shares,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.shared', [
            'shares' => $paginator,
            'tab' => $tab,
        ]);
    }

    /**
     * Display recent files and folders (last 30 days).
     */
    public function recent(Request $request)
    {
        $userId = Auth::id() ?? 1;
    $days = 30; // Các file/thư mục gần đây trong 30 ngày gần nhất
        
    // Lọc theo loại
        $type = $request->get('type'); // documents|images|videos|audio
        
    // Tìm kiếm
        $search = $request->get('search');
        
    // Sắp xếp
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

    // Truy vấn file gần đây
        $filesQuery = File::with(['user', 'folder'])
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->active();

        if ($search) {
            $filesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        if ($type) {
            switch ($type) {
                case 'documents':
                    $filesQuery->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']);
                    break;
                case 'images':
                    $filesQuery->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                    break;
                case 'videos':
                    $filesQuery->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
                    break;
                case 'audio':
                    $filesQuery->whereIn('extension', ['mp3', 'wav', 'ogg', 'wma']);
                    break;
            }
        }

        $filesQuery->orderBy($sort, $order);
        $files = $filesQuery->paginate(20)->appends($request->query());

    // Truy vấn thư mục gần đây
        $recentFolders = Folder::withCount(['files' => function ($q) {
                $q->where('is_trash', false);
            }])
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->active()
            ->latest()
            ->get();

    // Tất cả thư mục gốc để hiển thị trong dropdown modal upload
        $folders = Folder::where('user_id', $userId)
            ->active()
            ->root()
            ->orderBy('name')
            ->get();

        return view('pages.recent', [
            'files' => $files,
            'recentFolders' => $recentFolders,
            'folders' => $folders,
            'filter' => compact('type', 'search', 'sort', 'order'),
        ]);
    }

    /**
     * Show favorites.
     */
    public function favorites()
    {
        $files = File::with(['user', 'folder'])
            ->where('user_id', Auth::id() ?? 1)
            ->where('is_favorite', true)
            ->active()
            ->latest()
            ->paginate(20);

    // Thư mục yêu thích để hiển thị
        $favoriteFolders = Folder::withCount(['files' => function ($q) {
                $q->where('is_trash', false);
            }])
            ->where('user_id', Auth::id() ?? 1)
            ->where('is_favorite', true)
            ->active()
            ->latest()
            ->get();

        // All root folders for upload modal dropdown selection
        $folders = Folder::where('user_id', Auth::id() ?? 1)
            ->active()
            ->root()
            ->orderBy('name')
            ->get();

        return view('pages.favorites', [
            'files' => $files,
            'favoriteFolders' => $favoriteFolders,
            // Quan trọng: truyền $folders cho dropdown modal upload
            'folders' => $folders,
        ]);
    }

    /**
     * Show trash.
     */
    public function trash(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $item = $request->get('item', 'all'); // all | files | folders
        $type = $request->get('type'); // documents|images|videos|audio
        $search = $request->get('search');
        $sort = $request->get('sort', 'trashed_at'); // name|size|trashed_at
        $order = $request->get('order', 'desc');

    // File trong thùng rác
        $filesQuery = File::with(['user', 'folder'])
            ->where('user_id', $userId)
            ->where('is_trash', true);

        if ($search) {
            $filesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        if ($type) {
            switch ($type) {
                case 'documents':
                    $filesQuery->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx,', 'ppt', 'pptx']);
                    break;
                case 'images':
                    $filesQuery->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                    break;
                case 'videos':
                    $filesQuery->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
                    break;
                case 'audio':
                    $filesQuery->whereIn('extension', ['mp3', 'wav', 'ogg', 'wma']);
                    break;
            }
        }

        $fileSort = in_array($sort, ['name', 'size', 'trashed_at']) ? $sort : 'trashed_at';
        $filesQuery->orderBy($fileSort, $order);
        $files = $filesQuery->paginate(20)->appends($request->query());

    // Thư mục trong thùng rác
        $foldersQuery = \App\Models\Folder::query()
            ->where('user_id', $userId)
            ->where('is_trash', true);

        if ($search) {
            $foldersQuery->where('name', 'like', "%{$search}%");
        }

        $folderSort = in_array($sort, ['name', 'trashed_at']) ? $sort : 'trashed_at';
        $foldersQuery->orderBy($folderSort, $order);
        $folders = $foldersQuery->paginate(20)->appends($request->query());

        $filter = compact('item', 'type', 'search', 'sort', 'order');

        return view('pages.trash', compact('files', 'folders', 'filter'));
    }

    /**
     * Clean up the trash: permanently delete all trashed files and folders for the current user.
     */
    public function cleanupTrash(Request $request)
    {
        $userId = Auth::id() ?? 1;

    // 1) Xóa vĩnh viễn file trong thùng rác (xóa khỏi storage + db)
        $files = File::where('user_id', $userId)->where('is_trash', true)->get();
        foreach ($files as $file) {
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
        }

    // 2) Xóa vĩnh viễn thư mục trong thùng rác từ dưới lên (xóa lá trước)
    // Tiếp tục xóa các thư mục lá trong thùng rác cho đến khi không còn
        $safetyCounter = 0;
        do {
            $leafFolders = Folder::where('user_id', $userId)
                ->where('is_trash', true)
                ->whereDoesntHave('children', function ($q) {
                    $q->where('is_trash', true);
                })
                ->get();

            foreach ($leafFolders as $folder) {
                $folder->delete();
            }

            $safetyCounter++;
        } while ($leafFolders->count() > 0 && $safetyCounter < 200);

        return redirect()->back()->with('success', 'Trash cleaned up permanently!');
    }
}
