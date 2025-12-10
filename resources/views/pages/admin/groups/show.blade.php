@extends('layouts.app')

@section('title', $group->name . ' - Admin Groups')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white">
                    <li class="breadcrumb-item"><a href="{{ route('admin.groups.index') }}">{{ __('common.manage_groups') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $group->name }}</li>
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

            <!-- Group Information Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <div class="d-flex align-items-center">
                            @if($group->avatar)
                                <img src="{{ $group->avatar_url }}" alt="{{ $group->name }}" 
                                     class="rounded-lg mr-3" style="width: 60px; height: 60px; object-fit: cover;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="rounded-lg bg-primary text-white d-none align-items-center justify-content-center mr-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="ri-group-line font-size-32"></i>
                                </div>
                            @else
                                <div class="rounded-lg bg-primary text-white d-flex align-items-center justify-content-center mr-3" 
                                     style="width: 60px; height: 60px;">
                                    <i class="ri-group-line font-size-32"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="card-title mb-0">{{ $group->name }}</h4>
                                @if($group->description)
                                    <small class="text-muted">{{ $group->description }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($group->privacy === 'public')
                            <span class="badge badge-success badge-lg">
                                <i class="ri-global-line"></i> {{ __('common.public') }}
                            </span>
                        @else
                            <span class="badge badge-secondary badge-lg">
                                <i class="ri-lock-line"></i> {{ __('common.private') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('common.group_owner_admin') }}</h6>
                            <div class="d-flex align-items-center mb-3">
                                @if($group->owner->avatar)
                                    <img src="{{ $group->owner->avatar_url }}" alt="{{ $group->owner->name }}" 
                                         class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;"
                                         onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                         style="width: 40px; height: 40px; font-size: 16px;">
                                        {{ strtoupper(substr($group->owner->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div><strong>{{ $group->owner->name }}</strong></div>
                                    <small class="text-muted">{{ $group->owner->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('common.group_created_at') }}</h6>
                            <p>{{ $group->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-primary-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.group_members_count') }}</h6>
                                    <h3 class="mb-0">{{ $group->members->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.total_files') }}</h6>
                                    <h3 class="mb-0">{{ $group->files->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success-light">
                                <div class="card-body">
                                    <h6 class="text-muted">{{ __('common.total_folders') }}</h6>
                                    <h3 class="mb-0">{{ $group->folders->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-user-line"></i> {{ __('common.members') }} ({{ $group->members->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-color-heading">
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.email') }}</th>
                                    <th>{{ __('common.role') }}</th>
                                    <th>{{ __('common.joined_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($group->members as $member)
                                    <tr>
                                        <td>
                                            @if($member->avatar)
                                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" 
                                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $member->name }}</strong>
                                            @if($member->id === $group->owner_id)
                                                <span class="badge badge-warning ml-2">
                                                    <i class="ri-star-line"></i> {{ __('common.owner') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $member->email }}</td>
                                        <td>
                                            @if($member->pivot->role === 'admin')
                                                <span class="badge badge-danger">
                                                    <i class="ri-shield-user-line"></i> {{ __('common.admin') }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="ri-user-line"></i> {{ __('common.member') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $member->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $member->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            {{ __('common.no_members_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shared Files Section -->
            @if($group->files->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ri-file-line"></i> {{ __('common.shared_files') }} ({{ $group->files->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th style="width: 40px;"><i class="ri-file-line"></i></th>
                                        <th>{{ __('common.file_name_admin') }}</th>
                                        <th>{{ __('common.shared_by') }}</th>
                                        <th>{{ __('common.file_type') }}</th>
                                        <th>{{ __('common.file_size_admin') }}</th>
                                        <th>{{ __('common.shared_at') }}</th>
                                        <th class="text-right">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group->files as $file)
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
                                                    <small>{{ $file->user->name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ strtoupper($file->extension ?? 'N/A') }}</span>
                                            </td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>
                                                <div>{{ $file->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $file->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
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

            <!-- Shared Folders Section -->
            @if($group->folders->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ri-folder-line"></i> {{ __('common.shared_folders') }} ({{ $group->folders->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th style="width: 40px;"><i class="ri-folder-line"></i></th>
                                        <th>{{ __('common.folder_name_admin') }}</th>
                                        <th>{{ __('common.shared_by') }}</th>
                                        <th>{{ __('common.folder_privacy_admin') }}</th>
                                        <th>{{ __('common.shared_at') }}</th>
                                        <th class="text-right">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group->folders as $folder)
                                        <tr>
                                            <td>
                                                <i class="ri-folder-3-fill font-size-24" style="color: {{ $folder->color ?? '#3498db' }}"></i>
                                            </td>
                                            <td>
                                                <strong>{{ $folder->name }}</strong>
                                                @if($folder->description)
                                                    <br><small class="text-muted">{{ Str::limit($folder->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($folder->user->avatar)
                                                        <img src="{{ $folder->user->avatar_url }}" alt="{{ $folder->user->name }}" 
                                                             class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                             onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                                    @else
                                                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                             style="width: 30px; height: 30px; font-size: 12px;">
                                                            {{ strtoupper(substr($folder->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <small>{{ $folder->user->name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($folder->is_public)
                                                    <span class="badge badge-success"><i class="ri-global-line"></i> {{ __('common.folder_public') }}</span>
                                                @else
                                                    <span class="badge badge-secondary"><i class="ri-lock-line"></i> {{ __('common.folder_private') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $folder->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $folder->pivot->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.folders.view', $folder->id) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="{{ __('common.view_folder') }}"
                                                   target="_blank">
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

            @if($group->files->count() == 0 && $group->folders->count() == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-folder-open-line font-size-64 text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('common.no_shared_content') }}</h5>
                        <p class="text-muted">{{ __('common.no_files_or_folders_shared_in_group') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
