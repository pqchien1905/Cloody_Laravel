<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\Folder;
use App\Helpers\FileValidator;
use App\Helpers\StorageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * View/preview a file inline.
     */
    public function view($id)
    {
        $userId = Auth::id() ?? 1;
        $file = File::where('id', $id)
            ->where('is_trash', false)
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('shares', function($sq) use ($userId) {
                      $sq->where('shared_with', $userId);
                  })
                  ->orWhereHas('groups', function($gq) use ($userId) {
                      // Check if user is member of any group that this file is shared with
                      $gq->whereHas('members', function($mq) use ($userId) {
                          $mq->where('user_id', $userId);
                      });
                  });
            })
            ->firstOrFail();

        // Verify file exists
        $filePath = storage_path('app/public/' . $file->path);
        if (!file_exists($filePath) && !Storage::disk('public')->exists($file->path)) {
            return redirect()->back()->with('error', __('common.file_not_found'));
        }

        // Generate file URL using the serve route for better access control and headers
        $fileUrl = route('cloody.files.serve', $file->id);

        return view('pages.viewer', [
            'file' => $file,
            'fileUrl' => $fileUrl,
        ]);
    }

    /**
     * Store uploaded file.
     */
    public function store(Request $request)
    {
        Log::info('FileUploadController@store called', [
            'files_count' => count($request->file('files') ?? []),
            'folder_id' => $request->folder_id,
            'conflict_action' => $request->conflict_action,
        ]);
        
        $maxFileSize = FileValidator::getMaxFileSize();
        $maxFiles = FileValidator::getMaxFilesPerRequest();
        
        $request->validate([
            'files' => [
                'required',
                'array',
                'max:' . $maxFiles,
            ],
            'files.*' => [
                'file',
                'max:' . $maxFileSize, // Configurable max file size
            ],
            'folder_id' => 'nullable|exists:folders,id',
            'conflict_action' => 'nullable|in:replace,keep_both',
        ]);

    $userId = Auth::id() ?? 1;
        $folderId = $request->folder_id;
        $conflictAction = $request->conflict_action ?? 'skip';
    $favoriteOnUpload = $request->boolean('favorite_on_upload');

        $uploadedCount = 0;
        $skipped = [];
        $replaced = [];

        // Check total size of all files
        $totalSize = 0;
        foreach ($request->file('files') as $uploadedFile) {
            if ($uploadedFile) {
                $totalSize += $uploadedFile->getSize();
            }
        }
        
        $maxTotalSize = FileValidator::getMaxTotalSize() * 1024; // Convert KB to bytes
        if ($totalSize > $maxTotalSize) {
            $totalSizeMB = round($totalSize / 1024 / 1024, 2);
            $maxTotalSizeMB = round($maxTotalSize / 1024 / 1024, 2);
            return redirect()->back()->with('error', __('common.total_size_exceeds_maximum', ['size' => $totalSizeMB, 'max' => $maxTotalSizeMB]));
        }

        foreach ($request->file('files') as $uploadedFile) {
            if (!$uploadedFile) {
                continue;
            }

            // Validate file type (extension and MIME type)
            $validation = FileValidator::validateFile($uploadedFile);
            if (!$validation['valid']) {
                $skipped[] = $uploadedFile->getClientOriginalName() . ' - ' . implode(', ', $validation['errors']);
                continue;
            }

            // Check storage limits
            $fileSize = $uploadedFile->getSize();
            $userStorageCheck = StorageManager::canUserUpload($userId, $fileSize);
            if (!$userStorageCheck['allowed']) {
                $skipped[] = $uploadedFile->getClientOriginalName() . ' - ' . $userStorageCheck['message'];
                continue;
            }

            $systemStorageCheck = StorageManager::canSystemAccept($fileSize);
            if (!$systemStorageCheck['allowed']) {
                $skipped[] = $uploadedFile->getClientOriginalName() . ' - ' . $systemStorageCheck['message'];
                continue;
            }

            $originalName = $uploadedFile->getClientOriginalName();
            Log::info('Processing file upload', ['filename' => $originalName]);
            
            $extension = $uploadedFile->getClientOriginalExtension();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);

            // Kiểm tra trùng original_name trong cùng thư mục (chỉ file đang hoạt động)
            $existingFile = File::where('user_id', $userId)
                ->where('folder_id', $folderId)
                ->where('original_name', $originalName)
                ->where('is_trash', false)
                ->first();

            if ($existingFile) {
                if ($conflictAction === 'replace') {
                    // Xóa file cũ trên storage và khỏi DB
                    if ($existingFile->path && Storage::disk('public')->exists($existingFile->path)) {
                        Storage::disk('public')->delete($existingFile->path);
                    }
                    $existingFile->delete();
                    $replaced[] = $originalName;
                } elseif ($conflictAction === 'keep_both') {
                    // Tìm tên duy nhất: Âm nhạc (1).docx, Âm nhạc (2).docx, v.v.
                    $counter = 1;
                    do {
                        $newName = "{$baseName} ({$counter}).{$extension}";
                        $exists = File::where('user_id', $userId)
                            ->where('folder_id', $folderId)
                            ->where('original_name', $newName)
                            ->where('is_trash', false)
                            ->exists();
                        $counter++;
                    } while ($exists);
                    
                    $originalName = $newName;
                } else {
                    // Bỏ qua file này
                    $skipped[] = $originalName;
                    continue;
                }
            }

            // Cơ chế chặn trùng phía server: nếu bản ghi giống vừa được tạo vài giây trước, bỏ qua
            $incomingSize = $uploadedFile->getSize();
            $recentDuplicate = File::where('user_id', $userId)
                ->where('folder_id', $folderId)
                ->where('original_name', $originalName)
                ->where('size', $incomingSize)
                ->where('is_trash', false)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->exists();

            if ($recentDuplicate) {
                Log::warning('Duplicate upload guarded - skipping', [
                    'filename' => $originalName,
                    'size' => $incomingSize,
                    'user_id' => $userId,
                    'folder_id' => $folderId,
                ]);
                $skipped[] = $originalName;
                continue;
            }

            // Tạo tên file lưu trữ duy nhất
            $filename = time() . '_' . Str::random(10) . '.' . $uploadedFile->getClientOriginalExtension();
            
            // Lưu file
            $path = $uploadedFile->storeAs('uploads', $filename, 'public');
            
            // Tạo bản ghi file
            File::create([
                'user_id' => $userId,
                'folder_id' => $folderId,
                'name' => pathinfo($originalName, PATHINFO_FILENAME),
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $uploadedFile->getMimeType(),
                'extension' => $uploadedFile->getClientOriginalExtension(),
                'size' => $uploadedFile->getSize(),
                'is_favorite' => $favoriteOnUpload,
            ]);

            $uploadedCount++;
        }

    // Tạo thông báo thành công
        $messages = [];
        if ($uploadedCount > 0) {
            $messages[] = __('common.files_uploaded_successfully', ['count' => $uploadedCount]);
        }
        if (count($replaced) > 0) {
            $messages[] = __('common.files_replaced', ['files' => implode(', ', $replaced)]);
        }
        if (count($skipped) > 0) {
            $messages[] = __('common.files_skipped_duplicates', ['files' => implode(', ', $skipped)]);
        }
        
        if ($uploadedCount > 0) {
            return redirect()->back()->with('success', implode(' ', $messages));
        } else {
            return redirect()->back()->with('error', __('common.no_files_uploaded_duplicates', ['files' => implode(', ', $skipped)]));
        }
    }

    /**
     * Update file name.
     */
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($file) {
                    // Kiểm tra xem tên file mới (kèm đuôi) đã tồn tại trong cùng thư mục chưa
                    $newOriginalName = $value . '.' . $file->extension;
                    
                    $exists = File::where('user_id', Auth::id())
                        ->where('folder_id', $file->folder_id)
                        ->where('original_name', $newOriginalName)
                        ->where('is_trash', false)
                        ->where('id', '!=', $file->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('A file with this name already exists in this location.');
                    }
                },
            ],
        ]);

    // Cập nhật bản ghi file
        $file->update([
            'name' => $request->name,
            'original_name' => $request->name . '.' . $file->extension,
        ]);

        return redirect()->back()->with('success', 'File renamed successfully!');
    }

    /**
     * Check for duplicate filenames before upload.
     */
    public function checkDuplicates(Request $request)
    {
        $request->validate([
            'filenames' => 'required|array',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $userId = Auth::id() ?? 1;
        $folderId = $request->folder_id;
        $filenames = $request->filenames;

        $duplicates = File::where('user_id', $userId)
            ->where('folder_id', $folderId)
            ->where('is_trash', false)
            ->whereIn('original_name', $filenames)
            ->pluck('original_name')
            ->toArray();

        return response()->json(['duplicates' => $duplicates]);
    }

    /**
     * Serve file for preview (inline viewing).
     */
    public function serve($id)
    {
        $userId = Auth::id() ?? 1;
        $file = File::where('id', $id)
            ->where('is_trash', false)
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('shares', function($sq) use ($userId) {
                      $sq->where('shared_with', $userId);
                  })
                  ->orWhereHas('groups', function($gq) use ($userId) {
                      $gq->whereHas('members', function($mq) use ($userId) {
                          $mq->where('user_id', $userId);
                      });
                  });
            })
            ->firstOrFail();

        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            abort(404, __('common.file_not_found'));
        }

        // Serve file with proper headers for inline viewing
        return response()->file($filePath, [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Download file.
     */
    public function download($id)
    {
        $userId = Auth::id() ?? 1;
        $file = File::where('id', $id)
            ->where('is_trash', false)
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('shares', function($sq) use ($userId) {
                      $sq->where('shared_with', $userId);
                  })
                  ->orWhereHas('groups', function($gq) use ($userId) {
                      // Check if user is member of any group that this file is shared with
                      $gq->whereHas('members', function($mq) use ($userId) {
                          $mq->where('user_id', $userId);
                      });
                  });
            })
            ->firstOrFail();

        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', __('common.file_not_found'));
        }

        return response()->download($filePath, $file->original_name);
    }

    /**
     * Delete file.
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);
        
    // Di chuyển vào thùng rác thay vì xóa vĩnh viễn
        $file->update([
            'is_trash' => true,
            'trashed_at' => now(),
        ]);

        return redirect()->back()->with('success', __('common.files_moved_to_trash'));
    }

    /**
     * Bulk delete files.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id',
        ]);

        $count = 0;
        foreach ($request->file_ids as $fileId) {
            $file = File::find($fileId);
            if ($file && $file->user_id == (Auth::id() ?? 1)) {
                $file->update([
                    'is_trash' => true,
                    'trashed_at' => now(),
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', __('common.moved_files_to_trash', ['count' => $count]));
    }

    /**
     * Permanently delete file.
     */
    public function forceDelete($id)
    {
        $file = File::findOrFail($id);
        
    // Xóa file vật lý
        Storage::disk('public')->delete($file->path);
        
    // Xóa bản ghi trong cơ sở dữ liệu
        $file->delete();

        return redirect()->back()->with('success', __('common.file_permanently_deleted'));
    }

    /**
     * Restore file from trash.
     */
    public function restore($id)
    {
        $file = File::findOrFail($id);
        
        $file->update([
            'is_trash' => false,
            'trashed_at' => null,
        ]);

        return redirect()->back()->with('success', __('common.file_restored'));
    }
    
    /**
     * Bulk restore files from trash.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id',
        ]);

        $count = 0;
        foreach ($request->file_ids as $fileId) {
            $file = File::find($fileId);
            if ($file && $file->is_trash && $file->user_id == (Auth::id() ?? 1)) {
                $file->update([
                    'is_trash' => false,
                    'trashed_at' => null,
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', __('common.restored_files', ['count' => $count]));
    }

    /**
     * Toggle favorite.
     */
    public function toggleFavorite($id)
    {
        $file = File::findOrFail($id);
        
        $file->update([
            'is_favorite' => !$file->is_favorite,
        ]);

        return redirect()->back()->with('success', __('common.favorite_updated'));
    }

    /**
     * Bulk permanently delete files from trash.
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id',
        ]);

        $count = 0;
        foreach ($request->file_ids as $fileId) {
            $file = File::find($fileId);
            if ($file && $file->is_trash && $file->user_id == (Auth::id() ?? 1)) {
                // Xóa file vật lý nếu tồn tại
                if ($file->path && Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
                $file->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', __('common.permanently_deleted_files', ['count' => $count]));
    }
}
