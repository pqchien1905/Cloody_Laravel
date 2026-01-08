<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model Payment - Quản lý thông tin giao dịch thanh toán
 */

/**
 * Model Payment - Quản lý thông tin giao dịch thanh toán
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $subscription_id
 * @property string $plan_id
 * @property string $plan_name
 * @property int $storage_gb
 * @property string $billing_cycle
 * @property float $amount
 * @property string $currency
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $transaction_id
 * @property string|null $vnpay_txn_ref
 * @property string|null $vnpay_response
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $notes
 * @property-read User $user
 * @property-read Subscription|null $subscription
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|Payment byBillingCycle(string $billingCycle)
 * @method static Builder<static>|Payment byPaymentMethod(string $method)
 * @method static Builder<static>|Payment byPlan(string $planId)
 * @method static Builder<static>|Payment byUser(int $userId)
 * @method static Builder<static>|Payment completed()
 * @method static Builder<static>|Payment failed()
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment pending()
 * @method static Builder<static>|Payment processing()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment recent(int $days = 30)
 * @method static Builder<static>|Payment whereAmount($value)
 * @method static Builder<static>|Payment whereBillingCycle($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereCurrency($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereNotes($value)
 * @method static Builder<static>|Payment wherePaidAt($value)
 * @method static Builder<static>|Payment wherePaymentMethod($value)
 * @method static Builder<static>|Payment wherePaymentStatus($value)
 * @method static Builder<static>|Payment wherePlanId($value)
 * @method static Builder<static>|Payment wherePlanName($value)
 * @method static Builder<static>|Payment whereStorageGb($value)
 * @method static Builder<static>|Payment whereSubscriptionId($value)
 * @method static Builder<static>|Payment whereTransactionId($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @method static Builder<static>|Payment whereUserId($value)
 * @method static Builder<static>|Payment whereVnpayResponse($value)
 * @method static Builder<static>|Payment whereVnpayTxnRef($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'plan_name',
        'storage_gb',
        'billing_cycle',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_id',
        'vnpay_txn_ref',
        'vnpay_response',
        'paid_at',
        'notes',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Lấy người dùng sở hữu giao dịch thanh toán này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy gói đăng ký liên quan đến giao dịch thanh toán này.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Kiểm tra xem giao dịch thanh toán đã hoàn tất chưa
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Kiểm tra xem giao dịch thanh toán đang chờ xử lý không
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Kiểm tra xem giao dịch thanh toán có thất bại không
     */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    /**
     * Kiểm tra xem giao dịch thanh toán đang được xử lý không
     */
    public function isProcessing(): bool
    {
        return $this->payment_status === 'processing';
    }

    /**
     * Kiểm tra xem giao dịch thanh toán đã bị hủy chưa
     */
    public function isCancelled(): bool
    {
        return $this->payment_status === 'cancelled';
    }

    /**
     * Scope: Lấy các giao dịch thanh toán đã hoàn tất
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope: Lấy các giao dịch thanh toán đang chờ xử lý
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope: Lấy các giao dịch thanh toán thất bại
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('payment_status', 'failed');
    }

    /**
     * Scope: Lấy các giao dịch thanh toán đang được xử lý
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('payment_status', 'processing');
    }

    /**
     * Scope: Lấy các giao dịch thanh toán theo người dùng
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Lấy các giao dịch thanh toán gần đây
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Lấy các giao dịch thanh toán theo gói
     */
    public function scopeByPlan(Builder $query, string $planId): Builder
    {
        return $query->where('plan_id', $planId);
    }

    /**
     * Scope: Lấy các giao dịch thanh toán theo chu kỳ thanh toán
     */
    public function scopeByBillingCycle(Builder $query, string $billingCycle): Builder
    {
        return $query->where('billing_cycle', $billingCycle);
    }

    /**
     * Scope: Lấy các giao dịch thanh toán theo phương thức thanh toán
     */
    public function scopeByPaymentMethod(Builder $query, string $method): Builder
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Lấy số tiền đã định dạng kèm đơn vị tiền tệ
     */
    public function getFormattedAmount(): string
    {
        $amount = number_format($this->amount, 0, ',', '.');
        $currency = $this->currency === 'VND' ? '₫' : $this->currency;
        return $amount . ' ' . $currency;
    }

    /**
     * Lấy nhãn trạng thái bằng tiếng Việt
     */
    public function getStatusLabel(): string
    {
        return match($this->payment_status) {
            'completed' => 'Thành công',
            'pending' => 'Đang chờ',
            'processing' => 'Đang xử lý',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    /**
     * Lấy class badge trạng thái cho Bootstrap
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->payment_status) {
            'completed' => 'badge-success',
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'failed' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-secondary',
        };
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
     * Lấy nhãn phương thức thanh toán bằng tiếng Việt
     */
    public function getPaymentMethodLabel(): string
    {
        return match($this->payment_method) {
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
            'zalopay' => 'ZaloPay',
            'bank' => 'Chuyển khoản ngân hàng',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Lấy dữ liệu phản hồi từ VNPay dạng mảng
     */
    public function getVnpayResponseData(): ?array
    {
        if (empty($this->vnpay_response)) {
            return null;
        }
        
        $data = json_decode($this->vnpay_response, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Lấy số ngày kể từ khi giao dịch thanh toán được tạo
     */
    public function getDaysSinceCreated(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Lấy số ngày kể từ khi giao dịch thanh toán hoàn tất
     */
    public function getDaysSincePaid(): ?int
    {
        if (!$this->paid_at) {
            return null;
        }
        return $this->paid_at->diffInDays(now());
    }
}
