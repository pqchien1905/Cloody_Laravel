<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\FileShare;
use App\Models\User;
use App\Jobs\SendFileShareNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller - Xử lý việc chia sẻ file với người dùng khác
 */
class FileShareController extends Controller
{
    /**
     * Chia sẻ file với người dùng qua email hoặc tạo link công khai.
     */
    public function store(Request $request, $fileId)
    {
        $file = File::findOrFail($fileId);

        // Kiểm tra quyền sở hữu
        if ($file->user_id !== Auth::id()) {
            abort(403, __('common.not_allowed_share_file'));
        }

        // Xác định loại chia sẻ: public link hoặc chia sẻ với người dùng cụ thể
        $shareType = $request->input('share_type', 'user'); // 'user' hoặc 'public'

        if ($shareType === 'public') {
            // Tạo link công khai
            $request->validate([
                'permission' => 'required|in:view,download',
                'expires_in_days' => 'nullable|integer|min:1|max:365',
            ]);

            $expiresAt = null;
            if ($request->filled('expires_in_days')) {
                $expiresAt = now()->addDays($request->expires_in_days);
            }

            $share = FileShare::create([
                'file_id' => $file->id,
                'shared_by' => Auth::id(),
                'shared_with' => null,
                'permission' => $request->permission,
                'is_public' => true,
                'expires_at' => $expiresAt,
            ]);

            $shareUrl = route('file.shared', $share->share_token);

            return response()->json([
                'success' => true,
                'message' => __('common.public_link_created_successfully'),
                'share_url' => $shareUrl,
                'expires_at' => $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : null,
            ]);
        } else {
            // Chia sẻ với người dùng cụ thể
            $request->validate([
                'email' => 'required|email',
                'permission' => 'nullable|in:view,download',
            ]);

            // Tìm người nhận theo email
            $recipient = User::where('email', $request->email)->first();
            if (!$recipient) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.recipient_email_not_found'),
                ], 404);
            }

            // Kiểm tra xem đã chia sẻ với người này chưa
            $existingShare = FileShare::where('file_id', $file->id)
                ->where('shared_with', $recipient->id)
                ->first();

            if ($existingShare) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.already_shared_with_user'),
                ], 400);
            }

            // Tạo record chia sẻ
            $share = FileShare::create([
                'file_id' => $file->id,
                'shared_by' => Auth::id(),
                'shared_with' => $recipient->id,
                'permission' => $request->permission ?? 'view',
                'is_public' => false,
                'expires_at' => null,
            ]);

            // Tạo URL chia sẻ
            $shareUrl = route('file.shared', $share->share_token);

            // Đưa thông báo email vào hàng đợi
            SendFileShareNotification::dispatch($share, $shareUrl);

            return response()->json([
                'success' => true,
                'message' => __('common.file_shared_successfully'),
                'share_url' => $shareUrl,
            ]);
        }
    }

    /**
     * Xem file được chia sẻ qua token.
     */
    public function show($token)
    {
        // Tìm lượt chia sẻ theo token
        $share = FileShare::with('file')
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        // Kiểm tra lượt chia sẻ đã hết hạn chưa
        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        return view('pages.file-shared', compact('share'));
    }

    /**
     * Tải xuống file được chia sẻ.
     */
    public function download($token)
    {
        // Tìm lượt chia sẻ theo token
        $share = FileShare::with('file')
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        // Kiểm tra lượt chia sẻ đã hết hạn chưa
        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        // Kiểm tra quyền tải xuống
        if (!in_array($share->permission, ['download', 'edit'])) {
            abort(403, __('common.no_permission_download'));
        }

        $file = $share->file;
        $filePath = storage_path('app/public/' . $file->path);

        // Kiểm tra file có tồn tại không
        if (!file_exists($filePath)) {
            abort(404, __('common.file_not_found'));
        }

        return response()->download($filePath, $file->original_name);
    }

    /**
     * Xem nội dung file được chia sẻ (không download).
     */
    public function view($token)
    {
        // Tìm lượt chia sẻ theo token
        $share = FileShare::with('file')
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        // Kiểm tra lượt chia sẻ đã hết hạn chưa
        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        $file = $share->file;
        $filePath = storage_path('app/public/' . $file->path);

        // Kiểm tra file có tồn tại không
        if (!file_exists($filePath)) {
            abort(404, __('common.file_not_found'));
        }

        // Trả về file để xem trực tiếp (không download)
        return response()->file($filePath, [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"'
        ]);
    }

    /**
     * Thu hồi lượt chia sẻ file.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $type = $request->input('type', 'file');
            
            if ($type === 'folder') {
                $share = \App\Models\FolderShare::findOrFail($id);
                
                // Kiểm tra quyền
                if ($share->shared_by !== Auth::id()) {
                    return redirect()->back()->with('error', 'Bạn không có quyền thu hồi chia sẻ này');
                }
                
                $share->delete();
            } else {
                $share = FileShare::findOrFail($id);
                
                // Kiểm tra quyền
                if ($share->shared_by !== Auth::id()) {
                    return redirect()->back()->with('error', 'Bạn không có quyền thu hồi chia sẻ này');
                }
                
                $share->delete();
            }

            return redirect()->back()->with('success', __('common.share_revoked_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể thu hồi quyền chia sẻ: ' . $e->getMessage());
        }
    }

    /**
     * List all shares for a file.
     */
    public function listShares($fileId)
    {
        $file = File::with(['shares' => function($query) {
            $query->with('sharedWith', 'sharedBy')
                  ->orderBy('created_at', 'desc');
        }])->findOrFail($fileId);
        
        // Kiểm tra quyền truy cập
        if ($file->user_id !== Auth::id()) {
            abort(403, __('common.not_allowed_view_shares'));
        }
        
        return response()->json([
            'success' => true,
            'shares' => $file->shares->map(function($share) {
                return [
                    'id' => $share->id,
                    'is_public' => $share->is_public,
                    'permission' => $share->permission,
                    'share_url' => route('file.shared', $share->share_token),
                    'expires_at' => $share->expires_at?->format('Y-m-d H:i:s'),
                    'is_expired' => $share->isExpired(),
                    'shared_with' => $share->is_public ? null : [
                        'id' => $share->sharedWith->id ?? null,
                        'name' => $share->sharedWith->name ?? null,
                        'email' => $share->sharedWith->email ?? null,
                    ],
                    'created_at' => $share->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }
    
    /**
     * Get all shares created by current user (for all files)
     */
    public function getAllShares()
    {
        $fileShares = FileShare::with(['file', 'sharedWith', 'sharedBy'])
            ->where('shared_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $folderShares = \App\Models\FolderShare::with(['folder', 'sharedWith', 'sharedBy'])
            ->where('shared_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $allShares = collect();
        
        // Add file shares
        foreach ($fileShares as $share) {
            $allShares->push([
                'id' => $share->id,
                'type' => 'file',
                'item_name' => $share->file->original_name ?? 'Unknown',
                'is_public' => $share->is_public,
                'permission' => $share->permission,
                'share_url' => route('file.shared', $share->share_token),
                'expires_at' => $share->expires_at?->format('Y-m-d H:i:s'),
                'is_expired' => $share->isExpired(),
                'shared_with' => $share->is_public ? null : [
                    'id' => $share->sharedWith->id ?? null,
                    'name' => $share->sharedWith->name ?? null,
                    'email' => $share->sharedWith->email ?? null,
                ],
                'created_at' => $share->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        // Add folder shares
        foreach ($folderShares as $share) {
            $allShares->push([
                'id' => $share->id,
                'type' => 'folder',
                'item_name' => $share->folder->name ?? 'Unknown',
                'is_public' => $share->is_public,
                'permission' => $share->permission,
                'share_url' => route('folder.shared', $share->share_token),
                'expires_at' => $share->expires_at?->format('Y-m-d H:i:s'),
                'is_expired' => $share->isExpired(),
                'shared_with' => $share->is_public ? null : [
                    'id' => $share->sharedWith->id ?? null,
                    'name' => $share->sharedWith->name ?? null,
                    'email' => $share->sharedWith->email ?? null,
                ],
                'created_at' => $share->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'shares' => $allShares->sortByDesc('created_at')->values(),
        ]);
    }
}
