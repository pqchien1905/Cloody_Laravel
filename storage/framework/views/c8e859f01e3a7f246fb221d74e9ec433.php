

<?php $__env->startSection('title', 'Favorites - CloudBOX'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">
                            <i class="ri-star-fill text-warning"></i> Favorites
                        </h4>
                    </div>
                    
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <!-- Favorite Folders Section -->
                    <?php if(isset($favoriteFolders) && $favoriteFolders->count() > 0): ?>
                        <h5 class="mb-3"><i class="ri-folder-star-line"></i> Favorite Folders (<?php echo e($favoriteFolders->count()); ?>)</h5>
                        <div class="row mb-4">
                            <?php $__currentLoopData = $favoriteFolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <a href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>" class="folder">
                                                <div class="icon-small rounded mb-3" style="background-color: <?php echo e($folder->color ?? '#3498db'); ?>20;">
                                                    <i class="ri-folder-3-fill" style="color: <?php echo e($folder->color ?? '#3498db'); ?>; font-size: 24px;"></i>
                                                </div>
                                            </a>
                                            <div class="card-header-toolbar">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownFolder<?php echo e($folder->id); ?>" data-toggle="dropdown" style="cursor: pointer;">
                                                        <i class="ri-more-2-fill"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFolder<?php echo e($folder->id); ?>">
                                                        <a class="dropdown-item" href="<?php echo e(route('cloudbox.folders.show', $folder->id)); ?>">
                                                            <i class="ri-eye-fill mr-2"></i>View
                                                        </a>
                                                        <form action="<?php echo e(route('cloudbox.folders.favorite', $folder->id)); ?>" method="POST" style="display: inline; width: 100%;">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-star-fill mr-2 text-warning"></i>Remove from Favorites
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="<?php echo e(route('cloudbox.folders.destroy', $folder->id)); ?>" method="POST" style="display: inline; width: 100%;">
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
                                            <h6 class="mb-2">
                                                <?php echo e($folder->name); ?>

                                                <i class="ri-star-fill text-warning ml-1"></i>
                                            </h6>
                                            <p class="mb-2 text-muted small">
                                                <i class="ri-file-line mr-1"></i> <?php echo e($folder->files_count ?? 0); ?> Files
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="ri-time-line mr-1"></i> <?php echo e($folder->created_at->diffForHumans()); ?>

                                            </p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <!-- Favorite Files Section -->
                    <h5 class="mb-3"><i class="ri-file-star-line"></i> Favorite Files (<?php echo e($files->count()); ?>)</h5>
                    <?php if($files->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th>File Name</th>
                                        <th>Folder</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Added</th>
                                        <th class="text-center actions-col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>" class="d-flex align-items-center text-body" style="text-decoration: none;">
                                                    <?php
                                                        $iconClass = 'ri-file-line';
                                                        $iconColor = 'text-muted';
                                                        if(Str::contains($file->mime_type, 'pdf')) {
                                                            $iconClass = 'ri-file-pdf-line';
                                                            $iconColor = 'text-danger';
                                                        } elseif(Str::contains($file->mime_type, 'word')) {
                                                            $iconClass = 'ri-file-word-line';
                                                            $iconColor = 'text-primary';
                                                        } elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) {
                                                            $iconClass = 'ri-file-excel-line';
                                                            $iconColor = 'text-success';
                                                        } elseif(Str::contains($file->mime_type, 'image')) {
                                                            $iconClass = 'ri-image-line';
                                                            $iconColor = 'text-info';
                                                        }
                                                    ?>
                                                    <i class="<?php echo e($iconClass); ?> font-size-20 <?php echo e($iconColor); ?>"></i>
                                                    <span class="ml-3">
                                                        <?php echo e($file->original_name); ?>

                                                        <i class="ri-star-fill text-warning ml-1"></i>
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if($file->folder): ?>
                                                    <a href="<?php echo e(route('cloudbox.folders.show', $file->folder->id)); ?>"><?php echo e($file->folder->name); ?></a>
                                                <?php else: ?>
                                                    <span class="text-muted">Root</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(strtoupper($file->extension)); ?></td>
                                            <td><?php echo e($file->formatted_size); ?></td>
                                            <td><?php echo e($file->created_at->diffForHumans()); ?></td>
                                            <td class="text-center actions-col">
                                                <div class="d-flex align-items-center list-user-action">
                                                    <a class="action-icon text-primary" href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>" 
                                                       data-toggle="tooltip" title="Download">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('cloudbox.files.favorite', $file->id)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="action-icon bg-transparent border-0 text-warning" 
                                                                data-toggle="tooltip" title="Remove from favorites">
                                                            <i class="ri-star-fill"></i>
                                                        </button>
                                                    </form>
                                                    <form action="<?php echo e(route('cloudbox.files.delete', $file->id)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="action-icon bg-transparent border-0 text-danger" 
                                                                data-toggle="tooltip" title="Delete">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <?php echo e($files->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ri-star-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">No favorite files yet</h5>
                            <p class="text-muted">Star files to add them to your favorites</p>
                        </div>
                    <?php endif; ?>

                    <?php if((isset($favoriteFolders) ? $favoriteFolders->count() : 0) == 0 && $files->count() == 0): ?>
                        <div class="text-center py-5">
                            <i class="ri-star-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">No favorites yet</h5>
                            <p class="text-muted">Star files and folders to add them to your favorites</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Remove background/shadow behind action icons on Favorites table */
.list-user-action .action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: transparent !important;
    box-shadow: none !important;
}
.list-user-action { justify-content: center; gap: 8px; }
/* Ensure Actions column is centered */
.table .actions-col { text-align: center; }

/* Hover effect on file names - make text bold */
.table tbody tr:hover td a span {
    font-weight: 600;
    color: #333;
}
.table tbody tr {
    transition: background-color 0.2s ease;
}
.table tbody tr:hover {
    background-color: #f8f9fa;
}

.list-user-action .action-icon:hover,
.list-user-action .action-icon:focus {
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
}
.list-user-action .action-icon i { font-size: 18px; }
.list-user-action button.action-icon { padding: 0; }
/* In case old classes exist somewhere, neutralize their background here */
.list-user-action .iq-bg-primary,
.list-user-action .iq-bg-warning,
.list-user-action .iq-bg-danger {
    background: transparent !important;
    box-shadow: none !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()

    // Chỉ sử dụng tooltip trên trang này; không có tải lên từ Favorites
    })
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel\resources\views/pages/favorites.blade.php ENDPATH**/ ?>