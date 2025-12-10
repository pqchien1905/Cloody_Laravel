@extends('layouts.app')

@section('title', __('common.dashboard') . ' - Cloody')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-transparent card-block card-stretch card-height mb-3">
                <div class="d-flex justify-content-between">                             
                    <div class="select-dropdown input-prepend input-append">
                        <div class="btn-group">
                            <div data-toggle="dropdown">
                                <div class="dropdown-toggle search-query">{{ __('common.my_drive') }}<i class="las la-angle-down ml-3"></i></div>
                                <span class="search-replace"></span>
                                <span class="caret"><!--icon--></span>
                            </div>
                            <ul class="dropdown-menu">
                                <li><div class="item" data-toggle="modal" data-target="#createFolderModal"><i class="ri-folder-add-line pr-3"></i>{{ __('common.new_folder') }}</div></li>
                                <li><div class="item" data-toggle="modal" data-target="#uploadModal"><i class="ri-file-upload-line pr-3"></i>{{ __('common.upload_files') }}</div></li>
                                <li><div class="item"><i class="ri-folder-upload-line pr-3"></i>{{ __('common.upload_folders') }}</div></li>
                            </ul>
                        </div>
                    </div>
                    <div class="dashboard1-dropdown d-flex align-items-center">
                        <div class="dashboard1-info">
                            <a href="#calander" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <i class="ri-arrow-down-s-line"></i>
                            </a>
                            <ul id="calander" class="iq-dropdown collapse list-inline m-0 p-0 mt-2">
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Calendar"><i class="las la-calendar iq-arrow-left"></i></a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Keep"><i class="las la-lightbulb iq-arrow-left"></i></a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Tasks"><i class="las la-tasks iq-arrow-left"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Card -->
        <div class="col-lg-8">
            <div class="card card-block card-stretch card-height iq-welcome" style="background: url({{ asset('assets/images/layouts/mydrive/background.png') }}) no-repeat scroll right center; background-color: #ffffff; background-size: contain;">
                <div class="card-body property2-content">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="col-lg-6 col-sm-6 p-0">
                            <h3 class="mb-3">{{ __('common.welcome') }} {{ Auth::user()->name }}</h3>
                            <p class="mb-5">{{ __('common.you_have_files_folders', ['files' => $totalFiles, 'folders' => $totalFolders]) }}</p>
                            <a href="{{ route('cloody.files') }}">{{ __('common.view_all') }} {{ __('common.files') }}<i class="las la-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Access -->
        <div class="col-lg-4">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.quick_access') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-inline p-0 mb-0 row align-items-center">
                        <li class="col-lg-6 col-sm-6 mb-3 mb-sm-0"> 
                            <a href="{{ route('cloody.files') }}" style="cursor: pointer;" class="p-2 text-center border rounded d-block">
                                <div>
                                    <img src="{{ asset('assets/images/layouts/mydrive/folder-1.png') }}" class="img-fluid mb-1" alt="All Files">
                                </div>
                                <p class="mb-0">{{ __('common.all_files') }}</p>
                            </a>
                        </li>
                        <li class="col-lg-6 col-sm-6"> 
                            <a href="{{ route('cloody.favorites') }}" style="cursor: pointer;" class="p-2 text-center border rounded d-block">
                                <div>
                                    <img src="{{ asset('assets/images/layouts/mydrive/folder-2.png') }}" class="img-fluid mb-1" alt="Favorites">
                                </div>
                                <p class="mb-0">{{ __('common.favorites') }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.documents') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <a href="{{ route('cloody.files') }}" class="view-more">{{ __('common.view_all') }}</a>
                    </div>
                </div>
            </div>
        </div>

        @if($documents->count() > 0)
            @foreach($documents as $doc)
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body image-thumb">
                        <a href="{{ route('cloody.files.view', $doc->id) }}">
                            <div class="mb-4 text-center p-3 rounded iq-thumb">
                                <div class="iq-image-overlay"></div>
                                @if(str_contains($doc->mime_type, 'pdf'))
                                    <img src="{{ asset('assets/images/layouts/page-1/pdf.png') }}" class="img-fluid" alt="PDF">
                                @elseif(str_contains($doc->mime_type, 'word'))
                                    <img src="{{ asset('assets/images/layouts/page-1/doc.png') }}" class="img-fluid" alt="Word">
                                @elseif(str_contains($doc->mime_type, 'excel') || str_contains($doc->mime_type, 'spreadsheet'))
                                    <img src="{{ asset('assets/images/layouts/page-1/xlsx.png') }}" class="img-fluid" alt="Excel">
                                @elseif(str_contains($doc->mime_type, 'powerpoint') || str_contains($doc->mime_type, 'presentation'))
                                    <img src="{{ asset('assets/images/layouts/page-1/ppt.png') }}" class="img-fluid" alt="PowerPoint">
                                @else
                                    <img src="{{ asset('assets/images/layouts/page-1/pdf.png') }}" class="img-fluid" alt="Document">
                                @endif
                            </div>
                            <h6>{{ Str::limit($doc->original_name, 20) }}</h6>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">{{ __('common.no_documents_yet') }}</p>
                        <button class="btn btn-primary mt-2" data-toggle="modal" data-target="#uploadModal">
                            <i class="ri-upload-line"></i> {{ __('common.upload_files') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Folders Section -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.folders') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <div class="dropdown">
                            <span class="dropdown-toggle dropdown-bg btn bg-white" id="dropdownMenuButton1" data-toggle="dropdown">
                                {{ __('common.name') }}<i class="ri-arrow-down-s-line ml-1"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton1">
                                <a class="dropdown-item" href="#">{{ __('common.last_modified') }}</a>
                                <a class="dropdown-item" href="#">{{ __('common.last_modified_by_me') }}</a>
                                <a class="dropdown-item" href="#">{{ __('common.last_opened_by_me') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($recentFolders->count() > 0)
            @php
                $colors = ['danger', 'primary', 'info', 'success'];
            @endphp
            @foreach($recentFolders as $index => $folder)
            <div class="col-md-6 col-sm-6 col-lg-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cloody.folders.show', $folder->id) }}" class="folder">
                                <div class="icon-small bg-{{ $colors[$index % 4] }} rounded mb-4">
                                    <i class="ri-file-copy-line"></i>
                                </div>
                            </a>
                            <div class="card-header-toolbar">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownFolder{{ $folder->id }}" data-toggle="dropdown">
                                        <i class="ri-more-2-fill"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFolder{{ $folder->id }}">
                                        <a class="dropdown-item" href="{{ route('cloody.folders.show', $folder->id) }}"><i class="ri-eye-fill mr-2"></i>{{ __('common.view') }}</a>
                                        <a class="dropdown-item" href="{{ route('cloody.folders.edit', $folder->id) }}"><i class="ri-pencil-fill mr-2"></i>{{ __('common.edit') }}</a>
                                        <form action="{{ route('cloody.folders.favorite', $folder->id) }}" method="POST" style="display: inline; width: 100%;">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="ri-star-{{ $folder->is_favorite ? 'fill' : 'line' }} mr-2 text-warning"></i>
                                                {{ $folder->is_favorite ? __('common.remove_from_favorites') : __('common.add_to_favorites') }}
                                            </button>
                                        </form>
                                        <a class="dropdown-item" href="#"><i class="ri-printer-fill mr-2"></i>{{ __('common.print') }}</a>
                                        <a class="dropdown-item" href="#"><i class="ri-file-download-fill mr-2"></i>{{ __('common.download') }}</a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('cloody.folders.destroy', $folder->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ri-delete-bin-6-fill mr-2"></i>{{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('cloody.folders.show', $folder->id) }}" class="folder">
                            <h5 class="mb-2">{{ $folder->name }}</h5>
                            <p class="mb-2"><i class="lar la-clock text-{{ $colors[$index % 4] }} mr-2 font-size-20"></i> {{ $folder->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</p>
                            <p class="mb-0"><i class="las la-file-alt text-{{ $colors[$index % 4] }} mr-2 font-size-20"></i> {{ $folder->files_count }} Files</p>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-folder-line" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">{{ __('common.no_folders_desc') }}</p>
                        <button class="btn btn-primary mt-2" data-toggle="modal" data-target="#createFolderModal">
                            <i class="ri-folder-add-line"></i> {{ __('common.new_folder') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Files Table & Storage -->
        <div class="col-lg-8 col-xl-8">
            <div class="card card-block card-stretch card-height files-table">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.recent_files') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <a href="{{ route('cloody.files') }}" class="view-more">{{ __('common.view_all') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless tbl-server-info">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('common.name') }}</th>
                                    <th scope="col">{{ __('common.size') }}</th>
                                    <th scope="col">{{ __('common.last_edit') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFiles as $file)
                                <tr class="clickable-row" onclick="window.location='{{ route('cloody.files.view', $file->id) }}'" style="cursor: pointer;">
                                    <td>
                                        @if(str_contains($file->mime_type, 'pdf'))
                                            <i class="ri-file-pdf-line text-danger mr-2"></i>
                                        @elseif(str_contains($file->mime_type, 'word'))
                                            <i class="ri-file-word-line text-primary mr-2"></i>
                                        @elseif(str_contains($file->mime_type, 'excel') || str_contains($file->mime_type, 'spreadsheet'))
                                            <i class="ri-file-excel-line text-success mr-2"></i>
                                        @elseif(str_contains($file->mime_type, 'image'))
                                            <i class="ri-image-line text-info mr-2"></i>
                                        @else
                                            <i class="ri-file-line mr-2"></i>
                                        @endif
                                        {{ Str::limit($file->original_name, 30) }}
                                    </td>
                                    <td>{{ number_format($file->size / 1024 / 1024, 2) }} MB</td>
                                    <td>{{ $file->created_at->diffForHumans() }}</td>
                                    <td onclick="event.stopPropagation();">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownFile{{ $file->id }}" data-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFile{{ $file->id }}">
                                                <a class="dropdown-item" href="{{ route('cloody.files.view', $file->id) }}">
                                                    <i class="ri-eye-line mr-2"></i>{{ __('common.view') }}
                                                </a>
                                                <form action="{{ route('cloody.files.favorite', $file->id) }}" method="POST" style="display: inline; width: 100%;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ri-star-{{ $file->is_favorite ? 'fill' : 'line' }} mr-2 text-warning"></i>
                                                        {{ $file->is_favorite ? __('common.remove_from_favorites') : __('common.add_to_favorites') }}
                                                    </button>
                                                </form>
                                                <a class="dropdown-item" href="{{ route('cloody.files.download', $file->id) }}">
                                                    <i class="ri-download-line mr-2"></i>{{ __('common.download') }}
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('cloody.files.delete', $file->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-line mr-2"></i>{{ __('common.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="mt-3 text-muted">{{ __('common.no_files_yet') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Card -->
        <div class="col-lg-4">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.storage') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="mb-3">{{ number_format($storageUsed / 1024 / 1024 / 1024, 2) }} GB</h2>
                        <p class="mb-4 text-muted">{{ __('common.of_used') }} {{ $storageLimit ?? 1 }} GB</p>
                        <div class="iq-progress-bar mb-3">
                            <span class="bg-primary iq-progress progress-1 dashboard-storage-bar" data-percent="{{ number_format(min(($storageUsed / 1024 / 1024 / 1024 / ($storageLimit ?? 1)) * 100, 100), 2) }}" style="width: 0%; transition: width 1s ease;"></span>
                        </div>
                        <p class="mb-0">{{ number_format(max(($storageLimit ?? 1) - ($storageUsed / 1024 / 1024 / 1024), 0), 2) }} GB {{ __('common.free') }}</p>
                    </div>
                    <hr>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="ri-file-line text-primary mr-2"></i>{{ __('common.documents') }}</span>
                            <span class="font-weight-bold">{{ $totalFiles }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="ri-folder-line text-info mr-2"></i>{{ __('common.folders') }}</span>
                            <span class="font-weight-bold">{{ $totalFolders }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="ri-share-line text-success mr-2"></i>{{ __('common.shared') }}</span>
                            <span class="font-weight-bold">{{ $sharedFiles }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hoạt ảnh thanh tiến độ lưu trữ trên dashboard
    setTimeout(function() {
        const dashboardBar = document.querySelector('.dashboard-storage-bar');
        if (dashboardBar) {
            const percent = parseFloat(dashboardBar.getAttribute('data-percent')) || 0;
            dashboardBar.style.width = percent + '%';
        }
    }, 100);
});
</script>
@endpush
