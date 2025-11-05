<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-folder-add-line"></i> Create New Folder
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('cloudbox.folders.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="parent_id" value="<?php echo e($currentFolderId ?? null); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="folder_name">Folder Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               id="folder_name" name="name" placeholder="Enter folder name" required autofocus>
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
                        <label for="folder_color">Folder Color</label>
                        <input type="color" class="form-control" id="folder_color" name="color" value="#3498db" style="height: 45px;">
                        <small class="form-text text-muted">Choose a color for your folder icon</small>
                    </div>
                    <div class="form-group">
                        <label for="folder_description">Description (Optional)</label>
                        <textarea class="form-control" id="folder_description" name="description" 
                                  rows="3" placeholder="Add a description for this folder..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Privacy Settings <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="create_folder_privacy_private" name="is_public" value="0" 
                                   class="custom-control-input" checked>
                            <label class="custom-control-label" for="create_folder_privacy_private">
                                <i class="ri-lock-line"></i> Private
                                <small class="d-block text-muted">Only you can access this folder</small>
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="create_folder_privacy_public" name="is_public" value="1" 
                                   class="custom-control-input">
                            <label class="custom-control-label" for="create_folder_privacy_public">
                                <i class="ri-global-line"></i> Public
                                <small class="d-block text-muted">Anyone with the link can view</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-folder-add-line"></i> Create Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/partials/create-folder-modal.blade.php ENDPATH**/ ?>