<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
            cookie()->queue('cloudbox_email', $request->email, 43200); // 30 ngày
        } else {
            cookie()->queue(cookie()->forget('cloudbox_email'));
        }

        return redirect()->intended(route('cloudbox.dashboard', absolute: false));
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
