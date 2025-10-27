@php
    // Dữ liệu lưu trữ hiện được cung cấp bởi AppServiceProvider
    // Gỡ lỗi: Kiểm tra nếu các biến đã được thiết lập
    if (!isset($storageUsedGB)) {
        $storageUsedGB = 0;
        $storageLimit = 100;
        $storagePercent = 0;
        
        if (Auth::check()) {
            $userId = Auth::id();
            $storageUsed = App\Models\File::where('user_id', $userId)
                ->where('is_trash', false)
                ->sum('size');
            
            $storageUsedGB = $storageUsed / (1024 * 1024 * 1024);
            $storagePercent = min(($storageUsedGB / $storageLimit) * 100, 100);
        }
    }
@endphp

<div class="iq-sidebar sidebar-default">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('cloudbox.dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid rounded-normal light-logo" alt="logo">
        </a>
        <div class="iq-menu-bt-sidebar">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <div class="new-create select-dropdown input-prepend input-append">
            <div class="btn-group">
                <div data-toggle="dropdown">
                    <div class="search-query selet-caption"><i class="las la-plus pr-2"></i>Create New</div>
                    <span class="search-replace"></span>
                    <span class="caret"></span>
                </div>
                <ul class="dropdown-menu">
                    <li><div class="item" onclick="$('#createFolderModal').modal('show');" style="cursor: pointer;"><i class="ri-folder-add-line pr-3"></i>New Folder</div></li>
                    <li><div class="item" onclick="$('#uploadFileModal').modal('show');" style="cursor: pointer;"><i class="ri-file-upload-line pr-3"></i>Upload Files</div></li>
                    <li><div class="item" onclick="$('#uploadFolderModal').modal('show');" style="cursor: pointer;"><i class="ri-folder-upload-line pr-3"></i>Upload Folders</div></li>
                </ul>
            </div>
        </div>
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="{{ request()->routeIs('cloudbox.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('cloudbox.dashboard') }}" class="{{ request()->routeIs('cloudbox.dashboard') ? 'active' : '' }}">
                        <i class="las la-home iq-arrow-left"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'active' : '' }}">
                    <a href="#mydrive" class="{{ request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'true' : 'false' }}">
                        <i class="las la-hdd"></i><span>My Drive</span>
                        <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                        <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                    </a>
                    <ul id="mydrive" class="iq-submenu collapse {{ request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ request()->routeIs('cloudbox.files') ? 'active' : '' }}"><a href="{{ route('cloudbox.files') }}"><i class="las la-folder"></i><span>My Files</span></a></li>
                        <li class="{{ request()->routeIs('cloudbox.folders.*') ? 'active' : '' }}"><a href="{{ route('cloudbox.folders.index') }}"><i class="las la-folder-open"></i><span>Folders</span></a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('cloudbox.shared') ? 'active' : '' }}">
                    <a href="{{ route('cloudbox.shared') }}" class="{{ request()->routeIs('cloudbox.shared') ? 'active' : '' }}">
                        <i class="las la-share-alt iq-arrow-left"></i><span>Share With Me</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloudbox.recent') ? 'active' : '' }}">
                    <a href="{{ route('cloudbox.recent') }}" class="{{ request()->routeIs('cloudbox.recent') ? 'active' : '' }}">
                        <i class="las la-clock iq-arrow-left"></i><span>Recent Files</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloudbox.favorites') ? 'active' : '' }}">
                    <a href="{{ route('cloudbox.favorites') }}" class="{{ request()->routeIs('cloudbox.favorites') ? 'active' : '' }}">
                        <i class="las la-star iq-arrow-left"></i><span>Favorites</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloudbox.trash') ? 'active' : '' }}">
                    <a href="{{ route('cloudbox.trash') }}" class="{{ request()->routeIs('cloudbox.trash') ? 'active' : '' }}">
                        <i class="las la-trash iq-arrow-left"></i><span>Trash</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloudbox.user.*') ? 'active' : '' }}">
                    <a href="#otherpage" class="{{ request()->routeIs('cloudbox.user.*') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ request()->routeIs('cloudbox.user.*') ? 'true' : 'false' }}">
                        <i class="las la-pager"></i><span>Other Page</span>
                        <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                        <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                    </a>
                    <ul id="otherpage" class="iq-submenu collapse {{ request()->routeIs('cloudbox.user.*') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ request()->routeIs('cloudbox.user.*') ? 'active' : '' }}">
                            <a href="#user" class="{{ request()->routeIs('cloudbox.user.*') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ request()->routeIs('cloudbox.user.*') ? 'true' : 'false' }}">
                                <i class="las la-user-cog"></i><span>User Details</span>
                                <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                                <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="user" class="iq-submenu collapse {{ request()->routeIs('cloudbox.user.*') ? 'show' : '' }}" data-parent="#otherpage">
                                <li class="{{ request()->routeIs('cloudbox.user.profile') ? 'active' : '' }}"><a href="{{ route('cloudbox.user.profile') }}"><i class="las la-id-card-alt"></i>Profile</a></li>
                                <li class="{{ request()->routeIs('cloudbox.user.add') ? 'active' : '' }}"><a href="{{ route('cloudbox.user.add') }}"><i class="las la-plus-circle"></i>Add User</a></li>
                                <li class="{{ request()->routeIs('cloudbox.user.list') ? 'active' : '' }}"><a href="{{ route('cloudbox.user.list') }}"><i class="las la-th-list"></i>User List</a></li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="#auth" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <i class="las la-lock"></i><span>Authentication</span>
                                <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                                <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="auth" class="iq-submenu collapse" data-parent="#otherpage">
                                <li><a href="{{ route('login') }}"><i class="las la-sign-in-alt"></i>Login</a></li>
                                <li><a href="{{ route('register') }}"><i class="las la-user-plus"></i>Register</a></li>
                                <li><a href="#"><i class="las la-redo-alt"></i>Recover Password</a></li>
                                <li><a href="#"><i class="las la-envelope-open-text"></i>Confirm Mail</a></li>
                                <li><a href="#"><i class="las la-lock"></i>Lock Screen</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <h4 class="mb-3"><i class="las la-cloud mr-2"></i>Storage</h4>
            <p>{{ number_format($storageUsedGB, 2) }} GB of {{ $storageLimit }} GB used</p>
            <div class="iq-progress-bar mb-3">
                <span class="bg-primary iq-progress progress-1" data-percent="{{ number_format($storagePercent, 2) }}" style="width: 0%; transition: width 1s ease;">
                </span>
            </div>
            <a href="#" class="btn btn-outline-primary view-more mt-2">Buy Storage</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hoạt ảnh thanh tiến độ lưu trữ sau khi tải trang
    setTimeout(function() {
        const storageBar = document.querySelector('.sidebar-bottom .iq-progress');
        if (storageBar) {
            const percent = parseFloat(storageBar.getAttribute('data-percent')) || 0;
            console.log('Storage bar found, animating to:', percent + '%');
            storageBar.style.width = percent + '%';
        } else {
            console.log('Storage bar not found');
        }
    }, 100);
});
</script>
@endpush
