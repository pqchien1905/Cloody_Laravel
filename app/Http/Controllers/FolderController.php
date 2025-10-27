<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    /**
     * Display folders listing.
     */
    public function index()
    {
        $folders = Folder::query()
            ->withCount(['files' => function ($q) {
                $q->where('is_trash', false);
            }])
            ->active()
            ->root()
            ->latest()
            ->get();

        return view('pages.folders', compact('folders'));
    }

    /**
     * Store new folder.
     */
    public function store(Request $request)
    {
        $userId = Auth::id() ?? 1;
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $userId) {
                    $exists = Folder::where('user_id', $userId)
                        ->where('parent_id', $request->parent_id)
                        ->where('name', $value)
                        ->where('is_trash', false)
                        ->exists();
                    
                    if ($exists) {
                        $fail('A folder with this name already exists in this location.');
                    }
                },
            ],
            'parent_id' => 'nullable|exists:folders,id',
            'color' => 'nullable|string|max:7',
            'is_public' => 'required|boolean',
        ]);

        Folder::create([
            'user_id' => $userId,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'color' => $request->color ?? '#3498db',
            'description' => $request->description,
            'is_public' => $request->is_public,
        ]);

        return redirect()->back()->with('success', 'Folder created successfully!');
    }

    /**
     * Update folder.
     */
    public function update(Request $request, $id)
    {
        $folder = Folder::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $folder) {
                    $exists = Folder::where('user_id', $folder->user_id)
                        ->where('parent_id', $folder->parent_id)
                        ->where('name', $value)
                        ->where('is_trash', false)
                        ->where('id', '!=', $folder->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('A folder with this name already exists in this location.');
                    }
                },
            ],
            'color' => 'nullable|string|max:7',
            'is_public' => 'required|boolean',
        ]);

        $folder->update([
            'name' => $request->name,
            'color' => $request->color ?? $folder->color,
            'description' => $request->description,
            'is_public' => $request->is_public,
        ]);

        return redirect()->back()->with('success', 'Folder updated successfully!');
    }

    /**
     * Delete folder.
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        
    // Di chuyển thư mục và toàn bộ nội dung của nó vào thùng rác (đệ quy)
        $this->moveToTrashRecursive($folder);

        return redirect()->back()->with('success', 'Folders moved to trash!');
    }

    /**
     * Bulk delete folders.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'folder_ids' => 'required|array',
            'folder_ids.*' => 'exists:folders,id',
        ]);

        $count = 0;
        foreach ($request->folder_ids as $folderId) {
            $folder = Folder::find($folderId);
            if ($folder && $folder->user_id == (Auth::id() ?? 1)) {
                $this->moveToTrashRecursive($folder);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Moved {$count} folder(s) to trash!");
    }

    /**
     * Recursively move folder and all its contents to trash.
     */
    private function moveToTrashRecursive($folder)
    {
    // Di chuyển tất cả file trong thư mục này vào thùng rác
        $folder->files()->update([
            'is_trash' => true,
            'trashed_at' => now(),
        ]);

        // Đệ quy di chuyển tất cả thư mục con vào thùng rác
        foreach ($folder->children as $childFolder) {
            $this->moveToTrashRecursive($childFolder);
        }

        // Cuối cùng, di chuyển thư mục này vào thùng rác
        $folder->update([
            'is_trash' => true,
            'trashed_at' => now(),
        ]);
    }

    /**
     * Restore folder from trash.
     */
    public function restore($id)
    {
        $folder = Folder::findOrFail($id);
        
    // Khôi phục thư mục và toàn bộ nội dung của nó (đệ quy)
        $this->restoreRecursive($folder);

        return redirect()->back()->with('success', 'Folder restored successfully!');
    }

    /**
     * Recursively restore folder and all its contents.
     */
    private function restoreRecursive($folder)
    {
        // Khôi phục tất cả file trong thư mục này
        $folder->files()->update([
            'is_trash' => false,
            'trashed_at' => null,
        ]);

        // Đệ quy khôi phục tất cả thư mục con
        foreach ($folder->children as $childFolder) {
            $this->restoreRecursive($childFolder);
        }

        // Cuối cùng, khôi phục thư mục này
        $folder->update([
            'is_trash' => false,
            'trashed_at' => null,
        ]);
    }

    /**
     * Permanently delete folder.
     */
    public function forceDelete($id)
    {
        $folder = Folder::findOrFail($id);
        
    // Xóa vĩnh viễn thư mục và toàn bộ nội dung (đệ quy)
        $this->forceDeleteRecursive($folder);

        return redirect()->back()->with('success', 'Folder permanently deleted!');
    }

    /**
     * Bulk restore folders from trash.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'folder_ids' => 'required|array',
            'folder_ids.*' => 'exists:folders,id',
        ]);

        $count = 0;
        foreach ($request->folder_ids as $folderId) {
            $folder = Folder::find($folderId);
            if ($folder && $folder->is_trash && $folder->user_id == (Auth::id() ?? 1)) {
                $this->restoreRecursive($folder);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Restored {$count} folder(s)!");
    }

    /**
     * Bulk permanently delete folders.
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'folder_ids' => 'required|array',
            'folder_ids.*' => 'exists:folders,id',
        ]);

        $count = 0;
        foreach ($request->folder_ids as $folderId) {
            $folder = Folder::find($folderId);
            if ($folder && $folder->is_trash && $folder->user_id == (Auth::id() ?? 1)) {
                $this->forceDeleteRecursive($folder);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Permanently deleted {$count} folder(s)!");
    }

    /**
     * Recursively permanently delete folder and all its contents.
     */
    private function forceDeleteRecursive($folder)
    {
        // Xóa tất cả file trong thư mục này
        foreach ($folder->files as $file) {
            // Xóa file vật lý khỏi storage
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
        }

        // Đệ quy xóa tất cả thư mục con
        foreach ($folder->children as $childFolder) {
            $this->forceDeleteRecursive($childFolder);
        }

        // Cuối cùng, xóa thư mục này
        $folder->delete();
    }

    /**
     * Show folder contents.
     */
    public function show($id)
    {
        $folder = Folder::with([
            'files' => function($query) {
                $query->active();
            },
            'children' => function($query) {
                // Chỉ hiển thị thư mục con không trong thùng rác và đính kèm số file không trong thùng rác
                $query->active()->withCount(['files' => function($q) {
                    $q->where('is_trash', false);
                }]);
            }
        ])->findOrFail($id);

        // Get all folders for upload modal dropdown
        $folders = Folder::where('user_id', Auth::id() ?? 1)
            ->active()
            ->root()
            ->orderBy('name')
            ->get();

        return view('pages.folder-view', compact('folder', 'folders'));
    }

    /**
     * Check for duplicate root folder names before upload.
     */
    public function checkDuplicateFolders(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string',
        ]);

        $userId = Auth::id() ?? 1;
        $folderName = $request->folder_name;

        // Check if root folder with this name already exists
        $exists = Folder::where('user_id', $userId)
            ->where('parent_id', null) // Chỉ mức gốc (root)
            ->where('name', $folderName)
            ->where('is_trash', false)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Upload an entire folder structure from user's computer.
     */
    public function uploadFolder(Request $request)
    {
        Log::info('FolderController@uploadFolder called', [
            'files_count' => is_array($request->file('files')) ? count($request->file('files')) : 0,
            'is_public' => $request->is_public,
            'conflict_action' => $request->conflict_action,
        ]);

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // 100MB cho mỗi file
            'is_public' => 'required|boolean',
            'folder_paths' => 'required|string',
            'conflict_action' => 'nullable|in:replace,merge', // Cơ chế xử lý xung đột mới
            'description' => 'nullable|string|max:1000',
        ]);

    $userId = Auth::id() ?? 1;
        $isPublic = $request->is_public;
        $conflictAction = $request->conflict_action ?? 'merge'; // Mặc định là merge
        $description = $request->input('description');
    $favoriteOnUpload = $request->boolean('favorite_on_upload');
        
        // Lấy ánh xạ đường dẫn file từ input ẩn
        $folderPaths = json_decode($request->folder_paths, true);
        
        // Array to store folder ID mappings: path => folder_id
        $folderMap = [];
        $uploadedCount = 0;
        $errors = [];

        $files = $request->file('files');

        // Determine effective root folder name based on conflict action
        $effectiveRootName = null;
        if (!empty($files) && !empty($folderPaths[0])) {
            $firstPath = $folderPaths[0];
            $originalRootName = explode('/', str_replace('\\', '/', $firstPath))[0];

            if ($conflictAction === 'replace') {
                // If replacing, remove existing root folder and keep original name
                $existingRootFolder = Folder::where('user_id', $userId)
                    ->whereNull('parent_id')
                    ->where('name', $originalRootName)
                    ->where('is_trash', false)
                    ->first();
                if ($existingRootFolder) {
                    $this->forceDeleteRecursive($existingRootFolder);
                }
                $effectiveRootName = $originalRootName;
            } else { // merge => keep both by creating a new root folder name if needed
                $effectiveRootName = $originalRootName;
                $existsRoot = Folder::where('user_id', $userId)
                    ->whereNull('parent_id')
                    ->where('name', $originalRootName)
                    ->where('is_trash', false)
                    ->exists();
                if ($existsRoot) {
                    $baseName = $originalRootName;
                    $suffix = 1;
                    do {
                        $candidate = $baseName . ' (' . $suffix . ')';
                        $conflict = Folder::where('user_id', $userId)
                            ->whereNull('parent_id')
                            ->where('name', $candidate)
                            ->where('is_trash', false)
                            ->exists();
                        $suffix++;
                    } while ($conflict);
                    $effectiveRootName = $candidate;
                }
            }
        }
        
        // Xử lý từng file
        foreach ($files as $key => $uploadedFile) {
            try {
                // Lấy đường dẫn tương đối từ ánh xạ của chúng ta
                $relativePath = $folderPaths[$key] ?? $uploadedFile->getClientOriginalName();
                
                // Phân tích đường dẫn: "FolderName/Subfolder/file.txt"
                $pathParts = explode('/', str_replace('\\', '/', $relativePath));
                $fileName = array_pop($pathParts); // Last part is the file name
                
                if (empty($pathParts) || empty($pathParts[0])) {
                    // Không có cấu trúc thư mục, bỏ qua file này
                    continue;
                }

                // Áp dụng tên thư mục gốc đã xác định nếu có
                if (!empty($effectiveRootName)) {
                    $pathParts[0] = $effectiveRootName;
                }
                
                // Tạo cấu trúc thư mục
                $currentParentId = null;
                $currentPath = '';
                
                foreach ($pathParts as $folderName) {
                    if (empty($folderName)) continue;
                    
                    $currentPath .= ($currentPath ? '/' : '') . $folderName;
                    
                    // Kiểm tra xem chúng ta đã tạo thư mục này trong phiên tải lên này chưa
                    if (!isset($folderMap[$currentPath])) {
                        // Kiểm tra xem thư mục đã tồn tại chưa
                        $existingFolder = Folder::where('user_id', $userId)
                            ->where('parent_id', $currentParentId)
                            ->where('name', $folderName)
                            ->where('is_trash', false)
                            ->first();
                        
                        if ($existingFolder) {
                            $folderMap[$currentPath] = $existingFolder->id;
                        } else {
                            // Tạo thư mục mới
                            $newFolder = Folder::create([
                                'user_id' => $userId,
                                'parent_id' => $currentParentId,
                                'name' => $folderName,
                                'color' => '#3498db',
                                'is_public' => $isPublic,
                                'description' => $currentParentId === null ? $description : null,
                                // Auto-favorite only the root folder when uploading from Favorites page
                                'is_favorite' => $currentParentId === null ? $favoriteOnUpload : false,
                            ]);
                            $folderMap[$currentPath] = $newFolder->id;
                        }
                    }
                    
                    $currentParentId = $folderMap[$currentPath];
                }
                
                // Bây giờ tải file lên thư mục thích hợp
                $originalFileName = $fileName;
                $extension = $uploadedFile->getClientOriginalExtension();
                
                // Kiểm tra tên file trùng trong thư mục này
                $existingFile = File::where('user_id', $userId)
                    ->where('folder_id', $currentParentId)
                    ->where('original_name', $originalFileName)
                    ->where('is_trash', false)
                    ->first();
                
                if ($existingFile) {
                    if ($conflictAction === 'replace') {
                        // Xóa file cũ
                        if ($existingFile->path && Storage::disk('public')->exists($existingFile->path)) {
                            Storage::disk('public')->delete($existingFile->path);
                        }
                        $existingFile->delete();
                    } else {
                        // Tự động đổi tên với (1), (2), ...
                        $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
                        $counter = 1;
                        do {
                            $newName = "{$baseName} ({$counter}).{$extension}";
                            $exists = File::where('user_id', $userId)
                                ->where('folder_id', $currentParentId)
                                ->where('original_name', $newName)
                                ->where('is_trash', false)
                                ->exists();
                            $counter++;
                        } while ($exists);
                        
                        $originalFileName = $newName;
                    }
                }
                
                // Tạo tên file lưu trữ duy nhất
                $filename = time() . '_' . Str::random(10) . '.' . $extension;
                
                // Lưu file
                $path = $uploadedFile->storeAs('uploads', $filename, 'public');
                
                // Tạo bản ghi file
                File::create([
                    'user_id' => $userId,
                    'folder_id' => $currentParentId,
                    'name' => pathinfo($originalFileName, PATHINFO_FILENAME),
                    'original_name' => $originalFileName,
                    'path' => $path,
                    'mime_type' => $uploadedFile->getMimeType(),
                    'extension' => $extension,
                    'size' => $uploadedFile->getSize(),
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload: " . $e->getMessage();
            }
        }
        
        if ($uploadedCount > 0) {
            $folderCount = count($folderMap);
            $message = "Successfully uploaded {$uploadedCount} file(s) in {$folderCount} folder(s)!";
            if (count($errors) > 0) {
                $message .= " Note: " . count($errors) . " file(s) failed.";
            }

            Log::info('FolderController@uploadFolder success', [
                'uploaded_count' => $uploadedCount,
                'folder_count' => $folderCount,
                'errors_count' => count($errors),
            ]);

            // Return JSON for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'uploaded_count' => $uploadedCount,
                    'folder_count' => $folderCount,
                    'errors' => $errors,
                ]);
            }

            return redirect()->back()->with('success', $message);
        } else {
            $errorMsg = count($errors) > 0 ? implode(' | ', array_slice($errors, 0, 3)) : 'Unknown error';

            Log::warning('FolderController@uploadFolder failed', [
                'errors' => $errors,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload folder. ' . $errorMsg,
                ], 400);
            }

            return redirect()->back()->with('error', 'Failed to upload folder. ' . $errorMsg);
        }
    }

    /**
     * Toggle folder favorite status.
     */
    public function toggleFavorite($id)
    {
        $folder = Folder::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $folder->is_favorite = !$folder->is_favorite;
        $folder->save();

        $message = $folder->is_favorite 
            ? 'Folder added to favorites!' 
            : 'Folder removed from favorites!';

        return redirect()->back()->with('success', $message);
    }
}
