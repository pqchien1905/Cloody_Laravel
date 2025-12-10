@extends('layouts.app')

@section('title', __('common.shared_files') . ' - Cloody')

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
                        <h4 class="card-title">{{ __('common.share_with_me') }}</h4>
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
                               href="{{ route('cloody.shared', ['tab' => 'with-me']) }}">
                                <i class="ri-share-forward-line mr-2"></i>{{ __('common.shared_with_me') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab === 'by-me' ? 'active' : '' }}" 
                               href="{{ route('cloody.shared', ['tab' => 'by-me']) }}">
                                <i class="ri-share-line mr-2"></i>{{ __('common.shared_by_me') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4">
                        @if($shares->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('common.name') }}</th>
                                        @if($tab === 'with-me')
                                            <th scope="col">{{ __('common.shared_by') }}</th>
                                            <th scope="col">{{ __('common.permission') }}</th>
                                        @else
                                            <th scope="col">{{ __('common.shared_with') }}</th>
                                            <th scope="col">{{ __('common.permission') }}</th>
                                        @endif
                                        <th scope="col">{{ __('common.shared_date') }}</th>
                                        <th scope="col">{{ __('common.size') }}</th>
                                        <th scope="col" class="text-center">{{ __('common.actions') }}</th>
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
                                                    <a href="{{ route('cloody.folders.show', $share->folder->id) }}" class="text-dark">
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
                                                    <a href="{{ route('cloody.files.view', $share->file->id) }}" class="text-dark">
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
                                                    <span class="text-muted">{{ __('common.public_link') }}</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if($share->permission === 'view')
                                                <span class="badge badge-primary">
                                                    <i class="ri-eye-line"></i> {{ __('common.view_only') }}
                                                </span>
                                            @elseif($share->permission === 'edit')
                                                <span class="badge badge-success">
                                                    <i class="ri-pencil-line"></i> {{ __('common.can_edit') }}
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="ri-download-line"></i> {{ __('common.can_download') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $share->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
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
                                                    <a href="{{ route('cloody.folders.show', $share->folder->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="{{ __('common.view') }}">
                                                        <i class="ri-folder-open-line"></i>
                                                    </a>
                                                @else
                                                    {{-- File Actions --}}
                                                    @if($share->file)
                                                    <a href="{{ route('cloody.files.download', $share->file->id) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="{{ __('common.download') }}">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    @endif
                                                @endif

                                                @if($tab === 'by-me')
                                                <a href="#" 
                                                   onclick="event.preventDefault(); if(confirm('{{ __('common.revoke_share_access') }}')) document.getElementById('revoke-form-{{ $share->type }}-{{ $share->id }}').submit();"
                                                   class="btn btn-sm btn-danger" 
                                                   title="{{ __('common.revoke') }}">
                                                    <i class="ri-close-line"></i>
                                                </a>

                                                @if($share->type === 'folder')
                                                <form id="revoke-form-folder-{{ $share->id }}" action="{{ route('cloody.shares.revoke', $share->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="type" value="folder">
                                                </form>
                                                @else
                                                <form id="revoke-form-file-{{ $share->id }}" action="{{ route('cloody.shares.revoke', $share->id) }}" method="POST" style="display: none;">
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
                                {{ __('common.showing_to_of', ['first' => $shares->firstItem() ?? 0, 'last' => $shares->lastItem() ?? 0, 'total' => $shares->total()]) }}
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
                                <h4 class="mt-3">{{ __('common.no_files_shared_with_you') }}</h4>
                                <p class="text-muted">{{ __('common.files_shared_with_you_will_appear') }}</p>
                            @else
                                <h4 class="mt-3">{{ __('common.you_havent_shared_any_files') }}</h4>
                                <p class="text-muted">{{ __('common.files_you_share_will_appear') }}</p>
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
