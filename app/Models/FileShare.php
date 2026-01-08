<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Model FileShare - Quản lý việc chia sẻ file giữa các người dùng
 *
 * @property int $id
 * @property int $file_id
 * @property int $shared_by
 * @property int|null $shared_with
 * @property string $share_token
 * @property string $permission
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File $file
 * @property-read \App\Models\User $sharedBy
 * @property-read \App\Models\User|null $sharedWith
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereSharedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereSharedWith($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileShare whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileShare extends Model
{
    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'file_id',
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
     * Lấy file được chia sẻ.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Lấy người dùng đã chia sẻ file.
     */
    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    /**
     * Lấy người dùng được chia sẻ file.
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
