@php
    // Dữ liệu lưu trữ hiện được cung cấp bởi AppServiceProvider
    // Gỡ lỗi: Kiểm tra nếu các biến đã được thiết lập
    if (!isset($storageUsedGB)) {
        $storageUsedGB = 0;
        $storageLimit = 1; // Default 1GB
        $storagePercent = 0;
        
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
            $storageUsed = App\Models\File::where('user_id', $userId)
                ->where('is_trash', false)
                ->sum('size');
            
            $storageUsedGB = $storageUsed / (1024 * 1024 * 1024);
            
            // Lấy storage limit từ subscription của user
            $storageLimit = $user->getStorageLimitGB();
            
            $storagePercent = $storageLimit > 0 ? min(($storageUsedGB / $storageLimit) * 100, 100) : 0;
        }
    }
@endphp

<style>
.iq-sidebar-menu .iq-menu li a,
.iq-sidebar-menu .iq-menu li a span {
    text-transform: none !important;
}
</style>

<div class="iq-sidebar sidebar-default">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('cloody.dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/images/Cloody.png') }}" class="img-fluid rounded-normal light-logo" alt="logo">
        </a>
        <div class="iq-menu-bt-sidebar">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar sidebar-scrollable" data-scroll="1">
        <div class="sidebar-top-section">
            <div class="new-create select-dropdown input-prepend input-append">
                <div class="btn-group">
                    <div data-toggle="dropdown">
                        <div class="search-query selet-caption"><i class="las la-plus pr-2"></i>{{ __('common.create_new') }}</div>
                        <span class="search-replace"></span>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        <li><div class="item" onclick="$('#createFolderModal').modal('show');" style="cursor: pointer;"><i class="ri-folder-add-line pr-3"></i>{{ __('common.new_folder') }}</div></li>
                        <li><div class="item" onclick="$('#uploadFileModal').modal('show');" style="cursor: pointer;"><i class="ri-file-upload-line pr-3"></i>{{ __('common.upload_files') }}</div></li>
                        <li><div class="item" onclick="$('#uploadFolderModal').modal('show');" style="cursor: pointer;"><i class="ri-folder-upload-line pr-3"></i>{{ __('common.upload_folders') }}</div></li>
                    </ul>
                </div>
            </div>
        </div>
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                @if (request()->routeIs('admin.*'))
                <!-- Dashboard -->
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="las la-tachometer-alt"></i><span>{{ __('common.admin_dashboard') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý người dùng -->
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="las la-users-cog"></i><span>{{ __('common.manage_users') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý gói lưu trữ -->
                <li class="{{ request()->routeIs('admin.storage-plans.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.storage-plans.index') }}">
                        <i class="las la-box"></i><span>{{ __('common.manage_storage_plans') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý Files -->
                <li class="{{ request()->routeIs('admin.files.*') && !request('from') ? 'active' : '' }}">
                    <a href="{{ route('admin.files.index') }}">
                        <i class="las la-file"></i><span>{{ __('common.manage_files') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý Folders -->
                <li class="{{ request()->routeIs('admin.folders.*') && !request('from') ? 'active' : '' }}">
                    <a href="{{ route('admin.folders.index') }}">
                        <i class="las la-folder"></i><span>{{ __('common.manage_folders') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý Groups -->
                <li class="{{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.groups.index') }}">
                        <i class="las la-users"></i><span>{{ __('common.manage_groups') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý Shares -->
                <li class="{{ request()->routeIs('admin.shares.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shares.index') }}">
                        <i class="las la-share-alt"></i><span>{{ __('common.manage_shares') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý Favorites -->
                <li class="{{ request()->routeIs('admin.favorites.*') || request('from') === 'favorites' ? 'active' : '' }}">
                    <a href="{{ route('admin.favorites.index') }}">
                        <i class="las la-star"></i><span>{{ __('common.manage_favorites') }}</span>
                    </a>
                </li>
                
                <!-- Quản lý danh mục -->
                <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="las la-tags"></i><span>{{ __('common.file_categories') }}</span>
                    </a>
                </li>
                
                <!-- (Đã gỡ bỏ) Thùng rác -->
                {{--
                <li class="{{ request()->routeIs('admin.trash.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.trash.index') }}">
                        <i class="las la-trash-alt"></i><span>{{ __('common.manage_trash') }}</span>
                    </a>
                </li>
                --}}
                
                <!-- Báo cáo & Thống kê -->
                <li class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.index') }}">
                        <i class="las la-chart-bar"></i><span>{{ __('common.manage_reports') }}</span>
                    </a>
                </li>
                @else
                <li class="{{ request()->routeIs('cloody.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('cloody.dashboard') }}" class="{{ request()->routeIs('cloody.dashboard') ? 'active' : '' }}">
                        <i class="las la-home iq-arrow-left"></i><span>{{ __('common.dashboard') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloody.files', 'cloody.folders.*') ? 'active' : '' }}">
                    <a href="#mydrive" class="{{ request()->routeIs('cloody.files', 'cloody.folders.*') ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ request()->routeIs('cloody.files', 'cloody.folders.*') ? 'true' : 'false' }}">
                        <i class="las la-hdd"></i><span>{{ __('common.my_drive') }}</span>
                        <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                        <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                    </a>
                    <ul id="mydrive" class="iq-submenu collapse {{ request()->routeIs('cloody.files', 'cloody.folders.*') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ request()->routeIs('cloody.files') ? 'active' : '' }}"><a href="{{ route('cloody.files') }}"><i class="las la-folder"></i><span>{{ __('common.my_files') }}</span></a></li>
                        <li class="{{ request()->routeIs('cloody.folders.*') ? 'active' : '' }}"><a href="{{ route('cloody.folders.index') }}"><i class="las la-folder-open"></i><span>{{ __('common.folders') }}</span></a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('cloody.shared') ? 'active' : '' }}">
                    <a href="{{ route('cloody.shared') }}" class="{{ request()->routeIs('cloody.shared') ? 'active' : '' }}">
                        <i class="las la-share-alt iq-arrow-left"></i><span>{{ __('common.share_with_me') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloody.recent') ? 'active' : '' }}">
                    <a href="{{ route('cloody.recent') }}" class="{{ request()->routeIs('cloody.recent') ? 'active' : '' }}">
                        <i class="las la-clock iq-arrow-left"></i><span>{{ __('common.recent_files') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloody.favorites') ? 'active' : '' }}">
                    <a href="{{ route('cloody.favorites') }}" class="{{ request()->routeIs('cloody.favorites') ? 'active' : '' }}">
                        <i class="las la-star iq-arrow-left"></i><span>{{ __('common.favorites') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('groups.*') ? 'active' : '' }}">
                    <a href="{{ route('groups.index') }}" class="{{ request()->routeIs('groups.*') ? 'active' : '' }}">
                        <i class="las la-users iq-arrow-left"></i><span>{{ __('common.groups') }}</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('cloody.trash') ? 'active' : '' }}">
                    <a href="{{ route('cloody.trash') }}" class="{{ request()->routeIs('cloody.trash') ? 'active' : '' }}">
                        <i class="las la-trash iq-arrow-left"></i><span>{{ __('common.trash') }}</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <h4 class="mb-3"><i class="las la-cloud mr-2"></i>{{ __('common.storage') }}</h4>
            <p>{{ number_format($storageUsedGB, 2) }} GB {{ __('common.of_used') }} {{ $storageLimit }} GB</p>
            <div class="iq-progress-bar mb-3">
                <span class="bg-primary iq-progress progress-1" data-percent="{{ number_format($storagePercent, 2) }}" style="width: 0%; transition: width 1s ease;">
                </span>
            </div>
            <a href="{{ route('cloody.storage.plans') }}" class="btn btn-outline-primary view-more mt-2">{{ __('common.buy_storage') }}</a>
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

@push('styles')
<style>
.iq-sidebar-menu .iq-menu li a {
    text-transform: none !important;
}
</style>
@endpush
