<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Controller - Xử lý việc chuyển đổi ngôn ngữ (locale) của ứng dụng
 */
class LocaleController extends Controller
{
    /**
     * Chuyển đổi ngôn ngữ của ứng dụng.
     */
    public function switch(Request $request): RedirectResponse
    {
        $supported = ['en','vi'];
        $locale = $request->input('lang');
        
        // Kiểm tra ngôn ngữ có được hỗ trợ không, nếu không thì dùng ngôn ngữ mặc định
        if (!in_array($locale, $supported, true)) {
            $locale = config('app.locale');
        }
        
        // Lưu ngôn ngữ vào session
        $request->session()->put('locale', $locale);
        
        // Đặt ngôn ngữ ngay lập tức cho request hiện tại
        App::setLocale($locale);
        
        // Buộc lưu session để đảm bảo nó được lưu trữ
        $request->session()->save();
        
        // Lấy URL trước đó hoặc mặc định về dashboard
        $previousUrl = $request->header('referer') ?: route('cloody.dashboard');
        
        // Chuyển hướng về URL trước đó để đảm bảo middleware chạy lại
        return redirect($previousUrl)->with('locale_changed', true);
    }
}
