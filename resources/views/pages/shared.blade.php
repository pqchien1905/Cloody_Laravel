@extends('layouts.app')

@section('title', 'Shared Files')

@push('styles')
<style>
    /* Custom tab styling */
    .nav-tabs .nav-link {
        border: none;
        background: transparent;
        color: #6c757d;
        border-bottom: 2px solid transparent;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #007bff;
        background: transparent;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff !important;
        background: transparent !important;
        border-bottom: 2px solid #007bff !important;
        font-weight: 600;
        box-shadow: none !important;
    }
    
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-transparent">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <div class="header-title">
                        <h4 class="card-title">Share With Me</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab === 'with-me' ? 'active' : '' }}" 
                               href="{{ route('cloudbox.shared', ['tab' => 'with-me']) }}">
                                <i class="ri-share-forward-line mr-2"></i>Shared with me
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab === 'by-me' ? 'active' : '' }}" 
                               href="{{ route('cloudbox.shared', ['tab' => 'by-me']) }}">
                                <i class="ri-share-line mr-2"></i>Shared by me
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4">
                        @if($shares->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        @if($tab === 'with-me')
                                            <th scope="col">Shared By</th>
                                            <th scope="col">Permission</th>
                                        @else
                                            <th scope="col">Shared With</th>
                                            <th scope="col">Permission</th>
                                        @endif
                                        <th scope="col">Shared Date</th>
                                        <th scope="col">Size</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shares as $share)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($share->type === 'folder')
                                                    {{-- Folder Icon --}}
                                                    <i class="ri-folder-line text-primary mr-3" style="font-size: 24px;"></i>
                                                    <a href="{{ route('cloudbox.folders.show', $share->folder->id) }}" class="text-dark">
                                                        <span>{{ $share->folder->name }}</span>
                                                    </a>
                                                @else
                                                    {{-- File Icon --}}
                                                    @php
                                                        $iconClass = 'ri-file-line';
                                                        $iconColor = 'text-secondary';
                                                        
                                                        if ($share->file && in_array($share->file->extension, ['pdf'])) {
                                                            $iconClass = 'ri-file-pdf-line';
                                                            $iconColor = 'text-danger';
                                                        } elseif ($share->file && in_array($share->file->extension, ['doc', 'docx'])) {
                                                            $iconClass = 'ri-file-word-line';
                                                            $iconColor = 'text-primary';
                                                        } elseif ($share->file && in_array($share->file->extension, ['xls', 'xlsx'])) {
                                                            $iconClass = 'ri-file-excel-line';
                                                            $iconColor = 'text-success';
                                                        } elseif ($share->file && in_array($share->file->extension, ['ppt', 'pptx'])) {
                                                            $iconClass = 'ri-file-ppt-line';
                                                            $iconColor = 'text-warning';
                                                        } elseif ($share->file && in_array($share->file->extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                            $iconClass = 'ri-image-line';
                                                            $iconColor = 'text-info';
                                                        } elseif ($share->file && in_array($share->file->extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                                            $iconClass = 'ri-video-line';
                                                            $iconColor = 'text-danger';
                                                        }
                                                    @endphp
                                                    
                                                    <i class="{{ $iconClass }} {{ $iconColor }} mr-3" style="font-size: 24px;"></i>
                                                    @if($share->file)
                                                    <a href="{{ route('cloudbox.files.view', $share->file->id) }}" class="text-dark">
                                                        <span>{{ $share->file->original_name }}</span>
                                                    </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        @if($tab === 'with-me')
                                            <td>
                                                @if($share->sharedBy)
                                                    {{ $share->sharedBy->name }}
                                                @elseif($share->type === 'file' && $share->file)
                                                    {{ $share->file->user->name ?? 'Unknown' }}
                                                @elseif($share->type === 'folder' && $share->folder)
                                                    {{ $share->folder->user->name ?? 'Unknown' }}
                                                @endif
                                            </td>
                                        @else
                                            <td>
                                                @if($share->sharedWith)
                                                    {{ $share->sharedWith->name }} ({{ $share->sharedWith->email }})
                                                @else
                                                    <span class="text-muted">Public Link</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if($share->permission === 'view')
                                                <span class="badge badge-primary">
                                                    <i class="ri-eye-line"></i> View Only
                                                </span>
                                            @elseif($share->permission === 'edit')
                                                <span class="badge badge-success">
                                                    <i class="ri-pencil-line"></i> Can Edit
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="ri-download-line"></i> Can Download
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $share->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($share->type === 'folder')
                                                <span class="text-muted">-</span>
                                            @elseif($share->file)
                                                {{ number_format($share->file->size / 1024, 2) }} KB
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                                @if($share->type === 'folder')
                                                    {{-- Folder Actions --}}
                                                    <a href="{{ route('cloudbox.folders.show', $share->folder->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="View">
                                                        <i class="ri-folder-open-line"></i>
                                                    </a>
                                                @else
                                                    {{-- File Actions --}}
                                                    @if($share->file)
                                                    <a href="{{ route('cloudbox.files.download', $share->file->id) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Download">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    @endif
                                                @endif

                                                @if($tab === 'by-me')
                                                <a href="#" 
                                                   onclick="event.preventDefault(); if(confirm('Revoke share access?')) document.getElementById('revoke-form-{{ $share->type }}-{{ $share->id }}').submit();"
                                                   class="btn btn-sm btn-danger" 
                                                   title="Revoke">
                                                    <i class="ri-close-line"></i>
                                                </a>

                                                @if($share->type === 'folder')
                                                <form id="revoke-form-folder-{{ $share->id }}" action="{{ route('cloudbox.shares.revoke', $share->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="type" value="folder">
                                                </form>
                                                @else
                                                <form id="revoke-form-file-{{ $share->id }}" action="{{ route('cloudbox.shares.revoke', $share->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="type" value="file">
                                                </form>
                                                @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $shares->firstItem() ?? 0 }} to {{ $shares->lastItem() ?? 0 }} of {{ $shares->total() }} shares
                            </div>
                            <div>
                                {{ $shares->appends(['tab' => $tab])->links() }}
                            </div>
                        </div>
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="las la-share-alt" style="font-size: 64px; color: #ccc;"></i>
                            @if($tab === 'with-me')
                                <h4 class="mt-3">No Files Shared With You</h4>
                                <p class="text-muted">Files that others share with you will appear here.</p>
                            @else
                                <h4 class="mt-3">You Haven't Shared Any Files</h4>
                                <p class="text-muted">Files you share with others will appear here.</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
