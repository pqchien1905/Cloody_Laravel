@extends('layouts.app')

@section('title', $folder->name . ' - Admin Folders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white">
                    @if(request('from') === 'favorites')
                        <li class="breadcrumb-item"><a href="{{ route('admin.favorites.index') }}">{{ __('common.manage_favorites') }}</a></li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ route('admin.folders.index') }}">{{ __('common.manage_folders') }}</a></li>
                        @if($folder->parent)
                            <li class="breadcrumb-item"><a href="{{ route('admin.folders.show', $folder->parent->id) }}">{{ $folder->parent->name }}</a></li>
                        @endif
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                </ol>
            </nav>

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

            <!-- Folder Information Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <div class="d-flex align-items-center">
                            <i class="ri-folder-3-fill font-size-32 mr-3" style="color: {{ $folder->color ?? '#3498db' }}"></i>
                            <div>
                                <h4 class="card-title mb-0">{{ $folder->name }}</h4>
                                @if($folder->description)
                                    <small class="text-muted">{{ $folder->description }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.folders.download', $folder->id) }}" class="btn btn-primary">
                            <i class="las la-download"></i> {{ __('common.download_folder') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('common.folder_owner') }}</h6>
                            <div class="d-flex align-items-center mb-3">
                                @if($folder->user->avatar)
                                    <img src="{{ $folder->user->avatar_url }}" alt="{{ $folder->user->name }}" 
                                         class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;"
                                         onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                         style="width: 40px; height: 40px; font-size: 16px;">
                                        {{ strtoupper(substr($folder->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div><strong>{{ $folder->user->name }}</strong></div>
                                    <small class="text-muted">{{ $folder->user->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-muted">{{ __('common.folder_privacy_admin') }}</h6>
                                    <p>
                                        @if($folder->is_public)
                                            <span class="badge badge-success"><i class="ri-global-line"></i> {{ __('common.folder_public') }}</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="ri-lock-line"></i> {{ __('common.folder_private') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted">{{ __('common.folder_status') }}</h6>
                                    <p>
                                        @if($folder->is_trash)
                                            <span class="badge badge-warning">{{ __('common.folder_in_trash') }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ __('common.folder_active') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-primary-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.total_files') }}</h6>
                                    <h3 class="mb-0">{{ $folder->files->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.subfolders') }}</h6>
                                    <h3 class="mb-0">{{ $folder->children->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.total_size') }}</h6>
                                    <h3 class="mb-0">{{ number_format($totalSize / 1024 / 1024, 2) }} MB</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subfolders Section -->
            @if($folder->children->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ri-folder-line"></i> {{ __('common.subfolders') }} ({{ $folder->children->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th style="width: 40px;"><i class="ri-folder-line"></i></th>
                                        <th>{{ __('common.folder_name_admin') }}</th>
                                        <th>{{ __('common.folder_files_count') }}</th>
                                        <th>{{ __('common.folder_privacy_admin') }}</th>
                                        <th>{{ __('common.folder_created_at') }}</th>
                                        <th class="text-right">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($folder->children as $subfolder)
                                        <tr>
                                            <td>
                                                <i class="ri-folder-3-fill font-size-24" style="color: {{ $subfolder->color ?? '#3498db' }}"></i>
                                            </td>
                                            <td>
                                                <strong>{{ $subfolder->name }}</strong>
                                                @if($subfolder->is_favorite)
                                                    <i class="ri-star-fill text-warning ml-1"></i>
                                                @endif
                                                @if($subfolder->description)
                                                    <br><small class="text-muted">{{ Str::limit($subfolder->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $subfolder->files_count }} {{ __('common.files') }}</td>
                                            <td>
                                                @if($subfolder->is_public)
                                                    <span class="badge badge-success"><i class="ri-global-line"></i> {{ __('common.folder_public') }}</span>
                                                @else
                                                    <span class="badge badge-secondary"><i class="ri-lock-line"></i> {{ __('common.folder_private') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $subfolder->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $subfolder->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.folders.show', $subfolder->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Files Section -->
            @if($folder->files->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ri-file-line"></i> {{ __('common.files') }} ({{ $folder->files->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th style="width: 40px;"><i class="ri-file-line"></i></th>
                                        <th>{{ __('common.file_name_admin') }}</th>
                                        <th>{{ __('common.file_type') }}</th>
                                        <th>{{ __('common.file_size_admin') }}</th>
                                        <th>{{ __('common.file_uploaded_at') }}</th>
                                        <th class="text-right">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($folder->files as $file)
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
                                                <strong>{{ $file->name }}</strong>
                                                @if($file->original_name !== $file->name)
                                                    <br><small class="text-muted">{{ $file->original_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ strtoupper($file->extension ?? 'N/A') }}</span>
                                            </td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>
                                                <div>{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
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
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($folder->children->count() == 0 && $folder->files->count() == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-folder-open-line font-size-64 text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('common.empty_folder') }}</h5>
                        <p class="text-muted">{{ __('common.no_files_or_subfolders') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
