<?php

namespace App\Models;

// Sử dụng interface xác thực email (hiện tại chưa dùng)
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model User - Quản lý thông tin người dùng
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $bio
 * @property bool $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $groups
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Group> $ownedGroups
 * @property-read \Illuminate\Database\Eloquent\Collection<int, File> $files
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $folders
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Các thuộc tính có thể gán hàng loạt (mass assignment).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
        'phone',
        'address',
        'bio',
    ];

    /**
     * Các thuộc tính cần ẩn khi serialize (không hiển thị trong JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Lấy các thuộc tính cần ép kiểu (cast).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Lấy URL avatar của người dùng
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        // Sử dụng route để phục vụ avatar để kiểm soát truy cập tốt hơn và tránh lỗi 403
        try {
            // Thêm timestamp vào URL để xóa cache khi avatar được cập nhật
            $timestamp = $this->updated_at ? $this->updated_at->timestamp : time();
            return route('avatar.user', $this->id) . '?v=' . $timestamp;
        } catch (\Exception $e) {
            // Fallback về storage URL nếu route thất bại
            $timestamp = $this->updated_at ? $this->updated_at->timestamp : time();
            if (strpos($this->avatar, 'storage/') === 0) {
                return asset($this->avatar) . '?v=' . $timestamp;
            }
            return asset('storage/' . $this->avatar) . '?v=' . $timestamp;
        }
    }

    /**
     * Lấy tất cả các file thuộc sở hữu của người dùng.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Lấy tất cả các thư mục thuộc sở hữu của người dùng.
     */
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Lấy các file được chia sẻ bởi người dùng này.
     */
    public function sharedFiles()
    {
        return $this->hasMany(FileShare::class, 'shared_by');
    }

    /**
     * Lấy các file được chia sẻ với người dùng này.
     */
    public function receivedShares()
    {
        return $this->hasMany(FileShare::class, 'shared_with');
    }

    /**
     * Lấy tất cả các nhóm mà người dùng là chủ sở hữu.
     * 
     * @return HasMany<Group>
     */
    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * Lấy tất cả các nhóm mà người dùng là thành viên.
     * 
     * @return BelongsToMany<Group>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Lấy tất cả các gói đăng ký của người dùng.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Lấy gói đăng ký đang hoạt động của người dùng.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('is_active', true)
            ->where('payment_status', 'paid')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest();
    }

    /**
     * Lấy giới hạn lưu trữ hiện tại tính theo GB dựa trên gói đăng ký đang hoạt động
     */
    public function getStorageLimitGB(): float
    {
        $subscription = $this->activeSubscription;
        if ($subscription && $subscription->isValid()) {
            return $subscription->storage_gb;
        }
        // Mặc định 1GB cho gói miễn phí
        return 1;
    }

    /**
     * Lấy giới hạn lưu trữ hiện tại tính theo bytes dựa trên gói đăng ký đang hoạt động
     */
    public function getStorageLimitBytes(): int
    {
        return (int)($this->getStorageLimitGB() * 1024 * 1024 * 1024);
    }

    /**
     * Lấy tất cả các giao dịch thanh toán của người dùng.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Gửi thông báo đặt lại mật khẩu.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
