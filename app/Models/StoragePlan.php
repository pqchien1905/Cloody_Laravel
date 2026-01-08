<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model StoragePlan - Quản lý thông tin gói lưu trữ
 *
 * @property int $id
 * @property string $plan_id
 * @property string $name
 * @property int $storage_gb
 * @property float $price_monthly
 * @property float $price_yearly
 * @property array|null $features
 * @property bool $is_active
 * @property bool $is_popular
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|StoragePlan active()
 * @method static Builder<static>|StoragePlan newModelQuery()
 * @method static Builder<static>|StoragePlan newQuery()
 * @method static Builder<static>|StoragePlan ordered()
 * @method static Builder<static>|StoragePlan query()
 * @method static Builder<static>|StoragePlan whereCreatedAt($value)
 * @method static Builder<static>|StoragePlan whereFeatures($value)
 * @method static Builder<static>|StoragePlan whereId($value)
 * @method static Builder<static>|StoragePlan whereIsActive($value)
 * @method static Builder<static>|StoragePlan whereIsPopular($value)
 * @method static Builder<static>|StoragePlan whereName($value)
 * @method static Builder<static>|StoragePlan wherePlanId($value)
 * @method static Builder<static>|StoragePlan wherePriceMonthly($value)
 * @method static Builder<static>|StoragePlan wherePriceYearly($value)
 * @method static Builder<static>|StoragePlan whereSortOrder($value)
 * @method static Builder<static>|StoragePlan whereStorageGb($value)
 * @method static Builder<static>|StoragePlan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoragePlan extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'storage_plans';

    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'plan_id',
        'name',
        'storage_gb',
        'price_monthly',
        'price_yearly',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'storage_gb' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope: Lấy các gói đang hoạt động
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Lấy dung lượng hiển thị (GB hoặc TB)
     */
    public function getFormattedStorage(): string
    {
        if ($this->storage_gb >= 1024) {
            return round($this->storage_gb / 1024, 1) . ' TB';
        }
        return $this->storage_gb . ' GB';
    }

    /**
     * Lấy giá tháng đã định dạng
     */
    public function getFormattedPriceMonthly(): string
    {
        if ($this->price_monthly <= 0) {
            return 'Miễn phí';
        }
        return number_format($this->price_monthly, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy giá năm đã định dạng
     */
    public function getFormattedPriceYearly(): string
    {
        if ($this->price_yearly <= 0) {
            return 'Miễn phí';
        }
        return number_format($this->price_yearly, 0, ',', '.') . ' ₫';
    }

    /**
     * Kiểm tra có phải gói miễn phí
     */
    public function isFree(): bool
    {
        return $this->price_monthly <= 0 && $this->price_yearly <= 0;
    }

    /**
     * Lấy số subscriptions đang dùng gói này
     */
    public function getActiveSubscriptionsCount(): int
    {
        return Subscription::where('plan_id', $this->plan_id)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Lấy tổng doanh thu từ gói này
     */
    public function getTotalRevenue(): float
    {
        return Subscription::where('plan_id', $this->plan_id)
            ->where('payment_status', 'paid')
            ->sum('price');
    }
}
