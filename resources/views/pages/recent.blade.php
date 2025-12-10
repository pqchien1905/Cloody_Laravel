@extends('layouts.app')

@section('title', __('common.recent_files') . ' - Cloody')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Documents Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.documents') }}</h4>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <div class="card-header-toolbar">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn bg-white" id="dropdownMenuButton001" data-toggle="dropdown">
                                    {{ __('common.name') }}<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">{{ __('common.last_modified') }}</a>
                                    <a class="dropdown-item" href="#">{{ __('common.last_modified_by_me') }}</a>
                                    <a class="dropdown-item" href="#">{{ __('common.last_opened_by_me') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Grid (File Cards with Thumbnails) -->
        @php
            $documentFiles = $files->take(4); // Get first 4 files for document cards
        @endphp
        
        @foreach($documentFiles as $file)
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-body image-thumb">
                    <a href="{{ route('cloody.files.view', $file->id) }}">
                        <div class="mb-4 text-center p-3 rounded iq-thumb">
                            <div class="iq-image-overlay"></div>
                            @php
                                $isImage = in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                                
                                if ($isImage) {
                                    // Hiển thị hình ảnh thực cho các tệp ảnh
                                    $imageUrl = asset('storage/' . $file->path);
                                } else {
                                    // Hiển thị hình thu nhỏ cho các tệp không phải ảnh
                                    $thumbnail = 'layouts/page-1/pdf.png';
                                    if (in_array($file->extension, ['doc', 'docx'])) {
                                        $thumbnail = 'layouts/page-1/doc.png';
                                    } elseif (in_array($file->extension, ['xls', 'xlsx'])) {
                                        $thumbnail = 'layouts/page-1/xlsx.png';
                                    } elseif (in_array($file->extension, ['ppt', 'pptx'])) {
                                        $thumbnail = 'layouts/page-1/ppt.png';
                                    } elseif (in_array($file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                        $thumbnail = 'layouts/page-1/video-icon.svg';
                                    } elseif (in_array($file->extension, ['mp3', 'wav', 'ogg'])) {
                                        $thumbnail = 'layouts/page-1/mp3.png';
                                    }
                                    $imageUrl = asset('assets/images/' . $thumbnail);
                                }
                            @endphp
                            
                            @if($isImage)
                                <img src="{{ $imageUrl }}" class="img-fluid" alt="{{ $file->original_name }}" style="max-height: 150px; object-fit: cover;">
                            @else
                                <img src="{{ $imageUrl }}" class="img-fluid" alt="{{ $file->original_name }}">
                            @endif
                        </div>
                        <h6>{{ $file->original_name }}</h6>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Folders Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.folders') }}</h4>
                    </div>
                    <div class="card-header-toolbar">
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

        <!-- Folders Grid -->
        @php
            $folderColors = ['danger', 'primary', 'info', 'success'];
            $colorIndex = 0;
        @endphp
        
        @foreach($recentFolders as $folder)
        <div class="col-md-6 col-sm-6 col-lg-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cloody.folders.show', $folder->id) }}" class="folder">
                            <div class="icon-small bg-{{ $folderColors[$colorIndex % 4] }} rounded mb-4">
                                <i class="ri-file-copy-line"></i>
                            </div>
                        </a>
                        <div class="card-header-toolbar">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton{{ $folder->id }}" data-toggle="dropdown">
                                    <i class="ri-more-2-fill"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $folder->id }}">
                                    <a class="dropdown-item" href="{{ route('cloody.folders.show', $folder->id) }}">
                                        <i class="ri-eye-fill mr-2"></i>{{ __('common.view') }}
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('{{ __('common.move_to_trash') }}')) document.getElementById('delete-folder-form-{{ $folder->id }}').submit();">
                                        <i class="ri-delete-bin-6-fill mr-2"></i>{{ __('common.delete') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('cloody.folders.edit', $folder->id) }}">
                                        <i class="ri-pencil-fill mr-2"></i>{{ __('common.edit') }}
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ri-printer-fill mr-2"></i>{{ __('common.print') }}
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ri-file-download-fill mr-2"></i>{{ __('common.download') }}
                                    </a>
                                </div>
                            </div>
                            
                            <form id="delete-folder-form-{{ $folder->id }}" action="{{ route('cloody.folders.destroy', $folder->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                    <a href="{{ route('cloody.folders.show', $folder->id) }}" class="folder">
                        <h5 class="mb-2">{{ $folder->name }}</h5>
                        <p class="mb-2">
                            <i class="lar la-clock text-{{ $folderColors[$colorIndex % 4] }} mr-2 font-size-20"></i> 
                            {{ $folder->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="las la-file-alt text-{{ $folderColors[$colorIndex % 4] }} mr-2 font-size-20"></i> 
                            {{ $folder->files_count }} {{ __('common.files') }}
                        </p>
                    </a>
                </div>
            </div>
        </div>
        @php $colorIndex++; @endphp
        @endforeach

        <!-- Files Section Header -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.files') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files Table -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless tbl-server-info">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('common.name') }}</th>
                                    <th scope="col">{{ __('common.owner') }}</th>
                                    <th scope="col">{{ __('common.last_edit') }}</th>
                                    <th scope="col">{{ __('common.file_size') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconClass = 'ri-file-line';
                                                $iconBg = 'bg-secondary';
                                                
                                                if (in_array($file->extension, ['pdf'])) {
                                                    $iconClass = 'las la-file-pdf';
                                                    $iconBg = 'bg-danger';
                                                } elseif (in_array($file->extension, ['doc', 'docx'])) {
                                                    $iconClass = 'las la-file-word';
                                                    $iconBg = 'bg-primary';
                                                } elseif (in_array($file->extension, ['xls', 'xlsx'])) {
                                                    $iconClass = 'las la-file-excel';
                                                    $iconBg = 'bg-success';
                                                } elseif (in_array($file->extension, ['ppt', 'pptx'])) {
                                                    $iconClass = 'las la-file-powerpoint';
                                                    $iconBg = 'bg-warning';
                                                } elseif (in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                    $iconClass = 'las la-image';
                                                    $iconBg = 'bg-info';
                                                } elseif (in_array($file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                                    $iconClass = 'las la-video';
                                                    $iconBg = 'bg-danger';
                                                }
                                            @endphp
                                            
                                            <div class="icon-small {{ $iconBg }} rounded mr-3">
                                                <i class="{{ $iconClass }}"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('cloody.files.view', $file->id) }}" style="cursor: pointer;">
                                                    {{ $file->original_name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $file->user->name ?? __('common.you') }}</td>
                                    <td>{{ $file->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
                                    <td>{{ number_format($file->size / 1024 / 1024, 2) }} MB</td>
                                    <td>
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton{{ $file->id }}" data-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $file->id }}">
                                                <a class="dropdown-item" href="{{ route('cloody.files.view', $file->id) }}">
                                                    <i class="ri-eye-fill mr-2"></i>{{ __('common.view') }}
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); if(confirm('{{ __('common.move_to_trash') }}')) document.getElementById('delete-form-{{ $file->id }}').submit();">
                                                    <i class="ri-delete-bin-6-fill mr-2"></i>{{ __('common.delete') }}
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="ri-pencil-fill mr-2"></i>{{ __('common.edit') }}
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="ri-printer-fill mr-2"></i>{{ __('common.print') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('cloody.files.download', $file->id) }}">
                                                    <i class="ri-file-download-fill mr-2"></i>{{ __('common.download') }}
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <form id="delete-form-{{ $file->id }}" action="{{ route('cloody.files.delete', $file->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
