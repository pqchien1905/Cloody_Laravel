@extends('layouts.app')

@section('title', 'Trash - CloudBOX')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">
                            <i class="ri-delete-bin-line text-danger"></i> Trash
                        </h4>
                    </div>
                    @if($files->count() > 0 || $folders->count() > 0)
                        <div class="card-header-toolbar d-flex flex-column align-items-end text-right">
                            <div class="alert alert-warning mb-2">
                                <small><i class="ri-information-line"></i> Items in trash will be automatically deleted after 30 days</small>
                            </div>
                            <button id="btn-clean-trash" class="btn btn-danger">
                                <i class="ri-delete-bin-6-line"></i> Clean up the trash
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" action="{{ route('cloudbox.trash') }}" class="mb-3">
                        <div class="form-row">
                            <div class="col-md-4 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search in trash..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-control" name="item" onchange="this.form.submit()">
                                    <option value="all" {{ request('item', 'all') == 'all' ? 'selected' : '' }}>All Items</option>
                                    <option value="files" {{ request('item') == 'files' ? 'selected' : '' }}>Files only</option>
                                    <option value="folders" {{ request('item') == 'folders' ? 'selected' : '' }}>Folders only</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-control" name="type" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="documents" {{ request('type') == 'documents' ? 'selected' : '' }}>Documents</option>
                                    <option value="images" {{ request('type') == 'images' ? 'selected' : '' }}>Images</option>
                                    <option value="videos" {{ request('type') == 'videos' ? 'selected' : '' }}>Videos</option>
                                    <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <select class="form-control" name="sort" onchange="this.form.submit()">
                                    <option value="trashed_at" {{ request('sort', 'trashed_at') == 'trashed_at' ? 'selected' : '' }}>Deleted date</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="size" {{ request('sort') == 'size' ? 'selected' : '' }}>Size (files)</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    @if($files->count() > 0 || $folders->count() > 0)
                        <!-- Folders Section -->
                        @if(request('item', 'all') !== 'files' && $folders->count() > 0)
                            <h5 class="mb-3"><i class="ri-folder-line"></i> Folders</h5>
                            <!-- Bulk Actions Bar: Folders in Trash -->
                            <div id="trashBulkBarFolders" class="alert alert-primary d-none mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong><span id="trashSelectedFolders">0</span> folder(s) selected</strong></span>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-success" id="trashRestoreFoldersBtn">
                                            <i class="ri-arrow-go-back-line"></i> Restore Selected
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="trashDeleteFoldersBtn">
                                            <i class="ri-delete-bin-6-line"></i> Delete Permanently
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" id="trashClearFoldersBtn">Clear Selection</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover mb-0">
                                    <thead class="table-color-heading">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox" id="trashSelectAllFolders"></th>
                                            <th>Folder Name</th>
                                            <th>Items</th>
                                            <th>Deleted</th>
                                            <th>Days Remaining</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($folders as $folder)
                                            <tr>
                                                <td><input type="checkbox" class="trash-folder-checkbox" value="{{ $folder->id }}"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-folder-3-fill font-size-20 mr-2" style="color: {{ $folder->color ?? '#3498db' }}"></i>
                                                        <div>{{ $folder->name }}</div>
                                                    </div>
                                                </td>
                                                <td>{{ $folder->files()->count() }} files</td>
                                                <td>
                                                    @if($folder->trashed_at)
                                                        {{ is_string($folder->trashed_at) ? \Carbon\Carbon::parse($folder->trashed_at)->format('M d, Y') : $folder->trashed_at->format('M d, Y') }}
                                                    @else
                                                        Unknown
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        if ($folder->trashed_at) {
                                                            $trashedDate = is_string($folder->trashed_at) ? \Carbon\Carbon::parse($folder->trashed_at) : $folder->trashed_at;
                                                            $daysRemaining = 30 - $trashedDate->diffInDays(now());
                                                        } else {
                                                            $daysRemaining = 30;
                                                        }
                                                        $colorClass = $daysRemaining <= 7 ? 'text-danger' : ($daysRemaining <= 14 ? 'text-warning' : 'text-muted');
                                                    @endphp
                                                    <span class="{{ $colorClass }}">{{ (int)max(0, $daysRemaining) }} days</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center list-user-action">
                                                        <form action="{{ route('cloudbox.folders.restore', $folder->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="action-icon bg-transparent border-0 text-success" 
                                                                    data-toggle="tooltip" title="Restore">
                                                                <i class="ri-arrow-go-back-line"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('cloudbox.folders.force-delete', $folder->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="action-icon bg-transparent border-0 text-danger" 
                                                                    data-toggle="tooltip" title="Delete Permanently"
                                                                    onclick="return confirm('Permanently delete this folder and all its contents? This action cannot be undone!')">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $folders->links() }}</div>
                            <hr>
                        @endif

                        <!-- Files Section -->
                        @if(request('item', 'all') !== 'folders' && $files->count() > 0)
                            <h5 class="mb-3"><i class="ri-file-line"></i> Files</h5>
                            <!-- Bulk Actions Bar: Files in Trash -->
                            <div id="trashBulkBarFiles" class="alert alert-primary d-none mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong><span id="trashSelectedFiles">0</span> file(s) selected</strong></span>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-success" id="trashRestoreFilesBtn">
                                            <i class="ri-arrow-go-back-line"></i> Restore Selected
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="trashDeleteFilesBtn">
                                            <i class="ri-delete-bin-6-line"></i> Delete Permanently
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" id="trashClearFilesBtn">Clear Selection</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-color-heading">
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox" id="trashSelectAllFiles"></th>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Deleted</th>
                                            <th>Days Remaining</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                            <tr>
                                                <td><input type="checkbox" class="trash-file-checkbox" value="{{ $file->id }}"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
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
                                                        @endphp
                                                        <i class="{{ $iconClass }} font-size-20 {{ $iconColor }}"></i>
                                                        <div class="ml-3">{{ $file->original_name }}</div>
                                                    </div>
                                                </td>
                                                <td>{{ strtoupper($file->extension) }}</td>
                                                <td>{{ $file->formatted_size }}</td>
                                                <td>
                                                    @if($file->trashed_at)
                                                        {{ is_string($file->trashed_at) ? \Carbon\Carbon::parse($file->trashed_at)->format('M d, Y') : $file->trashed_at->format('M d, Y') }}
                                                    @else
                                                        Unknown
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        if ($file->trashed_at) {
                                                            $trashedDate = is_string($file->trashed_at) ? \Carbon\Carbon::parse($file->trashed_at) : $file->trashed_at;
                                                            $daysRemaining = 30 - $trashedDate->diffInDays(now());
                                                        } else {
                                                            $daysRemaining = 30;
                                                        }
                                                        $colorClass = $daysRemaining <= 7 ? 'text-danger' : ($daysRemaining <= 14 ? 'text-warning' : 'text-muted');
                                                    @endphp
                                                    <span class="{{ $colorClass }}">{{ (int)max(0, $daysRemaining) }} days</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center list-user-action">
                                                        <form action="{{ route('cloudbox.files.restore', $file->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="action-icon bg-transparent border-0 text-success" 
                                                                    data-toggle="tooltip" title="Restore">
                                                                <i class="ri-arrow-go-back-line"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('cloudbox.files.force-delete', $file->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="action-icon bg-transparent border-0 text-danger" 
                                                                    data-toggle="tooltip" title="Delete Permanently"
                                                                    onclick="return confirm('Permanently delete this file? This action cannot be undone!')">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $files->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ri-delete-bin-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">Trash is empty</h5>
                            <p class="text-muted">Deleted files and folders will appear here</p>
                        </div>
                    @endif

                    <!-- Cleanup confirm overlay -->
                    <div id="cleanup-confirm" class="cleanup-overlay d-none">
                        <div class="cleanup-card">
                            <h5 class="mb-3">Delete permanently?</h5>
                            <p class="text-muted mb-4">All items in the trash will be permanently deleted. You cannot undo this action once it is performed.</p>
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="#" class="mr-3 text-muted" id="cleanup-cancel">Cancel</a>
                                <form action="{{ route('cloudbox.trash.cleanup') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Delete permanently</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cleanup-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1060; /* above modals backdrop */
    }
    .cleanup-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(0,0,0,0.2);
        width: 520px;
        max-width: calc(100% - 32px);
        padding: 24px;
    }
    .cleanup-card h5 {
        font-weight: 600;
    }
    .d-none { display: none !important; }
    #btn-clean-trash { border-radius: 999px; }
    .cleanup-card .btn-danger { border-radius: 999px; padding: 8px 18px; }
    .cleanup-card a#cleanup-cancel { font-size: 14px; }
    .cleanup-card p { line-height: 1.5; }
    
    /* Remove background/shadow behind action icons on Trash page */
    .list-user-action .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0;
    }
    .list-user-action .action-icon:hover,
    .list-user-action .action-icon:focus {
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .list-user-action .action-icon i { font-size: 18px; }
    /* Neutralize old iq-bg classes if they exist */
    .list-user-action .iq-bg-success,
    .list-user-action .iq-bg-danger {
        background: transparent !important;
        box-shadow: none !important;
    }
    
    @media (max-width: 576px) {
        .cleanup-card { width: 92%; padding: 20px; }
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    // Hiển thị/Ẩn lớp phủ xác nhận dọn dẹp
    $(document).on('click', '#btn-clean-trash', function(e) {
        e.preventDefault();
        $('#cleanup-confirm').removeClass('d-none');
    });
    $(document).on('click', '#cleanup-cancel', function(e) {
        e.preventDefault();
        $('#cleanup-confirm').addClass('d-none');
    });
    // Đồng thời đóng khi nhấp ngoài thẻ
    $(document).on('click', '#cleanup-confirm', function(e) {
        if ($(e.target).is('#cleanup-confirm')) {
            $('#cleanup-confirm').addClass('d-none');
        }
    });

    // ===== Chọn nhiều & hành động: Thư mục trong Thùng rác =====
    const trashFolderCheckboxes = document.querySelectorAll('.trash-folder-checkbox');
    const trashSelectAllFolders = document.getElementById('trashSelectAllFolders');
    const trashBulkBarFolders = document.getElementById('trashBulkBarFolders');
    const trashSelectedFolders = document.getElementById('trashSelectedFolders');
    const trashRestoreFoldersBtn = document.getElementById('trashRestoreFoldersBtn');
    const trashDeleteFoldersBtn = document.getElementById('trashDeleteFoldersBtn');
    const trashClearFoldersBtn = document.getElementById('trashClearFoldersBtn');

    function updateTrashFoldersBar() {
        const checked = document.querySelectorAll('.trash-folder-checkbox:checked');
        if (checked.length > 0) {
            trashBulkBarFolders.classList.remove('d-none');
            trashSelectedFolders.textContent = checked.length;
        } else {
            trashBulkBarFolders.classList.add('d-none');
        }
    }
    if (trashSelectAllFolders) {
        trashSelectAllFolders.addEventListener('change', function() {
            trashFolderCheckboxes.forEach(cb => cb.checked = this.checked);
            updateTrashFoldersBar();
        });
    }
    trashFolderCheckboxes.forEach(cb => cb.addEventListener('change', function() {
        updateTrashFoldersBar();
        const allChecked = Array.from(trashFolderCheckboxes).every(c => c.checked);
        const someChecked = Array.from(trashFolderCheckboxes).some(c => c.checked);
        if (trashSelectAllFolders) {
            trashSelectAllFolders.checked = allChecked;
            trashSelectAllFolders.indeterminate = someChecked && !allChecked;
        }
    }));
    if (trashClearFoldersBtn) {
        trashClearFoldersBtn.addEventListener('click', function() {
            trashFolderCheckboxes.forEach(cb => cb.checked = false);
            if (trashSelectAllFolders) trashSelectAllFolders.checked = false;
            updateTrashFoldersBar();
        });
    }
    function submitFolderTrash(actionRouteName) {
        const checked = Array.from(document.querySelectorAll('.trash-folder-checkbox:checked'));
        if (checked.length === 0) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionRouteName;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'folder_ids[]'; input.value = cb.value;
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    }
    if (trashRestoreFoldersBtn) {
        trashRestoreFoldersBtn.addEventListener('click', function() {
            submitFolderTrash('{{ route("cloudbox.trash.folders.bulk-restore") }}');
        });
    }
    if (trashDeleteFoldersBtn) {
        trashDeleteFoldersBtn.addEventListener('click', function() {
            if (!confirm('Permanently delete selected folder(s)? This cannot be undone.')) return;
            submitFolderTrash('{{ route("cloudbox.trash.folders.bulk-force-delete") }}');
        });
    }

    // ===== Chọn nhiều & hành động: Tệp trong Thùng rác =====
    const trashFileCheckboxes = document.querySelectorAll('.trash-file-checkbox');
    const trashSelectAllFiles = document.getElementById('trashSelectAllFiles');
    const trashBulkBarFiles = document.getElementById('trashBulkBarFiles');
    const trashSelectedFiles = document.getElementById('trashSelectedFiles');
    const trashRestoreFilesBtn = document.getElementById('trashRestoreFilesBtn');
    const trashDeleteFilesBtn = document.getElementById('trashDeleteFilesBtn');
    const trashClearFilesBtn = document.getElementById('trashClearFilesBtn');

    function updateTrashFilesBar() {
        const checked = document.querySelectorAll('.trash-file-checkbox:checked');
        if (checked.length > 0) {
            trashBulkBarFiles.classList.remove('d-none');
            trashSelectedFiles.textContent = checked.length;
        } else {
            trashBulkBarFiles.classList.add('d-none');
        }
    }
    if (trashSelectAllFiles) {
        trashSelectAllFiles.addEventListener('change', function() {
            trashFileCheckboxes.forEach(cb => cb.checked = this.checked);
            updateTrashFilesBar();
        });
    }
    trashFileCheckboxes.forEach(cb => cb.addEventListener('change', function() {
        updateTrashFilesBar();
        const allChecked = Array.from(trashFileCheckboxes).every(c => c.checked);
        const someChecked = Array.from(trashFileCheckboxes).some(c => c.checked);
        if (trashSelectAllFiles) {
            trashSelectAllFiles.checked = allChecked;
            trashSelectAllFiles.indeterminate = someChecked && !allChecked;
        }
    }));
    if (trashClearFilesBtn) {
        trashClearFilesBtn.addEventListener('click', function() {
            trashFileCheckboxes.forEach(cb => cb.checked = false);
            if (trashSelectAllFiles) trashSelectAllFiles.checked = false;
            updateTrashFilesBar();
        });
    }
    function submitFileTrash(actionRouteName) {
        const checked = Array.from(document.querySelectorAll('.trash-file-checkbox:checked'));
        if (checked.length === 0) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionRouteName;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'file_ids[]'; input.value = cb.value;
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    }
    if (trashRestoreFilesBtn) {
        trashRestoreFilesBtn.addEventListener('click', function() {
            submitFileTrash('{{ route("cloudbox.trash.files.bulk-restore") }}');
        });
    }
    if (trashDeleteFilesBtn) {
        trashDeleteFilesBtn.addEventListener('click', function() {
            if (!confirm('Permanently delete selected file(s)? This cannot be undone.')) return;
            submitFileTrash('{{ route("cloudbox.trash.files.bulk-force-delete") }}');
        });
    }
</script>
@endpush
