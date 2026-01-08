@extends('layouts.app')

@section('title', __('common.shared_file'))

@section('content')
<div class="container-fluid py-4">
    @if(isset($share) && $share->file)
        <div class="row">
            <!-- File Info Sidebar -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="ri-information-line mr-2"></i>Thông tin tệp
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @php
                                $ext = strtolower($share->file->extension);
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
                                } elseif(in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                    $iconClass = 'ri-file-zip-line';
                                    $iconColor = '#ffc107';
                                } elseif(in_array($ext, ['txt', 'md', 'log'])) {
                                    $iconClass = 'ri-file-text-line';
                                    $iconColor = '#6c757d';
                                } elseif(in_array($ext, ['html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'json', 'xml'])) {
                                    $iconClass = 'ri-code-line';
                                    $iconColor = '#563d7c';
                                }
                            @endphp
                            <i class="{{ $iconClass }}" style="font-size: 64px; color: {{ $iconColor }};"></i>
                        </div>
                        
                        <h5 class="text-center mb-3 text-truncate" title="{{ $share->file->original_name }}">
                            {{ $share->file->original_name }}
                        </h5>

                        <hr>

                        <div class="file-details">
                            <p class="mb-2 d-flex justify-content-between">
                                <strong>Loại:</strong> 
                                <span class="badge badge-info">{{ strtoupper($share->file->extension) }}</span>
                            </p>
                            <p class="mb-2 d-flex justify-content-between">
                                <strong>Kích thước:</strong> 
                                <span>{{ $share->file->formatted_size }}</span>
                            </p>
                            <p class="mb-2 d-flex justify-content-between">
                                <strong>Chia sẻ bởi:</strong> 
                                <span>{{ $share->sharedBy->name ?? 'Không xác định' }}</span>
                            </p>
                            <p class="mb-2 d-flex justify-content-between">
                                <strong>Quyền:</strong> 
                                <span class="badge badge-{{ $share->permission === 'download' ? 'success' : 'secondary' }}">
                                    {{ $share->permission === 'download' ? 'Tải xuống' : 'Chỉ xem' }}
                                </span>
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
                                <a href="{{ route('file.shared.download', $share->share_token) }}" class="btn btn-primary btn-block">
                                    <i class="ri-download-line mr-1"></i> Tải xuống
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Preview/Content -->
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="ri-eye-line mr-2"></i>Xem trước nội dung
                        </h5>
                    </div>
                    <div class="card-body p-0" style="background: #f8f9fa;">
                        <div id="file-preview-container" style="min-height: 500px;">
                            @php
                                $mimeType = $share->file->mime_type;
                                $extension = strtolower($share->file->extension);
                                $downloadUrl = route('file.shared.download', $share->share_token);
                                $viewUrl = route('file.shared.view', $share->share_token);
                            @endphp

                            @if(str_starts_with($mimeType, 'image/'))
                                <!-- Image Preview -->
                                <div class="text-center p-4">
                                    <img src="{{ $viewUrl }}" class="img-fluid" alt="{{ $share->file->original_name }}" style="max-height: 80vh; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                </div>

                            @elseif(str_starts_with($mimeType, 'video/'))
                                <!-- Video Preview -->
                                <div class="p-3">
                                    <video controls class="w-100" style="max-height: 70vh; background: #000;">
                                        <source src="{{ $viewUrl }}" type="{{ $mimeType }}">
                                        Trình duyệt của bạn không hỗ trợ phát video.
                                    </video>
                                </div>

                            @elseif(str_starts_with($mimeType, 'audio/'))
                                <!-- Audio Preview -->
                                <div class="text-center p-5">
                                    <div class="mb-4">
                                        <i class="ri-music-line" style="font-size: 120px; color: #17a2b8;"></i>
                                    </div>
                                    <h4 class="mb-4">{{ $share->file->original_name }}</h4>
                                    <audio controls class="w-75">
                                        <source src="{{ $viewUrl }}" type="{{ $mimeType }}">
                                        Trình duyệt của bạn không hỗ trợ phát audio.
                                    </audio>
                                </div>

                            @elseif($mimeType === 'application/pdf')
                                <!-- PDF Preview -->
                                <iframe src="{{ $viewUrl }}" class="w-100" style="height: 85vh; border: none;"></iframe>

                            @elseif(in_array($extension, ['txt', 'md', 'log', 'json', 'xml', 'html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'sh', 'sql', 'yml', 'yaml']))
                                <!-- Text/Code Preview -->
                                <div class="p-3">
                                    <pre id="code-preview" style="background: #282c34; color: #abb2bf; padding: 20px; border-radius: 8px; max-height: 75vh; overflow: auto; font-family: 'Courier New', monospace; font-size: 14px;"><code>Đang tải nội dung...</code></pre>
                                </div>
                                <script>
                                    fetch('{{ $viewUrl }}')
                                        .then(response => response.text())
                                        .then(text => {
                                            const codeEl = document.querySelector('#code-preview code');
                                            codeEl.textContent = text;
                                            
                                            // Simple syntax highlighting for common patterns
                                            let html = text
                                                .replace(/&/g, '&amp;')
                                                .replace(/</g, '&lt;')
                                                .replace(/>/g, '&gt;')
                                                .replace(/(\/\/.*$)/gm, '<span style="color: #5c6370;">$1</span>')
                                                .replace(/('.*?'|".*?")/g, '<span style="color: #98c379;">$1</span>')
                                                .replace(/\b(function|var|let|const|if|else|for|while|return|class|public|private|protected|static)\b/g, '<span style="color: #c678dd;">$1</span>')
                                                .replace(/\b(\d+)\b/g, '<span style="color: #d19a66;">$1</span>');
                                            
                                            codeEl.innerHTML = html;
                                        })
                                        .catch(err => {
                                            document.querySelector('#code-preview code').textContent = 'Không thể tải nội dung file.';
                                        });
                                </script>

                            @elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                                <!-- Office Documents Preview with Client-Side Libraries -->
                                <div class="p-3">
                                    <div class="alert alert-info mb-3">
                                        <i class="ri-information-line mr-2"></i>
                                        <span id="viewer-status">Đang tải preview...</span>
                                    </div>
                                    
                                    <!-- Office Viewer Container -->
                                    <div id="office-viewer-container" style="min-height: 70vh; border: 1px solid #dee2e6; border-radius: 8px; overflow: auto; background: white; padding: 20px;">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-3">Đang tải nội dung...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(in_array($extension, ['docx']))
                                    <!-- Mammoth.js for DOCX -->
                                    <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const status = document.getElementById('viewer-status');
                                            const container = document.getElementById('office-viewer-container');
                                            
                                            status.innerHTML = '<i class="ri-loader-4-line ri-spin mr-2"></i>Đang tải file Word...';
                                            
                                            fetch('{{ $viewUrl }}')
                                                .then(response => {
                                                    console.log('Response status:', response.status);
                                                    return response.arrayBuffer();
                                                })
                                                .then(arrayBuffer => {
                                                    console.log('ArrayBuffer size:', arrayBuffer.byteLength);
                                                    return mammoth.convertToHtml({arrayBuffer: arrayBuffer});
                                                })
                                                .then(result => {
                                                    console.log('Mammoth result:', result);
                                                    container.innerHTML = '<div style="max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif; line-height: 1.6; padding: 20px;">' + result.value + '</div>';
                                                    status.innerHTML = '<i class="ri-check-line mr-2"></i>Đã tải thành công!';
                                                    
                                                    if (result.messages.length > 0) {
                                                        console.log('Mammoth warnings:', result.messages);
                                                    }
                                                })
                                                .catch(err => {
                                                    console.error('Error loading DOCX:', err);
                                                    container.innerHTML = `
                                                        <div class="text-center py-5">
                                                            <i class="ri-error-warning-line" style="font-size: 64px; color: #dc3545;"></i>
                                                            <h5 class="mt-3">Không thể xem trước file này</h5>
                                                            <p class="text-muted">Lỗi: ${err.message}</p>
                                                            <a href="{{ $downloadUrl }}" class="btn btn-primary mt-3">
                                                                <i class="ri-download-line mr-1"></i> Tải xuống
                                                            </a>
                                                        </div>
                                                    `;
                                                    status.innerHTML = '<i class="ri-error-warning-line mr-2"></i>Không thể tải preview';
                                                });
                                        });
                                    </script>
                                    
                                @elseif(in_array($extension, ['xls', 'xlsx']))
                                    <!-- SheetJS for Excel -->
                                    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const status = document.getElementById('viewer-status');
                                            const container = document.getElementById('office-viewer-container');
                                            
                                            status.innerHTML = '<i class="ri-loader-4-line ri-spin mr-2"></i>Đang tải file Excel...';
                                            
                                            fetch('{{ $viewUrl }}')
                                                .then(response => {
                                                    console.log('Response status:', response.status);
                                                    return response.arrayBuffer();
                                                })
                                                .then(arrayBuffer => {
                                                    console.log('ArrayBuffer size:', arrayBuffer.byteLength);
                                                    const workbook = XLSX.read(arrayBuffer, {type: 'array'});
                                                    console.log('Workbook:', workbook);
                                                    let html = '<div style="max-width: 100%; overflow-x: auto;">';
                                                    
                                                    // Nav tabs for sheets
                                                    if (workbook.SheetNames.length > 1) {
                                                        html += '<ul class="nav nav-tabs mb-3" role="tablist">';
                                                        workbook.SheetNames.forEach((name, index) => {
                                                            html += `<li class="nav-item">
                                                                <a class="nav-link ${index === 0 ? 'active' : ''}" data-toggle="tab" href="#sheet-${index}">${name}</a>
                                                            </li>`;
                                                        });
                                                        html += '</ul>';
                                                    }
                                                    
                                                    // Tab content
                                                    html += '<div class="tab-content">';
                                                    workbook.SheetNames.forEach((name, index) => {
                                                        const worksheet = workbook.Sheets[name];
                                                        const htmlTable = XLSX.utils.sheet_to_html(worksheet, {editable: false});
                                                        html += `<div class="tab-pane fade ${index === 0 ? 'show active' : ''}" id="sheet-${index}">
                                                            ${htmlTable}
                                                        </div>`;
                                                    });
                                                    html += '</div></div>';
                                                    
                                                    container.innerHTML = html;
                                                    status.innerHTML = '<i class="ri-check-line mr-2"></i>Đã tải thành công!';
                                                    
                                                    // Style tables
                                                    container.querySelectorAll('table').forEach(table => {
                                                        table.className = 'table table-bordered table-striped table-sm';
                                                        table.style.fontSize = '13px';
                                                    });
                                                })
                                                .catch(err => {
                                                    console.error('Error loading Excel:', err);
                                                    container.innerHTML = `
                                                        <div class="text-center py-5">
                                                            <i class="ri-error-warning-line" style="font-size: 64px; color: #dc3545;"></i>
                                                            <h5 class="mt-3">Không thể xem trước file này</h5>
                                                            <p class="text-muted">Lỗi: ${err.message}</p>
                                                            <a href="{{ $downloadUrl }}" class="btn btn-primary mt-3">
                                                                <i class="ri-download-line mr-1"></i> Tải xuống
                                                            </a>
                                                        </div>
                                                    `;
                                                    status.innerHTML = '<i class="ri-error-warning-line mr-2"></i>Không thể tải preview';
                                                });
                                        });
                                    </script>
                                    
                                @elseif(in_array($extension, ['ppt', 'pptx']))
                                    <!-- PowerPoint - Download only -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const status = document.getElementById('viewer-status');
                                            const container = document.getElementById('office-viewer-container');
                                            
                                            container.innerHTML = `
                                                <div class="text-center py-5">
                                                    <i class="ri-file-ppt-line" style="font-size: 120px; color: #d24726;"></i>
                                                    <h4 class="mt-4">{{ $share->file->original_name }}</h4>
                                                    <p class="text-muted mt-3">File PowerPoint không thể xem trước trực tiếp trên trình duyệt.</p>
                                                    <p class="text-muted">Vui lòng tải xuống để xem nội dung đầy đủ với Microsoft PowerPoint hoặc phần mềm tương thích.</p>
                                                    <a href="{{ $downloadUrl }}" class="btn btn-primary btn-lg mt-3">
                                                        <i class="ri-download-line mr-2"></i> Tải xuống PowerPoint
                                                    </a>
                                                </div>
                                            `;
                                            status.innerHTML = '<i class="ri-information-line mr-2"></i>File PowerPoint - Tải xuống để xem';
                                        });
                                    </script>
                                    
                                @elseif($extension === 'doc')
                                    <!-- DOC (Old Word) - Download only -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const status = document.getElementById('viewer-status');
                                            const container = document.getElementById('office-viewer-container');
                                            
                                            container.innerHTML = `
                                                <div class="text-center py-5">
                                                    <i class="ri-file-word-line" style="font-size: 120px; color: #2b579a;"></i>
                                                    <h4 class="mt-4">{{ $share->file->original_name }}</h4>
                                                    <p class="text-muted mt-3">File .DOC (định dạng Word cũ) không thể xem trước trực tiếp.</p>
                                                    <p class="text-muted">Vui lòng tải xuống để xem nội dung với Microsoft Word.</p>
                                                    <a href="{{ $downloadUrl }}" class="btn btn-primary btn-lg mt-3">
                                                        <i class="ri-download-line mr-2"></i> Tải xuống Word
                                                    </a>
                                                </div>
                                            `;
                                            status.innerHTML = '<i class="ri-information-line mr-2"></i>File .DOC - Tải xuống để xem';
                                        });
                                    </script>
                                @endif

                            @elseif(in_array($extension, ['csv']))
                                <!-- CSV Preview -->
                                <div class="p-3">
                                    <div class="table-responsive" style="max-height: 80vh; overflow: auto;">
                                        <table id="csv-table" class="table table-striped table-bordered table-sm">
                                            <thead id="csv-header" class="thead-dark"></thead>
                                            <tbody id="csv-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <script>
                                    fetch('{{ $viewUrl }}')
                                        .then(response => response.text())
                                        .then(csv => {
                                            const lines = csv.split('\n').filter(line => line.trim());
                                            if (lines.length === 0) return;
                                            
                                            // Header
                                            const headers = lines[0].split(',');
                                            const headerRow = document.getElementById('csv-header');
                                            headerRow.innerHTML = '<tr>' + headers.map(h => `<th>${h.trim()}</th>`).join('') + '</tr>';
                                            
                                            // Body
                                            const tbody = document.getElementById('csv-body');
                                            for (let i = 1; i < Math.min(lines.length, 1000); i++) {
                                                const cells = lines[i].split(',');
                                                tbody.innerHTML += '<tr>' + cells.map(c => `<td>${c.trim()}</td>`).join('') + '</tr>';
                                            }
                                            
                                            if (lines.length > 1000) {
                                                tbody.innerHTML += `<tr><td colspan="${headers.length}" class="text-center text-muted">... và ${lines.length - 1000} dòng nữa</td></tr>`;
                                            }
                                        })
                                        .catch(err => {
                                            document.getElementById('csv-body').innerHTML = '<tr><td class="text-danger">Không thể tải file CSV</td></tr>';
                                        });
                                </script>

                            @elseif(in_array($extension, ['svg']))
                                <!-- SVG Preview -->
                                <div class="text-center p-4" style="background: white;">
                                    <object data="{{ $viewUrl }}" type="image/svg+xml" style="max-width: 100%; max-height: 80vh;">
                                        <img src="{{ $viewUrl }}" alt="{{ $share->file->original_name }}" style="max-width: 100%; max-height: 80vh;">
                                    </object>
                                </div>

                            @elseif(in_array($extension, ['rtf']))
                                <!-- RTF Preview -->
                                <div class="p-3">
                                    <div class="alert alert-info">
                                        <i class="ri-information-line mr-2"></i>
                                        File RTF - Tải xuống để xem nội dung đầy đủ
                                    </div>
                                    <iframe src="https://docs.google.com/gview?url={{ urlencode(url($viewUrl)) }}&embedded=true" class="w-100" style="height: 80vh; border: none;"></iframe>
                                </div>

                            @else
                                <!-- Unsupported File Type -->
                                <div class="text-center p-5">
                                    <i class="{{ $iconClass }}" style="font-size: 120px; color: {{ $iconColor }};"></i>
                                    <h4 class="mt-4">{{ $share->file->original_name }}</h4>
                                    <p class="text-muted">Không thể xem trước loại file này trực tiếp.</p>
                                    @if(in_array($share->permission, ['download', 'edit']))
                                        <a href="{{ $downloadUrl }}" class="btn btn-primary mt-3">
                                            <i class="ri-download-line mr-1"></i> Tải xuống để xem
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger text-center">
                    <i class="ri-error-warning-line" style="font-size: 48px;"></i>
                    <h5 class="mt-3">{{ __('common.file_not_found_or_invalid_link') }}</h5>
                    <p class="mb-0">Link chia sẻ không hợp lệ hoặc đã hết hạn.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.file-details p {
    font-size: 0.9rem;
}
.file-details strong {
    color: #495057;
}
#file-preview-container {
    position: relative;
}
</style>
@endsection
