@extends('layouts.app')

@section('title', __('groups.index.title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h3 class="mb-2 font-weight-bold text-white">
                                <i class="ri-team-fill mr-2"></i>{{ __('groups.index.my_groups') }}
                            </h3>
                            <p class="mb-0 opacity-75">{{ __('groups.index.subtitle') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="d-flex">
                                <div class="text-center mr-4">
                                    <h2 class="mb-0 font-weight-bold text-white">{{ $ownedGroups->count() + $myGroups->count() }}</h2>
                                    <small class="opacity-75">{{ __('groups.index.total_groups') }}</small>
                                </div>
                                <div class="text-center">
                                    <h2 class="mb-0 font-weight-bold text-white">{{ $ownedGroups->count() }}</h2>
                                    <small class="opacity-75">{{ __('groups.index.managing') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('groups.discover') }}" class="btn btn-light btn-sm mr-2">
                            <i class="ri-compass-3-line mr-1"></i> {{ __('groups.index.discover_groups') }}
                        </a>
                        <a href="{{ route('groups.create') }}" class="btn btn-warning btn-sm">
                            <i class="ri-add-circle-line mr-1"></i> {{ __('groups.index.create_new_group') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Owned Groups Section -->
    @if($ownedGroups->count() > 0)
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle p-2 mr-3">
                    <i class="ri-shield-star-line font-size-20"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold">{{ __('groups.index.managed_section') }}</h5>
                    <small class="text-muted">{{ __('groups.index.managed_count', ['count' => $ownedGroups->count()]) }}</small>
                </div>
            </div>
        </div>
        @foreach($ownedGroups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm hover-shadow transition-all" style="cursor: pointer;" onclick="window.location='{{ route('groups.show', $group) }}'">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex align-items-start mb-3">
                        @if($group->avatar)
                            <img src="{{ $group->avatar ? $group->avatar_url : asset('assets/images/user/1.jpg') }}" alt="{{ $group->name }}" 
                                 class="avatar-60 rounded-lg mr-3 shadow-sm">
                        @else
                            <div class="avatar-60 rounded-lg shadow-sm d-flex align-items-center justify-content-center mr-3"
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="ri-group-line font-size-24 text-white"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1 font-weight-bold">{{ $group->name }}</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-{{ $group->privacy === 'public' ? 'success' : 'secondary' }} badge-sm mr-2">
                                    <i class="ri-{{ $group->privacy === 'public' ? 'earth' : 'lock' }}-line"></i>
                                    {{ $group->privacy === 'public' ? __('groups.common.public') : __('groups.common.private') }}
                                </span>
                                <span class="badge badge-warning badge-sm">
                                    <i class="ri-vip-crown-line"></i> {{ __('groups.common.owner') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-muted mb-3 small" style="min-height: 40px;">
                        {{ Str::limit($group->description, 100) }}
                    </p>
                    
                    <!-- Stats -->
                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-primary mb-1">
                                    <i class="ri-user-line font-size-20"></i>
                                </div>
                                <h6 class="mb-0 font-weight-bold">{{ $group->members->count() }}</h6>
                                <small class="text-muted">{{ __('groups.common.members') }}</small>
                            </div>
                            <div class="col-4">
                                <div class="text-success mb-1">
                                    <i class="ri-file-line font-size-20"></i>
                                </div>
                                <h6 class="mb-0 font-weight-bold">{{ $group->files->count() }}</h6>
                                <small class="text-muted">{{ __('groups.common.files') }}</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning mb-1">
                                    <i class="ri-folder-line font-size-20"></i>
                                </div>
                                <h6 class="mb-0 font-weight-bold">{{ $group->folders->count() }}</h6>
                                <small class="text-muted">{{ __('groups.common.folders') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Footer -->
                <div class="card-footer bg-light border-0 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="ri-time-line mr-1"></i>
                            {{ $group->updated_at->diffForHumans() }}
                        </small>
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-primary" onclick="event.stopPropagation();">
                            <i class="ri-arrow-right-line"></i> {{ __('groups.common.details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Member Groups Section -->
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex align-items-center">
                <div class="bg-info text-white rounded-circle p-2 mr-3">
                    <i class="ri-team-line font-size-20"></i>
                </div>
                <div>
                    <h5 class="mb-0 font-weight-bold">{{ __('groups.index.joined_section') }}</h5>
                    <small class="text-muted">{{ __('groups.index.joined_count', ['count' => $myGroups->count()]) }}</small>
                </div>
            </div>
        </div>
        @if($myGroups->count() > 0)
            @foreach($myGroups as $group)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm hover-shadow transition-all" style="cursor: pointer;" onclick="window.location='{{ route('groups.show', $group) }}'">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex align-items-start mb-3">
                            @if($group->avatar)
                                <img src="{{ $group->avatar ? $group->avatar_url : asset('assets/images/user/1.jpg') }}" alt="{{ $group->name }}" 
                                     class="avatar-60 rounded-lg mr-3 shadow-sm">
                            @else
                                <div class="avatar-60 rounded-lg shadow-sm d-flex align-items-center justify-content-center mr-3"
                                     style="background: linear-gradient(135deg, #06beb6 0%, #48b1bf 100%);">
                                    <i class="ri-group-line font-size-24 text-white"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1 font-weight-bold">{{ $group->name }}</h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <span class="badge badge-{{ $group->privacy === 'public' ? 'success' : 'secondary' }} badge-sm mr-2 mb-1">
                                        <i class="ri-{{ $group->privacy === 'public' ? 'earth' : 'lock' }}-line"></i>
                                        {{ $group->privacy === 'public' ? __('groups.common.public') : __('groups.common.private') }}
                                    </span>
                                    @if($group->pivot->role === 'admin')
                                        <span class="badge badge-primary badge-sm mb-1">
                                            <i class="ri-admin-line"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge badge-secondary badge-sm mb-1">
                                            <i class="ri-user-line"></i> {{ __('groups.common.members') }}
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ri-user-star-line mr-1"></i>{{ $group->owner->name }}
                                </small>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <p class="text-muted mb-3 small" style="min-height: 40px;">
                            {{ Str::limit($group->description, 100) }}
                        </p>
                        
                        <!-- Stats -->
                        <div class="border-top pt-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="text-info mb-1">
                                        <i class="ri-user-line font-size-20"></i>
                                    </div>
                                    <h6 class="mb-0 font-weight-bold">{{ $group->members->count() }}</h6>
                                    <small class="text-muted">{{ __('groups.common.members') }}</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-success mb-1">
                                        <i class="ri-file-line font-size-20"></i>
                                    </div>
                                    <h6 class="mb-0 font-weight-bold">{{ $group->files->count() }}</h6>
                                    <small class="text-muted">{{ __('groups.common.files') }}</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-warning mb-1">
                                        <i class="ri-folder-line font-size-20"></i>
                                    </div>
                                    <h6 class="mb-0 font-weight-bold">{{ $group->folders->count() }}</h6>
                                    <small class="text-muted">{{ __('groups.common.folders') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Footer -->
                    <div class="card-footer bg-light border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ri-time-line mr-1"></i>
                                {{ $group->updated_at->diffForHumans() }}
                            </small>
                            <a href="{{ route('groups.show', $group) }}" class="btn btn-sm btn-info" onclick="event.stopPropagation();">
                                <i class="ri-arrow-right-line"></i> {{ __('groups.common.details') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <div class="d-inline-block p-4 rounded-circle bg-light">
                                <i class="ri-group-line" style="font-size: 64px; color: #ddd;"></i>
                            </div>
                        </div>
                        <h5 class="font-weight-bold mb-2">{{ __('groups.index.empty_title') }}</h5>
                        <p class="text-muted mb-4">{{ __('groups.index.empty_desc') }}</p>
                        <div>
                            <a href="{{ route('groups.create') }}" class="btn btn-primary mr-2">
                                <i class="ri-add-circle-line mr-1"></i> {{ __('groups.index.empty_create') }}
                            </a>
                            <a href="{{ route('groups.discover') }}" class="btn btn-outline-primary">
                                <i class="ri-compass-3-line mr-1"></i> {{ __('groups.index.empty_discover') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
.transition-all {
    transition: all 0.3s ease;
}
.rounded-lg {
    border-radius: 12px !important;
}
.avatar-60 {
    width: 60px;
    height: 60px;
}
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}
.opacity-75 {
    opacity: 0.75;
}
</style>
@endsection
