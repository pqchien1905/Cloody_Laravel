

<?php $__env->startSection('title', 'Manage Users - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Users</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal"><i class="las la-plus mr-1"></i> New User</button>
        </div>

        <div class="col-12">
            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('status')); ?>

                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Name or email..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="role" class="small text-muted mb-1">Role</label>
                            <select class="form-control" id="role" name="role">
                                <option value="">All Users</option>
                                <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Admin Only</option>
                                <option value="user" <?php echo e(request('role') === 'user' ? 'selected' : ''); ?>>Regular Users</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">Sort By</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_desc" <?php echo e(request('sort') === 'created_desc' ? 'selected' : ''); ?>>Newest First</option>
                                <option value="created_asc" <?php echo e(request('sort') === 'created_asc' ? 'selected' : ''); ?>>Oldest First</option>
                                <option value="name_asc" <?php echo e(request('sort') === 'name_asc' ? 'selected' : ''); ?>>Name A-Z</option>
                                <option value="name_desc" <?php echo e(request('sort') === 'name_desc' ? 'selected' : ''); ?>>Name Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="las la-search mr-1"></i> Filter</button>
                        </div>
                    </form>
                    <?php if(request()->hasAny(['search', 'role', 'sort'])): ?>
                        <div class="mt-2">
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="las la-times mr-1"></i> Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-block card-stretch card-height">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Admin</th>
                                    <th>Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php if($user->avatar): ?>
                                            <img src="<?php echo e(asset($user->avatar)); ?>" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php if($user->is_admin): ?>
                                            <span class="badge badge-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($user->created_at?->format('Y-m-d')); ?></td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editUserModal<?php echo e($user->id); ?>"><i class="las la-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteUserModal<?php echo e($user->id); ?>"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if(method_exists($users, 'links')): ?>
                    <div class="card-footer"><?php echo e($users->links()); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.users.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="create_name">Name</label>
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

                    <div class="form-group">
                        <label for="create_email">Email</label>
                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="create_email" name="email" value="<?php echo e(old('email')); ?>" required>
                        <?php $__errorArgs = ['email'];
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

                    <div class="form-group">
                        <label for="create_password">Password</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="create_password" name="password" required>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="create_password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="create_is_admin" name="is_admin" value="1" <?php echo e(old('is_admin') ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="create_is_admin">Administrator</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editUserModal<?php echo e($user->id); ?>" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel<?php echo e($user->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel<?php echo e($user->id); ?>">Edit User: <?php echo e($user->name); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name<?php echo e($user->id); ?>">Name</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name<?php echo e($user->id); ?>" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
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

                    <div class="form-group">
                        <label for="email<?php echo e($user->id); ?>">Email</label>
                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email<?php echo e($user->id); ?>" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                        <?php $__errorArgs = ['email'];
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

                    <div class="form-group">
                        <label for="password<?php echo e($user->id); ?>">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password<?php echo e($user->id); ?>" name="password">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Leave blank if you don't want to change password</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_admin<?php echo e($user->id); ?>" name="is_admin" value="1" <?php echo e(old('is_admin', $user->is_admin) ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="is_admin<?php echo e($user->id); ?>">Administrator</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal<?php echo e($user->id); ?>" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel<?php echo e($user->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteUserModalLabel<?php echo e($user->id); ?>">Delete User</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete user <strong><?php echo e($user->name); ?></strong> (<?php echo e($user->email); ?>)?</p>
                    <p class="text-muted">All files, folders, and data associated with this user will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto open create modal if there are validation errors for new user
    <?php if($errors->any() && !old('_method')): ?>
        $('#createUserModal').modal('show');
    <?php endif; ?>

    // Auto open edit modal if there are validation errors
    <?php if($errors->any() && old('_method') === 'PUT'): ?>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(old('user_id') == $user->id || request()->route('user') == $user->id): ?>
                $('#editUserModal<?php echo e($user->id); ?>').modal('show');
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/admin/users/index.blade.php ENDPATH**/ ?>