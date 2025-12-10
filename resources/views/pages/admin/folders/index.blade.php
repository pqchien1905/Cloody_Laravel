@extends('layouts.app')

@section('title', __('common.manage_folders') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_folders') }}</h4>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_folders_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-folder text-primary"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.active_folders_count') }}</h6>
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
                            <h6 class="mb-2 text-muted">{{ __('common.root_folders_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['root']) }}</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-folder-open text-info"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.public_folders_count') }}</h6>
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
                    <form method="GET" action="{{ route('admin.folders.index') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="{{ __('common.folder_name_admin') }}..." value="{{ request('search') }}">
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
                            <label for="status" class="small text-muted mb-1">{{ __('common.filter_by_status') }}</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('common.folder_active') }}</option>
                                <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>{{ __('common.folder_in_trash') }}</option>
                                <option value="favorite" {{ request('status') === 'favorite' ? 'selected' : '' }}>{{ __('common.folder_favorite') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="privacy" class="small text-muted mb-1">{{ __('common.filter_by_privacy') }}</label>
                            <select class="form-control" id="privacy" name="privacy">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="public" {{ request('privacy') === 'public' ? 'selected' : '' }}>{{ __('common.folder_public') }}</option>
                                <option value="private" {{ request('privacy') === 'private' ? 'selected' : '' }}>{{ __('common.folder_private') }}</option>
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
                    @if(request()->hasAny(['search', 'user_id', 'status', 'privacy', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.folders.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                        <i class="ri-folder-line"></i>
                                    </th>
                                    <th>{{ __('common.folder_name_admin') }}</th>
                                    <th>{{ __('common.folder_owner') }}</th>
                                    <th>{{ __('common.folder_parent') }}</th>
                                    <th>{{ __('common.folder_files_count') }}</th>
                                    <th>{{ __('common.folder_privacy_admin') }}</th>
                                    <th>{{ __('common.folder_status') }}</th>
                                    <th>{{ __('common.folder_created_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($folders as $folder)
                                <tr>
                                    <td>
                                        <i class="ri-folder-3-fill font-size-24" style="color: {{ $folder->color ?? '#3498db' }}"></i>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $folder->name }}</strong>
                                            @if($folder->is_favorite)
                                                <i class="ri-star-fill text-warning ml-1"></i>
                                            @endif
                                        </div>
                                        @if($folder->description)
                                            <small class="text-muted">{{ Str::limit($folder->description, 50) }}</small>
                                        @endif
                                        @if($folder->children_count > 0)
                                            <br><small class="text-info">
                                                <i class="ri-folder-line"></i> {{ $folder->children_count }} {{ __('common.folder_children_count') }}
                                            </small>
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
                                            <div>
                                                <div>{{ $folder->user->name }}</div>
                                                <small class="text-muted">{{ $folder->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($folder->parent)
                                            <span class="badge badge-info">
                                                <i class="ri-folder-line"></i> {{ $folder->parent->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="ri-home-line"></i> {{ __('common.folder_root') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $folder->files_count }}</strong> {{ __('common.files') }}
                                    </td>
                                    <td>
                                        @if($folder->is_public)
                                            <span class="badge badge-success">
                                                <i class="ri-global-line"></i> {{ __('common.folder_public') }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="ri-lock-line"></i> {{ __('common.folder_private') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($folder->is_trash)
                                            <span class="badge badge-warning">{{ __('common.folder_in_trash') }}</span>
                                        @elseif($folder->is_favorite)
                                            <span class="badge badge-success">{{ __('common.folder_favorite') }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ __('common.folder_active') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $folder->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $folder->created_at?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.folders.view', $folder->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="{{ __('common.view_folder') }}"
                                               target="_blank">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.folders.download', $folder->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="{{ __('common.download_folder') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deleteFolderModal{{ $folder->id }}"
                                                    title="{{ __('common.delete_folder') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="ri-folder-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_folders_found_admin') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($folders, 'links'))
                    <div class="card-footer">{{ $folders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Folder Modals -->
@foreach($folders as $folder)
<div class="modal fade" id="deleteFolderModal{{ $folder->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteFolderModalLabel{{ $folder->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteFolderModalLabel{{ $folder->id }}">{{ __('common.delete_folder') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.folders.destroy', $folder) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_folder_confirm') }}</p>
                    <p class="mb-2"><strong>{{ $folder->name }}</strong></p>
                    @if($folder->files_count > 0 || $folder->children_count > 0)
                        <p class="text-warning">
                            <i class="ri-alert-line"></i> 
                            Folder này chứa <strong>{{ $folder->files_count }}</strong> file và <strong>{{ $folder->children_count }}</strong> thư mục con.
                        </p>
                    @endif
                    <p class="text-muted">{{ __('common.folder_will_be_permanently_deleted') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_folder') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

