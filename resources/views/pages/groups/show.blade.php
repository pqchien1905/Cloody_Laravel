@extends('layouts.app')

@section('title', $group->name . ' - Cloody')

@push('styles')
<style>
    /* Hover effect for file name links */
    .hover-primary:hover {
        color: #667eea !important;
        text-decoration: underline !important;
    }
    
    .hover-primary {
        transition: all 0.2s ease;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('groups.index') }}" class="btn btn-link text-decoration-none p-0">
                <i class="ri-arrow-left-line mr-1"></i> {{ __('common.back_to_groups_list') }}
            </a>
        </div>
    </div>

    <!-- Group Header with Gradient -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="overflow: visible;">
                <div class="card-header border-0 p-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 180px; border-radius: 0.25rem 0.25rem 0 0; overflow: hidden;">
                    <div class="p-4">
                        <div class="d-flex align-items-start">
                            @if($group->avatar)
                                <img src="{{ $group->avatar_url }}" alt="{{ $group->name }}" 
                                     class="avatar-100 rounded-lg shadow-lg border border-white mr-4"
                                     style="border-width: 4px !important;">
                            @else
                                <div class="avatar-100 rounded-lg shadow-lg d-flex align-items-center justify-content-center mr-4 border border-white"
                                     style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-width: 4px !important;">
                                    <i class="ri-group-line text-white" style="font-size: 48px;"></i>
                                </div>
                            @endif
                            
                            <div class="flex-grow-1 text-white">
                                <h2 class="mb-2 font-weight-bold text-white">{{ $group->name }}</h2>
                                <p class="mb-3 opacity-90">{{ $group->description }}</p>
                                <div class="d-flex align-items-center flex-wrap mb-3">
                                    <span class="badge badge-light badge-lg mr-2 mb-2">
                                        <i class="ri-{{ $group->privacy === 'public' ? 'earth' : 'lock' }}-line mr-1"></i>
                                        {{ $group->privacy === 'public' ? __('groups.common.public') : __('groups.common.private') }}
                                    </span>
                                    <span class="badge badge-light badge-lg mr-2 mb-2">
                                        <i class="ri-user-line mr-1"></i>{{ $group->members->count() }} {{ __('common.members') }}
                                    </span>
                                    <span class="badge badge-light badge-lg mb-2">
                                        <i class="ri-user-star-line mr-1"></i>{{ $group->owner->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="card-body bg-light" style="position: relative; overflow: visible;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex flex-wrap mb-2 mb-md-0">
                            @if($isMember)
                            <button class="btn btn-primary shadow-sm mr-2 mb-2" data-toggle="modal" data-target="#shareWithGroupModal">
                                <i class="ri-share-line mr-1"></i>{{ __('common.share_files') }}
                            </button>
                            @endif
                            
                            <button class="btn btn-info shadow-sm mr-2 mb-2" data-toggle="modal" data-target="#membersModal">
                                <i class="ri-group-line mr-1"></i>{{ __('common.members') }} ({{ $group->members->count() }})
                            </button>
                        </div>
                        
                        <div class="d-flex flex-wrap">
                            @if($isOwner || $isAdmin)
                            <div class="dropdown mr-2 mb-2" style="position: relative; z-index: 1050;">
                                <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" 
                                        id="groupActions" data-toggle="dropdown">
                                    <i class="ri-settings-3-line mr-1"></i>{{ __('common.settings') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="z-index: 1060;">
                                    <a class="dropdown-item" href="{{ route('groups.edit', $group) }}">
                                        <i class="ri-edit-line mr-2"></i>{{ __('common.edit_group') }}
                                    </a>
                                    @if($isOwner)
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('groups.destroy', $group) }}" method="POST" 
                                          onsubmit="return confirm('{{ __('common.are_you_sure_delete_group') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="ri-delete-bin-line mr-2"></i>{{ __('common.delete_group') }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            @if($isMember && !$isOwner)
                            <form action="{{ route('groups.leave', $group) }}" method="POST" class="d-inline mb-2"
                                  onsubmit="return confirm('{{ __('common.are_you_sure_leave_group') }}');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger shadow-sm">
                                    <i class="ri-logout-box-r-line mr-1"></i>{{ __('common.leave_group') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- {{ __('common.files_folders') }} -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <!-- Folders Section -->
            @if($folders->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded p-2 mr-3">
                            <i class="ri-folder-line font-size-20"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold">{{ __('common.folders') }}</h5>
                            <small class="text-muted">{{ $folders->count() }} {{ __('common.folders') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($folders as $folder)
                        <div class="col-sm-6 col-xl-4 mb-3">
                            <div class="card border-0 shadow-sm hover-card h-100" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" 
                                 data-toggle="modal" data-target="#folderDetailModal{{ $folder->id }}">
                                <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="folder-icon mr-3">
                                                <i class="ri-folder-fill text-warning" style="font-size: 42px;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 font-weight-bold text-truncate text-dark" title="{{ $folder->name }}">
                                                    {{ $folder->name }}
                                                </h6>
                                                <small class="text-muted d-block">
                                                    <i class="ri-user-line"></i>
                                                    {{ $folder->pivot->shared_by == Auth::id() ? __('common.you') : ($folder->sharedBy ? $folder->sharedBy->name : __('common.not_available')) }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge badge-{{ $folder->pivot->permission == 'full' ? 'success' : ($folder->pivot->permission == 'edit' ? 'primary' : 'secondary') }}">
                                                <i class="ri-shield-check-line"></i>
                                                {{ ucfirst($folder->pivot->permission) }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $folder->pivot->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- {{ __('common.files_section') }} -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded p-2 mr-3">
                            <i class="ri-file-line font-size-20"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold">{{ __('common.documents') }}</h5>
                            <small class="text-muted">{{ $files->count() }} {{ __('common.files') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($files->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($files as $file)
                        <div class="list-group-item list-group-item-action hover-bg">
                            <div class="row align-items-center">
                                <div class="col-md-8 mb-2 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-wrapper mr-3">
                                            @php
                                                $ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
                                                $iconMap = [
                                                    'pdf' => ['icon' => 'ri-file-pdf-line', 'color' => 'danger'],
                                                    'doc' => ['icon' => 'ri-file-word-line', 'color' => 'primary'],
                                                    'docx' => ['icon' => 'ri-file-word-line', 'color' => 'primary'],
                                                    'xls' => ['icon' => 'ri-file-excel-line', 'color' => 'success'],
                                                    'xlsx' => ['icon' => 'ri-file-excel-line', 'color' => 'success'],
                                                    'ppt' => ['icon' => 'ri-file-ppt-line', 'color' => 'warning'],
                                                    'pptx' => ['icon' => 'ri-file-ppt-line', 'color' => 'warning'],
                                                    'zip' => ['icon' => 'ri-file-zip-line', 'color' => 'secondary'],
                                                    'rar' => ['icon' => 'ri-file-zip-line', 'color' => 'secondary'],
                                                ];
                                                $fileIcon = $iconMap[$ext] ?? ['icon' => 'ri-file-line', 'color' => 'info'];
                                            @endphp
                                            <div class="file-icon bg-{{ $fileIcon['color'] }}-light text-{{ $fileIcon['color'] }} rounded-lg d-flex align-items-center justify-content-center"
                                                 style="width: 48px; height: 48px;">
                                                <i class="{{ $fileIcon['icon'] }} font-size-24"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            @if(in_array($file->pivot->permission, ['view', 'download', 'edit']))
                                            <a href="{{ route('cloody.files.view', $file->id) }}" 
                                               class="text-dark text-decoration-none hover-primary" 
                                               target="_blank"
                                               title="{{ __('common.click_to_view') }}">
                                                <h6 class="mb-1 font-weight-bold text-truncate">{{ $file->name }}</h6>
                                            </a>
                                            @else
                                            <h6 class="mb-1 font-weight-bold text-truncate">{{ $file->name }}</h6>
                                            @endif
                                            <div class="d-flex align-items-center flex-wrap">
                                                <small class="text-muted mr-2">
                                                    <i class="ri-hard-drive-line"></i> {{ number_format($file->size / 1024, 2) }} KB
                                                </small>
                                                <span class="text-muted mx-1">•</span>
                                                <small class="text-muted mr-2">
                                                    <i class="ri-user-line"></i> {{ $file->pivot->shared_by == Auth::id() ? __('common.you') : ($file->sharedBy ? $file->sharedBy->name : __('common.not_available')) }}
                                                </small>
                                                <span class="text-muted mx-1 d-none d-md-inline">•</span>
                                                <span class="badge badge-{{ $file->pivot->permission == 'edit' ? 'success' : ($file->pivot->permission == 'download' ? 'primary' : 'secondary') }} px-2 py-1">
                                                    <i class="ri-shield-check-line mr-1"></i>{{ ucfirst($file->pivot->permission) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if(in_array($file->pivot->permission, ['view', 'download', 'edit']))
                                        <a href="{{ route('cloody.files.view', $file->id) }}" 
                                           class="btn btn-outline-info" title="{{ __('common.view') }}" target="_blank">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endif
                                        @if(in_array($file->pivot->permission, ['download', 'edit']))
                                        <a href="{{ route('cloody.files.download', $file->id) }}" 
                                           class="btn btn-outline-primary" title="{{ __('common.download') }}">
                                            <i class="ri-download-line"></i>
                                        </a>
                                        @endif
                                        @if($isAdmin || $isOwner || $file->pivot->shared_by == Auth::id())
                                        <button type="button" class="btn btn-outline-danger" title="{{ __('common.delete') }}"
                                                onclick="if(confirm('{{ __('common.remove_file_from_group') }}')) document.getElementById('remove-file-{{ $file->id }}').submit();">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <form id="remove-file-{{ $file->id }}" action="{{ route('groups.files.remove-file', [$group, $file->id]) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ri-file-line text-muted" style="font-size: 64px;"></i>
                        </div>
                        <h6 class="text-muted">{{ __('common.no_files_shared_yet') }}</h6>
                        <p class="text-muted mb-0">{{ __('common.click_share_files') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Group Info Sidebar -->
        <div class="col-lg-4">
            <!-- Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="ri-bar-chart-line mr-2"></i>{{ __('common.statistics') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6 mb-3">
                            <div class="p-3 rounded bg-primary-light">
                                <i class="ri-user-line text-primary font-size-24 mb-2"></i>
                                <h4 class="mb-0 font-weight-bold text-primary">{{ $group->members->count() }}</h4>
                                <small class="text-muted">{{ __('common.members') }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 rounded bg-success-light">
                                <i class="ri-shield-user-line text-success font-size-24 mb-2"></i>
                                <h4 class="mb-0 font-weight-bold text-success">{{ $group->admins->count() }}</h4>
                                <small class="text-muted">{{ __('common.administrator') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-info-light">
                                <i class="ri-file-line text-info font-size-24 mb-2"></i>
                                <h4 class="mb-0 font-weight-bold text-info">{{ $files->count() }}</h4>
                                <small class="text-muted">{{ __('common.files') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-warning-light">
                                <i class="ri-folder-line text-warning font-size-24 mb-2"></i>
                                <h4 class="mb-0 font-weight-bold text-warning">{{ $folders->count() }}</h4>
                                <small class="text-muted">{{ __('common.folders') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="ri-information-line mr-2"></i>{{ __('common.information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-calendar-line text-primary mr-2"></i>
                            <small class="text-muted">{{ __('common.created_date') }}</small>
                        </div>
                        <strong>{{ $group->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</strong>
                        <br><small class="text-muted">{{ $group->created_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans() }}</small>
                    </div>
                    <div class="info-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-time-line text-success mr-2"></i>
                            <small class="text-muted">{{ __('common.last_updated') }}</small>
                        </div>
                        <strong>{{ $group->updated_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</strong>
                        <br><small class="text-muted">{{ $group->updated_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans() }}</small>
                    </div>
                    <div class="info-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-{{ $group->privacy === 'public' ? 'earth' : 'lock' }}-line text-info mr-2"></i>
                            <small class="text-muted">{{ __('common.privacy') }}</small>
                        </div>
                        <span class="badge badge-{{ $group->privacy === 'public' ? 'success' : 'secondary' }} px-3 py-2">
                            {{ $group->privacy === 'public' ? __('groups.common.public') : __('groups.common.private') }}
                        </span>
                    </div>
                    <div class="info-item">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-user-star-line text-warning mr-2"></i>
                            <small class="text-muted">{{ __('common.group_owner') }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            @if($group->owner->avatar)
                                <img src="{{ $group->owner->avatar ? $group->owner->avatar_url : asset('assets/images/user/1.jpg') }}" 
                                     alt="{{ $group->owner->name }}" class="avatar-40 rounded-circle mr-2">
                            @else
                                <div class="avatar-40 rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center mr-2">
                                    {{ strtoupper(substr($group->owner->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong>{{ $group->owner->name }}</strong>
                                <br><small class="text-muted">{{ $group->owner->email }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Members Modal -->
<div class="modal fade" id="membersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-group-line mr-2"></i>{{ __('common.group_members') }} ({{ $group->members->count() }})</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($isOwner || $isAdmin)
                <div class="mb-3">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addMemberModal" data-dismiss="modal">
                        <i class="ri-user-add-line mr-1"></i>{{ __('common.add_member') }}
                    </button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('common.member') }}</th>
                                <th>{{ __('common.email') }}</th>
                                <th>{{ __('common.role') }}</th>
                                <th>{{ __('common.joined') }}</th>
                                @if($isOwner || $isAdmin)
                                <th class="text-right">{{ __('common.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group->members as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($member->avatar)
                                            <img src="{{ $member->avatar ? $member->avatar_url : asset('assets/images/user/1.jpg') }}" 
                                                 alt="{{ $member->name }}" class="avatar-40 rounded-circle mr-2">
                                        @else
                                            <div class="avatar-40 rounded-circle bg-secondary-light text-secondary 
                                                        d-flex align-items-center justify-content-center mr-2">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $member->name }}</span>
                                        @if($group->isOwner($member->id))
                                            <span class="badge badge-warning badge-pill ml-2">{{ __('common.owner') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $member->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $member->pivot->role === 'admin' ? 'primary' : 'secondary' }} badge-pill">
                                        {{ $member->pivot->role === 'admin' ? __('common.admin') : __('common.member') }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($member->pivot->joined_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
                                @if($isOwner || $isAdmin)
                                <td class="text-right">
                                    @if(!$group->isOwner($member->id))
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if($isOwner)
                                            <form action="{{ route('groups.members.update-role', [$group, $member]) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" 
                                                       value="{{ $member->pivot->role === 'admin' ? 'member' : 'admin' }}">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="ri-shield-user-line mr-2"></i>
                                                    {{ $member->pivot->role === 'admin' ? __('common.demote_to_member') : __('common.promote_to_admin') }}
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            @endif
                                            <form action="{{ route('groups.members.remove', [$group, $member]) }}" 
                                                  method="POST" onsubmit="return confirm('{{ __('common.remove_this_member') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ri-user-unfollow-line mr-2"></i>{{ __('common.remove_member') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@if($isOwner || $isAdmin)
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('common.add_member') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('groups.members.add', $group) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_email">{{ __('common.user_email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="user_email" name="user_email" 
                               placeholder="{{ __('common.enter_user_email') }}" required>
                        <small class="form-text text-muted">{{ __('common.enter_email_registered_user') }}</small>
                    </div>
                    <div class="form-group">
                        <label>{{ __('common.role') }} <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="role_member" name="role" class="custom-control-input" 
                                   value="member" checked>
                            <label class="custom-control-label" for="role_member">
                                {{ __('common.member_permissions') }}
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="role_admin" name="role" class="custom-control-input" value="admin">
                            <label class="custom-control-label" for="role_admin">
                                {{ __('common.admin_permissions') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-user-add-line mr-1"></i>{{ __('common.add_member') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Share with Group Modal -->
@if($isMember)
<div class="modal fade" id="shareWithGroupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-share-line mr-2"></i>{{ __('common.share_with_group') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#shareFileTab">
                            <i class="ri-file-line mr-1"></i>{{ __('common.file') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#shareFolderTab">
                            <i class="ri-folder-line mr-1"></i>{{ __('common.folder') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Share File Tab -->
                    <div class="tab-pane fade show active" id="shareFileTab">
                        <form action="{{ route('groups.files.share-file', $group) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="file_id">{{ __('common.select_file') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="file_id" name="file_id" required>
                                    <option value="">-- {{ __('common.select_file') }} --</option>
                                    @foreach(Auth::user()->files()->where('is_trash', false)->get() as $userFile)
                                        <option value="{{ $userFile->id }}">{{ $userFile->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('common.access_permission') }} <span class="text-danger">*</span></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="file_perm_view" name="permission" class="custom-control-input" 
                                           value="view" checked>
                                    <label class="custom-control-label" for="file_perm_view">
                                        {{ __('common.view_only') }}
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="file_perm_download" name="permission" class="custom-control-input" 
                                           value="download">
                                    <label class="custom-control-label" for="file_perm_download">
                                        {{ __('common.view_and_download') }}
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="file_perm_edit" name="permission" class="custom-control-input" 
                                           value="edit">
                                    <label class="custom-control-label" for="file_perm_edit">
                                        {{ __('common.full_access') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="ri-share-line mr-1"></i>{{ __('common.share_file') }}
                            </button>
                        </form>
                    </div>
                    
                    <!-- Share Folder Tab -->
                    <div class="tab-pane fade" id="shareFolderTab">
                        <form action="{{ route('groups.files.share-folder', $group) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="folder_id">{{ __('common.select_folder') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="folder_id" name="folder_id" required>
                                    <option value="">-- {{ __('common.select_folder') }} --</option>
                                    @foreach(Auth::user()->folders()->where('is_trash', false)->get() as $userFolder)
                                        <option value="{{ $userFolder->id }}">{{ $userFolder->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('common.access_permission') }} <span class="text-danger">*</span></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="folder_perm_view" name="permission" class="custom-control-input" 
                                           value="view" checked>
                                    <label class="custom-control-label" for="folder_perm_view">
                                        {{ __('common.view_only') }}
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="folder_perm_edit" name="permission" class="custom-control-input" 
                                           value="edit">
                                    <label class="custom-control-label" for="folder_perm_edit">
                                        {{ __('common.view_and_edit') }}
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="folder_perm_full" name="permission" class="custom-control-input" 
                                           value="full">
                                    <label class="custom-control-label" for="folder_perm_full">
                                        {{ __('common.full_access') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="ri-share-line mr-1"></i>{{ __('common.share_folder') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Folder Detail Modals -->
@foreach($folders as $folder)
<div class="modal fade" id="folderDetailModal{{ $folder->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="ri-folder-fill mr-2"></i>{{ $folder->name }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Folder Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <p class="mb-2">
                            <i class="ri-user-line text-muted mr-2"></i>
                            <strong>{{ __('common.shared_by') }}:</strong> 
                            {{ $folder->pivot->shared_by == Auth::id() ? __('common.you') : ($folder->sharedBy ? $folder->sharedBy->name : __('common.not_available')) }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <i class="ri-shield-check-line text-muted mr-2"></i>
                            <strong>{{ __('common.permission') }}:</strong>
                            <span class="badge badge-{{ $folder->pivot->permission == 'full' ? 'success' : ($folder->pivot->permission == 'edit' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($folder->pivot->permission) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <i class="ri-time-line text-muted mr-2"></i>
                            <strong>{{ __('common.shared_date') }}:</strong> {{ $folder->pivot->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Loading State -->
                <div class="text-center py-5" id="folderLoading{{ $folder->id }}">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">{{ __('common.loading') }}...</p>
                </div>

                <!-- Folder Content -->
                <div id="folderContent{{ $folder->id }}" style="display: none;">
                    <h6 class="font-weight-bold mb-3">
                        <i class="ri-file-list-line mr-2"></i>{{ __('common.folder_contents') }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('common.file_name') }}</th>
                                    <th>{{ __('common.size') }}</th>
                                    <th>{{ __('common.uploaded_date') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="folderFiles{{ $folder->id }}">
                                <!-- Files will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="folderEmpty{{ $folder->id }}" style="display: none;">
                    <div class="text-center py-5">
                        <i class="ri-folder-open-line text-muted" style="font-size: 64px;"></i>
                        <p class="text-muted mt-3">{{ __('common.empty_folder') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// Wait for jQuery to be ready
$(document).ready(function() {
    console.log('jQuery loaded, initializing folder modals...');
    
    // Load folder content when modal opens
    @foreach($folders as $folder)
    $('#folderDetailModal{{ $folder->id }}').on('show.bs.modal', function () {
        const folderId = {{ $folder->id }};
        const loadingDiv = $('#folderLoading' + folderId);
        const contentDiv = $('#folderContent' + folderId);
        const emptyDiv = $('#folderEmpty' + folderId);
        const filesBody = $('#folderFiles' + folderId);
        
        console.log('Opening folder modal:', folderId);
        
        // Show loading
        loadingDiv.show();
        contentDiv.hide();
        emptyDiv.hide();
        
        // Fetch folder files via AJAX
        fetch(`/cloody/folders/${folderId}/files`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Folder data received:', data);
            loadingDiv.hide();
            
            if (data.success && data.files && data.files.length > 0) {
                filesBody.empty();
                data.files.forEach(file => {
                    const row = `
                        <tr>
                            <td>
                                <i class="ri-file-line text-primary mr-2"></i>
                                <a href="/cloody/files/${file.id}/view" target="_blank" 
                                   class="text-dark text-decoration-none hover-primary"
                                   title="{{ __('common.click_to_view') }}">
                                    ${file.name}
                                </a>
                            </td>
                            <td>${(file.size / 1024).toFixed(2)} KB</td>
                            <td>${file.created_at}</td>
                            <td class="text-right">
                                <a href="/cloody/files/${file.id}/view" target="_blank" 
                                   class="btn btn-sm btn-outline-info" title="{{ __('common.view') }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="/cloody/files/${file.id}/download" 
                                   class="btn btn-sm btn-outline-primary" title="{{ __('common.download') }}">
                                    <i class="ri-download-line"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    filesBody.append(row);
                });
                contentDiv.show();
            } else {
                console.log('No files or empty folder');
                emptyDiv.show();
            }
        })
        .catch(error => {
            console.error('Error loading folder:', error);
            loadingDiv.hide();
            contentDiv.hide();
            emptyDiv.html(`
                <div class="text-center py-5">
                    <i class="ri-error-warning-line text-danger" style="font-size: 64px;"></i>
                    <p class="text-danger mt-3">{{ __('common.unable_to_load_folder') }}</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `);
            emptyDiv.show();
        });
    });
    @endforeach
});
</script>
@endpush

<style>
/* Avatar Styles */
.avatar-100 {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.rounded-lg {
    border-radius: 12px !important;
}

/* Badge Styles */
.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

/* Hover Effects */
.hover-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.hover-card:hover, a:hover .hover-card {
    transform: translateY(-5px) !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.hover-bg:hover {
    background-color: #f8f9fa !important;
}

/* Background Light Colors */
.bg-primary-light {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.bg-success-light {
    background-color: rgba(40, 199, 111, 0.1) !important;
}

.bg-info-light {
    background-color: rgba(23, 162, 184, 0.1) !important;
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* List Group Item */
.list-group-item {
    border-left: none !important;
    border-right: none !important;
    padding: 1.25rem 1.5rem;
}

.list-group-item:first-child {
    border-top: none !important;
}

.list-group-item:last-child {
    border-bottom: none !important;
}

/* File Icon */
.file-icon {
    transition: all 0.3s ease;
}

.hover-bg:hover .file-icon {
    transform: scale(1.1);
}

/* Button Group */
.btn-group-sm > .btn, .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Card Shadow */
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* Opacity */
.opacity-90 {
    opacity: 0.9;
}

/* Info Item */
.info-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeIn 0.5s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .avatar-100 {
        width: 70px;
        height: 70px;
    }
    
    .btn-group-sm {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group-sm > .btn {
        border-radius: 0.25rem !important;
        margin-bottom: 0.25rem;
    }
}
</style>

@endsection
