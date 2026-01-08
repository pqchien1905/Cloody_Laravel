@extends('layouts.app')

@section('title', 'Folders - Cloody')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-success-light">
                            <i class="ri-folder-line text-success"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0">{{ $folders->count() }}</h5>
                            <p class="mb-0 text-muted">Total Folders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon iq-icon-box-2 bg-primary-light">
                            <i class="ri-file-line text-primary"></i>
                        </div>
                        <div class="ml-3">
                            @php
                                $totalFiles = $folders->sum(function($folder) {
                                    return $folder->files_count ?? 0;
                                });
                            @endphp
                            <h5 class="mb-0">{{ $totalFiles }}</h5>
                            <p class="mb-0 text-muted">Files in Folders</p>
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
                            <i class="ri-folder-star-line text-warning"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0">{{ $folders->where('is_favorite', true)->count() }}</h5>
                            <p class="mb-0 text-muted">Favorite Folders</p>
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
                            <i class="ri-global-line text-info"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="mb-0">{{ $folders->where('is_public', true)->count() }}</h5>
                            <p class="mb-0 text-muted">Public Folders</p>
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
                        <h4 class="card-title">{{ __('common.all_folders_title') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <button type="button" class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#uploadFolderModal">
                            <i class="ri-folder-upload-line"></i> {{ __('common.upload_folder') }}
                        </button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createFolderModal">
                            <i class="ri-folder-add-line"></i> {{ __('common.new_folder') }}
                        </button>
                    </div>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('cloody.folders.index') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="{{ __('common.search') }} {{ __('common.folders') }}..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?filter='+this.value">
                                <option value="">{{ __('common.all_folders') }}</option>
                                <option value="favorites" {{ request('filter') == 'favorites' ? 'selected' : '' }}>{{ __('common.favorites') }}</option>
                                <option value="shared" {{ request('filter') == 'shared' ? 'selected' : '' }}>{{ __('common.shared') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?sort='+this.value">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('common.newest') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('common.name') }}</option>
                                <option value="files_count" {{ request('sort') == 'files_count' ? 'selected' : '' }}>{{ __('common.files_count') }}</option>
                            </select>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if($folders->count() > 0)
                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-primary d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong id="selectedCount">0</strong> {{ __('common.folder') }}</span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                        <i class="ri-delete-bin-line"></i> {{ __('common.delete_selected') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary ml-2" id="clearSelectionBtn">
                                        {{ __('common.clear_selection') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th scope="col" style="width: 40px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="selectAllFolders">
                                                <label class="custom-control-label" for="selectAllFolders"></label>
                                            </div>
                                        </th>
                                        <th scope="col">Folder Name</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Files</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($folders as $folder)
                                        <tr class="folder-row" data-url="{{ route('cloody.folders.show', $folder->id) }}">
                                            <td onclick="event.stopPropagation();">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input folder-checkbox" 
                                                           id="folder{{ $folder->id }}" value="{{ $folder->id }}">
                                                    <label class="custom-control-label" for="folder{{ $folder->id }}"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-folder-3-fill font-size-20" style="color: {{ $folder->color ?? '#3498db' }}"></i>
                                                    <div class="ml-3">
                                                        <span class="font-weight-bold">{{ $folder->name }}</span>
                                                        @if($folder->is_favorite)
                                                            <i class="ri-star-fill text-warning ml-1"></i>
                                                        @endif
                                                        @if($folder->is_shared)
                                                            <i class="ri-share-line text-info ml-1"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($folder->description)
                                                    <span class="text-muted">{{ Str::limit($folder->description, 50) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $folder->files_count }} {{ __('common.files') }}
                                            </td>
                                            <td>
                                                @if($folder->is_public)
                                                    <span class="badge badge-success">
                                                        <i class="ri-global-line"></i> Public
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="ri-lock-line"></i> Private
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $folder->created_at->diffForHumans() }}</td>
                                            <td class="actions-cell">
                                                <div class="dropdown">
                                                    <span class="action-dots" id="dropdownFolder{{ $folder->id }}" data-toggle="dropdown">
                                                        <i class="ri-more-2-fill font-size-20"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFolder{{ $folder->id }}">
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editFolderModal{{ $folder->id }}" onclick="event.preventDefault();">
                                                            <i class="ri-pencil-fill mr-2"></i>{{ __('common.edit') }}
                                                        </a>
                                                        <form action="{{ route('cloody.folders.favorite', $folder->id) }}" method="POST" style="display: inline; width: 100%;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-star-{{ $folder->is_favorite ? 'fill' : 'line' }} mr-2 text-warning"></i>
                                                                {{ $folder->is_favorite ? __('common.remove_from_favorites') : __('common.add_to_favorites') }}
                                                            </button>
                                                        </form>
                                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); event.stopPropagation(); shareManager.openShareModal({{ $folder->id }}, 'folder');">
                                                            <i class="ri-share-line mr-2"></i>{{ __('common.share') }}
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('cloody.folders.download', $folder->id) }}">
                                                            <i class="ri-file-download-fill mr-2"></i>{{ __('common.download') }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <form id="delete-folder-{{ $folder->id }}" action="{{ route('cloody.folders.destroy', $folder->id) }}" method="POST" style="display: inline; width: 100%;" class="js-delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger js-delete-btn">
                                                                <i class="ri-delete-bin-6-fill mr-2"></i>{{ __('common.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Folder Modal -->
                                        <div class="modal fade" id="editFolderModal{{ $folder->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('common.edit') }} {{ __('common.folder') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('cloody.folders.update', $folder->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('common.folder') }} {{ __('common.name') }} <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="name" name="name" value="{{ $folder->name }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="color">Folder Color</label>
                                                                <input type="color" class="form-control" id="color" name="color" value="{{ $folder->color ?? '#3498db' }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="description">Description</label>
                                                                <textarea class="form-control" id="description" name="description" rows="3">{{ $folder->description }}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Privacy Settings <span class="text-danger">*</span></label>
                                                                <div class="custom-control custom-radio mb-2">
                                                                    <input type="radio" id="edit_privacy_private{{ $folder->id }}" name="is_public" value="0" 
                                                                           class="custom-control-input" {{ !$folder->is_public ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="edit_privacy_private{{ $folder->id }}">
                                                                        <i class="ri-lock-line"></i> Private
                                                                        <small class="d-block text-muted">{{ __('common.only_you_can_access') }}</small>
                                                                    </label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" id="edit_privacy_public{{ $folder->id }}" name="is_public" value="1" 
                                                                           class="custom-control-input" {{ $folder->is_public ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="edit_privacy_public{{ $folder->id }}">
                                                                        <i class="ri-global-line"></i> Public
                                                                        <small class="d-block text-muted">{{ __('common.anyone_with_link_can_view') }}</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">Update Folder</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @foreach($folders as $folder)
                        <!-- Share Folder Modal -->
                        <div class="modal fade" id="shareFolderModal{{ $folder->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('common.share_folder') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('cloody.folders.share', $folder->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="share_email_folder_{{ $folder->id }}">{{ __('common.recipient_email') }} <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="share_email_folder_{{ $folder->id }}" name="email" required placeholder="{{ __('common.enter_recipient_email') }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">{{ __('common.share') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="ri-folder-open-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">{{ __('common.no_folders') }}</h5>
                            <p class="text-muted">{{ __('common.no_folders_desc') }}</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createFolderModal">
                                <i class="ri-folder-add-line"></i> {{ __('common.new_folder') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .folder-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .folder-row:hover {
        background-color: #f8f9fa;
    }
    .action-dots {
        cursor: pointer !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
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
    .actions-cell {
        position: relative;
        overflow: visible !important;
    }
    .actions-cell .dropdown {
        position: relative;
    }
    .actions-cell .dropdown.show {
        z-index: 10000;
    }
    .actions-cell .dropdown-menu {
        position: absolute !important;
        top: calc(100% + 5px) !important;
        right: -10px !important;
        left: auto !important;
        z-index: 10001 !important;
        background-color: #ffffff !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.2) !important;
        min-width: 13rem !important;
        margin: 0 !important;
        padding: 0.75rem 0 !important;
    }
    .actions-cell .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -20px;
        right: 0;
        left: 0;
        height: 20px;
        background-color: transparent;
    }
    .actions-cell .dropdown-menu::after {
        content: '';
        position: absolute;
        top: -1px;
        right: -1px;
        bottom: -1px;
        left: -1px;
        background-color: #ffffff;
        border-radius: 0.375rem;
        z-index: -1;
    }
    .dropdown-item {
        cursor: pointer;
        padding: 0.5rem 1.5rem !important;
        clear: both;
        font-weight: 400;
        color: #212529 !important;
        text-align: inherit;
        white-space: nowrap;
        background-color: #fff !important;
        border: 0;
        display: block;
        width: 100%;
        text-decoration: none;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa !important;
        color: #16181b;
    }
    .dropdown-divider {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
    // Khởi tạo tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    // Tự động ẩn cảnh báo sau 5 giây (không ẩn thanh hành động hàng loạt)
    setTimeout(function() {
        $('.alert:not(#bulkActionsBar)').fadeOut('slow');
    }, 5000);

    // Mở lại modal nếu có lỗi xác thực
    @if($errors->any())
        $('#createFolderModal').modal('show');
    @endif

    // Xử lý click hàng thư mục
    $(document).on('click', '.folder-row', function(e) {
    // Không điều hướng nếu nhấp vào ô hành động hoặc phần tử con của nó
        if ($(e.target).closest('.actions-cell').length === 0) {
            var url = $(this).data('url');
            if (url) {
                window.location.href = url;
            }
        }
    });

    // Ngăn việc click hàng khi đang nhấn dropdown
    $(document).on('click', '.actions-cell', function(e) {
        e.stopPropagation();
    });

    // Đảm bảo nút xóa gửi form và không điều hướng hàng
    $(document).on('click', '.js-delete-btn', function(e) {
        e.stopPropagation();
    // Cho phép form gửi bình thường
    });

    // Xử lý click vào dấu chấm hành động
    $(document).on('click', '.action-dots', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // Xử lý chọn nhiều mục
    const folderCheckboxes = document.querySelectorAll('.folder-checkbox');
    const selectAllFolders = document.getElementById('selectAllFolders');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.folder-checkbox:checked');
        if (checked.length > 0) {
            bulkActionsBar.classList.remove('d-none');
            selectedCount.textContent = checked.length;
        } else {
            bulkActionsBar.classList.add('d-none');
        }
    }

    // Checkbox 'chọn tất cả'
    if (selectAllFolders) {
        selectAllFolders.addEventListener('change', function() {
            folderCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Checkbox từng mục
    folderCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkActions();
            // Cập nhật trạng thái 'chọn tất cả'
            const allChecked = Array.from(folderCheckboxes).every(c => c.checked);
            const someChecked = Array.from(folderCheckboxes).some(c => c.checked);
            if (selectAllFolders) {
                selectAllFolders.checked = allChecked;
                selectAllFolders.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Xóa lựa chọn
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            folderCheckboxes.forEach(cb => cb.checked = false);
            if (selectAllFolders) selectAllFolders.checked = false;
            updateBulkActions();
        });
    }

    // Xóa hàng loạt
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checked = Array.from(document.querySelectorAll('.folder-checkbox:checked'));
            if (checked.length === 0) return;

            const count = checked.length;
            const confirmMessage = '{{ __('common.move_folders_to_trash', ['count' => ':count']) }}'.replace(':count', count);
            if (!confirm(confirmMessage)) return;

            // Tạo và gửi form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("cloody.folders.bulk-delete") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'folder_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }
</script>
@endpush
