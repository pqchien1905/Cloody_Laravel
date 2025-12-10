<?php

namespace App\Jobs;

use App\Models\FileShare;
use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Job - Gửi thông báo email khi có file được chia sẻ
 */
class SendFileShareNotification implements ShouldQueue
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
        public FileShare $fileShare,
        public string $shareUrl
    ) {
        //
    }

    /**
     * Thực thi job - gửi email thông báo chia sẻ file.
     */
    public function handle(): void
    {
        try {
            // Tải các quan hệ cần thiết
            $fileShare = $this->fileShare->load(['file', 'sharedBy', 'sharedWith']);
            
            // Kiểm tra dữ liệu có đầy đủ không
            if (!$fileShare->file || !$fileShare->sharedBy || !$fileShare->sharedWith) {
                Log::warning('SendFileShareNotification: Missing required data', [
                    'file_share_id' => $this->fileShare->id,
                ]);
                return;
            }

            $file = $fileShare->file;
            $sharedBy = $fileShare->sharedBy;
            $sharedWith = $fileShare->sharedWith;

            // Gửi email thông báo
            Mail::send('emails.file-shared', [
                'file' => $file,
                'sharedBy' => $sharedBy,
                'sharedWith' => $sharedWith,
                'shareUrl' => $this->shareUrl,
                'expiresAt' => $fileShare->expires_at,
            ], function ($message) use ($sharedWith, $sharedBy, $file) {
                $message->to($sharedWith->email, $sharedWith->name)
                    ->subject($sharedBy->name . ' đã chia sẻ file với bạn: ' . $file->original_name);
            });

            Log::info('SendFileShareNotification: Email sent successfully', [
                'file_share_id' => $this->fileShare->id,
                'recipient' => $sharedWith->email,
            ]);
        } catch (\Exception $e) {
            Log::error('SendFileShareNotification: Failed to send email', [
                'file_share_id' => $this->fileShare->id,
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
        Log::error('SendFileShareNotification: Job failed after all retries', [
            'file_share_id' => $this->fileShare->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

