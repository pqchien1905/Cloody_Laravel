<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminGroupsController extends Controller
{
    /**
     * Display a listing of all groups.
     */
    public function index(Request $request)
    {
        $query = Group::with(['owner'])
            ->withCount(['members', 'files', 'folders']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo chủ sở hữu
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Lọc theo quyền riêng tư
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }

        // Lọc theo số thành viên
        if ($request->filled('members_min')) {
            $query->having('members_count', '>=', $request->members_min);
        }
        if ($request->filled('members_max')) {
            $query->having('members_count', '<=', $request->members_max);
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['name', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $groups = $query->paginate(20)->withQueryString();

        // Thống kê
        $stats = [
            'total' => Group::count(),
            'public' => Group::where('privacy', 'public')->count(),
            'private' => Group::where('privacy', 'private')->count(),
            'total_members' => DB::table('group_members')->distinct('user_id')->count('user_id'),
            'total_files' => DB::table('group_files')->count(),
            'total_folders' => DB::table('group_folders')->count(),
        ];

        // Lấy danh sách users cho filter
        $users = User::orderBy('name')->get();

        return view('pages.admin.groups.index', compact('groups', 'stats', 'users'));
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group)
    {
        $group->load(['owner', 'members', 'files.user', 'folders.user']);
        
        return view('pages.admin.groups.show', compact('group'));
    }

    /**
     * View group (Admin version - same as show).
     */
    public function view(Group $group)
    {
        return $this->show($group);
    }

    /**
     * Remove the specified group.
     */
    public function destroy(Group $group)
    {
        try {
            // Xóa avatar nếu có
            if ($group->avatar && Storage::disk('public')->exists($group->avatar)) {
                Storage::disk('public')->delete($group->avatar);
            }

            // Xóa các quan hệ
            $group->members()->detach();
            $group->files()->detach();
            $group->folders()->detach();

            // Xóa record
            $group->delete();

            return redirect()->route('admin.groups.index')
                ->with('status', __('common.group_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('common.error_deleting_group') . ': ' . $e->getMessage());
        }
    }
}

