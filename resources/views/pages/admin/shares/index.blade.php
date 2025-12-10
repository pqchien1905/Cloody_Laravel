@extends('layouts.app')

@section('title', __('common.manage_shares') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_shares') }}</h4>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_shares_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-share-alt text-primary"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.file_shares_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['file_shares']) }}</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-file text-info"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.active_shares_count') }}</h6>
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
                            <h6 class="mb-2 text-muted">{{ __('common.public_shares_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['public']) }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-globe text-warning"></i>
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
                    <form method="GET" action="{{ route('admin.shares.index') }}" class="row align-items-end">
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Tên file/folder..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="type" class="small text-muted mb-1">{{ __('common.filter_by_share_type') }}</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="file" {{ request('type') === 'file' ? 'selected' : '' }}>{{ __('common.share_file') }}</option>
                                <option value="folder" {{ request('type') === 'folder' ? 'selected' : '' }}>{{ __('common.share_folder') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="shared_by" class="small text-muted mb-1">{{ __('common.filter_by_shared_by') }}</label>
                            <select class="form-control" id="shared_by" name="shared_by">
                                <option value="">{{ __('common.all_users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('shared_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="status" class="small text-muted mb-1">{{ __('common.share_status') }}</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('common.share_active') }}</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('common.share_expired') }}</option>
                                <option value="public" {{ request('status') === 'public' ? 'selected' : '' }}>{{ __('common.share_public') }}</option>
                                <option value="private" {{ request('status') === 'private' ? 'selected' : '' }}>{{ __('common.share_private') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="permission" class="small text-muted mb-1">{{ __('common.filter_by_permission') }}</label>
                            <select class="form-control" id="permission" name="permission">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="view" {{ request('permission') === 'view' ? 'selected' : '' }}>{{ __('common.share_permission_view') }}</option>
                                <option value="download" {{ request('permission') === 'download' ? 'selected' : '' }}>{{ __('common.share_permission_download') }}</option>
                                <option value="edit" {{ request('permission') === 'edit' ? 'selected' : '' }}>{{ __('common.share_permission_edit') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="order" value="{{ request('order', 'desc') }}">
                            <button type="submit" class="btn btn-primary btn-block" title="{{ __('common.filter') }}">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'type', 'shared_by', 'status', 'permission']))
                        <div class="mt-2">
                            <a href="{{ route('admin.shares.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                    <th style="width: 50px;">
                                        <i class="ri-share-line"></i>
                                    </th>
                                    <th>{{ __('common.share_type') }}</th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.share_shared_by') }}</th>
                                    <th>{{ __('common.share_shared_with') }}</th>
                                    <th>{{ __('common.share_permission') }}</th>
                                    <th>{{ __('common.share_status') }}</th>
                                    <th>{{ __('common.share_expires_at') }}</th>
                                    <th>{{ __('common.share_created_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginator as $share)
                                <tr>
                                    <td>
                                        @if($share->share_type === 'file')
                                            <i class="ri-file-line font-size-24 text-primary"></i>
                                        @else
                                            <i class="ri-folder-line font-size-24 text-warning"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($share->share_type === 'file')
                                            <span class="badge badge-primary">{{ __('common.share_file') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('common.share_folder') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($share->share_type === 'file')
                                            <div>
                                                <strong>{{ $share->file->name ?? __('common.not_available') }}</strong>
                                            </div>
                                            <small class="text-muted">{{ $share->file->original_name ?? '' }}</small>
                                        @else
                                            <div>
                                                <strong>{{ $share->folder->name ?? __('common.not_available') }}</strong>
                                            </div>
                                            @if($share->folder->description)
                                                <small class="text-muted">{{ Str::limit($share->folder->description, 30) }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($share->sharedBy->avatar)
                                                <img src="{{ $share->sharedBy->avatar_url }}" alt="{{ $share->sharedBy->name }}" 
                                                     class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                     onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($share->sharedBy->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $share->sharedBy->name }}</div>
                                                <small class="text-muted">{{ $share->sharedBy->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($share->sharedWith)
                                            <div class="d-flex align-items-center">
                                                @if($share->sharedWith->avatar)
                                                    <img src="{{ $share->sharedWith->avatar_url }}" alt="{{ $share->sharedWith->name }}" 
                                                         class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                         onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mr-2" 
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
                                            <span class="badge badge-info">{{ __('common.share_public') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($share->permission === 'view')
                                            <span class="badge badge-secondary">{{ __('common.share_permission_view') }}</span>
                                        @elseif($share->permission === 'download')
                                            <span class="badge badge-info">{{ __('common.share_permission_download') }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ __('common.share_permission_edit') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($share->isExpired())
                                            <span class="badge badge-danger">{{ __('common.share_expired') }}</span>
                                        @elseif($share->is_public)
                                            <span class="badge badge-success">{{ __('common.share_public') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('common.share_private') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($share->expires_at)
                                            <div>{{ $share->expires_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $share->expires_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">{{ __('common.share_no_expiry') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $share->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $share->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-toggle="modal" 
                                                data-target="#revokeShareModal{{ $share->id }}"
                                                title="{{ __('common.revoke_share') }}">
                                            <i class="las la-ban"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="ri-share-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_shares_found_admin') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($paginator, 'links'))
                    <div class="card-footer">{{ $paginator->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Revoke Share Modals -->
@foreach($paginator as $share)
<div class="modal fade" id="revokeShareModal{{ $share->id }}" tabindex="-1" role="dialog" aria-labelledby="revokeShareModalLabel{{ $share->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title text-white" id="revokeShareModalLabel{{ $share->id }}">{{ __('common.revoke_share') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.shares.destroy', $share->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="type" value="{{ $share->share_type }}">
                <div class="modal-body">
                    <p>{{ __('common.revoke_share_confirm') }}</p>
                    <p class="mb-2">
                                <strong>
                                    @if($share->share_type === 'file')
                                        {{ $share->file->name ?? __('common.not_available') }}
                                    @else
                                        {{ $share->folder->name ?? __('common.not_available') }}
                                    @endif
                                </strong>
                    </p>
                    <p class="text-muted">
                        {{ __('common.share_shared_by') }}: <strong>{{ $share->sharedBy->name }}</strong>
                        @if($share->sharedWith)
                            <br>{{ __('common.share_shared_with') }}: <strong>{{ $share->sharedWith->name }}</strong>
                        @else
                            <br>{{ __('common.share_shared_with') }}: <strong>{{ __('common.share_public') }}</strong>
                        @endif
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('common.revoke_share') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

