<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileShareController extends Controller
{
    /**
     * Share file with user.
     */
    public function store(Request $request, $fileId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $file = File::findOrFail($fileId);

        $recipient = User::where('email', $request->email)->first();
        if (!$recipient) {
            return redirect()->back()->withErrors(['email' => 'Recipient email not found.']);
        }

        $share = FileShare::create([
            'file_id' => $file->id,
            'shared_by' => Auth::id() ?? 1,
            'shared_with' => $recipient->id,
            'permission' => 'view',
            'is_public' => false,
            'expires_at' => null,
        ]);

        $shareUrl = route('file.shared', $share->share_token);

        return redirect()->back()->with([
            'success' => 'File shared successfully!',
            'share_url' => $shareUrl,
        ]);
    }

    /**
     * View shared file.
     */
    public function show($token)
    {
        $share = FileShare::with('file')
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        return view('pages.file-shared', compact('share'));
    }

    /**
     * Download shared file.
     */
    public function download($token)
    {
        $share = FileShare::with('file')
            ->where('share_token', $token)
            ->active()
            ->firstOrFail();

        if ($share->isExpired()) {
            abort(403, 'This share link has expired.');
        }

        if (!in_array($share->permission, ['download', 'edit'])) {
            abort(403, 'You do not have permission to download this file.');
        }

        $file = $share->file;
        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $file->original_name);
    }

    /**
     * Revoke file share.
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type', 'file');
        if ($type === 'folder') {
            \App\Models\FolderShare::findOrFail($id)->delete();
        } else {
            FileShare::findOrFail($id)->delete();
        }

        return redirect()->back()->with('success', 'Share revoked successfully!');
    }

    /**
     * List all shares for a file.
     */
    public function listShares($fileId)
    {
        $file = File::with('shares.sharedWith')->findOrFail($fileId);
        
        return view('pages.file-shares', compact('file'));
    }
}
