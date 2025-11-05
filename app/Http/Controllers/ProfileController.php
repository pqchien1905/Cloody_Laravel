<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update other fields first (exclude avatar from mass assignment)
        $data = $request->validated();
        unset($data['avatar']); // Remove avatar from validated data
        
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatar = $request->file('avatar');
            
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            
            // Generate unique filename
            $avatarName = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            
            // Move file to public/uploads/avatars
            try {
                $avatar->move(public_path('uploads/avatars'), $avatarName);
                $user->avatar = 'uploads/avatars/' . $avatarName;
            } catch (\Exception $e) {
                return Redirect::route('profile.edit')->with('error', 'Failed to upload avatar. Please try again.');
            }
        }

        $user->save();

        return Redirect::route('cloudbox.user.profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
