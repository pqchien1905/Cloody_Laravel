

<?php $__env->startSection('title', 'Files - CloudBOX'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-primary-light">
                            <i class="ri-file-line text-primary"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0"><?php echo e($stats['total']); ?></h5>
                            <p class="mb-0 text-muted">Total Files</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-success-light">
                            <i class="ri-folder-line text-success"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0"><?php echo e($stats['folders']); ?></h5>
                            <p class="mb-0 text-muted">Folders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-warning-light">
                            <i class="ri-star-line text-warning"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0"><?php echo e($stats['favorites']); ?></h5>
                            <p class="mb-0 text-muted">Favorites</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-info-light">
                            <i class="ri-database-line text-info"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0"><?php echo e(number_format($stats['size'] / 1048576, 2)); ?> MB</h5>
                            <p class="mb-0 text-muted">Storage Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">All Files</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModal">
                            <i class="ri-upload-line"></i> Upload File
                        </button>
                    </div>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="<?php echo e(route('cloudbox.files')); ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search files..." value="<?php echo e(request('search')); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?category='+this.value+(this.value?'':'')">
                                <option value="">All Types</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->slug); ?>" <?php echo e(request('category') == $category->slug ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?folder_id='+this.value">
                                <option value="">All Folders</option>
                                <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($folder->id); ?>" <?php echo e(request('folder_id') == $folder->id ? 'selected' : ''); ?>>
                                        <?php echo e($folder->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" onchange="window.location.href='?sort='+this.value">
                                <option value="created_at" <?php echo e(request('sort') == 'created_at' ? 'selected' : ''); ?>>Newest</option>
                                <option value="name" <?php echo e(request('sort') == 'name' ? 'selected' : ''); ?>>Name</option>
                                <option value="size" <?php echo e(request('sort') == 'size' ? 'selected' : ''); ?>>Size</option>
                            </select>
                        </div>
                    </div>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <?php if($files->count() > 0): ?>
                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-primary d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong><span id="selectedCount">0</span> file(s) selected</strong></span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                        <i class="ri-delete-bin-line"></i> Delete Selected
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" id="clearSelectionBtn">Clear Selection</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th scope="col" style="width: 40px;">
                                            <input type="checkbox" id="selectAllFiles">
                                        </th>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Folder</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Modified</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td onclick="event.stopPropagation();">
                                                <input type="checkbox" class="file-checkbox" value="<?php echo e($file->id); ?>">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
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
                                                        } elseif(Str::contains($file->mime_type, 'video')) {
                                                            $iconClass = 'ri-video-line';
                                                            $iconColor = 'text-warning';
                                                        }
                                                    ?>
                                                    <i class="<?php echo e($iconClass); ?> font-size-20 <?php echo e($iconColor); ?>"></i>
                                                    <div class="ml-3">
                                                        <a href="<?php echo e(route('cloudbox.files.view', $file->id)); ?>" class="font-weight-500">
                                                            <?php echo e($file->original_name); ?>

                                                        </a>
                                                        <?php if($file->is_favorite): ?>
                                                            <i class="ri-star-fill text-warning ml-1"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($file->folder): ?>
                                                    <a href="<?php echo e(route('cloudbox.folders.show', $file->folder->id)); ?>">
                                                        <?php echo e($file->folder->name); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(strtoupper($file->extension)); ?></td>
                                            <td><?php echo e($file->formatted_size); ?></td>
                                            <td><?php echo e($file->updated_at->diffForHumans()); ?></td>
                                            <td class="actions-cell">
                                                <div class="dropdown">
                                                    <span class="action-dots" id="dropdownFile<?php echo e($file->id); ?>" data-toggle="dropdown">
                                                        <i class="ri-more-2-fill font-size-20"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFile<?php echo e($file->id); ?>">
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editFileModal<?php echo e($file->id); ?>" onclick="event.preventDefault();">
                                                            <i class="ri-pencil-fill mr-2"></i>Edit
                                                        </a>
                                                        <form action="<?php echo e(route('cloudbox.files.favorite', $file->id)); ?>" method="POST" style="display: inline; width: 100%;">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-star-<?php echo e($file->is_favorite ? 'fill' : 'line'); ?> mr-2 text-warning"></i>
                                                                <?php echo e($file->is_favorite ? 'Remove from Favorites' : 'Add to Favorites'); ?>

                                                            </button>
                                                        </form>
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#shareFileModal<?php echo e($file->id); ?>" onclick="event.preventDefault();">
                                                            <i class="ri-share-line mr-2"></i>Share
                                                        </a>
                                                        <a class="dropdown-item" href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>">
                                                            <i class="ri-file-download-fill mr-2"></i>Download
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="<?php echo e(route('cloudbox.files.delete', $file->id)); ?>" method="POST" style="display: inline; width: 100%;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-6-fill mr-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit File Modal -->
                                        <div class="modal fade" id="editFileModal<?php echo e($file->id); ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Rename File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?php echo e(route('cloudbox.files.update', $file->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="file_name<?php echo e($file->id); ?>">File Name <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="file_name<?php echo e($file->id); ?>" 
                                                                       name="name" value="<?php echo e(pathinfo($file->original_name, PATHINFO_FILENAME)); ?>" required>
                                                                <small class="form-text text-muted">Extension: .<?php echo e($file->extension); ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Rename</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Share File Modal -->
                                        <div class="modal fade" id="shareFileModal<?php echo e($file->id); ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Share File</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?php echo e(route('cloudbox.files.share', $file->id)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="share_email<?php echo e($file->id); ?>">Recipient Email <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" id="share_email<?php echo e($file->id); ?>" name="email" required placeholder="Enter recipient's email">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Share</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            <?php echo e($files->links('pagination::bootstrap-4')); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="ri-file-list-3-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">No files found</h5>
                            <p class="text-muted">Upload your first file to get started</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModal">
                                <i class="ri-upload-line"></i> Upload File
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Upload Modal -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .action-dots {
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .action-dots:hover {
        background-color: rgba(0, 0, 0, 0.08);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .action-dots i {
        transition: color 0.3s ease;
    }
    
    .action-dots:hover i {
        color: #000;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    // Tự động ẩn cảnh báo sau 5 giây (chỉ ẩn alert-success, alert-danger, ... giữ alert-light luôn hiển thị)
    setTimeout(function() {
        $('.alert:not(#bulkActionsBar):not(.alert-light)').fadeOut('slow');
    }, 5000);

    // Xử lý chọn nhiều mục
    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
    const selectAllFiles = document.getElementById('selectAllFiles');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.file-checkbox:checked');
        if (checked.length > 0) {
            bulkActionsBar.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkActionsBar.classList.add('d-none');
        }
    }

    // Checkbox chọn tất cả
    if (selectAllFiles) {
        selectAllFiles.addEventListener('change', function() {
            fileCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Checkbox từng mục
    fileCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkActions();
            // Cập nhật trạng thái 'chọn tất cả'
            const allChecked = Array.from(fileCheckboxes).every(c => c.checked);
            const someChecked = Array.from(fileCheckboxes).some(c => c.checked);
            if (selectAllFiles) {
                selectAllFiles.checked = allChecked;
                selectAllFiles.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Xóa lựa chọn
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            fileCheckboxes.forEach(cb => cb.checked = false);
            if (selectAllFiles) selectAllFiles.checked = false;
            updateBulkActions();
        });
    }

    // Xóa hàng loạt
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checked = Array.from(document.querySelectorAll('.file-checkbox:checked'));
            if (checked.length === 0) return;

            const count = checked.length;
            if (!confirm(`Move ${count} file(s) to trash?`)) return;

            // Tạo form và gửi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("cloudbox.files.bulk-delete")); ?>';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfInput);

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'file_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/files.blade.php ENDPATH**/ ?>