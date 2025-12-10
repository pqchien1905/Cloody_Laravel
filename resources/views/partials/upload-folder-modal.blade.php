<!-- Upload Folder Modal -->
<div class="modal fade" id="uploadFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-folder-upload-line"></i> {{ __('common.upload_folder') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadFolderForm" action="{{ route('cloody.folders.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="folder_paths" name="folder_paths" value="">
                <input type="hidden" id="folder_conflict_action" name="conflict_action" value="">
                <input type="hidden" id="folder_favorite_on_upload" name="favorite_on_upload" value="">
                <div class="modal-body">
                    <!-- Folder Selection -->
                    <div class="form-group">
                        <label for="folder_input">{{ __('common.select_folder_from_computer') }} <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" id="folder_input" name="files[]" webkitdirectory directory multiple 
                                   class="custom-file-input" required>
                            <label class="custom-file-label" for="folder_input" id="folder_input_label">
                                {{ __('common.choose_a_folder') }}
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('common.maximum_file_size_per_file', ['size' => 100]) }}
                        </small>
                    </div>

                    <!-- Upload Preview -->
                    <div id="uploadFolderPreview" class="upload-preview-success d-none mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong><i class="ri-folder-line"></i> {{ __('common.selected_folder') }}</strong>
                            <span id="folderName" class="badge badge-primary"></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="ri-file-list-line"></i> <span id="folderFileCount">0</span> {{ __('common.files_in_folders') }} 
                                <i class="ri-folder-2-line"></i> <span id="folderCount">0</span> {{ __('common.folder_s') }}
                            </small>
                        </div>
                        <div id="folderFileList" class="border rounded p-2" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
                            <!-- File list will be populated here -->
                        </div>
                    </div>

                    <!-- Upload Progress Bar -->
                    <div id="folderUploadProgress" class="d-none mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">{{ __('common.uploading') }}</small>
                            <small class="text-muted"><span id="folderProgressPercent">0</span>%</small>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="folderProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="form-group mt-3">
                        <label>{{ __('common.folder_privacy') }} <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="upload_folder_privacy_private" name="is_public" value="0" 
                                   class="custom-control-input" checked>
                            <label class="custom-control-label" for="upload_folder_privacy_private">
                                <i class="ri-lock-line"></i> {{ __('common.private') }}
                                <small class="d-block text-muted">{{ __('common.only_you_can_access_this_folder') }}</small>
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="upload_folder_privacy_public" name="is_public" value="1" 
                                   class="custom-control-input">
                            <label class="custom-control-label" for="upload_folder_privacy_public">
                                <i class="ri-global-line"></i> {{ __('common.public') }}
                                <small class="d-block text-muted">{{ __('common.anyone_with_link_can_view') }}</small>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="upload_folder_description">{{ __('common.description') }}</label>
                        <textarea id="upload_folder_description" name="description" class="form-control" rows="3" placeholder="{{ __('common.optional_description_for_folder') }}"></textarea>
                        <small class="form-text text-muted">{{ __('common.description_will_be_applied_to_root_folder') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="uploadFolderBtn" disabled>
                        <i class="ri-upload-2-line"></i> {{ __('common.upload_folder') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    if (window.uploadFolderModalInitialized) {
        console.log('Upload Folder modal already initialized, skipping...');
        return;
    }
    window.uploadFolderModalInitialized = true;
    console.log('Initializing Upload Folder modal...');

document.addEventListener('DOMContentLoaded', function() {
    // Phát hiện các phần tử modal trùng lặp để chẩn đoán
    const folderModals = document.querySelectorAll('#uploadFolderModal');
    const folderForms = document.querySelectorAll('#uploadFolderForm');
    console.log('Number of uploadFolderModal elements:', folderModals.length);
    console.log('Number of uploadFolderForm elements:', folderForms.length);
    if (folderModals.length > 1 || folderForms.length > 1) {
        console.error('PHÁT HIỆN MODAL THƯ MỤC TRÙNG LẶP! Điều này có thể gây tải lên hai lần.');
    }
    const folderInput = document.getElementById('folder_input');
    const folderInputLabel = document.getElementById('folder_input_label');
    const uploadPreview = document.getElementById('uploadFolderPreview');
    const fileList = document.getElementById('folderFileList');
    const fileCount = document.getElementById('folderFileCount');
    const folderCount = document.getElementById('folderCount');
    const folderName = document.getElementById('folderName');
    const uploadBtn = document.getElementById('uploadFolderBtn');
    const uploadForm = document.getElementById('uploadFolderForm');
    const folderPathsInput = document.getElementById('folder_paths');
    const uploadProgress = document.getElementById('folderUploadProgress');
    const progressBar = document.getElementById('folderProgressBar');
    const progressPercent = document.getElementById('folderProgressPercent');
    const conflictActionInput = document.getElementById('folder_conflict_action');
    const descriptionInput = document.getElementById('upload_folder_description');
    
    let selectedFiles = [];
    let isHandlingConflict = false; // Flag to prevent reset during conflict handling

    if (folderInput) {
        folderInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            selectedFiles = files;
            
            if (files.length === 0) {
                uploadPreview.classList.add('d-none');
                uploadBtn.disabled = true;
                return;
            }

            // Thu thập tất cả đường dẫn file
            const filePaths = {};
            files.forEach((file, index) => {
                const relativePath = file.webkitRelativePath || file.name;
                filePaths[index] = relativePath;
            });
            
            // Lưu các đường dẫn dưới dạng JSON vào trường ẩn
            folderPathsInput.value = JSON.stringify(filePaths);

            // Lấy tên thư mục gốc từ đường dẫn của file đầu tiên
            const firstPath = files[0].webkitRelativePath || files[0].name;
            const rootFolderName = firstPath.split('/')[0];
            
            // Đếm các thư mục duy nhất
            const folders = new Set();
            files.forEach(file => {
                const path = file.webkitRelativePath || file.name;
                const parts = path.split('/');
                for (let i = 0; i < parts.length - 1; i++) {
                    folders.add(parts.slice(0, i + 1).join('/'));
                }
            });

            // Cập nhật giao diện
            folderName.textContent = rootFolderName;
            fileCount.textContent = files.length;
            folderCount.textContent = folders.size;
            folderInputLabel.textContent = `${rootFolderName} (${files.length} files)`;

            // Hiển thị danh sách xem trước (15 file đầu)
            fileList.innerHTML = '';
            const displayFiles = files.slice(0, 15);
            displayFiles.forEach(file => {
                const path = file.webkitRelativePath || file.name;
                const size = (file.size / 1024).toFixed(2);
                const div = document.createElement('div');
                div.className = 'small mb-1';
                div.innerHTML = `<i class="ri-file-line text-muted"></i> ${path} <span class="text-muted">(${size} KB)</span>`;
                fileList.appendChild(div);
            });

            if (files.length > 15) {
                const more = document.createElement('div');
                more.className = 'small text-muted mt-2';
                more.innerHTML = `<i class="ri-more-line"></i> {{ __('common.and_more_files', ['count' => '']) }}`.replace(':count', files.length - 15);
                fileList.appendChild(more);
            }

            uploadPreview.classList.remove('d-none');
            uploadBtn.disabled = false;
        });
    }

    // Xử lý gửi form kèm tiến trình
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get root folder name to check for duplicates
            if (selectedFiles.length > 0 && folderPathsInput.value) {
                const paths = JSON.parse(folderPathsInput.value);
                const firstPath = paths[0] || '';
                const rootFolderName = firstPath.split('/')[0];
                
                // Check if folder already exists
                fetch('{{ route("cloody.folders.check-duplicates") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        folder_name: rootFolderName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // Hiển thị modal xung đột
                        document.getElementById('conflictFolderName').textContent = rootFolderName;
                        isHandlingConflict = true; // Set flag before hiding modal
                        $('#uploadFolderModal').modal('hide');
                        $('#folderConflictModal').modal('show');
                    } else {
                        // No conflict, proceed with upload
                        document.getElementById('folder_conflict_action').value = 'merge';
                        proceedWithUpload();
                    }
                })
                .catch(error => {
                    console.error('Error checking duplicates:', error);
                    // Proceed anyway
                    proceedWithUpload();
                });
            } else {
                proceedWithUpload();
            }
        });
    }
    
    // Handle continue upload button
    document.getElementById('continueUploadBtn')?.addEventListener('click', function() {
        const selectedChoice = document.querySelector('input[name="conflictChoice"]:checked')?.value || 'replace';
        document.getElementById('conflict_action').value = selectedChoice;
        $('#folderConflictModal').modal('hide');
        
        // Show upload modal back and proceed
        setTimeout(function() {
            $('#uploadFolderModal').modal('show');
            proceedWithUpload();
            isHandlingConflict = false; // Reset flag after upload starts
        }, 300);
    });
    
    // Highlight selected option
    document.getElementById('replaceOption')?.addEventListener('click', function() {
        document.getElementById('replaceRadio').checked = true;
        updateConflictSelection();
    });
    
    document.getElementById('mergeOption')?.addEventListener('click', function() {
        document.getElementById('mergeRadio').checked = true;
        updateConflictSelection();
    });
    
    function updateConflictSelection() {
        const replaceOption = document.getElementById('replaceOption');
        const mergeOption = document.getElementById('mergeOption');
        const isReplace = document.getElementById('replaceRadio')?.checked;
        
        if (isReplace) {
            replaceOption.style.backgroundColor = '#e7f3ff';
            replaceOption.style.borderColor = '#2196F3';
            mergeOption.style.backgroundColor = '';
            mergeOption.style.borderColor = '#dee2e6';
        } else {
            mergeOption.style.backgroundColor = '#e7f3ff';
            mergeOption.style.borderColor = '#2196F3';
            replaceOption.style.backgroundColor = '';
            replaceOption.style.borderColor = '#dee2e6';
        }
    }
    
    // Initialize selection styling on modal show
    $('#folderConflictModal').on('shown.bs.modal', function() {
        updateConflictSelection();
    });
    
    // Initialize selection styling
    document.querySelectorAll('input[name="conflictChoice"]').forEach(radio => {
        radio.addEventListener('change', updateConflictSelection);
    });
    
    function proceedWithUpload() {
        // Use selectedFiles variable that was saved when folder was selected
        if (!selectedFiles || selectedFiles.length === 0) {
            alert('{{ __('common.no_files_selected') }}');
            isHandlingConflict = false; // Reset flag
            $('#folderConflictModal').modal('hide');
            $('#uploadFolderModal').modal('show');
            return;
        }
        
        // Show progress bar
        uploadProgress.classList.remove('d-none');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="ri-loader-4-line"></i> {{ __('common.uploading') }}';
        
        // Create FormData manually to ensure we have the files
        const formData = new FormData();
        
        // Add all files from selectedFiles array
        selectedFiles.forEach((file) => {
            formData.append('files[]', file);
        });
        
    // Add other form data
    formData.append('folder_paths', folderPathsInput.value);
    // match the actual input name used in the form (is_public)
    formData.append('is_public', document.querySelector('input[name="is_public"]:checked')?.value || '0');
    formData.append('conflict_action', conflictActionInput?.value || 'merge');
    formData.append('description', descriptionInput?.value || '');
    formData.append('_token', '{{ csrf_token() }}');
        
        // Use XMLHttpRequest to track progress
        const xhr = new XMLHttpRequest();
        
        // Track upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressPercent.textContent = percentComplete;
            }
        });
        
        // Handle completion
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Success - reload page
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                    } else {
                        alert(response.message || '{{ __('common.upload_failed') }}');
                        resetUploadState();
                    }
                } catch (e) {
                    alert('{{ __('common.upload_failed') }}');
                    resetUploadState();
                }
            } else {
                // Error
                alert('{{ __('common.upload_failed') }}');
                resetUploadState();
            }
        });
        
        // Handle error
        xhr.addEventListener('error', function() {
            alert('{{ __('common.upload_error') }}');
            resetUploadState();
        });
        
        // Send request
        xhr.open('POST', uploadForm.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    }
    
    function resetUploadState() {
        uploadProgress.classList.add('d-none');
    progressBar.style.width = '0%';
    progressPercent.textContent = '0';
    progressBar.classList.add('progress-bar-animated');
    progressBar.classList.remove('bg-success');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="ri-upload-2-line"></i> {{ __('common.upload_folder') }}';
    }

    // Reset on modal close
    $('#uploadFolderModal').on('hidden.bs.modal', function () {
        // Don't reset if we're handling conflict (modal will reopen)
        if (isHandlingConflict) {
            return;
        }
        
        if (uploadForm) {
            uploadForm.reset();
        }
        selectedFiles = [];
        uploadPreview.classList.add('d-none');
        resetUploadState();
        folderInputLabel.textContent = '{{ __('common.choose_a_folder') }}';
        fileList.innerHTML = '';
        folderPathsInput.value = '';
    });
});
})();
</script>
@endpush

<!-- Folder Conflict Modal -->
<div class="modal fade" id="folderConflictModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-error-warning-line text-warning"></i> {{ __('common.replace_or_keep') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    {{ __('common.folder_exists', ['name' => '<span id="conflictFolderName"></span>']) }}
                </p>
                
                <div class="custom-control custom-radio mb-3 p-3 border rounded" style="cursor: pointer;" id="replaceOption">
                    <input type="radio" id="replaceRadio" name="conflictChoice" value="replace" class="custom-control-input" checked>
                    <label class="custom-control-label w-100" for="replaceRadio" style="cursor: pointer;">
                        <strong class="d-block">{{ __('common.replace_existing_folder') }}</strong>
                        <small class="text-muted">{{ __('common.current_version_will_be_overwritten') }}</small>
                    </label>
                </div>
                
                <div class="custom-control custom-radio p-3 border rounded" style="cursor: pointer;" id="mergeOption">
                    <input type="radio" id="mergeRadio" name="conflictChoice" value="merge" class="custom-control-input">
                    <label class="custom-control-label w-100" for="mergeRadio" style="cursor: pointer;">
                        <strong class="d-block">{{ __('common.keep_both_folders') }}</strong>
                        <small class="text-muted">{{ __('common.new_folder_will_be_created') }}</small>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel_upload') }}</button>
                <button type="button" class="btn btn-primary" id="continueUploadBtn">
                    <i class="ri-upload-2-line"></i> {{ __('common.continue_upload') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.upload-preview-success {
    background-color: #f6fef9;
    border: 1px solid #c3e6cb;
    color: #155724;
    border-radius: 6px;
    padding: 16px;
}

/* Conflict modal styling */
#replaceOption, #mergeOption {
    transition: all 0.2s ease;
}

#replaceOption:hover, #mergeOption:hover {
    background-color: #f8f9fa;
}

.custom-control-label {
    padding-top: 2px;
}
</style>
@endpush
