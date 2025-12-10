@extends('layouts.app')

@section('title', __('common.manage_files') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_files') }}</h4>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_files_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-file text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.active_files_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['active']) }}</h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-2">
                            <i class="las la-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.trash_files_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['trash']) }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-trash text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_storage_used') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_size'] / 1024 / 1024 / 1024, 2) }} GB</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-database text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.files.index') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="{{ __('common.file_name_admin') }}..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="user_id" class="small text-muted mb-1">{{ __('common.filter_by_user') }}</label>
                            <select class="form-control" id="user_id" name="user_id">
                                <option value="">{{ __('common.all_users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="type" class="small text-muted mb-1">{{ __('common.filter_by_type') }}</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">{{ __('common.all_types') }}</option>
                                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>{{ __('common.images') }}</option>
                                <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>{{ __('common.videos') }}</option>
                                <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>{{ __('common.audio') }}</option>
                                <option value="pdf" {{ request('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>{{ __('common.documents') }}</option>
                                <option value="spreadsheet" {{ request('type') === 'spreadsheet' ? 'selected' : '' }}>{{ __('common.sheets') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="status" class="small text-muted mb-1">{{ __('common.filter_by_status') }}</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('common.file_active') }}</option>
                                <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>{{ __('common.file_in_trash') }}</option>
                                <option value="favorite" {{ request('status') === 'favorite' ? 'selected' : '' }}>{{ __('common.file_favorite') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">{{ __('common.sort_by') }}</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>{{ __('common.sort_by_date') }}</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('common.sort_by_name') }}</option>
                                <option value="size" {{ request('sort') === 'size' ? 'selected' : '' }}>{{ __('common.sort_by_size') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="hidden" name="order" value="{{ request('order', 'desc') }}">
                            <button type="submit" class="btn btn-primary btn-block" title="{{ __('common.filter') }}">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'user_id', 'type', 'status', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.files.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="las la-times mr-1"></i> {{ __('common.clear_filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-block card-stretch card-height">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <i class="ri-file-line"></i>
                                    </th>
                                    <th>{{ __('common.file_name_admin') }}</th>
                                    <th>{{ __('common.file_owner') }}</th>
                                    <th>{{ __('common.file_type') }}</th>
                                    <th>{{ __('common.file_size_admin') }}</th>
                                    <th>{{ __('common.file_status') }}</th>
                                    <th>{{ __('common.file_uploaded_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $file)
                                <tr>
                                    <td>
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
                                                $iconColor = 'text-danger';
                                            } elseif(Str::contains($file->mime_type, 'audio')) {
                                                $iconClass = 'ri-music-line';
                                                $iconColor = 'text-warning';
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }} {{ $iconColor }} font-size-24"></i>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><a href="{{ route('admin.files.show', $file->id) }}" class="text-primary">{{ $file->name }}</a></strong>
                                            @if($file->original_name !== $file->name)
                                                <br><small class="text-muted">{{ $file->original_name }}</small>
                                            @endif
                                        </div>
                                        @if($file->folder)
                                            <small class="text-muted">
                                                <i class="ri-folder-line"></i> {{ $file->folder->name }}
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                <i class="ri-home-line"></i> {{ __('common.file_in_root') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($file->user->avatar)
                                                <img src="{{ $file->user->avatar_url }}" alt="{{ $file->user->name }}" 
                                                     class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                     onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($file->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $file->user->name }}</div>
                                                <small class="text-muted">{{ $file->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ strtoupper($file->extension ?? __('common.not_available')) }}</span>
                                        <br><small class="text-muted">{{ Str::limit($file->mime_type, 30) }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $file->formatted_size }}</strong>
                                    </td>
                                    <td>
                                        @if($file->is_trash)
                                            <span class="badge badge-warning">{{ __('common.file_in_trash') }}</span>
                                        @elseif($file->is_favorite)
                                            <span class="badge badge-success">{{ __('common.file_favorite') }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ __('common.file_active') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.files.show', $file->id) }}" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="{{ __('common.view_details') }}">
                                                <i class="las la-info-circle"></i>
                                            </a>
                                            <a href="{{ route('admin.files.view', $file->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="{{ __('common.view_file') }}"
                                               target="_blank">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.files.download', $file->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="{{ __('common.download_file') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deleteFileModal{{ $file->id }}"
                                                    title="{{ __('common.delete_file') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="ri-file-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_files_found_admin') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($files, 'links'))
                    <div class="card-footer">{{ $files->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete File Modals -->
@foreach($files as $file)
<div class="modal fade" id="deleteFileModal{{ $file->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteFileModalLabel{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteFileModalLabel{{ $file->id }}">{{ __('common.delete_file') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.files.destroy', $file) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_file_confirm') }}</p>
                    <p class="mb-2"><strong>{{ $file->name }}</strong></p>
                    <p class="text-muted">{{ __('common.file_will_be_permanently_deleted') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_file') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

