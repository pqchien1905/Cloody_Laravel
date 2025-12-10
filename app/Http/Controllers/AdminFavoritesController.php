<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;

class AdminFavoritesController extends Controller
{
    /**
     * Display a listing of all favorite files and folders.
     */
    public function index(Request $request)
    {
        // Query favorite files
        $filesQuery = File::with(['user', 'folder'])
            ->where('is_favorite', true);

        // Query favorite folders
        $foldersQuery = Folder::with(['user', 'parent'])
            ->where('is_favorite', true);

        // Filter by user
        if ($request->filled('user_id')) {
            $filesQuery->where('user_id', $request->user_id);
            $foldersQuery->where('user_id', $request->user_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $filesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
            $foldersQuery->where('name', 'like', "%{$search}%");
        }

        // Filter by type (files only, folders only, or all)
        $type = $request->get('type', 'all');
        
        // Get sorting parameters
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['name', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        // Initialize collections
        $files = collect();
        $folders = collect();
        
        if ($type === 'all' || $type === 'files') {
            if ($sortBy === 'name') {
                $filesQuery->orderBy('name', $sortOrder);
            } else {
                $filesQuery->orderBy($sortBy, $sortOrder);
            }
            $files = $filesQuery->get();
        }
        
        if ($type === 'all' || $type === 'folders') {
            $foldersQuery->orderBy($sortBy, $sortOrder);
            $folders = $foldersQuery->get();
        }

        // Combine and prepare for display
        $items = collect();
        
        foreach ($folders as $folder) {
            $items->push([
                'type' => 'folder',
                'id' => $folder->id,
                'name' => $folder->name,
                'user' => $folder->user,
                'parent' => $folder->parent,
                'size' => null,
                'mime_type' => null,
                'extension' => null,
                'created_at' => $folder->created_at,
                'updated_at' => $folder->updated_at,
                'is_trash' => $folder->is_trash,
                'model' => $folder,
            ]);
        }
        
        foreach ($files as $file) {
            $items->push([
                'type' => 'file',
                'id' => $file->id,
                'name' => $file->name,
                'original_name' => $file->original_name,
                'user' => $file->user,
                'folder' => $file->folder,
                'size' => $file->size,
                'formatted_size' => $file->formatted_size,
                'mime_type' => $file->mime_type,
                'extension' => $file->extension,
                'created_at' => $file->created_at,
                'updated_at' => $file->updated_at,
                'is_trash' => $file->is_trash,
                'model' => $file,
            ]);
        }

        // Sort combined items
        if ($sortOrder === 'desc') {
            $items = $items->sortByDesc($sortBy);
        } else {
            $items = $items->sortBy($sortBy);
        }

        // Paginate manually
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $items->count();
        $items = $items->forPage($currentPage, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Statistics
        $stats = [
            'total_favorites' => File::where('is_favorite', true)->count() + Folder::where('is_favorite', true)->count(),
            'favorite_files' => File::where('is_favorite', true)->count(),
            'favorite_folders' => Folder::where('is_favorite', true)->count(),
            'users_with_favorites' => File::where('is_favorite', true)->distinct('user_id')->count('user_id') 
                + Folder::where('is_favorite', true)->distinct('user_id')->count('user_id'),
        ];

        // Get users for filter
        $users = User::orderBy('name')->get();

        return view('pages.admin.favorites.index', compact('paginator', 'stats', 'users', 'type'));
    }

    /**
     * Remove favorite status from a file.
     */
    public function unfavoriteFile(Request $request, File $file)
    {
        try {
            $file->update(['is_favorite' => false]);
            
            // Redirect back to favorites page with current query parameters
            return redirect()->route('admin.favorites.index', $request->query())
                ->with('status', __('common.file_removed_from_favorites'));
        } catch (\Exception $e) {
            return redirect()->route('admin.favorites.index', $request->query())
                ->with('error', __('common.error_removing_favorite') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove favorite status from a folder.
     */
    public function unfavoriteFolder(Request $request, Folder $folder)
    {
        try {
            $folder->update(['is_favorite' => false]);
            
            // Redirect back to favorites page with current query parameters
            return redirect()->route('admin.favorites.index', $request->query())
                ->with('status', __('common.folder_removed_from_favorites'));
        } catch (\Exception $e) {
            return redirect()->route('admin.favorites.index', $request->query())
                ->with('error', __('common.error_removing_favorite') . ': ' . $e->getMessage());
        }
    }

    /**
     * Bulk remove favorites.
     */
    public function bulkUnfavorite(Request $request)
    {
        $request->validate([
            'file_ids' => 'array',
            'file_ids.*' => 'exists:files,id',
            'folder_ids' => 'array',
            'folder_ids.*' => 'exists:folders,id',
        ]);

        $count = 0;

        if ($request->filled('file_ids')) {
            $count += File::whereIn('id', $request->file_ids)->update(['is_favorite' => false]);
        }

        if ($request->filled('folder_ids')) {
            $count += Folder::whereIn('id', $request->folder_ids)->update(['is_favorite' => false]);
        }

        return redirect()->route('admin.favorites.index')
            ->with('status', __('common.items_removed_from_favorites', ['count' => $count]));
    }
}
