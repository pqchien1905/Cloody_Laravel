<?php
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
?>

<div class="iq-sidebar sidebar-default">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="<?php echo e(route('cloudbox.dashboard')); ?>" class="header-logo">
            <img src="<?php echo e(asset('assets/images/logo.png')); ?>" class="img-fluid rounded-normal light-logo" alt="logo">
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
                <?php if(request()->routeIs('admin.*')): ?>
                <li class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.dashboard')); ?>">
                        <i class="las la-tachometer-alt"></i><span>Admin Dashboard</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.users.index')); ?>">
                        <i class="las la-users-cog"></i><span>Manage Users</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.categories.index')); ?>">
                        <i class="las la-tags"></i><span>File Categories</span>
                    </a>
                </li>
                <?php else: ?>
                <li class="<?php echo e(request()->routeIs('cloudbox.dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.dashboard')); ?>" class="<?php echo e(request()->routeIs('cloudbox.dashboard') ? 'active' : ''); ?>">
                        <i class="las la-home iq-arrow-left"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'active' : ''); ?>">
                    <a href="#mydrive" class="<?php echo e(request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? '' : 'collapsed'); ?>" data-toggle="collapse" aria-expanded="<?php echo e(request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'true' : 'false'); ?>">
                        <i class="las la-hdd"></i><span>My Drive</span>
                        <i class="las la-angle-right iq-arrow-right arrow-active"></i>
                        <i class="las la-angle-down iq-arrow-right arrow-hover"></i>
                    </a>
                    <ul id="mydrive" class="iq-submenu collapse <?php echo e(request()->routeIs('cloudbox.files', 'cloudbox.folders.*') ? 'show' : ''); ?>" data-parent="#iq-sidebar-toggle">
                        <li class="<?php echo e(request()->routeIs('cloudbox.files') ? 'active' : ''); ?>"><a href="<?php echo e(route('cloudbox.files')); ?>"><i class="las la-folder"></i><span>My Files</span></a></li>
                        <li class="<?php echo e(request()->routeIs('cloudbox.folders.*') ? 'active' : ''); ?>"><a href="<?php echo e(route('cloudbox.folders.index')); ?>"><i class="las la-folder-open"></i><span>Folders</span></a></li>
                    </ul>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.shared') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.shared')); ?>" class="<?php echo e(request()->routeIs('cloudbox.shared') ? 'active' : ''); ?>">
                        <i class="las la-share-alt iq-arrow-left"></i><span>Share With Me</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.recent') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.recent')); ?>" class="<?php echo e(request()->routeIs('cloudbox.recent') ? 'active' : ''); ?>">
                        <i class="las la-clock iq-arrow-left"></i><span>Recent Files</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.favorites') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.favorites')); ?>" class="<?php echo e(request()->routeIs('cloudbox.favorites') ? 'active' : ''); ?>">
                        <i class="las la-star iq-arrow-left"></i><span>Favorites</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.trash') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.trash')); ?>" class="<?php echo e(request()->routeIs('cloudbox.trash') ? 'active' : ''); ?>">
                        <i class="las la-trash iq-arrow-left"></i><span>Trash</span>
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('cloudbox.user.profile') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('cloudbox.user.profile')); ?>">
                        <i class="las la-id-card-alt"></i><span>Profile</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <h4 class="mb-3"><i class="las la-cloud mr-2"></i>Storage</h4>
            <p><?php echo e(number_format($storageUsedGB, 2)); ?> GB of <?php echo e($storageLimit); ?> GB used</p>
            <div class="iq-progress-bar mb-3">
                <span class="bg-primary iq-progress progress-1" data-percent="<?php echo e(number_format($storagePercent, 2)); ?>" style="width: 0%; transition: width 1s ease;">
                </span>
            </div>
            <a href="#" class="btn btn-outline-primary view-more mt-2">Buy Storage</a>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>