<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\VNPayService;

class PaymentController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Tạo thanh toán và redirect đến VNPay
     */
    public function create(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            Log::warning('Payment create: User not authenticated', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        $request->validate([
            'plan_id' => 'required|string|in:basic,100gb,200gb,2tb',
            'billing_cycle' => 'required|string|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $planId = $request->input('plan_id');
        $billingCycle = $request->input('billing_cycle');

        // Định nghĩa các gói
        $planDefinitions = [
            'basic' => ['name' => 'Cơ bản', 'storage_gb' => 1, 'price_monthly' => 0, 'price_yearly' => 0],
            '100gb' => ['name' => '100 GB', 'storage_gb' => 100, 'price_monthly' => 45000, 'price_yearly' => round(45000 * 12 * 0.84)],
            '200gb' => ['name' => '200 GB', 'storage_gb' => 200, 'price_monthly' => 69000, 'price_yearly' => round(69000 * 12 * 0.84)],
            '2tb' => ['name' => '2 TB', 'storage_gb' => 2048, 'price_monthly' => 225000, 'price_yearly' => round(225000 * 12 * 0.84)],
        ];

        if (!isset($planDefinitions[$planId])) {
            return redirect()->route('cloody.storage.plans')
                ->with('error', __('common.invalid_plan'));
        }

        $plan = $planDefinitions[$planId];
        $amount = $billingCycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];

        // Kiểm tra nếu là gói miễn phí
        if ($amount == 0) {
            // Xử lý gói miễn phí trực tiếp
            return $this->handleFreePlan($user, $planId, $plan, $billingCycle);
        }

        try {
            DB::beginTransaction();

            // Tạo payment record
            $txnRef = 'CLOODY_' . time() . '_' . $user->id . '_' . uniqid();
            $orderInfo = 'Thanh toan goi ' . $plan['name'] . ' - ' . ($billingCycle === 'yearly' ? 'Nam' : 'Thang');

            $payment = Payment::create([
                'user_id' => $user->id,
                'plan_id' => $planId,
                'plan_name' => $plan['name'],
                'storage_gb' => $plan['storage_gb'],
                'billing_cycle' => $billingCycle,
                'amount' => $amount,
                'currency' => 'VND',
                'payment_method' => 'vnpay',
                'payment_status' => 'pending',
                'vnpay_txn_ref' => $txnRef,
                'notes' => 'Payment created',
            ]);

            DB::commit();

            // Tạo URL thanh toán VNPay
            $paymentUrl = $this->vnpayService->createPaymentUrl([
                'txn_ref' => $txnRef,
                'order_info' => $orderInfo,
                'order_type' => 'other',
                'amount' => $amount,
                'locale' => 'vn',
                'ip_address' => $request->ip(),
            ]);

            // Log để debug
            Log::info('VNPay Payment URL created', [
                'payment_id' => $payment->id,
                'txn_ref' => $txnRef,
                'amount' => $amount,
                'tmn_code' => config('cloody.payment.vnpay.tmn_code'),
                'hash_secret_length' => strlen(config('cloody.payment.vnpay.hash_secret', '')),
                'return_url' => config('cloody.payment.vnpay.return_url'),
                'ipn_url' => config('cloody.payment.vnpay.ipn_url'),
                'payment_url_length' => strlen($paymentUrl),
            ]);

            return redirect($paymentUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed: ' . $e->getMessage());
            
            return redirect()->route('cloody.storage.plans')
                ->with('error', __('common.payment_creation_failed'));
        }
    }

    /**
     * Xử lý callback từ VNPay
     * Route này KHÔNG yêu cầu đăng nhập vì VNPay gọi từ bên ngoài
     */
    public function callback(Request $request)
    {
        $data = $request->all();

        Log::info('VNPay Callback received', [
            'data' => $data,
            'ip' => $request->ip(),
        ]);

        // Validate signature
        if (!$this->vnpayService->validateSignature($data)) {
            Log::warning('Invalid VNPay signature', ['data' => $data]);
            return redirect()->route('login')
                ->with('error', 'Chữ ký không hợp lệ. Vui lòng đăng nhập để kiểm tra.');
        }

        // Process response
        $result = $this->vnpayService->processResponse($data);
        $txnRef = $result['txn_ref'];

        // Tìm payment
        $payment = Payment::where('vnpay_txn_ref', $txnRef)->first();

        if (!$payment) {
            Log::warning('Payment not found', ['txn_ref' => $txnRef]);
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy giao dịch. Vui lòng đăng nhập để kiểm tra.');
        }

        // Nếu đã xử lý rồi thì không xử lý lại
        if ($payment->payment_status === 'completed') {
            // Luôn redirect về login với thông báo (vì session có thể đã mất khi redirect từ VNPay)
            // Thêm payment_id vào URL để có thể lấy lại sau khi login
            return redirect()->route('login', ['payment_success' => $payment->id])
                ->with('success', 'Thanh toán thành công! Vui lòng đăng nhập để xem gói đã nâng cấp.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật payment
            $payment->payment_status = $result['status'] === 'completed' ? 'completed' : 'failed';
            $payment->transaction_id = $data['vnp_TransactionNo'] ?? null;
            $payment->vnpay_response = json_encode($data);
            
            if ($result['status'] === 'completed') {
                $payment->paid_at = now();
                
                // Tạo subscription
                $subscription = $this->createSubscription($payment);
                $payment->subscription_id = $subscription->id;
            }
            
            $payment->save();

            DB::commit();

            if ($result['status'] === 'completed') {
                // Luôn redirect về login với thông báo (vì session có thể đã mất khi redirect từ VNPay)
                // Thêm payment_id vào URL để có thể lấy lại sau khi login
                return redirect()->route('login', ['payment_success' => $payment->id])
                    ->with('success', 'Thanh toán thành công! Vui lòng đăng nhập để xem gói đã nâng cấp.');
            } else {
                // Thanh toán thất bại
                return redirect()->route('login', ['payment_failed' => $payment->id])
                    ->with('error', 'Thanh toán thất bại: ' . $result['message'] . '. Vui lòng đăng nhập để kiểm tra.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback processing failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'payment_id' => isset($payment) ? $payment->id : null,
            ]);
            
            // Luôn redirect về login với thông báo
            return redirect()->route('login')
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng đăng nhập để kiểm tra.');
        }
    }

    /**
     * Xử lý return URL từ VNPay
     * Route này KHÔNG yêu cầu đăng nhập vì VNPay redirect user về đây
     */
    public function return(Request $request)
    {
        $data = $request->all();

        Log::info('VNPay Return received', [
            'data' => $data,
            'ip' => $request->ip(),
        ]);

        // Validate signature
        if (!$this->vnpayService->validateSignature($data)) {
            Log::warning('Invalid VNPay return signature', ['data' => $data]);
            return redirect()->route('cloody.storage.plans')
                ->with('payment_failed', true)
                ->with('payment_message', 'Chữ ký không hợp lệ. Vui lòng thử lại.')
                ->with('payment_data', [
                    'error_message' => 'Invalid signature',
                ]);
        }

        // Process response
        $result = $this->vnpayService->processResponse($data);
        $txnRef = $result['txn_ref'];

        // Tìm payment
        $payment = Payment::where('vnpay_txn_ref', $txnRef)->first();

        if (!$payment) {
            Log::warning('Payment not found in return', ['txn_ref' => $txnRef]);
            return redirect()->route('cloody.storage.plans')
                ->with('payment_failed', true)
                ->with('payment_message', 'Không tìm thấy giao dịch. Vui lòng thử lại.')
                ->with('payment_data', [
                    'error_message' => 'Payment not found',
                ]);
        }

        // Nếu đã xử lý rồi thì chỉ hiển thị kết quả
        if ($payment->payment_status === 'completed') {
            // Redirect về trang plans với thông báo thành công và thông tin chi tiết
            return redirect()->route('cloody.storage.plans')
                ->with('payment_success', true)
                ->with('payment_message', 'Thanh toán thành công! Gói của bạn đã được nâng cấp.')
                ->with('payment_data', [
                    'plan_name' => $payment->plan_name,
                    'amount' => $payment->getFormattedAmount(),
                    'billing_cycle' => $payment->getBillingCycleLabel(),
                    'transaction_id' => $payment->transaction_id,
                ]);
        }

        try {
            DB::beginTransaction();

            // Cập nhật payment
            $payment->payment_status = $result['status'] === 'completed' ? 'completed' : 'failed';
            $payment->transaction_id = $data['vnp_TransactionNo'] ?? null;
            $payment->vnpay_response = json_encode($data);
            
            if ($result['status'] === 'completed') {
                $payment->paid_at = now();
                
                // Tạo subscription nếu chưa có
                if (!$payment->subscription_id) {
                    $subscription = $this->createSubscription($payment);
                    $payment->subscription_id = $subscription->id;
                }
            }
            
            $payment->save();

            DB::commit();

            if ($result['status'] === 'completed') {
                // Redirect về trang plans với thông báo thành công và thông tin chi tiết
                return redirect()->route('cloody.storage.plans')
                    ->with('payment_success', true)
                    ->with('payment_message', 'Thanh toán thành công! Gói của bạn đã được nâng cấp.')
                    ->with('payment_data', [
                        'plan_name' => $payment->plan_name,
                        'amount' => $payment->getFormattedAmount(),
                        'billing_cycle' => $payment->getBillingCycleLabel(),
                        'transaction_id' => $payment->transaction_id,
                    ]);
            } else {
                // Thanh toán thất bại
                return redirect()->route('cloody.storage.plans')
                    ->with('payment_failed', true)
                    ->with('payment_message', 'Thanh toán thất bại: ' . $result['message'] . '. Vui lòng thử lại.')
                    ->with('payment_data', [
                        'plan_name' => $payment->plan_name,
                        'amount' => $payment->getFormattedAmount(),
                        'error_message' => $result['message'] ?? 'Không xác định',
                    ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment return processing failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'payment_id' => isset($payment) ? $payment->id : null,
            ]);
            
            // Redirect về trang plans với thông báo lỗi
            return redirect()->route('cloody.storage.plans')
                ->with('payment_failed', true)
                ->with('payment_message', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng thử lại.')
                ->with('payment_data', [
                    'error_message' => $e->getMessage(),
                ]);
        }
    }

    /**
     * Xử lý IPN từ VNPay (Instant Payment Notification)
     */
    public function ipn(Request $request)
    {
        $data = $request->all();

        // Validate signature
        if (!$this->vnpayService->validateSignature($data)) {
            Log::warning('Invalid VNPay IPN signature', ['data' => $data]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 200);
        }

        $result = $this->vnpayService->processResponse($data);
        $txnRef = $result['txn_ref'];

        $payment = Payment::where('vnpay_txn_ref', $txnRef)->first();

        if (!$payment) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found'], 200);
        }

        if ($payment->payment_status === 'completed') {
            return response()->json(['RspCode' => '00', 'Message' => 'Success'], 200);
        }

        try {
            DB::beginTransaction();

            $payment->payment_status = $result['status'] === 'completed' ? 'completed' : 'failed';
            $payment->transaction_id = $data['vnp_TransactionNo'] ?? null;
            $payment->vnpay_response = json_encode($data);
            
            if ($result['status'] === 'completed') {
                $payment->paid_at = now();
                
                // Tạo subscription nếu chưa có
                if (!$payment->subscription_id) {
                    $subscription = $this->createSubscription($payment);
                    $payment->subscription_id = $subscription->id;
                }
            }
            
            $payment->save();

            DB::commit();

            return response()->json(['RspCode' => '00', 'Message' => 'Success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment IPN processing failed: ' . $e->getMessage());
            return response()->json(['RspCode' => '99', 'Message' => 'Unknown error'], 200);
        }
    }

    /**
     * Tạo subscription từ payment
     */
    protected function createSubscription(Payment $payment)
    {
        $user = $payment->user;

        // Định nghĩa thứ tự gói (số càng cao = gói càng cao)
        $planOrder = [
            'basic' => 0,
            '100gb' => 1,
            '200gb' => 2,
            '2tb' => 3,
        ];
        
        $newPlanOrder = $planOrder[$payment->plan_id] ?? 0;
        
        // Vô hiệu hóa các subscription cũ:
        // 1. Nếu mua gói trả phí (không phải basic), luôn vô hiệu hóa tất cả gói basic
        // 2. Vô hiệu hóa các gói có thứ tự thấp hơn hoặc bằng gói mới (để đảm bảo chỉ có 1 gói active)
        // Logic: Khi mua gói mới, vô hiệu hóa tất cả gói cũ để tránh xung đột
        Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Tính toán ngày hết hạn
        $startsAt = now();
        $expiresAt = null;
        
        if ($payment->plan_id !== 'basic' && $payment->amount > 0) {
            if ($payment->billing_cycle === 'yearly') {
                $expiresAt = now()->addYear();
            } else {
                $expiresAt = now()->addMonth();
            }
        }

        // Tạo subscription mới
        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $payment->plan_id,
            'plan_name' => $payment->plan_name,
            'storage_gb' => $payment->storage_gb,
            'billing_cycle' => $payment->billing_cycle,
            'price' => $payment->amount,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'is_active' => true,
            'payment_status' => 'paid',
            'notes' => 'Created from payment: ' . $payment->vnpay_txn_ref,
        ]);
    }

    /**
     * Xử lý gói miễn phí
     */
    protected function handleFreePlan($user, $planId, $plan, $billingCycle)
    {
        try {
            DB::beginTransaction();

            // Vô hiệu hóa các subscription cũ
            Subscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Tạo subscription miễn phí
            Subscription::create([
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

            return redirect()->route('cloody.storage.plans')
                ->with('payment_success', true)
                ->with('payment_message', __('common.upgrade_success', ['plan' => $plan['name']]))
                ->with('payment_data', [
                    'plan_name' => $plan['name'],
                    'amount' => 'Miễn phí',
                    'billing_cycle' => $billingCycle === 'yearly' ? 'Hàng năm' : 'Hàng tháng',
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Free plan activation failed: ' . $e->getMessage());
            
            return redirect()->route('cloody.storage.plans')
                ->with('error', 'Có lỗi xảy ra khi kích hoạt gói');
        }
    }
}
