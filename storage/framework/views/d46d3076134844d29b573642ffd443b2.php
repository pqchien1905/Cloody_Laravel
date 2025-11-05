

<?php $__env->startSection('title', 'Shared Files'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Custom tab styling */
    .nav-tabs .nav-link {
        border: none;
        background: transparent;
        color: #6c757d;
        border-bottom: 2px solid transparent;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #007bff;
        background: transparent;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff !important;
        background: transparent !important;
        border-bottom: 2px solid #007bff !important;
        font-weight: 600;
        box-shadow: none !important;
    }
    
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Share With Me</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($tab === 'with-me' ? 'active' : ''); ?>" 
                               href="<?php echo e(route('cloudbox.shared', ['tab' => 'with-me'])); ?>">
                                <i class="ri-share-forward-line mr-2"></i>Shared with me
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($tab === 'by-me' ? 'active' : ''); ?>" 
                               href="<?php echo e(route('cloudbox.shared', ['tab' => 'by-me'])); ?>">
                                <i class="ri-share-line mr-2"></i>Shared by me
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4">
                        <?php if($shares->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <?php if($tab === 'with-me'): ?>
                                            <th scope="col">Shared By</th>
                                            <th scope="col">Permission</th>
                                        <?php else: ?>
                                            <th scope="col">Shared With</th>
                                            <th scope="col">Permission</th>
                                        <?php endif; ?>
                                        <th scope="col">Shared Date</th>
                                        <th scope="col">Size</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $shares; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $share): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($share->type === 'folder'): ?>
                                                    
                                                    <i class="ri-folder-line text-primary mr-3" style="font-size: 24px;"></i>
                                                    <a href="<?php echo e(route('cloudbox.folders.show', $share->folder->id)); ?>" class="text-dark">
                                                        <span><?php echo e($share->folder->name); ?></span>
                                                    </a>
                                                <?php else: ?>
                                                    
                                                    <?php
                                                        $iconClass = 'ri-file-line';
                                                        $iconColor = 'text-secondary';
                                                        
                                                        if ($share->file && in_array($share->file->extension, ['pdf'])) {
                                                            $iconClass = 'ri-file-pdf-line';
                                                            $iconColor = 'text-danger';
                                                        } elseif ($share->file && in_array($share->file->extension, ['doc', 'docx'])) {
                                                            $iconClass = 'ri-file-word-line';
                                                            $iconColor = 'text-primary';
                                                        } elseif ($share->file && in_array($share->file->extension, ['xls', 'xlsx'])) {
                                                            $iconClass = 'ri-file-excel-line';
                                                            $iconColor = 'text-success';
                                                        } elseif ($share->file && in_array($share->file->extension, ['ppt', 'pptx'])) {
                                                            $iconClass = 'ri-file-ppt-line';
                                                            $iconColor = 'text-warning';
                                                        } elseif ($share->file && in_array($share->file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                            $iconClass = 'ri-image-line';
                                                            $iconColor = 'text-info';
                                                        } elseif ($share->file && in_array($share->file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                                            $iconClass = 'ri-video-line';
                                                            $iconColor = 'text-danger';
                                                        }
                                                    ?>
                                                    
                                                    <i class="<?php echo e($iconClass); ?> <?php echo e($iconColor); ?> mr-3" style="font-size: 24px;"></i>
                                                    <?php if($share->file): ?>
                                                    <a href="<?php echo e(route('cloudbox.files.view', $share->file->id)); ?>" class="text-dark">
                                                        <span><?php echo e($share->file->original_name); ?></span>
                                                    </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <?php if($tab === 'with-me'): ?>
                                            <td>
                                                <?php if($share->sharedBy): ?>
                                                    <?php echo e($share->sharedBy->name); ?>

                                                <?php elseif($share->type === 'file' && $share->file): ?>
                                                    <?php echo e($share->file->user->name ?? 'Unknown'); ?>

                                                <?php elseif($share->type === 'folder' && $share->folder): ?>
                                                    <?php echo e($share->folder->user->name ?? 'Unknown'); ?>

                                                <?php endif; ?>
                                            </td>
                                        <?php else: ?>
                                            <td>
                                                <?php if($share->sharedWith): ?>
                                                    <?php echo e($share->sharedWith->name); ?> (<?php echo e($share->sharedWith->email); ?>)
                                                <?php else: ?>
                                                    <span class="text-muted">Public Link</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if($share->permission === 'view'): ?>
                                                <span class="badge badge-primary">
                                                    <i class="ri-eye-line"></i> View Only
                                                </span>
                                            <?php elseif($share->permission === 'edit'): ?>
                                                <span class="badge badge-success">
                                                    <i class="ri-pencil-line"></i> Can Edit
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-info">
                                                    <i class="ri-download-line"></i> Can Download
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($share->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <?php if($share->type === 'folder'): ?>
                                                <span class="text-muted">-</span>
                                            <?php elseif($share->file): ?>
                                                <?php echo e(number_format($share->file->size / 1024, 2)); ?> KB
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                                <?php if($share->type === 'folder'): ?>
                                                    
                                                    <a href="<?php echo e(route('cloudbox.folders.show', $share->folder->id)); ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="View">
                                                        <i class="ri-folder-open-line"></i>
                                                    </a>
                                                <?php else: ?>
                                                    
                                                    <?php if($share->file): ?>
                                                    <a href="<?php echo e(route('cloudbox.files.download', $share->file->id)); ?>" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Download">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if($tab === 'by-me'): ?>
                                                <a href="#" 
                                                   onclick="event.preventDefault(); if(confirm('Revoke share access?')) document.getElementById('revoke-form-<?php echo e($share->type); ?>-<?php echo e($share->id); ?>').submit();"
                                                   class="btn btn-sm btn-danger" 
                                                   title="Revoke">
                                                    <i class="ri-close-line"></i>
                                                </a>

                                                <?php if($share->type === 'folder'): ?>
                                                <form id="revoke-form-folder-<?php echo e($share->id); ?>" action="<?php echo e(route('cloudbox.shares.revoke', $share->id)); ?>" method="POST" style="display: none;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="type" value="folder">
                                                </form>
                                                <?php else: ?>
                                                <form id="revoke-form-file-<?php echo e($share->id); ?>" action="<?php echo e(route('cloudbox.shares.revoke', $share->id)); ?>" method="POST" style="display: none;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="type" value="file">
                                                </form>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing <?php echo e($shares->firstItem() ?? 0); ?> to <?php echo e($shares->lastItem() ?? 0); ?> of <?php echo e($shares->total()); ?> shares
                            </div>
                            <div>
                                <?php echo e($shares->appends(['tab' => $tab])->links()); ?>

                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="las la-share-alt" style="font-size: 64px; color: #ccc;"></i>
                            <?php if($tab === 'with-me'): ?>
                                <h4 class="mt-3">No Files Shared With You</h4>
                                <p class="text-muted">Files that others share with you will appear here.</p>
                            <?php else: ?>
                                <h4 class="mt-3">You Haven't Shared Any Files</h4>
                                <p class="text-muted">Files you share with others will appear here.</p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/shared.blade.php ENDPATH**/ ?>