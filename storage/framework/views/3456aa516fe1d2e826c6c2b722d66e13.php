

<?php $__env->startSection('title', 'Dashboard - CloudBOX'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-transparent card-block card-stretch card-height mb-3">
                <div class="d-flex justify-content-between">                             
                    <div class="select-dropdown input-prepend input-append">
                        <div class="btn-group">
                            <div data-toggle="dropdown">
                                <div class="dropdown-toggle search-query">My Drive<i class="las la-angle-down ml-3"></i></div>
                                <span class="search-replace"></span>
                                <span class="caret"><!--icon--></span>
                            </div>
                            <ul class="dropdown-menu">
                                <li><div class="item" data-toggle="modal" data-target="#createFolderModal"><i class="ri-folder-add-line pr-3"></i>New Folder</div></li>
                                <li><div class="item" data-toggle="modal" data-target="#uploadModal"><i class="ri-file-upload-line pr-3"></i>Upload Files</div></li>
                                <li><div class="item"><i class="ri-folder-upload-line pr-3"></i>Upload Folders</div></li>
                            </ul>
                        </div>
                    </div>
                    <div class="dashboard1-dropdown d-flex align-items-center">
                        <div class="dashboard1-info">
                            <a href="#calander" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <i class="ri-arrow-down-s-line"></i>
                            </a>
                            <ul id="calander" class="iq-dropdown collapse list-inline m-0 p-0 mt-2">
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Calendar"><i class="las la-calendar iq-arrow-left"></i></a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Keep"><i class="las la-lightbulb iq-arrow-left"></i></a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Tasks"><i class="las la-tasks iq-arrow-left"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Card -->
        <div class="col-lg-8">
            <div class="card card-block card-stretch card-height iq-welcome" style="background: url(<?php echo e(asset('assets/images/layouts/mydrive/background.png')); ?>) no-repeat scroll right center; background-color: #ffffff; background-size: contain;">
                <div class="card-body property2-content">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="col-lg-6 col-sm-6 p-0">
                            <h3 class="mb-3">Welcome <?php echo e(Auth::user()->name); ?></h3>
                            <p class="mb-5">You have <?php echo e($totalFiles); ?> files and <?php echo e($totalFolders); ?> folders in your storage</p>
                            <a href="<?php echo e(route('cloudbox.files')); ?>">View All Files<i class="las la-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Access -->
        <div class="col-lg-4">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Quick Access</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-inline p-0 mb-0 row align-items-center">
                        <li class="col-lg-6 col-sm-6 mb-3 mb-sm-0"> 
                            <a href="<?php echo e(route('cloudbox.files')); ?>" style="cursor: pointer;" class="p-2 text-center border rounded d-block">
                                <div>
                                    <img src="<?php echo e(asset('assets/images/layouts/mydrive/folder-1.png')); ?>" class="img-fluid mb-1" alt="All Files">
                                </div>
                                <p class="mb-0">All Files</p>
                            </a>
                        </li>
                        <li class="col-lg-6 col-sm-6"> 
                            <a href="<?php echo e(route('cloudbox.favorites')); ?>" style="cursor: pointer;" class="p-2 text-center border rounded d-block">
                                <div>
                                    <img src="<?php echo e(asset('assets/images/layouts/mydrive/folder-2.png')); ?>" class="img-fluid mb-1" alt="Favorites">
                                </div>
                                <p class="mb-0">Favorites</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Documents</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <a href="<?php echo e(route('cloudbox.files')); ?>" class="view-more">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if($documents->count() > 0): ?>
            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body image-thumb">
                        <a href="<?php echo e(route('cloudbox.files.view', $doc->id)); ?>">
                            <div class="mb-4 text-center p-3 rounded iq-thumb">
                                <div class="iq-image-overlay"></div>
                                <?php if(str_contains($doc->mime_type, 'pdf')): ?>
                                    <img src="<?php echo e(asset('assets/images/layouts/page-1/pdf.png')); ?>" class="img-fluid" alt="PDF">
                                <?php elseif(str_contains($doc->mime_type, 'word')): ?>
                                    <img src="<?php echo e(asset('assets/images/layouts/page-1/doc.png')); ?>" class="img-fluid" alt="Word">
                                <?php elseif(str_contains($doc->mime_type, 'excel') || str_contains($doc->mime_type, 'spreadsheet')): ?>
                                    <img src="<?php echo e(asset('assets/images/layouts/page-1/xlsx.png')); ?>" class="img-fluid" alt="Excel">
                                <?php elseif(str_contains($doc->mime_type, 'powerpoint') || str_contains($doc->mime_type, 'presentation')): ?>
                                    <img src="<?php echo e(asset('assets/images/layouts/page-1/ppt.png')); ?>" class="img-fluid" alt="PowerPoint">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('assets/images/layouts/page-1/pdf.png')); ?>" class="img-fluid" alt="Document">
                                <?php endif; ?>
                            </div>
                            <h6><?php echo e(Str::limit($doc->original_name, 20)); ?></h6>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">No documents yet. Upload your first file!</p>
                        <button class="btn btn-primary mt-2" data-toggle="modal" data-target="#uploadModal">
                            <i class="ri-upload-line"></i> Upload Files
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Folders Section -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Folders</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <div class="dropdown">
                            <span class="dropdown-toggle dropdown-bg btn bg-white" id="dropdownMenuButton1" data-toggle="dropdown">
                                Name<i class="ri-arrow-down-s-line ml-1"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton1">
                                <a class="dropdown-item" href="#">Last modified</a>
                                <a class="dropdown-item" href="#">Last modified by me</a>
                                <a class="dropdown-item" href="#">Last opened by me</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($recentFolders->count() > 0): ?>
            <?php
                $colors = ['danger', 'primary', 'info', 'success'];
            ?>
            <?php $__currentLoopData = $recentFolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-sm-6 col-lg-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>" class="folder">
                                <div class="icon-small bg-<?php echo e($colors[$index % 4]); ?> rounded mb-4">
                                    <i class="ri-file-copy-line"></i>
                                </div>
                            </a>
                            <div class="card-header-toolbar">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownFolder<?php echo e($folder->id); ?>" data-toggle="dropdown">
                                        <i class="ri-more-2-fill"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFolder<?php echo e($folder->id); ?>">
                                        <a class="dropdown-item" href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>"><i class="ri-eye-fill mr-2"></i>View</a>
                                        <a class="dropdown-item" href="<?php echo e(route('cloudbox.folders.edit', $folder->id)); ?>"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                        <form action="<?php echo e(route('cloudbox.folders.favorite', $folder->id)); ?>" method="POST" style="display: inline; width: 100%;">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="dropdown-item">
                                                <i class="ri-star-<?php echo e($folder->is_favorite ? 'fill' : 'line'); ?> mr-2 text-warning"></i>
                                                <?php echo e($folder->is_favorite ? 'Remove from Favorites' : 'Add to Favorites'); ?>

                                            </button>
                                        </form>
                                        <a class="dropdown-item" href="#"><i class="ri-printer-fill mr-2"></i>Print</a>
                                        <a class="dropdown-item" href="#"><i class="ri-file-download-fill mr-2"></i>Download</a>
                                        <div class="dropdown-divider"></div>
                                        <form action="<?php echo e(route('cloudbox.folders.destroy', $folder->id)); ?>" method="POST" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ri-delete-bin-6-fill mr-2"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>" class="folder">
                            <h5 class="mb-2"><?php echo e($folder->name); ?></h5>
                            <p class="mb-2"><i class="lar la-clock text-<?php echo e($colors[$index % 4]); ?> mr-2 font-size-20"></i> <?php echo e($folder->created_at->format('M d, Y')); ?></p>
                            <p class="mb-0"><i class="las la-file-alt text-<?php echo e($colors[$index % 4]); ?> mr-2 font-size-20"></i> <?php echo e($folder->files_count); ?> Files</p>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-folder-line" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">No folders yet. Create your first folder!</p>
                        <button class="btn btn-primary mt-2" data-toggle="modal" data-target="#createFolderModal">
                            <i class="ri-folder-add-line"></i> New Folder
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Files Table & Storage -->
        <div class="col-lg-8 col-xl-8">
            <div class="card card-block card-stretch card-height files-table">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Recent Files</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <a href="<?php echo e(route('cloudbox.files')); ?>" class="view-more">View All</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless tbl-server-info">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Last Edit</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row" onclick="window.location='<?php echo e(route('cloudbox.files.view', $file->id)); ?>'" style="cursor: pointer;">
                                    <td>
                                        <?php if(str_contains($file->mime_type, 'pdf')): ?>
                                            <i class="ri-file-pdf-line text-danger mr-2"></i>
                                        <?php elseif(str_contains($file->mime_type, 'word')): ?>
                                            <i class="ri-file-word-line text-primary mr-2"></i>
                                        <?php elseif(str_contains($file->mime_type, 'excel') || str_contains($file->mime_type, 'spreadsheet')): ?>
                                            <i class="ri-file-excel-line text-success mr-2"></i>
                                        <?php elseif(str_contains($file->mime_type, 'image')): ?>
                                            <i class="ri-image-line text-info mr-2"></i>
                                        <?php else: ?>
                                            <i class="ri-file-line mr-2"></i>
                                        <?php endif; ?>
                                        <?php echo e(Str::limit($file->original_name, 30)); ?>

                                    </td>
                                    <td><?php echo e(number_format($file->size / 1024 / 1024, 2)); ?> MB</td>
                                    <td><?php echo e($file->created_at->diffForHumans()); ?></td>
                                    <td onclick="event.stopPropagation();">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownFile<?php echo e($file->id); ?>" data-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFile<?php echo e($file->id); ?>">
                                                <a class="dropdown-item" href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>">
                                                    <i class="ri-eye-line mr-2"></i>View
                                                </a>
                                                <form action="<?php echo e(route('cloudbox.files.favorite', $file->id)); ?>" method="POST" style="display: inline; width: 100%;">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ri-star-<?php echo e($file->is_favorite ? 'fill' : 'line'); ?> mr-2 text-warning"></i>
                                                        <?php echo e($file->is_favorite ? 'Remove from Favorites' : 'Add to Favorites'); ?>

                                                    </button>
                                                </form>
                                                <a class="dropdown-item" href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>">
                                                    <i class="ri-download-line mr-2"></i>Download
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?php echo e(route('cloudbox.files.delete', $file->id)); ?>" method="POST" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-line mr-2"></i>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="mt-3 text-muted">No files yet</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Card -->
        <div class="col-lg-4">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Storage</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="mb-3"><?php echo e(number_format($storageUsed / 1024 / 1024 / 1024, 2)); ?> GB</h2>
                        <p class="mb-4 text-muted">of 100 GB Used</p>
                        <div class="iq-progress-bar mb-3">
                            <span class="bg-primary iq-progress progress-1 dashboard-storage-bar" data-percent="<?php echo e(number_format(min(($storageUsed / 1024 / 1024 / 1024 / 100) * 100, 100), 2)); ?>" style="width: 0%; transition: width 1s ease;"></span>
                        </div>
                        <p class="mb-0"><?php echo e(number_format((100 - ($storageUsed / 1024 / 1024 / 1024)), 2)); ?> GB Free</p>
                    </div>
                    <hr>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="ri-file-line text-primary mr-2"></i>Documents</span>
                            <span class="font-weight-bold"><?php echo e($totalFiles); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="ri-folder-line text-info mr-2"></i>Folders</span>
                            <span class="font-weight-bold"><?php echo e($totalFolders); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="ri-share-line text-success mr-2"></i>Shared</span>
                            <span class="font-weight-bold"><?php echo e($sharedFiles); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hoạt ảnh thanh tiến độ lưu trữ trên dashboard
    setTimeout(function() {
        const dashboardBar = document.querySelector('.dashboard-storage-bar');
        if (dashboardBar) {
            const percent = parseFloat(dashboardBar.getAttribute('data-percent')) || 0;
            dashboardBar.style.width = percent + '%';
        }
    }, 100);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/dashboard.blade.php ENDPATH**/ ?>