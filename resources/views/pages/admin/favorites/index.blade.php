@extends('layouts.app')

@section('title', __('common.manage_favorites') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="las la-star text-warning mr-2"></i>{{ __('common.manage_favorites') }}</h4>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_favorites') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_favorites']) }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-star text-warning"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.favorite_files') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['favorite_files']) }}</h3>
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
                            <h6 class="mb-2 text-muted">{{ __('common.favorite_folders') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['favorite_folders']) }}</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-folder text-info"></i>
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
                            <h6 class="mb-2 text-muted">{{ __('common.users_with_favorites') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['users_with_favorites']) }}</h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-2">
                            <i class="las la-users text-success"></i>
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
                    <form method="GET" action="{{ route('admin.favorites.index') }}" class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="{{ __('common.search_by_name') }}..." value="{{ request('search') }}">
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
                                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>{{ __('common.all') }}</option>
                                <option value="files" {{ $type === 'files' ? 'selected' : '' }}>{{ __('common.files_only') }}</option>
                                <option value="folders" {{ $type === 'folders' ? 'selected' : '' }}>{{ __('common.folders_only') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">{{ __('common.sort_by') }}</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>{{ __('common.sort_by_date') }}</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('common.sort_by_name') }}</option>
                                <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>{{ __('common.sort_by_updated') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <label for="order" class="small text-muted mb-1">{{ __('common.order') }}</label>
                            <select class="form-control" id="order" name="order">
                                <option value="desc" {{ request('order', 'desc') === 'desc' ? 'selected' : '' }}>{{ __('common.newest_first') }}</option>
                                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>{{ __('common.oldest_first') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary btn-block" title="{{ __('common.filter') }}">
                                <i class="las la-search"></i>
                            </button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'user_id', 'type', 'sort', 'order']))
                        <div class="mt-2">
                            <a href="{{ route('admin.favorites.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                        <i class="ri-star-fill text-warning"></i>
                                    </th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.type') }}</th>
                                    <th>{{ __('common.owner') }}</th>
                                    <th>{{ __('common.location') }}</th>
                                    <th>{{ __('common.size') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.favorited_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginator as $item)
                                <tr>
                                    <td>
                                        @if($item['type'] === 'folder')
                                            <i class="las la-folder text-warning font-size-24"></i>
                                        @else
                                            @php
                                                $iconClass = 'ri-file-line';
                                                $iconColor = 'text-muted';
                                                $mime = $item['mime_type'] ?? '';
                                                if(Str::contains($mime, 'pdf')) {
                                                    $iconClass = 'ri-file-pdf-line';
                                                    $iconColor = 'text-danger';
                                                } elseif(Str::contains($mime, 'word')) {
                                                    $iconClass = 'ri-file-word-line';
                                                    $iconColor = 'text-primary';
                                                } elseif(Str::contains($mime, 'excel') || Str::contains($mime, 'spreadsheet')) {
                                                    $iconClass = 'ri-file-excel-line';
                                                    $iconColor = 'text-success';
                                                } elseif(Str::contains($mime, 'image')) {
                                                    $iconClass = 'ri-image-line';
                                                    $iconColor = 'text-info';
                                                } elseif(Str::contains($mime, 'video')) {
                                                    $iconClass = 'ri-video-line';
                                                    $iconColor = 'text-danger';
                                                } elseif(Str::contains($mime, 'audio')) {
                                                    $iconClass = 'ri-music-line';
                                                    $iconColor = 'text-warning';
                                                }
                                            @endphp
                                            <i class="{{ $iconClass }} {{ $iconColor }} font-size-24"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if($item['type'] === 'folder')
                                                <strong><a href="{{ route('admin.folders.show', $item['id']) }}" class="text-primary">{{ $item['name'] }}</a></strong>
                                            @else
                                                <strong><a href="{{ route('admin.files.show', $item['id']) }}" class="text-primary">{{ $item['name'] }}</a></strong>
                                                @if(isset($item['original_name']) && $item['original_name'] !== $item['name'])
                                                    <br><small class="text-muted">{{ $item['original_name'] }}</small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($item['type'] === 'folder')
                                            <span class="badge badge-info">{{ __('common.folder') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ strtoupper($item['extension'] ?? __('common.file')) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['user'])
                                            <div class="d-flex align-items-center">
                                                @if($item['user']->avatar)
                                                    <img src="{{ $item['user']->avatar_url }}" alt="{{ $item['user']->name }}" 
                                                         class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;"
                                                         onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2" 
                                                         style="width: 30px; height: 30px; font-size: 12px;">
                                                        {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div>{{ $item['user']->name }}</div>
                                                    <small class="text-muted">{{ $item['user']->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('common.unknown') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['type'] === 'folder')
                                            @if($item['parent'])
                                                <small class="text-muted">
                                                    <i class="ri-folder-line"></i> {{ $item['parent']->name }}
                                                </small>
                                            @else
                                                <small class="text-muted">
                                                    <i class="ri-home-line"></i> {{ __('common.root') }}
                                                </small>
                                            @endif
                                        @else
                                            @if($item['folder'])
                                                <small class="text-muted">
                                                    <i class="ri-folder-line"></i> {{ $item['folder']->name }}
                                                </small>
                                            @else
                                                <small class="text-muted">
                                                    <i class="ri-home-line"></i> {{ __('common.root') }}
                                                </small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['type'] === 'file')
                                            <strong>{{ $item['formatted_size'] }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['is_trash'])
                                            <span class="badge badge-warning">{{ __('common.in_trash') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ __('common.active') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $item['created_at']?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $item['created_at']?->timezone('Asia/Ho_Chi_Minh')->format('H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            @if($item['type'] === 'folder')
                                                <a href="{{ route('admin.folders.show', ['folder' => $item['id'], 'from' => 'favorites']) }}" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="{{ __('common.view_details') }}"
                                                   target="_blank">
                                                    <i class="las la-info-circle"></i>
                                                </a>
                                                <a href="{{ route('admin.folders.view', ['folder' => $item['id'], 'from' => 'favorites']) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="{{ __('common.view_contents') }}"
                                                   target="_blank">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.favorites.unfavorite-folder', $item['id']) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('common.confirm_remove_favorite') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-warning" 
                                                            title="{{ __('common.remove_from_favorites') }}">
                                                        <i class="las la-star"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.files.show', ['file' => $item['id'], 'from' => 'favorites']) }}" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="{{ __('common.view_details') }}"
                                                   target="_blank">
                                                    <i class="las la-info-circle"></i>
                                                </a>
                                                <a href="{{ route('admin.files.view', ['file' => $item['id'], 'from' => 'favorites']) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="{{ __('common.view_file') }}"
                                                   target="_blank">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.files.download', $item['id']) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="{{ __('common.download') }}"
                                                   download>
                                                    <i class="las la-download"></i>
                                                </a>
                                                <form action="{{ route('admin.favorites.unfavorite-file', $item['id']) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('common.confirm_remove_favorite') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-warning" 
                                                            title="{{ __('common.remove_from_favorites') }}">
                                                        <i class="las la-star"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="ri-star-line font-size-48 text-muted mb-3 d-block"></i>
                                        {{ __('common.no_favorites_found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($paginator->hasPages())
                    <div class="card-footer">{{ $paginator->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
