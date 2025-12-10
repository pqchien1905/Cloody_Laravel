<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Đăng ký dịch vụ ứng dụng (chỗ trống)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sử dụng view phân trang Bootstrap toàn cục để khớp với giao diện
        if (method_exists(Paginator::class, 'useBootstrapFour')) {
            Paginator::useBootstrapFour();
        } elseif (method_exists(Paginator::class, 'useBootstrap')) {
            // Dự phòng cho các phiên bản Laravel cũ hơn
            Paginator::useBootstrap();
        }

        // Chia sẻ dữ liệu bộ nhớ với partial thanh bên
        View::composer('partials.sidebar', function ($view) {
            $storageUsedGB = 0;
            $storageLimit = 1; // Default 1GB
            $storagePercent = 0;
            
            if (Auth::check()) {
                $user = Auth::user();
                $userId = $user->id;
                
                // Lấy storage đã sử dụng
                $storageUsed = File::where('user_id', $userId)
                    ->where('is_trash', false)
                    ->sum('size');
                
                // Chuyển đổi sang GB
                $storageUsedGB = $storageUsed / (1024 * 1024 * 1024);
                
                // Lấy storage limit từ subscription của user
                $storageLimit = $user->getStorageLimitGB();
                
                // Tính phần trăm
                $storagePercent = $storageLimit > 0 ? min(($storageUsedGB / $storageLimit) * 100, 100) : 0;
            }
            
            $view->with([
                'storageUsedGB' => $storageUsedGB,
                'storageLimit' => $storageLimit,
                'storagePercent' => $storagePercent
            ]);
        });
    }
}
