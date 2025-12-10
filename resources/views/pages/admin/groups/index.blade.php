@extends('layouts.app')

@section('title', __('common.manage_groups') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_groups') }}</h4>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_groups_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-users text-primary"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.public_groups_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['public']) }}</h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-2">
                            <i class="las la-globe text-success"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.total_members_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_members']) }}</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-user-friends text-info"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.total_group_files_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_files']) }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-file text-warning"></i>
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
                    <form method="GET" action="{{ route('admin.groups.index') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="{{ __('common.group_name_admin') }}..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="owner_id" class="small text-muted mb-1">{{ __('common.filter_by_owner') }}</label>
                            <select class="form-control" id="owner_id" name="owner_id">
                                <option value="">{{ __('common.all_users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('owner_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="privacy" class="small text-muted mb-1">{{ __('common.group_privacy_admin') }}</label>
                            <select class="form-control" id="privacy" name="privacy">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="public" {{ request('privacy') === 'public' ? 'selected' : '' }}>{{ __('common.public') }}</option>
                                <option value="private" {{ request('privacy') === 'private' ? 'selected' : '' }}>{{ __('common.private') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">{{ __('common.sort_by') }}</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>{{ __('common.sort_by_date') }}</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('common.sort_by_name') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="hidden" name="order" value="{{ request('order', 'desc') }}">
                            <button type="submit" class="btn btn-primary btn-block" title="{{ __('common.filter') }}">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'owner_id', 'privacy', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.groups.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                    <th style="width: 60px;">
                                        <i class="ri-group-line"></i>
                                    </th>
                                    <th>{{ __('common.group_name_admin') }}</th>
                                    <th>{{ __('common.group_owner_admin') }}</th>
                                    <th>{{ __('common.group_members_count') }}</th>
                                    <th>{{ __('common.group_files_count_admin') }}</th>
                                    <th>{{ __('common.group_folders_count_admin') }}</th>
                                    <th>{{ __('common.group_privacy_admin') }}</th>
                                    <th>{{ __('common.group_created_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                <tr>
                                    <td>
                                        @if($group->avatar)
                                            <img src="{{ $group->avatar_url }}" alt="{{ $group->name }}" 
                                                 class="rounded-lg" style="width: 50px; height: 50px; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-lg bg-primary text-white d-none align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="ri-group-line font-size-24"></i>
                                            </div>
                                        @else
                                            <div class="rounded-lg bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="ri-group-line font-size-24"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $group->name }}</strong>
                                        </div>
                                        @if($group->description)
                                            <small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($group->owner->avatar)
                                                <img src="{{ $group->owner->avatar_url }}" alt="{{ $group->owner->name }}" 
                                                     class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                     onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($group->owner->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $group->owner->name }}</div>
                                                <small class="text-muted">{{ $group->owner->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="ri-user-line"></i> {{ $group->members_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <i class="ri-file-line"></i> {{ $group->files_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <i class="ri-folder-line"></i> {{ $group->folders_count }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($group->privacy === 'public')
                                            <span class="badge badge-success">
                                                <i class="ri-global-line"></i> {{ __('common.public') }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="ri-lock-line"></i> {{ __('common.private') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $group->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $group->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.groups.view', $group->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="{{ __('common.view_group') }}"
                                               target="_blank">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deleteGroupModal{{ $group->id }}"
                                                    title="{{ __('common.delete_group') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="ri-group-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_groups_found_admin') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($groups, 'links'))
                    <div class="card-footer">{{ $groups->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Group Modals -->
@foreach($groups as $group)
<div class="modal fade" id="deleteGroupModal{{ $group->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteGroupModalLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteGroupModalLabel{{ $group->id }}">{{ __('common.delete_group') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.groups.destroy', $group) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_group_confirm') }}</p>
                    <p class="mb-2"><strong>{{ $group->name }}</strong></p>
                    @if($group->members_count > 0 || $group->files_count > 0 || $group->folders_count > 0)
                        <p class="text-warning">
                            <i class="ri-alert-line"></i> 
                            Group này có <strong>{{ $group->members_count }}</strong> thành viên, 
                            <strong>{{ $group->files_count }}</strong> file và 
                            <strong>{{ $group->folders_count }}</strong> folder.
                        </p>
                    @endif
                    <p class="text-muted">{{ __('common.group_will_be_permanently_deleted') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_group') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

