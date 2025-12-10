<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Subscription;
use App\Models\User;
use App\Models\StoragePlan;

class AdminStoragePlansController extends Controller
{
    /**
     * Lấy định nghĩa các gói (từ database hoặc mặc định)
     */
    private function getPlanDefinitions()
    {
        // Kiểm tra xem bảng storage_plans có tồn tại không
        if (Schema::hasTable('storage_plans')) {
            $plans = StoragePlan::orderBy('sort_order')->get();
            if ($plans->count() > 0) {
                $definitions = [];
                foreach ($plans as $plan) {
                    $definitions[$plan->plan_id] = [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'storage_gb' => $plan->storage_gb,
                        'price_monthly' => $plan->price_monthly,
                        'price_yearly' => $plan->price_yearly,
                        'features' => $plan->features ?? [],
                        'is_active' => $plan->is_active,
                        'is_popular' => $plan->is_popular,
                        'sort_order' => $plan->sort_order,
                    ];
                }
                return $definitions;
            }
        }

        // Fallback nếu chưa có bảng hoặc dữ liệu
        return [
            'basic' => ['name' => 'Cơ bản', 'storage_gb' => 1, 'price_monthly' => 0, 'price_yearly' => 0, 'is_active' => true, 'is_popular' => false, 'sort_order' => 0],
            '100gb' => ['name' => '100 GB', 'storage_gb' => 100, 'price_monthly' => 45000, 'price_yearly' => round(45000 * 12 * 0.84), 'is_active' => true, 'is_popular' => false, 'sort_order' => 1],
            '200gb' => ['name' => '200 GB', 'storage_gb' => 200, 'price_monthly' => 69000, 'price_yearly' => round(69000 * 12 * 0.84), 'is_active' => true, 'is_popular' => true, 'sort_order' => 2],
            '2tb' => ['name' => '2 TB', 'storage_gb' => 2048, 'price_monthly' => 225000, 'price_yearly' => round(225000 * 12 * 0.84), 'is_active' => true, 'is_popular' => false, 'sort_order' => 3],
        ];
    }

    /**
     * Display admin storage plans management page
     */
    public function index()
    {
        $planDefinitions = $this->getPlanDefinitions();

        // Thống kê theo từng gói
        $planStats = [];
        foreach ($planDefinitions as $planId => $plan) {
            $activeCount = Subscription::where('plan_id', $planId)
                ->where('is_active', true)
                ->count();
            
            $totalRevenue = Subscription::where('plan_id', $planId)
                ->where('payment_status', 'paid')
                ->sum('price');
            
            $planStats[] = [
                'id' => $plan['id'] ?? null,
                'plan_id' => $planId,
                'name' => $plan['name'],
                'storage_gb' => $plan['storage_gb'],
                'price_monthly' => $plan['price_monthly'],
                'price_yearly' => $plan['price_yearly'],
                'features' => $plan['features'] ?? [],
                'is_active' => $plan['is_active'] ?? true,
                'is_popular' => $plan['is_popular'] ?? false,
                'sort_order' => $plan['sort_order'] ?? 0,
                'active_users' => $activeCount,
                'total_revenue' => $totalRevenue,
            ];
        }

        // Lấy danh sách subscriptions gần đây
        $recentSubscriptions = Subscription::with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($sub) use ($planDefinitions) {
                return [
                    'id' => $sub->id,
                    'user_name' => $sub->user->name ?? 'N/A',
                    'user_email' => $sub->user->email ?? 'N/A',
                    'plan_id' => $sub->plan_id,
                    'plan_name' => $sub->plan_name,
                    'billing_cycle' => $sub->billing_cycle,
                    'price' => $sub->price,
                    'storage_gb' => $sub->storage_gb,
                    'is_active' => $sub->is_active,
                    'payment_status' => $sub->payment_status,
                    'starts_at' => $sub->starts_at,
                    'expires_at' => $sub->expires_at,
                    'created_at' => $sub->created_at,
                ];
            });

        // Tổng quan
        $totalActiveSubscriptions = Subscription::where('is_active', true)->count();
        $totalRevenue = Subscription::where('payment_status', 'paid')->sum('price');
        $monthlyRevenue = Subscription::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('price');

        // Kiểm tra xem bảng storage_plans có tồn tại không
        $hasStoragePlansTable = Schema::hasTable('storage_plans');

        return view('pages.admin.storage-plans.index', [
            'planStats' => $planStats,
            'recentSubscriptions' => $recentSubscriptions,
            'totalActiveSubscriptions' => $totalActiveSubscriptions,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'hasStoragePlansTable' => $hasStoragePlansTable,
        ]);
    }

    /**
     * Store a new storage plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string|max:50|unique:storage_plans,plan_id',
            'name' => 'required|string|max:100',
            'storage_gb' => 'required|integer|min:1',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
        ]);

        // Parse features từ textarea (mỗi dòng là 1 feature)
        $features = [];
        if ($request->features) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        // Lấy sort_order cao nhất
        $maxOrder = StoragePlan::max('sort_order') ?? -1;

        StoragePlan::create([
            'plan_id' => $request->plan_id,
            'name' => $request->name,
            'storage_gb' => $request->storage_gb,
            'price_monthly' => $request->price_monthly,
            'price_yearly' => $request->price_yearly,
            'features' => $features,
            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.storage-plans.index')
            ->with('success', 'Đã thêm gói lưu trữ mới thành công!');
    }

    /**
     * Update a storage plan
     */
    public function update(Request $request, $id)
    {
        $plan = StoragePlan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'storage_gb' => 'required|integer|min:1',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Parse features từ textarea (mỗi dòng là 1 feature)
        $features = [];
        if ($request->features) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }

        $plan->update([
            'name' => $request->name,
            'storage_gb' => $request->storage_gb,
            'price_monthly' => $request->price_monthly,
            'price_yearly' => $request->price_yearly,
            'features' => $features,
            'is_active' => $request->has('is_active'),
            'is_popular' => $request->has('is_popular'),
            'sort_order' => $request->sort_order ?? $plan->sort_order,
        ]);

        return redirect()->route('admin.storage-plans.index')
            ->with('success', 'Đã cập nhật gói lưu trữ thành công!');
    }

    /**
     * Delete a storage plan
     */
    public function destroy($id)
    {
        $plan = StoragePlan::findOrFail($id);

        // Kiểm tra xem có subscription nào đang dùng gói này không
        $activeSubscriptions = Subscription::where('plan_id', $plan->plan_id)
            ->where('is_active', true)
            ->count();

        if ($activeSubscriptions > 0) {
            return redirect()->route('admin.storage-plans.index')
                ->with('error', "Không thể xóa gói này vì có {$activeSubscriptions} người dùng đang sử dụng!");
        }

        $plan->delete();

        return redirect()->route('admin.storage-plans.index')
            ->with('success', 'Đã xóa gói lưu trữ thành công!');
    }

    /**
     * Toggle plan active status
     */
    public function toggleActive($id)
    {
        $plan = StoragePlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->route('admin.storage-plans.index')
            ->with('success', "Đã {$status} gói {$plan->name}!");
    }

    /**
     * Deactivate a subscription
     */
    public function deactivateSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['is_active' => false]);

        return back()->with('success', 'Đã hủy kích hoạt gói lưu trữ!');
    }

    /**
     * Activate a subscription
     */
    public function activateSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Vô hiệu hóa các subscription khác của user này
        Subscription::where('user_id', $subscription->user_id)
            ->where('id', '!=', $id)
            ->update(['is_active' => false]);
        
        $subscription->update(['is_active' => true]);

        return back()->with('success', 'Đã kích hoạt gói lưu trữ!');
    }

    /**
     * Delete a subscription
     */
    public function deleteSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return back()->with('success', 'Đã xóa gói lưu trữ!');
    }
}
