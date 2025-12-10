{{-- Removed: Admin Trash page has been disabled --}}
@php( abort(404) )
@extends('layouts.app')

@section('title', __('common.manage_trash') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_trash') }}</h4>
            @if($paginator->count() > 0)
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#emptyTrashModal">
                <i class="las la-trash-alt"></i> {{ __('common.empty_trash') }}
            </button>
            @endif
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_trash_files_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_files']) }}</h3>
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
                            <h6 class="mb-2 text-muted">{{ __('common.total_trash_folders_count') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_folders']) }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-folder text-warning"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.total_trash_size') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_size'] / 1024 / 1024, 2) }} MB</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-database text-info"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.oldest_trash_item') }}</h6>
                            <h3 class="mb-0" style="font-size: 14px;">
                                @if($stats['oldest_trash'])
                                    {{ $stats['oldest_trash']->diffForHumans() }}
                                @else
                                    —
                                @endif
                            </h3>
                        </div>
                        <div class="icon-small bg-danger-light rounded p-2">
                            <i class="las la-clock text-danger"></i>
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
                    <form method="GET" action="{{ route('admin.trash.index') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Tên file/folder..." value="{{ request('search') }}">
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
                            <label for="type" class="small text-muted mb-1">{{ __('common.trash_item_type') }}</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="file" {{ request('type') === 'file' ? 'selected' : '' }}>{{ __('common.trash_file') }}</option>
                                <option value="folder" {{ request('type') === 'folder' ? 'selected' : '' }}>{{ __('common.trash_folder') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">{{ __('common.sort_by') }}</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="trashed_at" {{ request('sort') === 'trashed_at' ? 'selected' : '' }}>{{ __('common.sort_by_date') }}</option>
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
                    @if(request()->hasAny(['search', 'user_id', 'type', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.trash.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                        <i class="ri-delete-bin-line"></i>
                                    </th>
                                    <th>{{ __('common.trash_item_type') }}</th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.trash_owner') }}</th>
                                    <th>{{ __('common.file_size_admin') }}</th>
                                    <th>{{ __('common.trash_deleted_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginator as $item)
                                <tr>
                                    <td>
                                        @if($item->item_type === 'file')
                                            <i class="ri-file-line font-size-24 text-primary"></i>
                                        @else
                                            <i class="ri-folder-line font-size-24 text-warning"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->item_type === 'file')
                                            <span class="badge badge-primary">{{ __('common.trash_file') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('common.trash_folder') }}</span>
                                            @if(isset($item->files_count) && $item->files_count > 0)
                                                <br><small class="text-muted">{{ $item->files_count }} {{ __('common.files') }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $item->name }}</strong>
                                        </div>
                                        @if($item->item_type === 'file' && isset($item->original_name) && $item->original_name !== $item->name)
                                            <small class="text-muted">{{ $item->original_name }}</small>
                                        @endif
                                        @if($item->item_type === 'folder' && isset($item->description))
                                            <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->user->avatar)
                                                <img src="{{ $item->user->avatar_url }}" alt="{{ $item->user->name }}" 
                                                     class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                     onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $item->user->name }}</div>
                                                <small class="text-muted">{{ $item->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->item_type === 'file' && isset($item->size))
                                            <strong>{{ $item->formatted_size ?? number_format($item->size / 1024, 2) . ' KB' }}</strong>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->trashed_at)
                                            <div>{{ $item->trashed_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $item->trashed_at->timezone('Asia/Ho_Chi_Minh')->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('admin.trash.restore', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="type" value="{{ $item->item_type }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        title="{{ __('common.restore_item') }}"
                                                        onclick="return confirm('{{ __('common.restore_item_confirm') }}')">
                                                    <i class="las la-undo"></i>
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#deletePermanentlyModal{{ $item->id }}"
                                                    title="{{ __('common.delete_permanently') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ri-delete-bin-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_trash_items_found_admin') }}
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

<!-- Delete Permanently Modals -->
@foreach($paginator as $item)
<div class="modal fade" id="deletePermanentlyModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="deletePermanentlyModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deletePermanentlyModalLabel{{ $item->id }}">{{ __('common.delete_permanently') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.trash.destroy', $item->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="type" value="{{ $item->item_type }}">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_permanently_confirm') }}</p>
                    <p class="mb-2"><strong>{{ $item->name }}</strong></p>
                    @if($item->item_type === 'folder' && isset($item->files_count) && $item->files_count > 0)
                        <p class="text-warning">
                            <i class="ri-alert-line"></i> 
                            Folder này chứa <strong>{{ $item->files_count }}</strong> file.
                        </p>
                    @endif
                    <p class="text-muted">{{ __('common.item_will_be_permanently_deleted') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_permanently') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Empty Trash Modal -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1" role="dialog" aria-labelledby="emptyTrashModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="emptyTrashModalLabel">{{ __('common.empty_trash') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line mr-2"></i>
                    <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                </div>
                <p>{{ __('common.empty_trash_confirm') }}</p>
                <p class="text-muted">
                    {!! __('common.total_items_will_be_deleted', ['files' => $stats['total_files'], 'folders' => $stats['total_folders']]) !!}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                <form action="{{ route('admin.trash.empty') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('common.empty_trash') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

