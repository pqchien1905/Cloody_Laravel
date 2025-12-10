<?php

namespace App\Jobs;

use App\Models\FolderShare;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Job - Gửi thông báo email khi có thư mục được chia sẻ
 */
class SendFolderShareNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Số lần thử lại nếu job thất bại
     */
    public $tries = 3;
    
    /**
     * Thời gian timeout cho job (giây)
     */
    public $timeout = 60;

    /**
     * Tạo một instance job mới.
     */
    public function __construct(
        public FolderShare $folderShare,
        public string $shareUrl
    ) {
        //
    }

    /**
     * Thực thi job - gửi email thông báo chia sẻ thư mục.
     */
    public function handle(): void
    {
        try {
            // Tải các quan hệ cần thiết
            $folderShare = $this->folderShare->load(['folder', 'sharedBy', 'sharedWith']);
            
            // Kiểm tra dữ liệu có đầy đủ không
            if (!$folderShare->folder || !$folderShare->sharedBy || !$folderShare->sharedWith) {
                Log::warning('SendFolderShareNotification: Missing required data', [
                    'folder_share_id' => $this->folderShare->id,
                ]);
                return;
            }

            $folder = $folderShare->folder;
            $sharedBy = $folderShare->sharedBy;
            $sharedWith = $folderShare->sharedWith;

            // Gửi email thông báo
            Mail::send('emails.folder-shared', [
                'folder' => $folder,
                'sharedBy' => $sharedBy,
                'sharedWith' => $sharedWith,
                'shareUrl' => $this->shareUrl,
                'expiresAt' => $folderShare->expires_at,
            ], function ($message) use ($sharedWith, $sharedBy, $folder) {
                $message->to($sharedWith->email, $sharedWith->name)
                    ->subject($sharedBy->name . ' đã chia sẻ thư mục với bạn: ' . $folder->name);
            });

            Log::info('SendFolderShareNotification: Email sent successfully', [
                'folder_share_id' => $this->folderShare->id,
                'recipient' => $sharedWith->email,
            ]);
        } catch (\Exception $e) {
            Log::error('SendFolderShareNotification: Failed to send email', [
                'folder_share_id' => $this->folderShare->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Xử lý khi job thất bại sau tất cả các lần thử lại.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendFolderShareNotification: Job failed after all retries', [
            'folder_share_id' => $this->folderShare->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

