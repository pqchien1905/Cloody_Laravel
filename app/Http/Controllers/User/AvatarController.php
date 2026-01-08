<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Group;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controller - Xử lý việc phục vụ avatar cho người dùng và nhóm
 */
class AvatarController extends Controller
{
    /**
     * Phục vụ avatar của người dùng
     */
    public function user($id): BinaryFileResponse
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra người dùng có avatar không
        if (!$user->avatar) {
            abort(404, 'Avatar not found');
        }

        $filePath = storage_path('app/public/' . $user->avatar);

        // Kiểm tra file avatar có tồn tại trên đĩa không
        if (!file_exists($filePath)) {
            abort(404, 'Avatar file not found');
        }

        // Lấy loại MIME của file
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';

        // Phục vụ file với các header phù hợp
        // Giảm thời gian cache để cập nhật avatar hiển thị nhanh hơn
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600', // 1 giờ thay vì 1 năm để cập nhật nhanh hơn
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT',
        ]);
    }

    /**
     * Phục vụ avatar của nhóm
     */
    public function group($id): BinaryFileResponse
    {
        $group = Group::findOrFail($id);
        
        // Kiểm tra nhóm có avatar không
        if (!$group->avatar) {
            abort(404, 'Avatar not found');
        }

        $filePath = storage_path('app/public/' . $group->avatar);

        // Kiểm tra file avatar có tồn tại trên đĩa không
        if (!file_exists($filePath)) {
            abort(404, 'Avatar file not found');
        }

        // Lấy loại MIME của file
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';

        // Phục vụ file với các header phù hợp
        // Giảm thời gian cache để cập nhật avatar hiển thị nhanh hơn
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600', // 1 giờ thay vì 1 năm để cập nhật nhanh hơn
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT',
        ]);
    }
}

