<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFilesController extends Controller
{
    /**
     * Display a listing of all files.
     */
    public function index(Request $request)
    {
        $query = File::with(['user', 'folder']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('extension', 'like', "%{$search}%");
            });
        }

        // Lọc theo người dùng
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo loại file
        if ($request->filled('type')) {
            $type = $request->type;
            switch ($type) {
                case 'image':
                    $query->where('mime_type', 'like', 'image%');
                    break;
                case 'video':
                    $query->where('mime_type', 'like', 'video%');
                    break;
                case 'audio':
                    $query->where('mime_type', 'like', 'audio%');
                    break;
                case 'pdf':
                    $query->where('mime_type', 'like', '%pdf%');
                    break;
                case 'document':
                    $query->where(function($q) {
                        $q->where('mime_type', 'like', '%word%')
                          ->orWhere('mime_type', 'like', '%officedocument%');
                    });
                    break;
                case 'spreadsheet':
                    $query->where(function($q) {
                        $q->where('mime_type', 'like', '%excel%')
                          ->orWhere('mime_type', 'like', '%spreadsheet%');
                    });
                    break;
            }
        }

        // Lọc theo category
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->where('is_active', true)->first();
            if ($category && !empty($category->extensions)) {
                $query->whereIn('extension', $category->extensions);
            }
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'trash') {
                $query->where('is_trash', true);
            } elseif ($request->status === 'active') {
                $query->where('is_trash', false);
            } elseif ($request->status === 'favorite') {
                $query->where('is_favorite', true);
            }
        }

        // Lọc theo kích thước
        if ($request->filled('size_min')) {
            $query->where('size', '>=', $request->size_min * 1024 * 1024); // MB to bytes
        }
        if ($request->filled('size_max')) {
            $query->where('size', '<=', $request->size_max * 1024 * 1024); // MB to bytes
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['name', 'size', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $files = $query->paginate(20)->withQueryString();

        // Thống kê
        $stats = [
            'total' => File::count(),
            'active' => File::where('is_trash', false)->count(),
            'trash' => File::where('is_trash', true)->count(),
            'favorites' => File::where('is_favorite', true)->count(),
            'total_size' => File::sum('size'),
            'by_type' => [
                'images' => File::where('mime_type', 'like', 'image%')->count(),
                'videos' => File::where('mime_type', 'like', 'video%')->count(),
                'audio' => File::where('mime_type', 'like', 'audio%')->count(),
                'pdf' => File::where('mime_type', 'like', '%pdf%')->count(),
                'documents' => File::where(function($q) {
                    $q->where('mime_type', 'like', '%word%')
                      ->orWhere('mime_type', 'like', '%officedocument%');
                })->count(),
                'spreadsheets' => File::where(function($q) {
                    $q->where('mime_type', 'like', '%excel%')
                      ->orWhere('mime_type', 'like', '%spreadsheet%');
                })->count(),
            ],
        ];

        // Lấy danh sách users và categories cho filter
        $users = User::orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        return view('pages.admin.files.index', compact('files', 'stats', 'users', 'categories'));
    }

    /**
     * Display the specified file.
     */
    public function show(File $file)
    {
        $file->load(['user', 'folder', 'shares.sharedBy', 'shares.sharedWith']);
        
        return view('pages.admin.files.show', compact('file'));
    }

    /**
     * Serve file content with proper headers (Admin version).
     */
    public function serve(File $file)
    {
        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            abort(404, __('common.file_not_found'));
        }

        return response()->file($filePath, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
        ]);
    }

    /**
     * View/preview a file inline (Admin version - no permission check).
     */
    public function view(File $file)
    {
        // Verify file exists
        $filePath = storage_path('app/public/' . $file->path);
        if (!file_exists($filePath) && !Storage::disk('public')->exists($file->path)) {
            return redirect()->back()->with('error', __('common.file_not_found'));
        }

        // Generate file URL using the serve route for better access control and headers
        $fileUrl = route('admin.files.serve', $file->id);

        return view('pages.admin.files.view', [
            'file' => $file,
            'fileUrl' => $fileUrl,
        ]);
    }

    /**
     * Download file (Admin version - no permission check).
     */
    public function download(File $file)
    {
        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', __('common.file_not_found'));
        }

        return response()->download($filePath, $file->original_name);
    }

    /**
     * Remove the specified file from storage.
     */
    public function destroy(File $file)
    {
        try {
            // Xóa file vật lý
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            // Xóa record
            $file->delete();

            return redirect()->route('admin.files.index')
                ->with('status', __('common.file_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('common.error_deleting_file') . ': ' . $e->getMessage());
        }
    }
}

