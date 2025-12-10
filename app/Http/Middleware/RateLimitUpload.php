<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware - Giới hạn tần suất upload để tránh spam và bảo vệ hệ thống
 */
class RateLimitUpload
{
    /**
     * Xử lý request đến.
     * Kiểm tra và giới hạn số lần upload trong một phút và một giờ.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Sử dụng ID người dùng nếu đã đăng nhập, nếu không thì dùng IP
        $userId = $user ? $user->id : $request->ip();

        // Lấy giới hạn tần suất từ config
        $perMinute = config('cloody.rate_limit.upload_per_minute', 10);
        $perHour = config('cloody.rate_limit.upload_per_hour', 100);

        // Kiểm tra giới hạn mỗi phút
        $keyMinute = 'upload:minute:' . $userId;
        if (RateLimiter::tooManyAttempts($keyMinute, $perMinute)) {
            $seconds = RateLimiter::availableIn($keyMinute);
            return response()->json([
                'error' => 'Too many upload requests. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ], 429)->withHeaders([
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => $perMinute,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        // Kiểm tra giới hạn mỗi giờ
        $keyHour = 'upload:hour:' . $userId;
        if (RateLimiter::tooManyAttempts($keyHour, $perHour)) {
            $seconds = RateLimiter::availableIn($keyHour);
            return response()->json([
                'error' => 'Hourly upload limit exceeded. Please try again in ' . ceil($seconds / 3600) . ' hour(s).',
            ], 429)->withHeaders([
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => $perHour,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        // Tăng bộ đếm
        RateLimiter::hit($keyMinute, 60); // Reset sau 60 giây
        RateLimiter::hit($keyHour, 3600); // Reset sau 3600 giây (1 giờ)

        $response = $next($request);

        // Thêm các header về rate limit
        $response->headers->set('X-RateLimit-Limit-Minute', $perMinute);
        $response->headers->set('X-RateLimit-Remaining-Minute', max(0, $perMinute - RateLimiter::attempts($keyMinute)));
        $response->headers->set('X-RateLimit-Limit-Hour', $perHour);
        $response->headers->set('X-RateLimit-Remaining-Hour', max(0, $perHour - RateLimiter::attempts($keyHour)));

        return $response;
    }
}

