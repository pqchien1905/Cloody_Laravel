<?php

namespace App\Http\Controllers;

use App\Models\FileShare;
use App\Models\FolderShare;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSharesController extends Controller
{
    /**
     * Display a listing of all shares.
     */
    public function index(Request $request)
    {
        // Lấy file shares
        $fileSharesQuery = FileShare::with(['file.user', 'sharedBy', 'sharedWith']);
        $folderSharesQuery = FolderShare::with(['folder.user', 'sharedBy', 'sharedWith']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $fileSharesQuery->whereHas('file', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
            $folderSharesQuery->whereHas('folder', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Lọc theo người chia sẻ
        if ($request->filled('shared_by')) {
            $fileSharesQuery->where('shared_by', $request->shared_by);
            $folderSharesQuery->where('shared_by', $request->shared_by);
        }

        // Lọc theo người nhận
        if ($request->filled('shared_with')) {
            $fileSharesQuery->where('shared_with', $request->shared_with);
            $folderSharesQuery->where('shared_with', $request->shared_with);
        }

        // Lọc theo loại chia sẻ
        if ($request->filled('type')) {
            if ($request->type === 'file') {
                $folderSharesQuery->whereRaw('1 = 0'); // Không lấy folder shares
            } elseif ($request->type === 'folder') {
                $fileSharesQuery->whereRaw('1 = 0'); // Không lấy file shares
            }
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $fileSharesQuery->active();
                $folderSharesQuery->active();
            } elseif ($request->status === 'expired') {
                $fileSharesQuery->where('expires_at', '<=', now());
                $folderSharesQuery->where('expires_at', '<=', now());
            } elseif ($request->status === 'public') {
                $fileSharesQuery->where('is_public', true);
                $folderSharesQuery->where('is_public', true);
            } elseif ($request->status === 'private') {
                $fileSharesQuery->where('is_public', false);
                $folderSharesQuery->where('is_public', false);
            }
        }

        // Lọc theo quyền truy cập
        if ($request->filled('permission')) {
            $fileSharesQuery->where('permission', $request->permission);
            $folderSharesQuery->where('permission', $request->permission);
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['created_at', 'expires_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $fileSharesQuery->orderBy($sortBy, $sortOrder);
            $folderSharesQuery->orderBy($sortBy, $sortOrder);
        } else {
            $fileSharesQuery->orderBy('created_at', 'desc');
            $folderSharesQuery->orderBy('created_at', 'desc');
        }

        // Lấy dữ liệu
        $fileShares = $fileSharesQuery->get()->map(function($share) {
            $share->share_type = 'file';
            return $share;
        });

        $folderShares = $folderSharesQuery->get()->map(function($share) {
            $share->share_type = 'folder';
            return $share;
        });

        // Gộp và sắp xếp
        $allShares = $fileShares->concat($folderShares)->sortByDesc('created_at');

        // Phân trang thủ công
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $allShares->count();
        $shares = $allShares->forPage($currentPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $shares,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Thống kê
        $stats = [
            'total' => FileShare::count() + FolderShare::count(),
            'file_shares' => FileShare::count(),
            'folder_shares' => FolderShare::count(),
            'active' => FileShare::active()->count() + FolderShare::active()->count(),
            'expired' => FileShare::where('expires_at', '<=', now())->count() + 
                        FolderShare::where('expires_at', '<=', now())->count(),
            'public' => FileShare::where('is_public', true)->count() + 
                       FolderShare::where('is_public', true)->count(),
            'private' => FileShare::where('is_public', false)->count() + 
                        FolderShare::where('is_public', false)->count(),
        ];

        // Lấy danh sách users cho filter
        $users = User::orderBy('name')->get();

        return view('pages.admin.shares.index', compact('paginator', 'stats', 'users'));
    }

    /**
     * Remove the specified share.
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type', 'file');
        
        try {
            if ($type === 'folder') {
                $share = FolderShare::findOrFail($id);
            } else {
                $share = FileShare::findOrFail($id);
            }

            $share->delete();

            return redirect()->route('admin.shares.index')
                ->with('status', __('common.share_revoked_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('common.error_revoking_share') . ': ' . $e->getMessage());
        }
    }
}

