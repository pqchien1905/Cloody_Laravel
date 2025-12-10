<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Middleware - Thiết lập ngôn ngữ cho ứng dụng
 * 
 * Đọc ngôn ngữ ưa thích từ session và áp dụng cho ứng dụng.
 * Nếu không có trong session thì dùng ngôn ngữ từ config.
 * Chỉ cho phép các ngôn ngữ trong whitelist được hỗ trợ.
 */
class SetLocale
{
	/**
	 * Xử lý request đến.
	 */
	public function handle(Request $request, Closure $next)
	{
		$supported = ['en', 'vi'];

		// Lấy ngôn ngữ từ session, nếu không có thì dùng từ config
		// Sử dụng has() để kiểm tra session key có tồn tại không, sau đó lấy giá trị
		$locale = null;
		if ($request->hasSession() && $request->session()->has('locale')) {
			$locale = $request->session()->get('locale');
		}

		if (!$locale) {
			// Tùy chọn: phát hiện từ trình duyệt và chuẩn hóa về ngôn ngữ được hỗ trợ
			$browser = substr((string) $request->getPreferredLanguage($supported), 0, 2);
			$locale = in_array($browser, $supported, true) ? $browser : config('app.locale');
			if ($request->hasSession()) {
				$request->session()->put('locale', $locale);
			}
		}

		// Xác thực ngôn ngữ
		if (!in_array($locale, $supported, true)) {
			$locale = config('app.locale');
			if ($request->hasSession()) {
				$request->session()->put('locale', $locale);
			}
		}

		// Thiết lập ngôn ngữ cho ứng dụng
		App::setLocale($locale);

		return $next($request);
	}
}

