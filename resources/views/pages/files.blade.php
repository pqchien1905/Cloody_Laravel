@extends('layouts.app')

@section('title', __('common.files') . ' - Cloody')

@section('content')
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
                            <h5 class="mb-0">{{ $stats['total'] }}</h5>
                            <p class="mb-0 text-muted">{{ __('common.total_files') }}</p>
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
                            <h5 class="mb-0">{{ $stats['folders'] }}</h5>
                            <p class="mb-0 text-muted">{{ __('common.folders') }}</p>
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
                            <h5 class="mb-0">{{ $stats['favorites'] }}</h5>
                            <p class="mb-0 text-muted">{{ __('common.favorites') }}</p>
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
                            <h5 class="mb-0">{{ number_format($stats['size'] / 1048576, 2) }} MB</h5>
                            <p class="mb-0 text-muted">{{ __('common.storage_used') }}</p>
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
                        <h4 class="card-title">{{ __('common.all_files') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModal">
                            <i class="ri-upload-line"></i> {{ __('common.upload_file') }}
                        </button>
                    </div>
                </div>
                <div class="card-body" style="overflow: visible;">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('cloody.files') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="{{ __('common.search_files') }}" value="{{ request('search') }}">
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
                                <option value="">{{ __('common.all_types') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?folder_id='+this.value">
                                <option value="">{{ __('common.all_folders') }}</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ request('folder_id') == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" onchange="window.location.href='?sort='+this.value">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('common.newest') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('common.name') }}</option>
                                <option value="size" {{ request('sort') == 'size' ? 'selected' : '' }}>{{ __('common.size') }}</option>
                            </select>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if($files->count() > 0)
                        <!-- Bulk Actions Bar -->
                        <div id="bulkActionsBar" class="alert alert-primary d-none mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong><span id="selectedCount">0</span> {{ __('common.files_selected') }}</strong></span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                        <i class="ri-delete-bin-line"></i> {{ __('common.delete_selected') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" id="clearSelectionBtn">{{ __('common.clear_selection') }}</button>
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
                                        <th scope="col">{{ __('common.file_name') }}</th>
                                        <th scope="col">{{ __('common.folder') }}</th>
                                        <th scope="col">{{ __('common.type') }}</th>
                                        <th scope="col">{{ __('common.size') }}</th>
                                        <th scope="col">{{ __('common.last_modified') }}</th>
                                        <th scope="col">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <td onclick="event.stopPropagation();">
                                                <input type="checkbox" class="file-checkbox" value="{{ $file->id }}">
                                            </td>
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
                                                        } elseif(Str::contains($file->mime_type, 'video')) {
                                                            $iconClass = 'ri-video-line';
                                                            $iconColor = 'text-warning';
                                                        }
                                                    @endphp
                                                    <i class="{{ $iconClass }} font-size-20 {{ $iconColor }}"></i>
                                                    <div class="ml-3">
                                                        <a href="{{ route('cloody.files.view', $file->id) }}" class="font-weight-500">
                                                            {{ $file->original_name }}
                                                        </a>
                                                        @if($file->is_favorite)
                                                            <i class="ri-star-fill text-warning ml-1"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($file->folder)
                                                    <a href="{{ route('cloody.folders.show', $file->folder->id) }}">
                                                        {{ $file->folder->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ strtoupper($file->extension) }}</td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>{{ $file->updated_at->diffForHumans() }}</td>
                                            <td class="actions-cell">
                                                <div class="dropdown">
                                                    <span class="action-dots" id="dropdownFile{{ $file->id }}" data-toggle="dropdown">
                                                        <i class="ri-more-2-fill font-size-20"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFile{{ $file->id }}">
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editFileModal{{ $file->id }}" onclick="event.preventDefault();">
                                                            <i class="ri-pencil-fill mr-2"></i>{{ __('common.edit') }}
                                                        </a>
                                                        <form action="{{ route('cloody.files.favorite', $file->id) }}" method="POST" style="display: inline; width: 100%;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-star-{{ $file->is_favorite ? 'fill' : 'line' }} mr-2 text-warning"></i>
                                                                {{ $file->is_favorite ? __('common.remove_from_favorites') : __('common.add_to_favorites') }}
                                                            </button>
                                                        </form>
                                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); shareManager.openShareModal({{ $file->id }}, 'file');">
                                                            <i class="ri-share-line mr-2"></i>{{ __('common.share') }}
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('cloody.files.download', $file->id) }}">
                                                            <i class="ri-file-download-fill mr-2"></i>{{ __('common.download') }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('cloody.files.delete', $file->id) }}" method="POST" style="display: inline; width: 100%;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-6-fill mr-2"></i>{{ __('common.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit File Modal -->
                                        <div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('common.rename_file') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('cloody.files.update', $file->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="file_name{{ $file->id }}">{{ __('common.file_name') }} <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="file_name{{ $file->id }}" 
                                                                       name="name" value="{{ pathinfo($file->original_name, PATHINFO_FILENAME) }}" required>
                                                                <small class="form-text text-muted">{{ __('common.extension') }}: .{{ $file->extension }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('common.rename') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Share File Modal -->
                                        <div class="modal fade" id="shareFileModal{{ $file->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('common.share_file') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('cloody.files.share', $file->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="share_email{{ $file->id }}">{{ __('common.recipient_email') }} <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" id="share_email{{ $file->id }}" name="email" required placeholder="{{ __('common.enter_recipient_email') }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('common.share') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $files->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-file-list-3-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">{{ __('common.no_files_found') }}</h5>
                            <p class="text-muted">{{ __('common.upload_first_file') }}</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModal">
                                <i class="ri-upload-line"></i> {{ __('common.upload_file') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Upload Modal -->
@endsection

@push('styles')
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
@endpush

@push('scripts')
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
            const confirmMessage = '{{ __('common.move_to_trash_confirm', ['count' => ':count']) }}'.replace(':count', count);
            if (!confirm(confirmMessage)) return;

            // Tạo form và gửi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("cloody.files.bulk-delete") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
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
@endpush
