@extends('layouts.app')

@section('title', $group->name . ' - Files')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="ri-arrow-left-line mr-1"></i> Quay lại nhóm
            </a>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="ri-folder-shared-line mr-2"></i>Files & Folders của nhóm</h4>
                    <p class="text-muted mb-0">{{ $group->name }}</p>
                </div>
                @if($isMember)
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="ri-share-line mr-1"></i> Chia sẻ với nhóm
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#shareFileModal">
                            <i class="ri-file-line mr-2"></i>Chia sẻ File
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#shareFolderModal">
                            <i class="ri-folder-line mr-2"></i>Chia sẻ Thư mục
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DEBUG INFO -->
    @if(config('app.debug'))
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        User: {{ Auth::user()->name }} (ID: {{ Auth::id() }})<br>
        Group: {{ $group->name }} (ID: {{ $group->id }})<br>
        Is Member: {{ $isMember ? 'Yes' : 'No' }}<br>
        Is Admin: {{ $isAdmin ? 'Yes' : 'No' }}<br>
        Is Owner: {{ $isOwner ? 'Yes' : 'No' }}<br>
        Files count: {{ $files->count() }}<br>
        Folders count: {{ $folders->count() }}
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Folders Section -->
    @if($folders->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="ri-folder-line mr-2"></i>Thư mục ({{ $folders->count() }})</h5>
        </div>
        @foreach($folders as $folder)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cloody.folders.show', $folder->id) }}" class="folder">
                            <div class="icon-small bg-primary mb-3">
                                <i class="ri-folder-line"></i>
                            </div>
                        </a>
                        @if($isAdmin || $isOwner)
                        <div class="dropdown">
                            <span class="dropdown-toggle" data-toggle="dropdown">
                                <i class="ri-more-2-fill"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <form action="{{ route('groups.files.remove-folder', [$group, $folder->id]) }}" 
                                      method="POST" onsubmit="return confirm('Xóa thư mục khỏi nhóm?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ri-delete-bin-line mr-2"></i>Xóa khỏi nhóm
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('cloody.folders.show', $folder->id) }}">
                        <h6 class="mb-2">{{ Str::limit($folder->name, 30) }}</h6>
                    </a>
                    <p class="card-text text-muted mb-1">
                        <small>{{ __('common.shared_by_label') }}: {{ $folder->pivot->shared_by == Auth::id() ? __('common.you') : ($folder->sharedBy ? $folder->sharedBy->name : __('common.not_available')) }}</small>
                    </p>
                    <span class="badge badge-{{ $folder->pivot->permission == 'full' ? 'success' : 'secondary' }} badge-pill">
                        {{ ucfirst($folder->pivot->permission) }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Files Section -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3"><i class="ri-file-line mr-2"></i>{{ __('common.files') }} ({{ $files->count() }})</h5>
        </div>
        @if($files->count() > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.size') }}</th>
                                    <th>{{ __('common.shared_by_label') }}</th>
                                    <th>{{ __('common.share_permission') }}</th>
                                    <th>{{ __('common.share_created_at') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri-file-line font-size-20 mr-2 text-primary"></i>
                                            <span>{{ $file->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                                    <td>{{ $file->pivot->shared_by == Auth::id() ? __('common.you') : ($file->sharedBy ? $file->sharedBy->name : __('common.not_available')) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $file->pivot->permission == 'edit' ? 'success' : 'secondary' }} badge-pill">
                                            @if($file->pivot->permission === 'view')
                                                {{ __('common.permission_view') }}
                                            @elseif($file->pivot->permission === 'download')
                                                {{ __('common.permission_download') }}
                                            @elseif($file->pivot->permission === 'edit')
                                                {{ __('common.permission_edit') }}
                                            @else
                                                {{ ucfirst($file->pivot->permission) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $file->pivot->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
                                    <td class="text-right">
                                        @if(in_array($file->pivot->permission, ['view', 'download', 'edit']))
                                        <a href="{{ route('cloody.files.view', $file->id) }}" 
                                           class="btn btn-sm btn-outline-info" title="Xem" target="_blank">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endif
                                        @if(in_array($file->pivot->permission, ['download', 'edit']))
                                        <a href="{{ route('cloody.files.download', $file->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Tải xuống">
                                            <i class="ri-download-line"></i>
                                        </a>
                                        @endif
                                        @if($isAdmin || $isOwner || $file->pivot->shared_by == Auth::id())
                                        <form action="{{ route('groups.files.remove-file', [$group, $file->id]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Xóa file khỏi nhóm?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-file-list-line font-size-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có file nào trong nhóm</h5>
                    <p class="text-muted mb-4">Chia sẻ files để cộng tác với thành viên nhóm</p>
                    @if($isMember)
                    <button class="btn btn-primary" data-toggle="modal" data-target="#shareFileModal">
                        <i class="ri-share-line mr-1"></i> Chia sẻ file đầu tiên
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Share File Modal -->
@if($isMember)
<div class="modal fade" id="shareFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chia sẻ File với nhóm</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('groups.files.share-file', $group) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn file <span class="text-danger">*</span></label>
                        <select name="file_id" class="form-control" required>
                            <option value="">-- Chọn file --</option>
                            @foreach(Auth::user()->files as $myFile)
                                <option value="{{ $myFile->id }}">{{ $myFile->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quyền truy cập <span class="text-danger">*</span></label>
                        <select name="permission" class="form-control" required>
                            <option value="view">View - Chỉ xem</option>
                            <option value="download">Download - Xem và tải</option>
                            <option value="edit">Edit - Toàn quyền</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-share-line mr-1"></i>Chia sẻ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Folder Modal -->
<div class="modal fade" id="shareFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chia sẻ Thư mục với nhóm</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('groups.files.share-folder', $group) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn thư mục <span class="text-danger">*</span></label>
                        <select name="folder_id" class="form-control" required>
                            <option value="">-- Chọn thư mục --</option>
                            @foreach(Auth::user()->folders as $myFolder)
                                <option value="{{ $myFolder->id }}">{{ $myFolder->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quyền truy cập <span class="text-danger">*</span></label>
                        <select name="permission" class="form-control" required>
                            <option value="view">View - Chỉ xem</option>
                            <option value="edit">Edit - Xem và sửa</option>
                            <option value="full">Full - Toàn quyền</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-share-line mr-1"></i>Chia sẻ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
