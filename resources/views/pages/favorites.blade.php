@extends('layouts.app')

@section('title', 'Favorites - CloudBOX')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">
                            <i class="ri-star-fill text-warning"></i> Favorites
                        </h4>
                    </div>
                    
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <!-- Favorite Folders Section -->
                    @if(isset($favoriteFolders) && $favoriteFolders->count() > 0)
                        <h5 class="mb-3"><i class="ri-folder-star-line"></i> Favorite Folders ({{ $favoriteFolders->count() }})</h5>
                        <div class="row mb-4">
                            @foreach($favoriteFolders as $folder)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('cloudbox.folders.show', $folder->id) }}" class="folder">
                                                <div class="icon-small rounded mb-3" style="background-color: {{ $folder->color ?? '#3498db' }}20;">
                                                    <i class="ri-folder-3-fill" style="color: {{ $folder->color ?? '#3498db' }}; font-size: 24px;"></i>
                                                </div>
                                            </a>
                                            <div class="card-header-toolbar">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownFolder{{ $folder->id }}" data-toggle="dropdown" style="cursor: pointer;">
                                                        <i class="ri-more-2-fill"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownFolder{{ $folder->id }}">
                                                        <a class="dropdown-item" href="{{ route('cloudbox.folders.show', $folder->id) }}">
                                                            <i class="ri-eye-fill mr-2"></i>View
                                                        </a>
                                                        <form action="{{ route('cloudbox.folders.favorite', $folder->id) }}" method="POST" style="display: inline; width: 100%;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-star-fill mr-2 text-warning"></i>Remove from Favorites
                                                            </button>
                                                        </form>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('cloudbox.folders.destroy', $folder->id) }}" method="POST" style="display: inline; width: 100%;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-6-fill mr-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('cloudbox.folders.show', $folder->id) }}" class="folder">
                                            <h6 class="mb-2">
                                                {{ $folder->name }}
                                                <i class="ri-star-fill text-warning ml-1"></i>
                                            </h6>
                                            <p class="mb-2 text-muted small">
                                                <i class="ri-file-line mr-1"></i> {{ $folder->files_count ?? 0 }} Files
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="ri-time-line mr-1"></i> {{ $folder->created_at->diffForHumans() }}
                                            </p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                    @endif

                    <!-- Favorite Files Section -->
                    <h5 class="mb-3"><i class="ri-file-star-line"></i> Favorite Files ({{ $files->count() }})</h5>
                    @if($files->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-color-heading">
                                    <tr>
                                        <th>File Name</th>
                                        <th>Folder</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Added</th>
                                        <th class="text-center actions-col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <td>
                                                <a href="{{ route('cloudbox.files.view', $file->id) }}" class="d-flex align-items-center text-body" style="text-decoration: none;">
                                                    @php
                                                        $iconClass = 'ri-file-line';
                                                        $iconColor = 'text-muted';
                                                        if(Str::contains($file->mime_type, 'pdf')) {
                                                            $iconClass = 'ri-file-pdf-line';
                                                            $iconColor = 'text-danger';
                                                        } elseif(Str::contains($file->mime_type, 'word')) {
                                                            $iconClass = 'ri-file-word-line';
                                                            $iconColor = 'text-primary';
                                                        } elseif(Str::contains($file->mime_type, 'excel') || Str::contains($file->mime_type, 'spreadsheet')) {
                                                            $iconClass = 'ri-file-excel-line';
                                                            $iconColor = 'text-success';
                                                        } elseif(Str::contains($file->mime_type, 'image')) {
                                                            $iconClass = 'ri-image-line';
                                                            $iconColor = 'text-info';
                                                        }
                                                    @endphp
                                                    <i class="{{ $iconClass }} font-size-20 {{ $iconColor }}"></i>
                                                    <span class="ml-3">
                                                        {{ $file->original_name }}
                                                        <i class="ri-star-fill text-warning ml-1"></i>
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                @if($file->folder)
                                                    <a href="{{ route('cloudbox.folders.show', $file->folder->id) }}">{{ $file->folder->name }}</a>
                                                @else
                                                    <span class="text-muted">Root</span>
                                                @endif
                                            </td>
                                            <td>{{ strtoupper($file->extension) }}</td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>{{ $file->created_at->diffForHumans() }}</td>
                                            <td class="text-center actions-col">
                                                <div class="d-flex align-items-center list-user-action">
                                                    <a class="action-icon text-primary" href="{{ route('cloudbox.files.download', $file->id) }}" 
                                                       data-toggle="tooltip" title="Download">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    <form action="{{ route('cloudbox.files.favorite', $file->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="action-icon bg-transparent border-0 text-warning" 
                                                                data-toggle="tooltip" title="Remove from favorites">
                                                            <i class="ri-star-fill"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('cloudbox.files.delete', $file->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-icon bg-transparent border-0 text-danger" 
                                                                data-toggle="tooltip" title="Delete">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $files->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-star-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">No favorite files yet</h5>
                            <p class="text-muted">Star files to add them to your favorites</p>
                        </div>
                    @endif

                    @if((isset($favoriteFolders) ? $favoriteFolders->count() : 0) == 0 && $files->count() == 0)
                        <div class="text-center py-5">
                            <i class="ri-star-line font-size-80 text-muted"></i>
                            <h5 class="mt-3 text-muted">No favorites yet</h5>
                            <p class="text-muted">Star files and folders to add them to your favorites</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Remove background/shadow behind action icons on Favorites table */
.list-user-action .action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: transparent !important;
    box-shadow: none !important;
}
.list-user-action { justify-content: center; gap: 8px; }
/* Ensure Actions column is centered */
.table .actions-col { text-align: center; }

/* Hover effect on file names - make text bold */
.table tbody tr:hover td a span {
    font-weight: 600;
    color: #333;
}
.table tbody tr {
    transition: background-color 0.2s ease;
}
.table tbody tr:hover {
    background-color: #f8f9fa;
}

.list-user-action .action-icon:hover,
.list-user-action .action-icon:focus {
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
}
.list-user-action .action-icon i { font-size: 18px; }
.list-user-action button.action-icon { padding: 0; }
/* In case old classes exist somewhere, neutralize their background here */
.list-user-action .iq-bg-primary,
.list-user-action .iq-bg-warning,
.list-user-action .iq-bg-danger {
    background: transparent !important;
    box-shadow: none !important;
}
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()

    // Chỉ sử dụng tooltip trên trang này; không có tải lên từ Favorites
    })
</script>
@endpush
