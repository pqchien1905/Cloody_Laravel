<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model Subscription - Quản lý thông tin gói đăng ký lưu trữ của người dùng
 * 
 * @property int $id
 * @property int $user_id
 * @property string $plan_id
 * @property string $plan_name
 * @property int $storage_gb
 * @property string $billing_cycle
 * @property float $price
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property string $payment_status
 * @property string|null $notes
 * @property-read User $user
 */
class Subscription extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';

    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_name',
        'storage_gb',
        'billing_cycle',
        'price',
        'starts_at',
        'expires_at',
        'is_active',
        'payment_status',
        'notes',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Lấy người dùng sở hữu gói đăng ký này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra xem gói đăng ký đã hết hạn chưa
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false; // Không có ngày hết hạn nghĩa là vĩnh viễn
        }
        return $this->expires_at->isPast();
    }

    /**
     * Kiểm tra xem gói đăng ký có còn hiệu lực không (đang hoạt động và chưa hết hạn)
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired() && $this->payment_status === 'paid';
    }

    /**
     * Lấy giới hạn lưu trữ tính theo bytes
     */
    public function getStorageLimitBytes(): int
    {
        return $this->storage_gb * 1024 * 1024 * 1024;
    }

    /**
     * Lấy tất cả các giao dịch thanh toán cho gói đăng ký này
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    /**
     * Scope: Lấy các gói đăng ký đang hoạt động
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Lấy các gói đăng ký đã hết hạn
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope: Lấy các gói đăng ký còn hiệu lực (đang hoạt động, đã thanh toán, chưa hết hạn)
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('payment_status', 'paid')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: Lấy các gói đăng ký theo plan
     */
    public function scopeByPlan(Builder $query, string $planId): Builder
    {
        return $query->where('plan_id', $planId);
    }

    /**
     * Scope: Lấy các gói đăng ký theo chu kỳ thanh toán
     */
    public function scopeByBillingCycle(Builder $query, string $billingCycle): Builder
    {
        return $query->where('billing_cycle', $billingCycle);
    }

    /**
     * Scope: Lấy các gói đăng ký theo người dùng
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Lấy các gói đăng ký sắp hết hạn
     */
    public function scopeExpiringSoon(Builder $query, int $days = 7): Builder
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>=', now())
            ->where('expires_at', '<=', now()->addDays($days));
    }

    /**
     * Lấy giá đã định dạng kèm đơn vị tiền tệ
     */
    public function getFormattedPrice(): string
    {
        $price = number_format($this->price, 0, ',', '.');
        return $price . ' ₫';
    }

    /**
     * Lấy nhãn chu kỳ thanh toán bằng tiếng Việt
     */
    public function getBillingCycleLabel(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'Hàng tháng',
            'yearly' => 'Hàng năm',
            default => $this->billing_cycle,
        };
    }

    /**
     * Lấy số ngày còn lại đến khi hết hạn
     */
    public function getDaysRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null; // Không có ngày hết hạn
        }
        
        if ($this->expires_at->isPast()) {
            return 0; // Đã hết hạn
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Kiểm tra xem gói đăng ký có sắp hết hạn không (trong số ngày đã chỉ định)
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return false;
        }
        
        return $this->expires_at->diffInDays(now(), false) <= $days;
    }

    /**
     * Lấy thứ tự gói (để so sánh)
     */
    public function getPlanOrder(): int
    {
        $planOrder = [
            'basic' => 0,
            '100gb' => 1,
            '200gb' => 2,
            '2tb' => 3,
        ];
        
        return $planOrder[$this->plan_id] ?? 0;
    }

    /**
     * Kiểm tra xem gói đăng ký này có cao cấp hơn gói khác không
     */
    public function isHigherThan(Subscription $other): bool
    {
        return $this->getPlanOrder() > $other->getPlanOrder();
    }

    /**
     * Kiểm tra xem gói đăng ký này có thấp cấp hơn gói khác không
     */
    public function isLowerThan(Subscription $other): bool
    {
        return $this->getPlanOrder() < $other->getPlanOrder();
    }

    /**
     * Lấy ngày hết hạn đã định dạng bằng tiếng Việt
     */
    public function getFormattedExpiresAt(): ?string
    {
        if (!$this->expires_at) {
            return 'Không giới hạn';
        }
        
        $months = [
            1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6',
            7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'
        ];
        
        $day = $this->expires_at->format('d');
        $month = $months[(int)$this->expires_at->format('n')] ?? $this->expires_at->format('n');
        $year = $this->expires_at->format('Y');
        
        return $day . ' thg ' . $month . ', ' . $year;
    }

    /**
     * Lấy ngày bắt đầu đã định dạng bằng tiếng Việt
     */
    public function getFormattedStartsAt(): ?string
    {
        if (!$this->starts_at) {
            return null;
        }
        
        $months = [
            1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6',
            7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'
        ];
        
        $day = $this->starts_at->format('d');
        $month = $months[(int)$this->starts_at->format('n')] ?? $this->starts_at->format('n');
        $year = $this->starts_at->format('Y');
        
        return $day . ' thg ' . $month . ', ' . $year;
    }

    /**
     * Lấy nhãn trạng thái bằng tiếng Việt
     */
    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Đã hủy';
        }
        
        if ($this->isExpired()) {
            return 'Đã hết hạn';
        }
        
        if ($this->payment_status === 'paid') {
            return 'Đang hoạt động';
        }
        
        return match($this->payment_status) {
            'pending' => 'Đang chờ thanh toán',
            'failed' => 'Thanh toán thất bại',
            default => 'Không xác định',
        };
    }

    /**
     * Lấy class badge trạng thái cho Bootstrap
     */
    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'badge-secondary';
        }
        
        if ($this->isExpired()) {
            return 'badge-danger';
        }
        
        if ($this->payment_status === 'paid' && $this->is_active) {
            return 'badge-success';
        }
        
        return match($this->payment_status) {
            'pending' => 'badge-warning',
            'failed' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Lấy phần trăm thời gian gói đăng ký đã sử dụng
     */
    public function getUsagePercentage(): ?float
    {
        if (!$this->starts_at || !$this->expires_at) {
            return null;
        }
        
        $totalDays = $this->starts_at->diffInDays($this->expires_at);
        $usedDays = $this->starts_at->diffInDays(now());
        
        if ($totalDays <= 0) {
            return null;
        }
        
        return min(100, max(0, ($usedDays / $totalDays) * 100));
    }
}
