

<?php $__env->startSection('title', 'Manage Categories - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">File Categories</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCategoryModal"><i class="las la-plus mr-1"></i> New Category</button>
        </div>

        <div class="col-12">
            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('status')); ?>

                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.categories.index')); ?>" class="row align-items-end">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Category name or description..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label for="status" class="small text-muted mb-1">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Categories</option>
                                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active Only</option>
                                <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="las la-search mr-1"></i> Filter</button>
                        </div>
                    </form>
                    <?php if(request()->hasAny(['search', 'status'])): ?>
                        <div class="mt-2">
                            <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="las la-times mr-1"></i> Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Order</th>
                                    <th>Icon</th>
                                    <th>Name</th>
                                    <th>Extensions</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><span class="badge badge-light"><?php echo e($category->order); ?></span></td>
                                    <td>
                                        <?php if($category->icon): ?>
                                            <div class="icon-small rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: <?php echo e($category->color); ?>20;">
                                                <i class="<?php echo e($category->icon); ?> font-size-20" style="color: <?php echo e($category->color); ?>;"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="icon-small bg-secondary-light rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-folder-line font-size-20 text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo e($category->name); ?></strong>
                                        <?php if($category->description): ?>
                                            <br><small class="text-muted"><?php echo e(Str::limit($category->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($category->extensions): ?>
                                            <?php $__currentLoopData = array_slice($category->extensions, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ext): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge badge-info"><?php echo e($ext); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(count($category->extensions) > 5): ?>
                                                <span class="badge badge-secondary">+<?php echo e(count($category->extensions) - 5); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($category->is_active): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editCategoryModal<?php echo e($category->id); ?>"><i class="las la-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteCategoryModal<?php echo e($category->id); ?>"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if(method_exists($categories, 'links')): ?>
                    <div class="card-footer"><?php echo e($categories->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="create_name" name="name" value="<?php echo e(old('name')); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_slug">Slug</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="create_slug" name="slug" value="<?php echo e(old('slug')); ?>" placeholder="Auto-generated if empty">
                                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">Leave blank to auto-generate from name</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_icon">Icon Class</label>
                                <input type="text" class="form-control" id="create_icon" name="icon" value="<?php echo e(old('icon')); ?>" placeholder="e.g., ri-file-pdf-line">
                                <small class="text-muted">Remixicon class name</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_color">Color</label>
                                <input type="color" class="form-control" id="create_color" name="color" value="<?php echo e(old('color', '#667eea')); ?>" style="height: 38px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_order">Order</label>
                                <input type="number" class="form-control" id="create_order" name="order" value="<?php echo e(old('order', 0)); ?>" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="create_extensions">File Extensions</label>
                        <input type="text" class="form-control" id="create_extensions" name="extensions" value="<?php echo e(old('extensions')); ?>" placeholder="e.g., pdf, doc, docx">
                        <small class="text-muted">Comma-separated list of extensions</small>
                    </div>

                    <div class="form-group">
                        <label for="create_description">Description</label>
                        <textarea class="form-control" id="create_description" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="create_is_active" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="create_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editCategoryModal<?php echo e($category->id); ?>" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel<?php echo e($category->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel<?php echo e($category->id); ?>">Edit Category: <?php echo e($category->name); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.categories.update', $category)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="category_id" value="<?php echo e($category->id); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name<?php echo e($category->id); ?>">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name<?php echo e($category->id); ?>" name="name" value="<?php echo e(old('name', $category->name)); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug<?php echo e($category->id); ?>">Slug</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="slug<?php echo e($category->id); ?>" name="slug" value="<?php echo e(old('slug', $category->slug)); ?>">
                                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon<?php echo e($category->id); ?>">Icon Class</label>
                                <input type="text" class="form-control" id="icon<?php echo e($category->id); ?>" name="icon" value="<?php echo e(old('icon', $category->icon)); ?>" placeholder="e.g., ri-file-pdf-line">
                                <small class="text-muted">Remixicon class name</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="color<?php echo e($category->id); ?>">Color</label>
                                <input type="color" class="form-control" id="color<?php echo e($category->id); ?>" name="color" value="<?php echo e(old('color', $category->color)); ?>" style="height: 38px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="order<?php echo e($category->id); ?>">Order</label>
                                <input type="number" class="form-control" id="order<?php echo e($category->id); ?>" name="order" value="<?php echo e(old('order', $category->order)); ?>" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="extensions<?php echo e($category->id); ?>">File Extensions</label>
                        <input type="text" class="form-control" id="extensions<?php echo e($category->id); ?>" name="extensions" value="<?php echo e(old('extensions', is_array($category->extensions) ? implode(', ', $category->extensions) : '')); ?>" placeholder="e.g., pdf, doc, docx">
                        <small class="text-muted">Comma-separated list of extensions</small>
                    </div>

                    <div class="form-group">
                        <label for="description<?php echo e($category->id); ?>">Description</label>
                        <textarea class="form-control" id="description<?php echo e($category->id); ?>" name="description" rows="3"><?php echo e(old('description', $category->description)); ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active<?php echo e($category->id); ?>" name="is_active" value="1" <?php echo e(old('is_active', $category->is_active) ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="is_active<?php echo e($category->id); ?>">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal<?php echo e($category->id); ?>" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel<?php echo e($category->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteCategoryModalLabel<?php echo e($category->id); ?>">Delete Category</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete category <strong><?php echo e($category->name); ?></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto open create modal if there are validation errors for new category
    <?php if($errors->any() && !old('_method')): ?>
        $('#createCategoryModal').modal('show');
    <?php endif; ?>

    // Auto open edit modal if there are validation errors
    <?php if($errors->any() && old('_method') === 'PUT'): ?>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(old('category_id') == $category->id): ?>
                $('#editCategoryModal<?php echo e($category->id); ?>').modal('show');
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/admin/categories/index.blade.php ENDPATH**/ ?>