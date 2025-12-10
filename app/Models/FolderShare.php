<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Model FolderShare - Quản lý việc chia sẻ thư mục giữa các người dùng
 */
class FolderShare extends Model
{
    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'folder_id',
        'shared_by',
        'shared_with',
        'share_token',
        'permission',
        'is_public',
        'expires_at',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected $casts = [
        'is_public' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Phương thức boot để tự động tạo share token khi tạo mới.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->share_token)) {
                // Tự động tạo token ngẫu nhiên 32 ký tự nếu chưa có
                $model->share_token = Str::random(32);
            }
        });
    }

    /**
     * Lấy thư mục được chia sẻ.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Lấy người dùng đã chia sẻ thư mục.
     */
    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    /**
     * Lấy người dùng được chia sẻ thư mục.
     */
    public function sharedWith(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with');
    }

    /**
     * Kiểm tra xem lượt chia sẻ đã hết hạn chưa.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Scope để lấy các lượt chia sẻ còn hiệu lực (chưa hết hạn).
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
