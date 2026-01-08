<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller - Xử lý các chức năng liên quan đến nhóm (Group)
 */
class GroupController extends Controller
{
    /**
     * Hiển thị danh sách các nhóm
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        // Lấy các nhóm mà người dùng là thành viên
        $myGroups = $user->groups()->with('owner', 'members', 'files', 'folders')->get();
        // Lấy các nhóm mà người dùng là chủ sở hữu
        $ownedGroups = $user->ownedGroups()->with('members', 'files', 'folders')->get();
        
        return view('pages.groups.index', compact('myGroups', 'ownedGroups'));
    }

    /**
     * Hiển thị form tạo nhóm mới
     */
    public function create()
    {
        return view('pages.groups.create');
    }

    /**
     * Lưu nhóm mới được tạo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:private,public',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['owner_id'] = Auth::id();

        // Xử lý upload avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('group-avatars', $filename, 'public');
            $validated['avatar'] = $path;
        }

        $group = Group::create($validated);

        // Thêm chủ sở hữu như là thành viên admin
        $group->members()->attach(Auth::id(), [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Nhóm đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết nhóm được chỉ định
     */
    public function show(Group $group)
    {
        // Kiểm tra người dùng có quyền truy cập nhóm không
        if ($group->privacy === 'private' && !$group->isMember(Auth::id())) {
            abort(403, 'Bạn không có quyền truy cập nhóm này.');
        }

        // Tải các quan hệ cần thiết
        $group->load(['owner', 'members', 'admins']);
        $isMember = $group->isMember(Auth::id());
        $isAdmin = $group->isAdmin(Auth::id());
        $isOwner = $group->isOwner(Auth::id());

        // Load files với thông tin user và người chia sẻ
        $files = $group->files()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->withPivot('shared_by', 'permission', 'created_at')
            ->get();
        
        // Load thông tin người chia sẻ
        $sharedByIds = $files->pluck('pivot.shared_by')->unique();
        $sharedByUsers = User::whereIn('id', $sharedByIds)->get()->keyBy('id');
        foreach ($files as $file) {
            $file->sharedBy = $sharedByUsers->get($file->pivot->shared_by);
        }
            
        $folders = $group->folders()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->withPivot('shared_by', 'permission', 'created_at')
            ->get();
        
        // Load thông tin người chia sẻ cho folders
        $folderSharedByIds = $folders->pluck('pivot.shared_by')->unique();
        $folderSharedByUsers = User::whereIn('id', $folderSharedByIds)->get()->keyBy('id');
        foreach ($folders as $folder) {
            $folder->sharedBy = $folderSharedByUsers->get($folder->pivot->shared_by);
        }

        return view('pages.groups.show', compact('group', 'isMember', 'isAdmin', 'isOwner', 'files', 'folders'));
    }

    /**
     * Show the form for editing the specified group
     */
    public function edit(Group $group)
    {
        // Only owner and admins can edit
        if (!$group->isOwner(Auth::id()) && !$group->isAdmin(Auth::id())) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        return view('pages.groups.edit', compact('group'));
    }

    /**
     * Update the specified group
     */
    public function update(Request $request, Group $group)
    {
        // Only owner and admins can update
        if (!$group->isOwner(Auth::id()) && !$group->isAdmin(Auth::id())) {
            abort(403, 'Bạn không có quyền cập nhật nhóm này.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:private,public',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($group->avatar) {
                Storage::disk('public')->delete($group->avatar);
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('group-avatars', $filename, 'public');
            $validated['avatar'] = $path;
        }

        $group->update($validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Nhóm đã được cập nhật thành công!');
    }

    /**
     * Remove the specified group
     */
    public function destroy(Group $group)
    {
        // Only owner can delete
        if (!$group->isOwner(Auth::id())) {
            abort(403, 'Chỉ chủ nhóm mới có thể xóa nhóm này.');
        }

        // Delete avatar
        if ($group->avatar) {
            Storage::disk('public')->delete($group->avatar);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Nhóm đã được xóa thành công!');
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, Group $group)
    {
        // Only owner and admins can add members
        if (!$group->isOwner(Auth::id()) && !$group->isAdmin(Auth::id())) {
            abort(403, 'Bạn không có quyền thêm thành viên.');
        }

        $validated = $request->validate([
            'user_email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,member',
        ]);

        $user = User::where('email', $validated['user_email'])->first();

        // Check if user is already a member
        if ($group->isMember($user->id)) {
            return back()->with('error', 'Người dùng đã là thành viên của nhóm!');
        }

        $group->members()->attach($user->id, [
            'role' => $validated['role'],
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Đã thêm thành viên thành công!');
    }

    /**
     * Remove member from group
     */
    public function removeMember(Group $group, User $user)
    {
        // Only owner and admins can remove members
        if (!$group->isOwner(Auth::id()) && !$group->isAdmin(Auth::id())) {
            abort(403, 'Bạn không có quyền xóa thành viên.');
        }

        // Cannot remove owner
        if ($group->isOwner($user->id)) {
            return back()->with('error', 'Không thể xóa chủ nhóm!');
        }

        $group->members()->detach($user->id);

        return back()->with('success', 'Đã xóa thành viên thành công!');
    }

    /**
     * Update member role
     */
    public function updateMemberRole(Request $request, Group $group, User $user)
    {
        // Only owner can change roles
        if (!$group->isOwner(Auth::id())) {
            abort(403, 'Chỉ chủ nhóm mới có thể thay đổi vai trò.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,member',
        ]);

        // Cannot change owner role
        if ($group->isOwner($user->id)) {
            return back()->with('error', 'Không thể thay đổi vai trò của chủ nhóm!');
        }

        $group->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Đã cập nhật vai trò thành công!');
    }

    /**
     * Leave group
     */
    public function leave(Group $group)
    {
        $userId = Auth::id();

        // Owner cannot leave
        if ($group->isOwner($userId)) {
            return back()->with('error', 'Chủ nhóm không thể rời khỏi nhóm! Hãy chuyển quyền sở hữu hoặc xóa nhóm.');
        }

        $group->members()->detach($userId);

        return redirect()->route('groups.index')
            ->with('success', 'Bạn đã rời khỏi nhóm thành công!');
    }

    /**
     * Show group files
     */
    public function files(Group $group)
    {
        // Check if user has access
        if ($group->privacy === 'private' && !$group->isMember(Auth::id())) {
            abort(403, 'Bạn không có quyền truy cập nhóm này.');
        }

        // Load files với thông tin user và người chia sẻ
        $files = $group->files()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->withPivot('shared_by', 'permission', 'created_at')
            ->get();
        
        // Load thông tin người chia sẻ
        $sharedByIds = $files->pluck('pivot.shared_by')->unique();
        $sharedByUsers = User::whereIn('id', $sharedByIds)->get()->keyBy('id');
        foreach ($files as $file) {
            $file->sharedBy = $sharedByUsers->get($file->pivot->shared_by);
        }
            
        $folders = $group->folders()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->withPivot('shared_by', 'permission', 'created_at')
            ->get();
        
        // Load thông tin người chia sẻ cho folders
        $folderSharedByIds = $folders->pluck('pivot.shared_by')->unique();
        $folderSharedByUsers = User::whereIn('id', $folderSharedByIds)->get()->keyBy('id');
        foreach ($folders as $folder) {
            $folder->sharedBy = $folderSharedByUsers->get($folder->pivot->shared_by);
        }
            
        $isMember = $group->isMember(Auth::id());
        $isAdmin = $group->isAdmin(Auth::id());
        $isOwner = $group->isOwner(Auth::id());

        return view('pages.groups.files', compact('group', 'files', 'folders', 'isMember', 'isAdmin', 'isOwner'));
    }

    /**
     * Share file with group
     */
    public function shareFile(Request $request, Group $group)
    {
        if (!$group->isMember(Auth::id())) {
            abort(403, 'Bạn không phải thành viên của nhóm này.');
        }

        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'permission' => 'required|in:view,download,edit',
        ]);

        // Check if already shared
        if ($group->files()->where('file_id', $validated['file_id'])->exists()) {
            return back()->with('error', 'File đã được chia sẻ với nhóm này!');
        }

        $group->files()->attach($validated['file_id'], [
            'shared_by' => Auth::id(),
            'permission' => $validated['permission'],
        ]);

        return back()->with('success', 'Đã chia sẻ file với nhóm thành công!');
    }

    /**
     * Share folder with group
     */
    public function shareFolder(Request $request, Group $group)
    {
        if (!$group->isMember(Auth::id())) {
            abort(403, 'Bạn không phải thành viên của nhóm này.');
        }

        $validated = $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'permission' => 'required|in:view,edit,full',
        ]);

        // Check if already shared
        if ($group->folders()->where('folder_id', $validated['folder_id'])->exists()) {
            return back()->with('error', 'Thư mục đã được chia sẻ với nhóm này!');
        }

        $group->folders()->attach($validated['folder_id'], [
            'shared_by' => Auth::id(),
            'permission' => $validated['permission'],
        ]);

        return back()->with('success', 'Đã chia sẻ thư mục với nhóm thành công!');
    }

    /**
     * Remove file from group
     */
    public function removeFile(Group $group, $fileId)
    {
        if (!$group->isAdmin(Auth::id()) && !$group->isOwner(Auth::id())) {
            abort(403, 'Bạn không có quyền xóa file khỏi nhóm.');
        }

        $group->files()->detach($fileId);

        return back()->with('success', 'Đã xóa file khỏi nhóm!');
    }

    /**
     * Remove folder from group
     */
    public function removeFolder(Group $group, $folderId)
    {
        if (!$group->isAdmin(Auth::id()) && !$group->isOwner(Auth::id())) {
            abort(403, 'Bạn không có quyền xóa thư mục khỏi nhóm.');
        }

        $group->folders()->detach($folderId);

        return back()->with('success', 'Đã xóa thư mục khỏi nhóm!');
    }

    /**
     * Discover public groups
     */
    public function discover()
    {
        /** @var User $user */
        $user = Auth::user();
        $publicGroups = Group::where('privacy', 'public')
            ->whereNotIn('id', $user->groups()->pluck('groups.id'))
            ->with('owner', 'members')
            ->paginate(12);

        return view('pages.groups.discover', compact('publicGroups'));
    }

    /**
     * Request to join group
     */
    public function requestJoin(Group $group)
    {
        if ($group->privacy !== 'public') {
            return back()->with('error', 'Chỉ có thể yêu cầu tham gia nhóm công khai!');
        }

        if ($group->isMember(Auth::id())) {
            return back()->with('error', 'Bạn đã là thành viên của nhóm này!');
        }

        // For demo, auto-approve. In production, you'd create a join_requests table
        $group->members()->attach(Auth::id(), [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Bạn đã tham gia nhóm thành công!');
    }
}
