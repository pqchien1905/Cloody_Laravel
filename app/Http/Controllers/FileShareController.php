<?php

namespace App\Http\Controllers;

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
     * Chia sẻ file với người dùng qua email.
     */
    public function store(Request $request, $fileId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $file = File::findOrFail($fileId);

        // Tìm người nhận theo email
        $recipient = User::where('email', $request->email)->first();
        if (!$recipient) {
            return redirect()->back()->withErrors(['email' => __('common.recipient_email_not_found')]);
        }

        // Tạo record chia sẻ
        $share = FileShare::create([
            'file_id' => $file->id,
            'shared_by' => Auth::id() ?? 1,
            'shared_with' => $recipient->id,
            'permission' => 'view',
            'is_public' => false,
            'expires_at' => null,
        ]);

        // Tạo URL chia sẻ
        $shareUrl = route('file.shared', $share->share_token);

        // Đưa thông báo email vào hàng đợi
        SendFileShareNotification::dispatch($share, $shareUrl);

        return redirect()->back()->with([
            'success' => __('common.file_shared_successfully'),
            'share_url' => $shareUrl,
        ]);
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
     * Thu hồi lượt chia sẻ file.
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type', 'file');
        if ($type === 'folder') {
            \App\Models\FolderShare::findOrFail($id)->delete();
        } else {
            FileShare::findOrFail($id)->delete();
        }

        return redirect()->back()->with('success', __('common.share_revoked_successfully'));
    }

    /**
     * List all shares for a file.
     */
    public function listShares($fileId)
    {
        $file = File::with('shares.sharedWith')->findOrFail($fileId);
        
        return view('pages.file-shares', compact('file'));
    }
}
