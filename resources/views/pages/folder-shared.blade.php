@extends('layouts.app')

@section('title', __('common.shared_folder'))

@section('content')
<div class="container-fluid py-4">
    @if(isset($share) && $share->folder)
        <div class="row">
            <!-- Folder Info Sidebar -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="ri-information-line mr-2"></i>Thông tin thư mục
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="ri-folder-3-fill" style="font-size: 64px; color: {{ $share->folder->color ?? '#3498db' }};"></i>
                        </div>
                        
                        <h5 class="text-center mb-3">{{ $share->folder->name }}</h5>

                        <hr>

                        <div class="folder-details">
                            <p class="mb-2">
                                <strong>Chia sẻ bởi:</strong><br>
                                <span>{{ $share->sharedBy->name ?? 'Không xác định' }}</span>
                            </p>
                            
                            @if($share->folder->description)
                                <p class="mb-2">
                                    <strong>Mô tả:</strong><br>
                                    <small>{{ $share->folder->description }}</small>
                                </p>
                            @endif
                            
                            <p class="mb-2">
                                <strong>Quyền:</strong><br>
                                <span class="badge badge-{{ $share->permission === 'download' ? 'success' : 'secondary' }}">
                                    {{ $share->permission === 'download' ? 'Tải xuống' : 'Chỉ xem' }}
                                </span>
                            </p>
                            
                            <p class="mb-2">
                                <strong>Số file:</strong> {{ $share->folder->files->count() }}
                            </p>
                            
                            <p class="mb-2">
                                <strong>Thư mục con:</strong> {{ $share->folder->subfolders->count() }}
                            </p>
                            
                            @if($share->expires_at)
                                <p class="mb-2">
                                    <strong>Hết hạn:</strong><br>
                                    <small class="text-{{ $share->isExpired() ? 'danger' : 'warning' }}">
                                        {{ $share->expires_at->format('d/m/Y H:i') }}
                                        @if($share->isExpired())
                                            <br>(Đã hết hạn)
                                        @else
                                            <br>({{ $share->expires_at->diffForHumans() }})
                                        @endif
                                    </small>
                                </p>
                            @endif
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            @if(in_array($share->permission, ['download', 'edit']))
                                <a href="{{ route('folder.shared.download', $share->share_token) }}" class="btn btn-primary btn-block">
                                    <i class="ri-download-line mr-1"></i> Tải xuống thư mục
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Folder Content -->
            <div class="col-md-9">
                <!-- Subfolders Section -->
                @if($share->folder->subfolders && $share->folder->subfolders->count() > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="ri-folder-line mr-2"></i>Thư mục con ({{ $share->folder->subfolders->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($share->folder->subfolders as $subfolder)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 folder-card" onclick="openSubfolderPreview({{ $subfolder->id }})">
                                            <div class="card-body text-center">
                                                <i class="ri-folder-3-fill" style="font-size: 48px; color: {{ $subfolder->color ?? '#ffc107' }};"></i>
                                                <h6 class="mt-2 mb-1">{{ $subfolder->name }}</h6>
                                                @if($subfolder->description)
                                                    <small class="text-muted">{{ Str::limit($subfolder->description, 50) }}</small>
                                                @endif
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="ri-file-line"></i> {{ $subfolder->files->count() }} file
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Files Section -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="ri-file-line mr-2"></i>Tệp tin ({{ $share->folder->files->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($share->folder->files && $share->folder->files->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tên tệp</th>
                                            <th>Loại</th>
                                            <th>Kích thước</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($share->folder->files as $file)
                                            <tr class="file-row" style="cursor: pointer;" onclick="openFilePreview({{ $file->id }})">
                                                <td>
                                                    @php
                                                        $ext = strtolower($file->extension);
                                                        $iconClass = 'ri-file-line';
                                                        $iconColor = '#6c757d';
                                                        
                                                        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                                                            $iconClass = 'ri-image-line';
                                                            $iconColor = '#28a745';
                                                        } elseif(in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'])) {
                                                            $iconClass = 'ri-video-line';
                                                            $iconColor = '#dc3545';
                                                        } elseif(in_array($ext, ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'])) {
                                                            $iconClass = 'ri-music-line';
                                                            $iconColor = '#17a2b8';
                                                        } elseif($ext === 'pdf') {
                                                            $iconClass = 'ri-file-pdf-line';
                                                            $iconColor = '#dc3545';
                                                        } elseif(in_array($ext, ['doc', 'docx'])) {
                                                            $iconClass = 'ri-file-word-line';
                                                            $iconColor = '#2b579a';
                                                        } elseif(in_array($ext, ['xls', 'xlsx'])) {
                                                            $iconClass = 'ri-file-excel-line';
                                                            $iconColor = '#217346';
                                                        } elseif(in_array($ext, ['ppt', 'pptx'])) {
                                                            $iconClass = 'ri-file-ppt-line';
                                                            $iconColor = '#d24726';
                                                        }
                                                    @endphp
                                                    <i class="{{ $iconClass }} mr-2" style="color: {{ $iconColor }};"></i>
                                                    {{ $file->original_name }}
                                                </td>
                                                <td><span class="badge badge-info">{{ strtoupper($file->extension) }}</span></td>
                                                <td>{{ $file->formatted_size }}</td>
                                                <td onclick="event.stopPropagation();">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="openFilePreview({{ $file->id }})" title="Xem">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    @if(in_array($share->permission, ['download', 'edit']))
                                                        <a href="{{ Storage::url($file->path) }}" download="{{ $file->original_name }}" class="btn btn-sm btn-primary" title="Tải xuống">
                                                            <i class="ri-download-line"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="ri-file-line" style="font-size: 64px;"></i>
                                <p class="mt-3">Thư mục này trống</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger text-center">
                    <i class="ri-error-warning-line" style="font-size: 48px;"></i>
                    <h5 class="mt-3">{{ __('common.folder_not_found_or_invalid_link') }}</h5>
                    <p class="mb-0">Link chia sẻ không hợp lệ hoặc đã hết hạn.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalTitle">Xem trước tệp</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="min-height: 500px;">
                <div id="previewContent" style="background: #f8f9fa;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Đang tải...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <a id="downloadFileBtn" href="#" class="btn btn-primary" style="display: none;">
                    <i class="ri-download-line mr-1"></i> Tải xuống
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Subfolder Preview Modal -->
<div class="modal fade" id="subfolderPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subfolderModalTitle">
                    <i class="ri-folder-3-line mr-2"></i>
                    <span id="subfolderName">Thư mục con</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="subfolderContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Đang tải...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
.file-row:hover {
    background-color: #f8f9fa;
}
.folder-card {
    transition: all 0.3s;
    cursor: pointer;
}
.folder-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.folder-details p {
    font-size: 0.9rem;
}
</style>

<script>
const files = @json($share->folder->files ?? []);
const subfolders = @json($share->folder->subfolders ?? []);
const sharePermission = '{{ $share->permission }}';

function openFilePreview(fileId) {
    const file = files.find(f => f.id === fileId);
    if (!file) return;
    
    $('#filePreviewModal').modal('show');
    $('#previewModalTitle').text(file.original_name);
    
    const downloadUrl = '{{ Storage::url("") }}' + file.path;
    const extension = file.extension.toLowerCase();
    const mimeType = file.mime_type;
    
    // Show/hide download button based on permission
    if (['download', 'edit'].includes(sharePermission)) {
        $('#downloadFileBtn').attr('href', downloadUrl).attr('download', file.original_name).show();
    } else {
        $('#downloadFileBtn').hide();
    }
    
    let html = '';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
        // Image
        html = `<div class="text-center p-4"><img src="${downloadUrl}" class="img-fluid" style="max-height: 80vh;"></div>`;
    } else if (['mp4', 'avi', 'mov', 'wmv', 'webm'].includes(extension)) {
        // Video
        html = `<div class="p-3"><video controls class="w-100" style="max-height: 70vh;"><source src="${downloadUrl}" type="${mimeType}"></video></div>`;
    } else if (['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'].includes(extension)) {
        // Audio
        html = `<div class="text-center p-5">
            <i class="ri-music-line" style="font-size: 120px; color: #17a2b8;"></i>
            <h4 class="my-4">${file.original_name}</h4>
            <audio controls class="w-75"><source src="${downloadUrl}" type="${mimeType}"></audio>
        </div>`;
    } else if (extension === 'pdf') {
        // PDF
        html = `<iframe src="${downloadUrl}" class="w-100" style="height: 85vh; border: none;"></iframe>`;
    } else if (['txt', 'md', 'log', 'json', 'xml', 'html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'sh', 'sql', 'yml', 'yaml'].includes(extension)) {
        // Text/Code
        html = `<div class="p-3"><pre id="code-preview-modal" style="background: #282c34; color: #abb2bf; padding: 20px; border-radius: 8px; max-height: 75vh; overflow: auto;"><code>Đang tải...</code></pre></div>`;
        $('#previewContent').html(html);
        fetch(downloadUrl)
            .then(r => r.text())
            .then(text => {
                document.querySelector('#code-preview-modal code').textContent = text;
            });
        return;
    } else {
        // Unsupported
        html = `<div class="text-center p-5">
            <i class="ri-file-line" style="font-size: 120px; color: #6c757d;"></i>
            <h4 class="mt-4">${file.original_name}</h4>
            <p class="text-muted">Không thể xem trước loại file này</p>
        </div>`;
    }
    
    $('#previewContent').html(html);
}

function openSubfolderPreview(subfolderId) {
    const subfolder = subfolders.find(sf => sf.id === subfolderId);
    if (!subfolder) return;
    
    $('#subfolderPreviewModal').modal('show');
    $('#subfolderName').text(subfolder.name);
    
    let html = '';
    
    if (subfolder.description) {
        html += `<div class="alert alert-info mb-3">
            <i class="ri-information-line mr-2"></i>${subfolder.description}
        </div>`;
    }
    
    if (subfolder.files && subfolder.files.length > 0) {
        html += `<h6 class="mb-3"><i class="ri-file-line mr-2"></i>Tệp tin (${subfolder.files.length})</h6>`;
        html += `<div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tên tệp</th>
                        <th>Loại</th>
                        <th>Kích thước</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>`;
        
        subfolder.files.forEach(file => {
            const ext = file.extension.toLowerCase();
            let iconClass = 'ri-file-line';
            let iconColor = '#6c757d';
            
            if(['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(ext)) {
                iconClass = 'ri-image-line';
                iconColor = '#28a745';
            } else if(['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'].includes(ext)) {
                iconClass = 'ri-video-line';
                iconColor = '#dc3545';
            } else if(['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'].includes(ext)) {
                iconClass = 'ri-music-line';
                iconColor = '#17a2b8';
            } else if(ext === 'pdf') {
                iconClass = 'ri-file-pdf-line';
                iconColor = '#dc3545';
            } else if(['doc', 'docx'].includes(ext)) {
                iconClass = 'ri-file-word-line';
                iconColor = '#2b579a';
            } else if(['xls', 'xlsx'].includes(ext)) {
                iconClass = 'ri-file-excel-line';
                iconColor = '#217346';
            } else if(['ppt', 'pptx'].includes(ext)) {
                iconClass = 'ri-file-ppt-line';
                iconColor = '#d24726';
            }
            
            const downloadUrl = '{{ Storage::url("") }}' + file.path;
            
            html += `<tr class="file-row" style="cursor: pointer;" onclick="openSubfolderFilePreview(${file.id}, ${subfolderId})">
                <td>
                    <i class="${iconClass} mr-2" style="color: ${iconColor};"></i>
                    ${file.original_name}
                </td>
                <td><span class="badge badge-info">${ext.toUpperCase()}</span></td>
                <td>${file.formatted_size}</td>
                <td onclick="event.stopPropagation();">
                    <button class="btn btn-sm btn-outline-primary" onclick="openSubfolderFilePreview(${file.id}, ${subfolderId})" title="Xem">
                        <i class="ri-eye-line"></i>
                    </button>`;
            
            if(['download', 'edit'].includes(sharePermission)) {
                html += `<a href="${downloadUrl}" download="${file.original_name}" class="btn btn-sm btn-primary" title="Tải xuống">
                        <i class="ri-download-line"></i>
                    </a>`;
            }
            
            html += `</td>
            </tr>`;
        });
        
        html += `</tbody>
            </table>
        </div>`;
    } else {
        html += `<div class="text-center py-5 text-muted">
            <i class="ri-file-line" style="font-size: 64px;"></i>
            <p class="mt-3">Thư mục này trống</p>
        </div>`;
    }
    
    $('#subfolderContent').html(html);
}

function openSubfolderFilePreview(fileId, subfolderId) {
    const subfolder = subfolders.find(sf => sf.id === subfolderId);
    if (!subfolder) return;
    
    const file = subfolder.files.find(f => f.id === fileId);
    if (!file) return;
    
    // Close subfolder modal first
    $('#subfolderPreviewModal').modal('hide');
    
    // Wait for modal to close, then open file preview
    setTimeout(() => {
        $('#filePreviewModal').modal('show');
        $('#previewModalTitle').text(file.original_name);
        
        const downloadUrl = '{{ Storage::url("") }}' + file.path;
        const extension = file.extension.toLowerCase();
        const mimeType = file.mime_type;
        
        // Show/hide download button based on permission
        if (['download', 'edit'].includes(sharePermission)) {
            $('#downloadFileBtn').attr('href', downloadUrl).attr('download', file.original_name).show();
        } else {
            $('#downloadFileBtn').hide();
        }
        
        let html = '';
        
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
            html = `<div class="text-center p-4"><img src="${downloadUrl}" class="img-fluid" style="max-height: 80vh;"></div>`;
        } else if (['mp4', 'avi', 'mov', 'wmv', 'webm'].includes(extension)) {
            html = `<div class="p-3"><video controls class="w-100" style="max-height: 70vh;"><source src="${downloadUrl}" type="${mimeType}"></video></div>`;
        } else if (['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'].includes(extension)) {
            html = `<div class="text-center p-5">
                <i class="ri-music-line" style="font-size: 120px; color: #17a2b8;"></i>
                <h4 class="my-4">${file.original_name}</h4>
                <audio controls class="w-75"><source src="${downloadUrl}" type="${mimeType}"></audio>
            </div>`;
        } else if (extension === 'pdf') {
            html = `<iframe src="${downloadUrl}" class="w-100" style="height: 85vh; border: none;"></iframe>`;
        } else if (['txt', 'md', 'log', 'json', 'xml', 'html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'sh', 'sql', 'yml', 'yaml'].includes(extension)) {
            html = `<div class="p-3"><pre id="code-preview-modal" style="background: #282c34; color: #abb2bf; padding: 20px; border-radius: 8px; max-height: 75vh; overflow: auto;"><code>Đang tải...</code></pre></div>`;
            $('#previewContent').html(html);
            fetch(downloadUrl)
                .then(r => r.text())
                .then(text => {
                    document.querySelector('#code-preview-modal code').textContent = text;
                });
            return;
        } else {
            html = `<div class="text-center p-5">
                <i class="ri-file-line" style="font-size: 120px; color: #6c757d;"></i>
                <h4 class="mt-4">${file.original_name}</h4>
                <p class="text-muted">Không thể xem trước loại file này</p>
            </div>`;
        }
        
        $('#previewContent').html(html);
    }, 300);
}
</script>
@endsection
