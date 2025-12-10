<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controller - Xử lý việc cập nhật thông tin profile người dùng
 */
class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa profile của người dùng.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật thông tin profile của người dùng.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Cập nhật các trường khác trước (loại trừ avatar khỏi mass assignment)
        $data = $request->validated();
        unset($data['avatar']); // Xóa avatar khỏi dữ liệu đã validate
        
        $user->fill($data);

        // Nếu email thay đổi, xóa xác thực email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            
            // Xóa avatar cũ nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Tạo tên file duy nhất với timestamp để tránh vấn đề cache
            $avatarName = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            
            // Lưu file vào storage/app/public/avatars
            try {
                $path = $avatar->storeAs('avatars', $avatarName, 'public');
                $user->avatar = $path;
            } catch (\Exception $e) {
                \Log::error('Avatar upload failed: ' . $e->getMessage());
                return Redirect::route('profile.edit')->with('error', __('common.failed_upload_avatar'));
            }
        }

        $user->save();
        
        // Làm mới instance người dùng đã xác thực để tránh vấn đề cache
        Auth::user()->refresh();

        return Redirect::route('cloody.user.profile')->with('status', 'profile-updated');
    }

    /**
     * Xóa tài khoản người dùng.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Đăng xuất người dùng
        Auth::logout();

        // Xóa tài khoản
        $user->delete();

        // Vô hiệu hóa và tạo lại session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
