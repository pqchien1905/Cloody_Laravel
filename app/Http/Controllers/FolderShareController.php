<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderShare;
use App\Models\User;
use App\Jobs\SendFolderShareNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller - Xử lý việc chia sẻ thư mục với người dùng khác
 */
class FolderShareController extends Controller
{
    /**
     * Chia sẻ thư mục với người dùng qua email.
     */
    public function store(Request $request, $folderId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $folder = Folder::findOrFail($folderId);

        // Chỉ chủ sở hữu mới được chia sẻ thư mục
        if (($folder->user_id ?? null) !== (Auth::id() ?? 0)) {
            abort(403, __('common.not_allowed_share_folder'));
        }

        // Tìm người nhận theo email
        $recipient = User::where('email', $request->email)->first();
        if (!$recipient) {
            return back()->withErrors(['email' => __('common.recipient_email_not_found')]);
        }

        // Tạo record chia sẻ thư mục
        $share = FolderShare::create([
            'folder_id' => $folder->id,
            'shared_by' => Auth::id() ?? 1,
            'shared_with' => $recipient->id,
            'permission' => 'view',
            'is_public' => false,
            'expires_at' => null,
        ]);

        // Tạo URL chia sẻ (hiện tại dùng placeholder - có thể mở rộng sau)
        $shareUrl = url('/cloody/shared?token=' . ($share->share_token ?? ''));

        // Đưa thông báo email vào hàng đợi
        SendFolderShareNotification::dispatch($share, $shareUrl);

        return back()->with('success', __('common.folder_shared_successfully'));
    }
}
