<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadFileModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFileModalLabel">
                    <i class="ri-file-upload-line"></i> Upload Files
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cloudbox.files.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <input type="hidden" name="conflict_action" id="conflict_action" value="">
                <input type="hidden" name="favorite_on_upload" id="favorite_on_upload" value="">
                <div class="modal-body">
                    <!-- Upload to Folder Selection -->
                    @if(isset($currentFolderId))
                        <input type="hidden" id="upload_folder_id" name="folder_id" value="{{ $currentFolderId }}">
                    @else
                    <div class="form-group">
                        <label for="upload_folder_id">Upload to Folder (Optional)</label>
                        <select class="form-control" id="upload_folder_id" name="folder_id">
                            <option value="">Root / My Files</option>
                            @if(isset($folders))
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    @endif

                    <!-- Drag & Drop Zone -->
                    <div class="upload-zone" id="uploadZone">
                        <i class="ri-upload-cloud-2-line font-size-64 text-primary"></i>
                        <h5 class="mt-3">Drag & Drop files here</h5>
                        <p class="text-muted">or click to browse</p>
                        <input type="file" 
                               class="d-none" 
                               id="fileInput" 
                               name="files[]" 
                               accept="*/*"
                               multiple
                               required>
                        <button type="button" class="btn btn-primary mt-3" id="browseBtn">
                            <i class="ri-folder-open-line"></i> Browse Files
                        </button>
                    </div>

                    <!-- File Preview -->
                    <div id="filePreview" class="mt-3 d-none">
                        <div class="upload-preview-success border-0">
                            <div class="d-flex align-items-start">
                                <i class="ri-checkbox-circle-line font-size-24 mr-3 text-success"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2"><strong id="fileCount"></strong></h6>
                                    <ul class="mb-0" id="fileList" style="list-style: none; padding-left: 0;"></ul>
                                </div>
                                <button type="button" class="btn btn-sm btn-light" onclick="clearFile()" title="Remove all">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="mt-3 d-none">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 id="progressBar" 
                                 style="width: 0%">0%</div>
                        </div>
                        <p class="text-center mt-2 text-muted">Uploading...</p>
                    </div>

                    <!-- Upload Limits Info -->
                    <div class="alert alert-light mt-3">
                        <small class="text-muted">
                            <i class="ri-information-line"></i> 
                            Maximum file size: <strong>100 MB</strong> | 
                            Supported formats: All file types
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <i class="ri-upload-line"></i> Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Resolution Modal -->
<div id="conflictModal" class="conflict-overlay d-none">
    <div class="conflict-card">
        <div class="d-flex align-items-start mb-3">
            <i class="ri-alert-line font-size-32 text-warning mr-3"></i>
            <div>
                <h5 class="mb-2">Replace or keep existing files?</h5>
                <p class="text-muted mb-0" id="conflictMessage"></p>
            </div>
        </div>
        
        <div class="custom-control custom-radio mb-3 conflict-option">
            <input type="radio" id="conflict_replace" name="conflict_choice" value="replace" class="custom-control-input" checked>
            <label class="custom-control-label" for="conflict_replace">
                <strong>Replace existing files</strong>
                <small class="d-block text-muted">The current version will be overwritten</small>
            </label>
        </div>
        <div class="custom-control custom-radio mb-4 conflict-option">
            <input type="radio" id="conflict_keep_both" name="conflict_choice" value="keep_both" class="custom-control-input">
            <label class="custom-control-label" for="conflict_keep_both">
                <strong>Keep both files</strong>
                <small class="d-block text-muted">Files will be renamed (e.g., File (1).docx, File (2).docx)</small>
            </label>
        </div>
        
        <div class="d-flex justify-content-end align-items-center">
            <a href="#" class="mr-3 text-muted" id="conflict-cancel">Cancel upload</a>
            <button type="button" class="btn btn-primary px-4" id="conflict-confirm">
                <i class="ri-upload-line mr-1"></i> Continue Upload
            </button>
        </div>
    </div>
</div>

<style>
.upload-zone {
    border: 2px dashed #3498db;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    background-color: #e3f2fd;
    border-color: #2980b9;
}

.upload-zone.dragover {
    background-color: #d1ecf1;
    border-color: #17a2b8;
    border-style: solid;
}

.upload-zone-inner {
    pointer-events: none;
}

.conflict-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1070;
}

.conflict-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.25);
    width: 540px;
    max-width: calc(100% - 32px);
    padding: 32px;
}

.conflict-card h5 {
    font-weight: 600;
    font-size: 1.25rem;
    color: #212529;
}

.conflict-card .btn-primary {
    border-radius: 4px;
    padding: 10px 20px;
    font-weight: 500;
}

.conflict-card a#conflict-cancel {
    font-size: 15px;
    text-decoration: none;
    font-weight: 500;
}

.conflict-card a#conflict-cancel:hover {
    text-decoration: underline;
}

.conflict-option {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s;
    cursor: pointer;
}

.conflict-option:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.conflict-option .custom-control-input:checked ~ .custom-control-label {
    color: #007bff;
}

.conflict-option:has(.custom-control-input:checked) {
    background-color: #e7f3ff;
    border-color: #007bff;
}
/* Upload preview success (not auto-dismiss) */
.upload-preview-success {
    background-color: #f6fef9;
    color: #218838;
    border-radius: 6px;
    padding: 16px 18px 12px 18px;
    margin-bottom: 0;
    font-size: 1rem;
    box-shadow: none;
}
</style>

@push('scripts')
<script>
(function() {
    // Ngăn chặn khởi tạo nhiều lần
    if (window.uploadModalInitialized) {
        console.log('Upload modal already initialized, skipping...');
        return;
    }
    window.uploadModalInitialized = true;
    console.log('Initializing upload modal...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - setting up upload modal');
    
    // Kiểm tra các form/modal trùng lặp
    const allModals = document.querySelectorAll('#uploadFileModal');
    const allForms = document.querySelectorAll('#uploadForm');
    console.log('Number of uploadFileModal elements:', allModals.length);
    console.log('Number of uploadForm elements:', allForms.length);
    
    if (allModals.length > 1 || allForms.length > 1) {
        console.error('DUPLICATE MODALS DETECTED! This will cause double upload.');
    }
    
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    const fileCount = document.getElementById('fileCount');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const browseBtn = document.getElementById('browseBtn');
    const uploadZone = document.getElementById('uploadZone');

    if (!fileInput || !browseBtn) {
        console.log('Upload modal elements not found');
        return; // Elements not found, exit
    }

    // Xử lý click nút mở file picker - chỉ một handler
    browseBtn.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Browse button clicked');
        fileInput.click();
    };

    // Xử lý kéo & thả
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    // Khi chọn file
    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        if (fileInput.files && fileInput.files.length > 0) {
            // Tạo danh sách các file đã chọn (hiển thị tối đa 10 để dễ nhìn)
            const files = Array.from(fileInput.files);
            fileList.innerHTML = '';
            
            const maxShow = Math.min(files.length, 10);
            files.slice(0, maxShow).forEach((f, index) => {
                const li = document.createElement('li');
                li.className = 'd-flex align-items-center mb-1 py-1';
                li.innerHTML = `
                    <i class="ri-file-line mr-2 text-primary"></i>
                    <span class="flex-grow-1">${f.name}</span>
                    <small class="text-muted ml-2">${formatFileSize(f.size)}</small>
                `;
                fileList.appendChild(li);
            });
            
            if (files.length > maxShow) {
                const more = document.createElement('li');
                more.className = 'text-muted mt-2';
                more.innerHTML = `<i class="ri-more-line mr-1"></i> and ${files.length - maxShow} more file(s)`;
                fileList.appendChild(more);
            }
            
            fileCount.textContent = `${files.length} file(s) ready to upload`;
            filePreview.classList.remove('d-none');
            uploadBtn.disabled = false;
            
            // Cuộn đến vùng xem trước
            filePreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    window.clearFile = function() {
        fileInput.value = '';
        filePreview.classList.add('d-none');
        fileList.innerHTML = '';
        fileCount.textContent = '';
        uploadBtn.disabled = true;
    }

    // Xử lý gửi form kèm tiến trình
    const uploadForm = document.getElementById('uploadForm');
    let isSubmitting = false;
    
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) {
            console.log('Form already submitting, preventing duplicate');
            return false;
        }
        
        console.log('Form submit triggered');
        
        // Kiểm tra file trùng trước khi tải lên
        checkForDuplicates().then(duplicates => {
            if (duplicates.length > 0) {
                showConflictModal(duplicates);
            } else {
                // No conflicts, submit directly
                submitForm();
            }
        });
    });

    function checkForDuplicates() {
        return new Promise((resolve) => {
            const files = Array.from(fileInput.files);
            if (files.length === 0) {
                resolve([]);
                return;
            }

            // Get current folder files list (we'll send AJAX to check)
            const folderId = document.getElementById('upload_folder_id').value;
            
            fetch(`{{ route('cloudbox.files.check-duplicates') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    filenames: files.map(f => f.name),
                    folder_id: folderId || null
                })
            })
            .then(res => res.json())
            .then(data => {
                resolve(data.duplicates || []);
            })
            .catch(() => {
                // If check fails, proceed without conflict check
                resolve([]);
            });
        });
    }

    function showConflictModal(duplicates) {
        const message = duplicates.length === 1 
            ? `The file <strong>${duplicates[0]}</strong> already exists in this location.`
            : `<strong>${duplicates.length} files</strong> already exist in this location:<br><ul class="mt-2 mb-0">${duplicates.slice(0, 5).map(d => `<li>${d}</li>`).join('')}${duplicates.length > 5 ? `<li class="text-muted">...and ${duplicates.length - 5} more</li>` : ''}</ul>`;
        
        document.getElementById('conflictMessage').innerHTML = message;
        document.getElementById('conflictModal').classList.remove('d-none');
        
        // Reset radio selection
        document.getElementById('conflict_replace').checked = true;
    }

    function submitForm() {
        if (isSubmitting) {
            console.log('Already submitting, skipping');
            return;
        }
        
        isSubmitting = true;
        console.log('Starting form submission');
        
        uploadProgress.classList.remove('d-none');
        uploadBtn.disabled = true;
        
        let progress = 0;
        const interval = setInterval(function() {
            progress += 10;
            progressBar.style.width = progress + '%';
            progressBar.textContent = progress + '%';
            
            if (progress >= 90) {
                clearInterval(interval);
            }
        }, 200);
        
        uploadForm.submit();
    }

    // Conflict modal handlers
    document.getElementById('conflict-confirm').addEventListener('click', function() {
        const choice = document.querySelector('input[name="conflict_choice"]:checked').value;
        document.getElementById('conflict_action').value = choice;
        document.getElementById('conflictModal').classList.add('d-none');
        submitForm();
    });

    document.getElementById('conflict-cancel').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('conflictModal').classList.add('d-none');
    });

    // Reset modal on close
    $('#uploadFileModal').on('hidden.bs.modal', function() {
        clearFile();
        uploadProgress.classList.add('d-none');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        uploadBtn.disabled = true;
        isSubmitting = false; // Reset submit flag
        console.log('Modal closed, reset isSubmitting flag');
    });
});
})(); // End of IIFE
</script>
@endpush
