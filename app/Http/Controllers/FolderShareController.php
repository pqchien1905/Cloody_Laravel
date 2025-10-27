<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderShareController extends Controller
{
    /**
     * Share folder with a user by email.
     */
    public function store(Request $request, $folderId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $folder = Folder::findOrFail($folderId);

        // Chỉ chủ sở hữu mới được chia sẻ
        if (($folder->user_id ?? null) !== (Auth::id() ?? 0)) {
            abort(403, 'You are not allowed to share this folder.');
        }

        $recipient = User::where('email', $request->email)->first();
        if (!$recipient) {
            return back()->withErrors(['email' => 'Recipient email not found.']);
        }

        FolderShare::create([
            'folder_id' => $folder->id,
            'shared_by' => Auth::id() ?? 1,
            'shared_with' => $recipient->id,
            'permission' => 'view',
            'is_public' => false,
            'expires_at' => null,
        ]);

        return back()->with('success', 'Folder shared successfully.');
    }
}
