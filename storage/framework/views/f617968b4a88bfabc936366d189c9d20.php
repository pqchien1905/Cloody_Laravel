

<?php $__env->startSection('title', 'User Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(session('status')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php if(session('status') == 'profile-updated'): ?>
                Profile updated successfully!
            <?php elseif(session('status') == 'password-updated'): ?>
                Password updated successfully!
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?php echo e(Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('assets/images/user/1.jpg')); ?>" alt="profile-img" class="avatar-130 img-fluid rounded-circle">
                    <h4 class="mb-2 mt-3"><?php echo e(Auth::user()->name); ?></h4>
                    <p class="mb-2"><?php echo e(Auth::user()->email); ?></p>
                    <?php if(Auth::user()->phone): ?>
                        <p class="mb-2"><i class="las la-phone mr-1"></i><?php echo e(Auth::user()->phone); ?></p>
                    <?php endif; ?>
                    <?php if(Auth::user()->address): ?>
                        <p class="mb-2 text-muted"><i class="las la-map-marker mr-1"></i><?php echo e(Str::limit(Auth::user()->address, 50)); ?></p>
                    <?php endif; ?>
                    <p class="text-muted">Member since <?php echo e(Auth::user()->created_at->format('M Y')); ?></p>
                    <p class="mb-2"><strong><?php echo e($totalFiles); ?></strong> files • <strong><?php echo e($totalFolders); ?></strong> folders</p>
                    <?php if(Auth::user()->bio): ?>
                        <p class="mt-3 text-muted" style="font-size: 14px;"><?php echo e(Str::limit(Auth::user()->bio, 100)); ?></p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary btn-sm">Edit Profile</a>
                        <a href="<?php echo e(route('cloudbox.dashboard')); ?>" class="btn btn-secondary btn-sm">Dashboard</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Storage Info</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-2">Used: <strong><?php echo e(number_format($storageUsedGB, 2)); ?> GB</strong> of <?php echo e($storageLimit); ?> GB</p>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e(number_format($storagePercent, 1)); ?>%" aria-valuenow="<?php echo e($storagePercent); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="ri-folder-line mr-2 text-primary"></i> Documents: <?php echo e(number_format($documentsGB, 2)); ?> GB</li>
                        <li class="mb-2"><i class="ri-image-line mr-2 text-success"></i> Photos: <?php echo e(number_format($imagesGB, 2)); ?> GB</li>
                        <li class="mb-2"><i class="ri-video-line mr-2 text-danger"></i> Videos: <?php echo e(number_format($videosGB, 2)); ?> GB</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Profile Information</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Full Name</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->name); ?>

                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->email); ?>

                        </div>
                    </div>
                    <?php if(Auth::user()->phone): ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Phone</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->phone); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(Auth::user()->address): ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Address</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->address); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(Auth::user()->bio): ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Bio</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->bio); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Joined</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?php echo e(Auth::user()->created_at->format('F d, Y')); ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-primary">Edit Profile</a>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#updatePasswordModal">Update Password</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <?php if($recentFiles->count() > 0 || $recentFolders->count() > 0): ?>
                    <div class="iq-timeline">
                        <?php $__currentLoopData = $recentFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-file-line text-primary"></i></div>
                            <div class="timeline-content">
                                <h6>Uploaded: <?php echo e(Str::limit($file->original_name, 30)); ?></h6>
                                <p class="text-muted mb-0"><?php echo e($file->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $recentFolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-folder-add-line text-success"></i></div>
                            <div class="timeline-content">
                                <h6>Created folder: <?php echo e($folder->name); ?></h6>
                                <p class="text-muted mb-0"><?php echo e($folder->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted py-4">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Password Modal -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('password.update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="current_password" name="current_password" required>
                        <?php $__errorArgs = ['current_password', 'updatePassword'];
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
                        <label for="password">New Password</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" name="password" required>
                        <?php $__errorArgs = ['password', 'updatePassword'];
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
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteAccountModalLabel">Delete Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('profile.destroy')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    <p>Please enter your password to confirm you would like to permanently delete your account.</p>
                    
                    <div class="form-group">
                        <label for="password_delete">Password</label>
                        <input type="password" class="form-control <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password_delete" name="password" required>
                        <?php $__errorArgs = ['password', 'userDeletion'];
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto open password modal if there are password update errors
    <?php if($errors->updatePassword->any()): ?>
        $('#updatePasswordModal').modal('show');
    <?php endif; ?>
    
    // Auto open delete account modal if there are user deletion errors
    <?php if($errors->userDeletion->any()): ?>
        $('#deleteAccountModal').modal('show');
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/user/profile.blade.php ENDPATH**/ ?>