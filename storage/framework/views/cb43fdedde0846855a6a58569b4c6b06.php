

<?php $__env->startSection('title', 'Recent Files'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <!-- Documents Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Documents</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <div class="card-header-toolbar">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn bg-white" id="dropdownMenuButton001" data-toggle="dropdown">
                                    Name<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Last modified</a>
                                    <a class="dropdown-item" href="#">Last modified by me</a>
                                    <a class="dropdown-item" href="#">Last opened by me</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Grid (File Cards with Thumbnails) -->
        <?php
            $documentFiles = $files->take(4); // Get first 4 files for document cards
        ?>
        
        <?php $__currentLoopData = $documentFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-body image-thumb">
                    <a href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>">
                        <div class="mb-4 text-center p-3 rounded iq-thumb">
                            <div class="iq-image-overlay"></div>
                            <?php
                                $isImage = in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                                
                                if ($isImage) {
                                    // Hiển thị hình ảnh thực cho các tệp ảnh
                                    $imageUrl = asset('storage/' . $file->path);
                                } else {
                                    // Hiển thị hình thu nhỏ cho các tệp không phải ảnh
                                    $thumbnail = 'layouts/page-1/pdf.png';
                                    if (in_array($file->extension, ['doc', 'docx'])) {
                                        $thumbnail = 'layouts/page-1/doc.png';
                                    } elseif (in_array($file->extension, ['xls', 'xlsx'])) {
                                        $thumbnail = 'layouts/page-1/xlsx.png';
                                    } elseif (in_array($file->extension, ['ppt', 'pptx'])) {
                                        $thumbnail = 'layouts/page-1/ppt.png';
                                    } elseif (in_array($file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                        $thumbnail = 'layouts/page-1/video.png';
                                    } elseif (in_array($file->extension, ['mp3', 'wav', 'ogg'])) {
                                        $thumbnail = 'layouts/page-1/mp3.png';
                                    }
                                    $imageUrl = asset('assets/images/' . $thumbnail);
                                }
                            ?>
                            
                            <?php if($isImage): ?>
                                <img src="<?php echo e($imageUrl); ?>" class="img-fluid" alt="<?php echo e($file->original_name); ?>" style="max-height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?php echo e($imageUrl); ?>" class="img-fluid" alt="<?php echo e($file->original_name); ?>">
                            <?php endif; ?>
                        </div>
                        <h6><?php echo e($file->original_name); ?></h6>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- Folders Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Folders</h4>
                    </div>
                    <div class="card-header-toolbar">
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

        <!-- Folders Grid -->
        <?php
            $folderColors = ['danger', 'primary', 'info', 'success'];
            $colorIndex = 0;
        ?>
        
        <?php $__currentLoopData = $recentFolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6 col-sm-6 col-lg-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>" class="folder">
                            <div class="icon-small bg-<?php echo e($folderColors[$colorIndex % 4]); ?> rounded mb-4">
                                <i class="ri-file-copy-line"></i>
                            </div>
                        </a>
                        <div class="card-header-toolbar">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton<?php echo e($folder->id); ?>" data-toggle="dropdown">
                                    <i class="ri-more-2-fill"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton<?php echo e($folder->id); ?>">
                                    <a class="dropdown-item" href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>">
                                        <i class="ri-eye-fill mr-2"></i>View
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('Move to trash?')) document.getElementById('delete-folder-form-<?php echo e($folder->id); ?>').submit();">
                                        <i class="ri-delete-bin-6-fill mr-2"></i>Delete
                                    </a>
                                    <a class="dropdown-item" href="<?php echo e(route('cloudbox.folders.edit', $folder->id)); ?>">
                                        <i class="ri-pencil-fill mr-2"></i>Edit
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ri-printer-fill mr-2"></i>Print
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ri-file-download-fill mr-2"></i>Download
                                    </a>
                                </div>
                            </div>
                            
                            <form id="delete-folder-form-<?php echo e($folder->id); ?>" action="<?php echo e(route('cloudbox.folders.destroy', $folder->id)); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                            </form>
                        </div>
                    </div>
                    <a href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>" class="folder">
                        <h5 class="mb-2"><?php echo e($folder->name); ?></h5>
                        <p class="mb-2">
                            <i class="lar la-clock text-<?php echo e($folderColors[$colorIndex % 4]); ?> mr-2 font-size-20"></i> 
                            <?php echo e($folder->created_at->format('d M, Y')); ?>

                        </p>
                        <p class="mb-0">
                            <i class="las la-file-alt text-<?php echo e($folderColors[$colorIndex % 4]); ?> mr-2 font-size-20"></i> 
                            <?php echo e($folder->files_count); ?> Files
                        </p>
                    </a>
                </div>
            </div>
        </div>
        <?php $colorIndex++; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- Files Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Files</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files Table -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless tbl-server-info">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Owner</th>
                                    <th scope="col">Last Edit</th>
                                    <th scope="col">File Size</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                                $iconClass = 'ri-file-line';
                                                $iconBg = 'bg-secondary';
                                                
                                                if (in_array($file->extension, ['pdf'])) {
                                                    $iconClass = 'las la-file-pdf';
                                                    $iconBg = 'bg-danger';
                                                } elseif (in_array($file->extension, ['doc', 'docx'])) {
                                                    $iconClass = 'las la-file-word';
                                                    $iconBg = 'bg-primary';
                                                } elseif (in_array($file->extension, ['xls', 'xlsx'])) {
                                                    $iconClass = 'las la-file-excel';
                                                    $iconBg = 'bg-success';
                                                } elseif (in_array($file->extension, ['ppt', 'pptx'])) {
                                                    $iconClass = 'las la-file-powerpoint';
                                                    $iconBg = 'bg-warning';
                                                } elseif (in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                    $iconClass = 'las la-image';
                                                    $iconBg = 'bg-info';
                                                } elseif (in_array($file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                                    $iconClass = 'las la-video';
                                                    $iconBg = 'bg-danger';
                                                }
                                            ?>
                                            
                                            <div class="icon-small <?php echo e($iconBg); ?> rounded mr-3">
                                                <i class="<?php echo e($iconClass); ?>"></i>
                                            </div>
                                            <div>
                                                <a href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>" style="cursor: pointer;">
                                                    <?php echo e($file->original_name); ?>

                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e($file->user->name ?? 'Me'); ?></td>
                                    <td><?php echo e($file->created_at->format('M d, Y')); ?></td>
                                    <td><?php echo e(number_format($file->size / 1024 / 1024, 2)); ?> MB</td>
                                    <td>
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton<?php echo e($file->id); ?>" data-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton<?php echo e($file->id); ?>">
                                                <a class="dropdown-item" href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>">
                                                    <i class="ri-eye-fill mr-2"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('Move to trash?')) document.getElementById('delete-form-<?php echo e($file->id); ?>').submit();">
                                                    <i class="ri-delete-bin-6-fill mr-2"></i>Delete
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="ri-pencil-fill mr-2"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="ri-printer-fill mr-2"></i>Print
                                                </a>
                                                <a class="dropdown-item" href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>">
                                                    <i class="ri-file-download-fill mr-2"></i>Download
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <form id="delete-form-<?php echo e($file->id); ?>" action="<?php echo e(route('cloudbox.files.delete', $file->id)); ?>" method="POST" style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel\resources\views/pages/recent.blade.php ENDPATH**/ ?>