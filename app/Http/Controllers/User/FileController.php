<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\Folder;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller - Xử lý các chức năng liên quan đến file
 */
class FileController extends Controller
{
    /**
     * Hiển thị trang danh sách tất cả các file.
     */
    public function index(Request $request)
    {
        // Tạo query cơ bản với các quan hệ
        $query = File::with(['user', 'folder'])
            ->where('user_id', Auth::id() ?? 1)
            ->active();

        // Lọc theo thư mục
        if ($request->has('folder_id') && $request->folder_id) {
            $query->where('folder_id', $request->folder_id);
        }

        // Lọc theo danh mục (category)
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->where('is_active', true)->first();
            if ($category && !empty($category->extensions)) {
                $query->whereIn('extension', $category->extensions);
            }
        }

        // Tìm kiếm theo tên file
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%');
            });
        }

        // Sắp xếp file
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang danh sách file
        $files = $query->paginate(20);
        // Lấy danh sách thư mục gốc để hiển thị
        $folders = Folder::where('user_id', Auth::id() ?? 1)->active()->root()->get();
        
        // Lấy danh sách các danh mục đang hoạt động để hiển thị trong dropdown
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        // Tính toán thống kê
        $stats = [
            'total' => File::where('user_id', Auth::id() ?? 1)->active()->count(),
            'size' => File::where('user_id', Auth::id() ?? 1)->active()->sum('size'),
            'folders' => Folder::where('user_id', Auth::id() ?? 1)->active()->count(),
            'favorites' => File::where('user_id', Auth::id() ?? 1)->where('is_favorite', true)->count(),
        ];

        return view('pages.files', compact('files', 'folders', 'categories', 'stats'));
    }

    /**
     * Hiển thị các file và thư mục được chia sẻ.
     */
    public function shared(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $user = Auth::user();
        // Xác định tab hiện tại: 'with-me' (chia sẻ với tôi) hoặc 'by-me' (tôi chia sẻ)
        $tab = $request->get('tab', 'with-me');
        
        // Lấy các lượt chia sẻ file và thư mục liên quan đến người dùng
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

        // Gộp file và folder shares lại và sắp xếp theo thời gian tạo (mới nhất trước)
        $shares = $fileShares->concat($folderShares)->sortByDesc('created_at');
        
        // Phân trang thủ công vì dữ liệu đã được lấy ra
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $total = $shares->count();
        $shares = $shares->forPage($currentPage, $perPage)->values();
        
        // Tạo paginator để hiển thị phân trang
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
     * Hiển thị các file và thư mục gần đây (30 ngày vừa qua).
     */
    public function recent(Request $request)
    {
        $userId = Auth::id() ?? 1;
        // Các file/thư mục gần đây trong 30 ngày gần nhất
        $days = 30;
        
        // Lọc theo danh mục
        $category = $request->get('category');
        
        // Tìm kiếm
        $search = $request->get('search');
        
        // Sắp xếp
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        // Truy vấn các file gần đây
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

        if ($category) {
            $categoryModel = Category::where('slug', $category)->where('is_active', true)->first();
            if ($categoryModel && !empty($categoryModel->extensions)) {
                $filesQuery->whereIn('extension', $categoryModel->extensions);
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
            
        // Lấy danh sách categories active
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        return view('pages.recent', [
            'files' => $files,
            'recentFolders' => $recentFolders,
            'folders' => $folders,
            'categories' => $categories,
            'filter' => compact('category', 'search', 'sort', 'order'),
        ]);
    }

    /**
     * Hiển thị các file và thư mục yêu thích.
     */
    public function favorites()
    {
        // Lấy danh sách file yêu thích
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

        // Tất cả các thư mục gốc cho dropdown lựa chọn trong modal upload
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
     * Hiển thị thùng rác chứa các file và thư mục đã bị xóa.
     */
    public function trash(Request $request)
    {
        $userId = Auth::id() ?? 1;
        // Loại item: 'all' | 'files' | 'folders'
        $item = $request->get('item', 'all');
        $category = $request->get('category');
        $search = $request->get('search');
        // Tiêu chí sắp xếp: 'name' | 'size' | 'trashed_at'
        $sort = $request->get('sort', 'trashed_at');
        $order = $request->get('order', 'desc');

        // Query các file trong thùng rác
        $filesQuery = File::with(['user', 'folder'])
            ->where('user_id', $userId)
            ->where('is_trash', true);

        // Lọc theo tìm kiếm
        if ($search) {
            $filesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($category) {
            $categoryModel = Category::where('slug', $category)->where('is_active', true)->first();
            if ($categoryModel && !empty($categoryModel->extensions)) {
                $filesQuery->whereIn('extension', $categoryModel->extensions);
            }
        }

        // Sắp xếp file
        $fileSort = in_array($sort, ['name', 'size', 'trashed_at']) ? $sort : 'trashed_at';
        $filesQuery->orderBy($fileSort, $order);
        $files = $filesQuery->paginate(20)->appends($request->query());

        // Query các thư mục trong thùng rác
        $foldersQuery = \App\Models\Folder::query()
            ->where('user_id', $userId)
            ->where('is_trash', true);

        // Lọc theo tìm kiếm
        if ($search) {
            $foldersQuery->where('name', 'like', "%{$search}%");
        }

        // Sắp xếp thư mục
        $folderSort = in_array($sort, ['name', 'trashed_at']) ? $sort : 'trashed_at';
        $foldersQuery->orderBy($folderSort, $order);
        $folders = $foldersQuery->paginate(20)->appends($request->query());

        // Lấy danh sách các danh mục đang hoạt động
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        $filter = compact('item', 'category', 'search', 'sort', 'order');

        return view('pages.trash', compact('files', 'folders', 'categories', 'filter'));
    }

    /**
     * Dọn dẹp thùng rác: Xóa vĩnh viễn tất cả file và thư mục trong thùng rác của người dùng hiện tại.
     */
    public function cleanupTrash(Request $request)
    {
        $userId = Auth::id() ?? 1;

        // 1) Xóa vĩnh viễn các file trong thùng rác (xóa khỏi storage + database)
        $files = File::where('user_id', $userId)->where('is_trash', true)->get();
        foreach ($files as $file) {
            // Xóa file từ storage nếu tồn tại
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            // Xóa record khỏi database
            $file->delete();
        }

        // 2) Xóa vĩnh viễn các thư mục trong thùng rác từ dưới lên (xóa thư mục lá trước)
        // Tiếp tục xóa các thư mục lá trong thùng rác cho đến khi không còn
        $safetyCounter = 0;
        do {
            // Lấy các thư mục lá (không có thư mục con trong thùng rác)
            $leafFolders = Folder::where('user_id', $userId)
                ->where('is_trash', true)
                ->whereDoesntHave('children', function ($q) {
                    $q->where('is_trash', true);
                })
                ->get();

            // Xóa từng thư mục lá
            foreach ($leafFolders as $folder) {
                $folder->delete();
            }

            // Tăng bộ đếm an toàn để tránh vòng lặp vô hạn
            $safetyCounter++;
        } while ($leafFolders->count() > 0 && $safetyCounter < 200);

        return redirect()->back()->with('success', 'Trash cleaned up permanently!');
    }
}
