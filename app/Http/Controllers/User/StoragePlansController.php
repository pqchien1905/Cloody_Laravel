<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\File;
use App\Models\Subscription;
use App\Models\StoragePlan;
use App\Models\User;
use App\Helpers\StorageManager;

class StoragePlansController extends Controller
{
    /**
     * Lấy định nghĩa các gói từ database hoặc mặc định
     */
    private function getPlanDefinitions()
    {
        // Kiểm tra xem bảng storage_plans có tồn tại không
        if (Schema::hasTable('storage_plans')) {
            $plans = StoragePlan::where('is_active', true)->orderBy('sort_order')->get();
            if ($plans->count() > 0) {
                $definitions = [];
                foreach ($plans as $plan) {
                    $definitions[$plan->plan_id] = [
                        'id' => $plan->plan_id,
                        'name' => $plan->name,
                        'storage_gb' => $plan->storage_gb,
                        'price_monthly' => $plan->price_monthly,
                        'price_yearly' => $plan->price_yearly,
                        'price_yearly_original' => $plan->price_monthly * 12,
                        'discount_percent' => $plan->price_monthly > 0 ? round(100 - ($plan->price_yearly / ($plan->price_monthly * 12) * 100)) : 0,
                        'features' => $plan->features ?? [],
                        'is_popular' => $plan->is_popular,
                        'order' => $plan->sort_order,
                    ];
                }
                return $definitions;
            }
        }

        // Fallback nếu chưa có bảng hoặc dữ liệu
        return [
            'basic' => [
                'id' => 'basic',
                'name' => 'Cơ bản',
                'storage_gb' => 1,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'order' => 0,
                'features' => [
                    '1 GB dung lượng lưu trữ',
                    'Hỗ trợ tất cả loại file',
                    'Chia sẻ file không giới hạn',
                ],
            ],
            '100gb' => [
                'id' => '100gb',
                'name' => '100 GB',
                'storage_gb' => 100,
                'price_monthly' => 45000,
                'price_yearly' => round(45000 * 12 * 0.84),
                'price_yearly_original' => 45000 * 12,
                'discount_percent' => 16,
                'order' => 1,
                'features' => [
                    '100 GB dung lượng lưu trữ',
                    'Hỗ trợ từ chuyên gia',
                    'Chia sẻ với tối đa 5 thành viên',
                    'Hỗ trợ tất cả loại file',
                ],
            ],
            '200gb' => [
                'id' => '200gb',
                'name' => '200 GB',
                'storage_gb' => 200,
                'price_monthly' => 69000,
                'price_yearly' => round(69000 * 12 * 0.84),
                'price_yearly_original' => 69000 * 12,
                'discount_percent' => 16,
                'order' => 2,
                'features' => [
                    '200 GB dung lượng lưu trữ',
                    'Hỗ trợ từ chuyên gia',
                    'Chia sẻ với tối đa 5 thành viên',
                    'Hỗ trợ tất cả loại file',
                    'Ưu tiên hỗ trợ khách hàng',
                ],
            ],
            '2tb' => [
                'id' => '2tb',
                'name' => '2 TB',
                'storage_gb' => 2048,
                'price_monthly' => 225000,
                'price_yearly' => round(225000 * 12 * 0.84),
                'price_yearly_original' => 225000 * 12,
                'discount_percent' => 16,
                'order' => 3,
                'features' => [
                    '2 TB dung lượng lưu trữ',
                    'Hỗ trợ từ chuyên gia',
                    'Chia sẻ với tối đa 5 thành viên',
                    'Hỗ trợ tất cả loại file',
                    'Hỗ trợ 24/7',
                    'Sao lưu tự động',
                    'VPN bảo vệ khi duyệt web',
                ],
            ],
        ];
    }

    /**
     * Display storage plans page
     */
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(401);
        }
        
        // Tính storage hiện tại của user
        $storageUsed = File::where('user_id', $user->id)
            ->where('is_trash', false)
            ->sum('size');
        
        $storageUsedGB = $storageUsed / (1024 * 1024 * 1024);
        
        // Lấy gói hiện tại của user
        $activeSubscription = $user->activeSubscription;
        $currentLimitGB = $user->getStorageLimitGB();
        $currentPlanId = $activeSubscription ? $activeSubscription->plan_id : 'basic';
        $currentBillingCycle = $activeSubscription ? $activeSubscription->billing_cycle : null;
        $expiresAt = $activeSubscription ? $activeSubscription->expires_at : null;
        
        // Lấy định nghĩa các gói từ database
        $planDefinitions = $this->getPlanDefinitions();
        
        // Tạo planOrder từ dữ liệu
        $planOrder = [];
        foreach ($planDefinitions as $id => $plan) {
            $planOrder[$id] = $plan['order'];
        }
        $currentPlanOrder = $planOrder[$currentPlanId] ?? 0;
        
        // Lọc chỉ hiển thị gói hiện tại và các gói cao hơn (giống Google Drive)
        $plans = [];
        foreach ($planDefinitions as $plan) {
            $thisPlanOrder = $planOrder[$plan['id']] ?? 0;
            
            // Chỉ hiển thị nếu gói >= gói hiện tại
            if ($thisPlanOrder >= $currentPlanOrder) {
                $plan['current_monthly'] = ($currentPlanId === $plan['id'] && $currentBillingCycle === 'monthly');
                $plan['current_yearly'] = ($currentPlanId === $plan['id'] && $currentBillingCycle === 'yearly');
                $plan['can_purchase'] = ($thisPlanOrder > $currentPlanOrder); // Chỉ cho phép mua gói cao hơn
                $plan['is_current'] = ($plan['id'] === $currentPlanId);
                
                // Thêm recommended cho gói đầu tiên sau gói hiện tại hoặc gói popular
                if ($thisPlanOrder === $currentPlanOrder + 1 || ($plan['is_popular'] ?? false)) {
                    $plan['recommended'] = true;
                }
                
                $plans[] = $plan;
            }
        }
        
        return view('pages.storage-plans', [
            'storageUsedGB' => $storageUsedGB,
            'currentLimitGB' => $currentLimitGB,
            'plans' => $plans,
            'currentPlanId' => $currentPlanId,
            'currentBillingCycle' => $currentBillingCycle,
            'activeSubscription' => $activeSubscription,
            'expiresAt' => $expiresAt,
        ]);
    }

    /**
     * Handle storage plan upgrade
     */
    public function upgrade(Request $request)
    {
        // Lấy danh sách plan_id hợp lệ từ database
        $planDefinitions = $this->getPlanDefinitions();
        $validPlanIds = array_keys($planDefinitions);
        
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|string|in:' . implode(',', $validPlanIds),
            'billing_cycle' => 'required|string|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('common.validation_error'),
                'errors' => $validator->errors(),
            ], 422);
        }

        /** @var User|null $user */
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
            ], 401);
        }
        $planId = $request->input('plan_id');
        $billingCycle = $request->input('billing_cycle');

        if (!isset($planDefinitions[$planId])) {
            return response()->json([
                'success' => false,
                'message' => __('common.invalid_plan'),
            ], 400);
        }

        $plan = $planDefinitions[$planId];
        $price = $billingCycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];

        // Tạo planOrder từ dữ liệu
        $planOrder = [];
        foreach ($planDefinitions as $id => $p) {
            $planOrder[$id] = $p['order'];
        }
        
        // Kiểm tra nếu đang chọn gói hiện tại (cùng plan_id và billing_cycle)
        $activeSubscription = $user->activeSubscription;
        if ($activeSubscription && 
            $activeSubscription->plan_id === $planId && 
            $activeSubscription->billing_cycle === $billingCycle) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đang sử dụng gói này với chu kỳ thanh toán này.',
            ], 400);
        }
        
        // Kiểm tra nếu đang cố mua gói thấp hơn gói hiện tại
        if ($activeSubscription && $planId !== 'basic') {
            $currentPlanOrder = $planOrder[$activeSubscription->plan_id] ?? 0;
            $newPlanOrder = $planOrder[$planId] ?? 0;
            
            if ($newPlanOrder < $currentPlanOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể mua gói thấp hơn gói hiện tại. Vui lòng chọn gói cao hơn hoặc chuyển về gói cơ bản.',
                ], 400);
            }
        }

        // Nếu là gói miễn phí, xử lý trực tiếp
        if ($price == 0) {
            try {
                DB::beginTransaction();

                // Vô hiệu hóa tất cả các subscription cũ (khi chuyển về gói basic)
                Subscription::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                // Tạo subscription miễn phí
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $planId,
                    'plan_name' => $plan['name'],
                    'storage_gb' => $plan['storage_gb'],
                    'billing_cycle' => $billingCycle,
                    'price' => 0,
                    'starts_at' => now(),
                    'expires_at' => null,
                    'is_active' => true,
                    'payment_status' => 'paid',
                    'notes' => 'Free plan activated',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => __('common.upgrade_success', ['plan' => $plan['name']]),
                    'subscription' => [
                        'plan_id' => $subscription->plan_id,
                        'plan_name' => $subscription->plan_name,
                        'storage_gb' => $subscription->storage_gb,
                        'expires_at' => null,
                    ],
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => __('common.upgrade_failed') . ': ' . $e->getMessage(),
                ], 500);
            }
        }

           // Nếu là gói trả phí, redirect đến trang thanh toán
           // Tạo form data để submit
           $paymentUrl = route('cloody.payment.create');
           
           return response()->json([
               'success' => true,
               'redirect' => true,
               'payment_url' => $paymentUrl,
               'payment_method' => 'form', // Sử dụng form submit
               'form_data' => [
                   'plan_id' => $planId,
                   'billing_cycle' => $billingCycle,
               ],
               'message' => 'Đang chuyển đến trang thanh toán...',
           ]);
    }
}

