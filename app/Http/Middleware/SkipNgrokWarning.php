<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SkipNgrokWarning
{
    /**
     * Handle an incoming request.
     *
     * Thêm header để bỏ qua trang cảnh báo ngrok
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Thêm header để bỏ qua trang cảnh báo ngrok
        $response->headers->set('ngrok-skip-browser-warning', 'true');
        
        return $response;
    }
}

