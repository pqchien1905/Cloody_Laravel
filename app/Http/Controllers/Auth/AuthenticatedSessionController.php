<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Payment;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Lưu email vào cookie nếu 'remember me' được chọn
        if ($request->filled('remember')) {
            cookie()->queue('cloody_email', $request->email, 43200); // 30 ngày
        } else {
            cookie()->queue(cookie()->forget('cloody_email'));
        }

        // Kiểm tra payment_success hoặc payment_callback_id trong session/URL
        $paymentId = $request->input('payment_success') 
            ?? $request->input('payment_failed')
            ?? $request->session()->pull('payment_callback_id');
            
        if ($paymentId) {
            // Kiểm tra payment có thuộc về user này không
            $payment = Payment::find($paymentId);
            if ($payment && $payment->user_id === Auth::id()) {
                if ($payment->payment_status === 'completed') {
                    return redirect()->route('cloody.storage.plans')
                        ->with('success', 'Thanh toán thành công! Gói của bạn đã được nâng cấp.');
                } else {
                    return redirect()->route('cloody.storage.plans')
                        ->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
                }
            }
        }

        return redirect()->intended(route('cloody.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

    // Giữ cookie email nếu đã chọn 'remember'
    // Chỉ xóa session, không xóa cookie email

        return redirect()->route('login');
    }
}
