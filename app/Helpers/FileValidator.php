<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Helper class - Xác thực file trước khi upload
 */
class FileValidator
{
    /**
     * Xác thực phần mở rộng file và MIME type dựa trên whitelist/blacklist
     */
    public static function validateFile(UploadedFile $file): array
    {
        $config = config('cloody.validation');
        $errors = [];

        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        // Kiểm tra danh sách phần mở rộng được phép (whitelist)
        if (!empty($config['allowed_extensions'])) {
            if (!in_array($extension, array_map('strtolower', $config['allowed_extensions']))) {
                $errors[] = "File extension '{$extension}' is not allowed. Allowed extensions: " . implode(', ', $config['allowed_extensions']);
            }
        }

        // Kiểm tra danh sách phần mở rộng bị chặn (blacklist)
        if (!empty($config['blocked_extensions'])) {
            if (in_array($extension, array_map('strtolower', $config['blocked_extensions']))) {
                $errors[] = "File extension '{$extension}' is blocked for security reasons.";
            }
        }

        // Kiểm tra danh sách MIME type được phép (whitelist)
        if (!empty($config['allowed_mime_types'])) {
            if (!in_array($mimeType, $config['allowed_mime_types'])) {
                $errors[] = "File type '{$mimeType}' is not allowed. Allowed types: " . implode(', ', $config['allowed_mime_types']);
            }
        }

        // Kiểm tra danh sách MIME type bị chặn (blacklist)
        if (!empty($config['blocked_mime_types'])) {
            if (in_array($mimeType, $config['blocked_mime_types'])) {
                $errors[] = "File type '{$mimeType}' is blocked for security reasons.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Lấy kích thước file tối đa tính theo KB từ cấu hình
     */
    public static function getMaxFileSize(): int
    {
        return config('cloody.upload.max_file_size', 102400);
    }

    /**
     * Lấy tổng kích thước tối đa cho upload hàng loạt tính theo KB
     */
    public static function getMaxTotalSize(): int
    {
        return config('cloody.upload.max_total_size', 512000);
    }

    /**
     * Lấy số lượng file tối đa mỗi request
     */
    public static function getMaxFilesPerRequest(): int
    {
        return config('cloody.upload.max_files_per_request', 50);
    }
}

