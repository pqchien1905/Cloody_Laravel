<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware - Chỉ cho phép người dùng có quyền admin truy cập
 */
class AdminOnly
{
    /**
     * Xử lý request đến.
     * Kiểm tra xem người dùng có phải là admin không, nếu không thì trả về lỗi 403.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Kiểm tra người dùng đã đăng nhập và có quyền admin không
        if (!$user || !$user->is_admin) {
            abort(403, 'Forbidden');
        }
        return $next($request);
    }
}
