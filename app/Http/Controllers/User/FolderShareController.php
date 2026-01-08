<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Folder;
use App\Models\FolderShare;
use App\Models\User;
use App\Jobs\SendFolderShareNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller - Xử lý việc chia sẻ thư mục với người dùng khác
 */
class FolderShareController extends Controller
{
    /**
     * Chia sẻ thư mục với người dùng qua email hoặc tạo link công khai.
     */
    public function store(Request $request, $folderId)
    {
        $folder = Folder::findOrFail($folderId);

        // Chỉ chủ sở hữu mới được chia sẻ thư mục
        if (($folder->user_id ?? null) !== (Auth::id() ?? 0)) {
            abort(403, __('common.not_allowed_share_folder'));
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

            $share = FolderShare::create([
                'folder_id' => $folder->id,
                'shared_by' => Auth::id(),
                'shared_with' => null,
                'permission' => $request->permission,
                'is_public' => true,
                'expires_at' => $expiresAt,
            ]);

            $shareUrl = route('folder.shared', $share->share_token);

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
            $existingShare = FolderShare::where('folder_id', $folder->id)
                ->where('shared_with', $recipient->id)
                ->first();

            if ($existingShare) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.already_shared_with_user'),
                ], 400);
            }

            // Tạo record chia sẻ thư mục
            $share = FolderShare::create([
                'folder_id' => $folder->id,
                'shared_by' => Auth::id(),
                'shared_with' => $recipient->id,
                'permission' => $request->permission ?? 'view',
                'is_public' => false,
                'expires_at' => null,
            ]);

            // Tạo URL chia sẻ
            $shareUrl = route('folder.shared', $share->share_token);

            // Đưa thông báo email vào hàng đợi
            SendFolderShareNotification::dispatch($share, $shareUrl);

            return response()->json([
                'success' => true,
                'message' => __('common.folder_shared_successfully'),
                'share_url' => $shareUrl,
            ]);
        }
    }

    /**
     * Xem thư mục được chia sẻ qua token.
     */
    public function show($token)
    {
        // Tìm lượt chia sẻ theo token
        $share = FolderShare::with([
                'folder.files' => function($query) {
                    $query->where('is_trash', false);
                },
                'folder.subfolders' => function($query) {
                    $query->where('is_trash', false)
                          ->with(['files' => function($q) {
                              $q->where('is_trash', false);
                          }]);
                }
            ])
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        // Kiểm tra lượt chia sẻ đã hết hạn chưa
        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        return view('pages.folder-shared', compact('share'));
    }

    /**
     * Tải xuống thư mục được chia sẻ dưới dạng ZIP.
     */
    public function download($token)
    {
        // Tìm lượt chia sẻ theo token
        $share = FolderShare::with([
                'folder.files' => function($query) {
                    $query->where('is_trash', false);
                },
                'folder.subfolders' => function($query) {
                    $query->where('is_trash', false)
                          ->with(['files' => function($q) {
                              $q->where('is_trash', false);
                          }]);
                }
            ])
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

        $folder = $share->folder;
        
        Log::info('Download folder', [
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'files_count' => $folder->files->count(),
            'subfolders_count' => $folder->subfolders->count()
        ]);
        
        // Tạo file ZIP tạm thời - sanitize tên file để tránh lỗi encoding
        $safeFolderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $folder->name);
        $zipFileName = $safeFolderName . '_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Đảm bảo thư mục temp tồn tại
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        $zipOpened = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        
        if ($zipOpened !== TRUE) {
            Log::error('Không thể mở ZIP file', ['zipPath' => $zipPath, 'error' => $zipOpened]);
            abort(500, 'Không thể tạo file ZIP. Error code: ' . $zipOpened);
        }
        
        $filesAdded = 0;
        
        // Thêm các file trong thư mục chính
        foreach ($folder->files as $file) {
            // File path là relative từ storage/app/public/
            $filePath = storage_path('app/public/' . $file->path);
            
            Log::info('Adding main file', ['file' => $file->original_name, 'path' => $filePath, 'exists' => file_exists($filePath)]);
            
            if (file_exists($filePath)) {
                $added = $zip->addFile($filePath, $file->original_name);
                if ($added) {
                    $filesAdded++;
                }
            }
        }
        
        // Thêm các file trong thư mục con
        foreach ($folder->subfolders as $subfolder) {
            Log::info('Processing subfolder', ['subfolder' => $subfolder->name, 'files_count' => $subfolder->files->count()]);
            
            foreach ($subfolder->files as $file) {
                // File path là relative từ storage/app/public/
                $filePath = storage_path('app/public/' . $file->path);
                
                Log::info('Adding subfolder file', ['file' => $file->original_name, 'path' => $filePath, 'exists' => file_exists($filePath)]);
                
                if (file_exists($filePath)) {
                    // Thêm vào ZIP với đường dẫn thư mục con
                    $added = $zip->addFile($filePath, $subfolder->name . '/' . $file->original_name);
                    if ($added) {
                        $filesAdded++;
                    }
                }
            }
        }
        
        $zip->close();
        
        Log::info('ZIP created', ['filesAdded' => $filesAdded, 'zipPath' => $zipPath, 'exists' => file_exists($zipPath)]);

        // Kiểm tra có file nào được thêm không
        if ($filesAdded === 0) {
            @unlink($zipPath); // Xóa file ZIP rỗng
            abort(400, 'Thư mục không có file để tải xuống');
        }

        // Tên file download giữ nguyên tiếng Việt
        $downloadName = $folder->name . '_' . time() . '.zip';
        
        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Liệt kê tất cả các lượt chia sẻ của thư mục.
     */
    public function listShares($folderId)
    {
        $folder = Folder::with(['shares' => function($query) {
            $query->with('sharedWith', 'sharedBy')
                  ->orderBy('created_at', 'desc');
        }])->findOrFail($folderId);
        
        // Kiểm tra quyền truy cập
        if ($folder->user_id !== Auth::id()) {
            abort(403, __('common.not_allowed_view_shares'));
        }
        
        return response()->json([
            'success' => true,
            'shares' => $folder->shares->map(function($share) {
                return [
                    'id' => $share->id,
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
                ];
            }),
        ]);
    }
}
