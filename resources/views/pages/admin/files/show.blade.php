@extends('layouts.app')

@section('title', $file->name . ' - Admin Files')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.files.index') }}">{{ __('common.manage_files') }}</a></li>
                        @if($file->folder)
                            <li class="breadcrumb-item"><a href="{{ route('admin.folders.show', $file->folder->id) }}">{{ $file->folder->name }}</a></li>
                        @endif
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $file->name }}</li>
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

            <!-- File Information Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
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
                                    $iconColor = 'text-danger';
                                } elseif(Str::contains($file->mime_type, 'audio')) {
                                    $iconClass = 'ri-music-line';
                                    $iconColor = 'text-warning';
                                }
                            @endphp
                            <i class="{{ $iconClass }} {{ $iconColor }} font-size-32 mr-3"></i>
                            <div>
                                <h4 class="card-title mb-0">{{ $file->name }}</h4>
                                @if($file->original_name !== $file->name)
                                    <small class="text-muted">{{ $file->original_name }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.files.view', $file->id) }}" 
                           class="btn btn-primary mr-2" 
                           target="_blank">
                            <i class="las la-eye"></i> {{ __('common.view_file') }}
                        </a>
                        <a href="{{ route('admin.files.download', $file->id) }}" 
                           class="btn btn-info">
                            <i class="las la-download"></i> {{ __('common.download_file') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('common.file_owner') }}</h6>
                            <div class="d-flex align-items-center mb-3">
                                @if($file->user->avatar)
                                    <img src="{{ $file->user->avatar_url }}" alt="{{ $file->user->name }}" 
                                         class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;"
                                         onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                         style="width: 40px; height: 40px; font-size: 16px;">
                                        {{ strtoupper(substr($file->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div><strong>{{ $file->user->name }}</strong></div>
                                    <small class="text-muted">{{ $file->user->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-muted">{{ __('common.file_status') }}</h6>
                                    <p>
                                        @if($file->is_trash)
                                            <span class="badge badge-warning">{{ __('common.file_in_trash') }}</span>
                                        @elseif($file->is_favorite)
                                            <span class="badge badge-success">{{ __('common.file_favorite') }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ __('common.file_active') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted">{{ __('common.file_type') }}</h6>
                                    <p>
                                        <span class="badge badge-secondary">{{ strtoupper($file->extension ?? __('common.not_available')) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-primary-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.file_size_admin') }}</h6>
                                    <h3 class="mb-0">{{ $file->formatted_size }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.file_uploaded_at') }}</h6>
                                    <h5 class="mb-0">{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</h5>
                                    <small class="text-muted">{{ $file->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.file_updated_at') }}</h6>
                                    <h5 class="mb-0">{{ $file->updated_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</h5>
                                    <small class="text-muted">{{ $file->updated_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.shares_count') }}</h6>
                                    <h3 class="mb-0">{{ $file->shares->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">{{ __('common.file_details') }}</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 150px;"><strong>{{ __('common.file_name_admin') }}:</strong></td>
                                    <td>{{ $file->name }}</td>
                                </tr>
                                @if($file->original_name !== $file->name)
                                <tr>
                                    <td><strong>{{ __('common.original_name') }}:</strong></td>
                                    <td>{{ $file->original_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>{{ __('common.mime_type') }}:</strong></td>
                                    <td><code>{{ $file->mime_type }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('common.file_path') }}:</strong></td>
                                    <td><code class="text-muted">{{ $file->path }}</code></td>
                                </tr>
                                @if($file->folder)
                                <tr>
                                    <td><strong>{{ __('common.folder') }}:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.folders.show', $file->folder->id) }}">
                                            <i class="ri-folder-line"></i> {{ $file->folder->name }}
                                        </a>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td><strong>{{ __('common.folder') }}:</strong></td>
                                    <td><i class="ri-home-line"></i> {{ __('common.file_in_root') }}</td>
                                </tr>
                                @endif
                                @if($file->description)
                                <tr>
                                    <td><strong>{{ __('common.description') }}:</strong></td>
                                    <td>{{ $file->description }}</td>
                                </tr>
                                @endif
                                @if($file->trashed_at)
                                <tr>
                                    <td><strong>{{ __('common.trashed_at') }}:</strong></td>
                                    <td>{{ $file->trashed_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shares Section -->
            @if($file->shares->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ri-share-line"></i> {{ __('common.shares') }} ({{ $file->shares->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th>{{ __('common.shared_by') }}</th>
                                        <th>{{ __('common.shared_with') }}</th>
                                        <th>{{ __('common.permission') }}</th>
                                        <th>{{ __('common.share_type') }}</th>
                                        <th>{{ __('common.expires_at') }}</th>
                                        <th>{{ __('common.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($file->shares as $share)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($share->sharedBy && $share->sharedBy->avatar)
                                                        <img src="{{ $share->sharedBy->avatar_url }}" alt="{{ $share->sharedBy->name }}" 
                                                             class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                             onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                                    @elseif($share->sharedBy)
                                                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                             style="width: 30px; height: 30px; font-size: 12px;">
                                                            {{ strtoupper(substr($share->sharedBy->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div>{{ $share->sharedBy->name ?? __('common.unknown') }}</div>
                                                        @if($share->sharedBy)
                                                            <small class="text-muted">{{ $share->sharedBy->email }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($share->is_public)
                                                    <span class="badge badge-success"><i class="ri-global-line"></i> {{ __('common.public') }}</span>
                                                @elseif($share->sharedWith)
                                                    <div class="d-flex align-items-center">
                                                        @if($share->sharedWith->avatar)
                                                            <img src="{{ $share->sharedWith->avatar_url }}" alt="{{ $share->sharedWith->name }}" 
                                                                 class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                                 onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                                        @else
                                                            <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                                {{ strtoupper(substr($share->sharedWith->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div>{{ $share->sharedWith->name }}</div>
                                                            <small class="text-muted">{{ $share->sharedWith->email }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ __('common.not_available') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($share->permission ?? 'view') }}</span>
                                            </td>
                                            <td>
                                                @if($share->is_public)
                                                    <span class="badge badge-success">{{ __('common.public') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('common.private') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->expires_at)
                                                    @if($share->isExpired())
                                                        <span class="badge badge-danger">{{ __('common.expired') }}</span>
                                                    @else
                                                        <div>{{ $share->expires_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $share->expires_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-success">{{ __('common.no_expiry') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $share->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $share->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-settings-3-line"></i> {{ __('common.actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.files.view', $file->id) }}" 
                           class="btn btn-primary" 
                           target="_blank">
                            <i class="las la-eye"></i> {{ __('common.view_file') }}
                        </a>
                        <a href="{{ route('admin.files.download', $file->id) }}" 
                           class="btn btn-info">
                            <i class="las la-download"></i> {{ __('common.download_file') }}
                        </a>
                        <button type="button" 
                                class="btn btn-danger" 
                                data-toggle="modal" 
                                data-target="#deleteFileModal"
                                title="{{ __('common.delete_file') }}">
                            <i class="las la-trash"></i> {{ __('common.delete_file') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete File Modal -->
<div class="modal fade" id="deleteFileModal" tabindex="-1" role="dialog" aria-labelledby="deleteFileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteFileModalLabel">{{ __('common.delete_file') }}</h5>
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
@endsection

