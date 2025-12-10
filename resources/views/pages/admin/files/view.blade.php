@extends('layouts.app')

@section('title', 'Preview: ' . $file->original_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if(request('from') === 'favorites')
                            <a href="{{ route('admin.favorites.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="ri-arrow-left-line"></i> {{ __('common.back') }}
                            </a>
                        @else
                            <a href="{{ route('admin.files.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="ri-arrow-left-line"></i> {{ __('common.back') }}
                            </a>
                        @endif
                        <h5 class="mb-0 text-truncate" title="{{ $file->original_name }}">
                            <i class="ri-file-2-line mr-2"></i> {{ $file->original_name }}
                        </h5>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($file->folder && request('from') !== 'favorites')
                            <a href="{{ route('admin.files.index', ['folder_id' => $file->folder_id]) }}" class="btn btn-sm btn-light mr-2">
                                <i class="ri-folder-3-line"></i> {{ __('common.open_folder') }}
                            </a>
                        @endif
                        <a href="{{ route('admin.files.show', ['file' => $file->id, 'from' => request('from')]) }}" class="btn btn-sm btn-info mr-2">
                            <i class="ri-information-line"></i> {{ __('common.details') }}
                        </a>
                        <a href="{{ route('admin.files.download', $file->id) }}" class="btn btn-sm btn-primary">
                            <i class="ri-download-2-line"></i> {{ __('common.download') }}
                        </a>
                    </div>
                </div>
                <div class="card-body" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
                    @php
                        $mime = strtolower($file->mime_type ?? '');
                        $ext = strtolower($file->extension ?? '');
                        $isImage = Str::startsWith($mime, 'image/');
                        $isPdf = Str::contains($mime, 'pdf');
                        $isVideo = Str::startsWith($mime, 'video/');
                        $isAudio = Str::startsWith($mime, 'audio/');
                        $isText = Str::startsWith($mime, 'text/') || in_array($ext, ['txt','md','json','log']);
                        $isDocx = Str::contains($mime, 'officedocument.wordprocessingml.document') || $ext === 'docx';
                        $isExcel = Str::contains($mime, 'spreadsheetml') || Str::contains($mime, 'ms-excel') || in_array($ext, ['xlsx', 'xls', 'xlsm', 'xlsb']);
                        $isCsv = $ext === 'csv';
                    @endphp

                    @if($isImage)
                        <img src="{{ $fileUrl }}" alt="{{ $file->original_name }}" class="img-fluid" style="max-height: 75vh;">
                    @elseif($isPdf)
                        <iframe src="{{ $fileUrl }}#toolbar=1" style="width:100%; height:75vh; border: none;" title="PDF preview"></iframe>
                    @elseif($isVideo)
                        <video controls style="width:100%; max-height:75vh; background:#000;">
                            <source src="{{ $fileUrl }}" type="{{ $file->mime_type }}" />
                            Your browser does not support the video tag.
                        </video>
                    @elseif($isAudio)
                        <audio controls style="width:100%;">
                            <source src="{{ $fileUrl }}" type="{{ $file->mime_type }}" />
                            Your browser does not support the audio element.
                        </audio>
                    @elseif($isDocx)
                        <div id="docxContainer" class="w-100" style="max-width: 900px; margin: 0 auto;">
                            <div id="docxLoading" class="text-center text-muted">
                                <i class="ri-loader-4-line ri-spin"></i> {{ __('common.loading_document') }}...
                            </div>
                        </div>
                        @push('scripts')
                        <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
                        <script>
                            (function(){
                                const url = @json($fileUrl);
                                const container = document.getElementById('docxContainer');
                                const loading = document.getElementById('docxLoading');
                                fetch(url).then(r => {
                                    if(!r.ok) throw new Error('Network error');
                                    return r.arrayBuffer();
                                }).then(arrayBuffer => {
                                    return window.mammoth.convertToHtml({arrayBuffer});
                                }).then(result => {
                                    loading && loading.remove();
                                    const wrapper = document.createElement('div');
                                    wrapper.className = 'docx-content';
                                    wrapper.innerHTML = result.value;
                                    container.appendChild(wrapper);
                                }).catch(err => {
                                    loading && (loading.innerHTML = '{{ __('common.preview_not_available_download') }}');
                                });
                            })();
                        </script>
                        <style>
                            /* Simple readable docx styles */
                            .docx-content { background:#fff; color:#111; line-height:1.6; padding:24px; border:1px solid #e5e7eb; border-radius:6px; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
                            .docx-content h1, .docx-content h2, .docx-content h3 { margin-top:1.2em; margin-bottom:.5em; }
                            .docx-content p { margin: .5em 0; }
                            .docx-content table { width:100%; border-collapse: collapse; margin:1em 0; }
                            .docx-content table, .docx-content th, .docx-content td { border:1px solid #ddd; }
                            .docx-content th, .docx-content td { padding:.5em; }
                        </style>
                        @endpush
                    @elseif($isExcel)
                        <div id="excelContainer" class="w-100" style="max-width: 100%; margin: 0 auto;">
                            <div id="excelLoading" class="text-center text-muted py-5">
                                <i class="ri-loader-4-line ri-spin font-size-32"></i>
                                <p class="mt-3">{{ __('common.loading') }}...</p>
                            </div>
                            <div id="excelContent" style="display: none;">
                                <div class="mb-3">
                                    <select id="excelSheetSelect" class="form-control" style="max-width: 300px;">
                                        <option value="">{{ __('common.select_sheet') }}</option>
                                    </select>
                                </div>
                                <div id="excelTableWrapper" style="overflow-x: auto; max-height: 70vh; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                    <table id="excelTable" class="table table-bordered table-sm mb-0" style="background: #fff;">
                                        <thead id="excelTableHead"></thead>
                                        <tbody id="excelTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @push('scripts')
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
                        <script>
                            (function(){
                                const url = @json($fileUrl);
                                const container = document.getElementById('excelContainer');
                                const loading = document.getElementById('excelLoading');
                                const content = document.getElementById('excelContent');
                                const sheetSelect = document.getElementById('excelSheetSelect');
                                const tableHead = document.getElementById('excelTableHead');
                                const tableBody = document.getElementById('excelTableBody');
                                
                                let workbook = null;
                                
                                fetch(url).then(r => {
                                    if(!r.ok) throw new Error('Network error');
                                    return r.arrayBuffer();
                                }).then(arrayBuffer => {
                                    workbook = XLSX.read(arrayBuffer, {type: 'array'});
                                    
                                    // Populate sheet selector
                                    workbook.SheetNames.forEach((name, index) => {
                                        const option = document.createElement('option');
                                        option.value = index;
                                        option.textContent = name;
                                        if(index === 0) option.selected = true;
                                        sheetSelect.appendChild(option);
                                    });
                                    
                                    // Load first sheet
                                    loadSheet(0);
                                    
                                    loading.style.display = 'none';
                                    content.style.display = 'block';
                                }).catch(err => {
                                    console.error('Error loading Excel:', err);
                                    loading.innerHTML = '<div class="text-danger"><i class="ri-error-warning-line"></i> {{ __('common.unable_to_load_file') }}</div>';
                                });
                                
                                function loadSheet(sheetIndex) {
                                    if(!workbook || !workbook.SheetNames[sheetIndex]) return;
                                    
                                    const sheetName = workbook.SheetNames[sheetIndex];
                                    const worksheet = workbook.Sheets[sheetName];
                                    
                                    // Convert to JSON with header row
                                    const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
                                    
                                    if(jsonData.length === 0) {
                                        tableHead.innerHTML = '<tr><td colspan="1" class="text-center text-muted">{{ __('common.empty_sheet') }}</td></tr>';
                                        tableBody.innerHTML = '';
                                        return;
                                    }
                                    
                                    // Get header row (first row) or generate column names
                                    const headers = jsonData[0] || [];
                                    const maxCols = jsonData.length > 0 ? Math.max(...jsonData.map(row => row ? row.length : 0), headers.length) : 0;
                                    
                                    // Build header
                                    let headerHtml = '<tr>';
                                    for(let i = 0; i < maxCols; i++) {
                                        let header = headers[i] || '';
                                        if(!header) {
                                            // Generate column name: A, B, C, ..., Z, AA, AB, etc.
                                            let colName = '';
                                            let num = i;
                                            while(num >= 0) {
                                                colName = String.fromCharCode(65 + (num % 26)) + colName;
                                                num = Math.floor(num / 26) - 1;
                                            }
                                            header = colName;
                                        }
                                        headerHtml += `<th style="background: #f8f9fa; position: sticky; top: 0; z-index: 10; font-weight: 600; white-space: nowrap; padding: 8px;">${header}</th>`;
                                    }
                                    headerHtml += '</tr>';
                                    tableHead.innerHTML = headerHtml;
                                    
                                    // Build body
                                    let bodyHtml = '';
                                    const startRow = jsonData.length > 0 && headers.length > 0 ? 1 : 0;
                                    for(let rowIndex = startRow; rowIndex < jsonData.length; rowIndex++) {
                                        const row = jsonData[rowIndex] || [];
                                        bodyHtml += '<tr>';
                                        for(let colIndex = 0; colIndex < maxCols; colIndex++) {
                                            const cell = row[colIndex] !== undefined ? row[colIndex] : '';
                                            bodyHtml += `<td style="white-space: nowrap; padding: 6px 8px; border: 1px solid #dee2e6;">${cell}</td>`;
                                        }
                                        bodyHtml += '</tr>';
                                    }
                                    tableBody.innerHTML = bodyHtml;
                                }
                                
                                sheetSelect.addEventListener('change', function() {
                                    if(this.value !== '') {
                                        loadSheet(parseInt(this.value));
                                    }
                                });
                            })();
                        </script>
                        <style>
                            #excelTable {
                                font-size: 13px;
                            }
                            #excelTable th {
                                background: #f8f9fa !important;
                                border: 1px solid #dee2e6;
                            }
                            #excelTable td {
                                border: 1px solid #dee2e6;
                            }
                            #excelTableWrapper {
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            }
                        </style>
                        @endpush
                    @elseif($isCsv)
                        <div id="csvContainer" class="w-100" style="max-width: 100%; margin: 0 auto;">
                            <div id="csvLoading" class="text-center text-muted py-5">
                                <i class="ri-loader-4-line ri-spin font-size-32"></i>
                                <p class="mt-3">{{ __('common.loading') }}...</p>
                            </div>
                            <div id="csvContent" style="display: none;">
                                <div id="csvTableWrapper" style="overflow-x: auto; max-height: 70vh; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                    <table id="csvTable" class="table table-bordered table-sm mb-0" style="background: #fff;">
                                        <thead id="csvTableHead"></thead>
                                        <tbody id="csvTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @push('scripts')
                        <script>
                            (function(){
                                const url = @json($fileUrl);
                                const loading = document.getElementById('csvLoading');
                                const content = document.getElementById('csvContent');
                                const tableHead = document.getElementById('csvTableHead');
                                const tableBody = document.getElementById('csvTableBody');
                                
                                fetch(url).then(r => {
                                    if(!r.ok) throw new Error('Network error');
                                    return r.text();
                                }).then(text => {
                                    const lines = text.split('\n').filter(line => line.trim());
                                    if(lines.length === 0) {
                                        tableHead.innerHTML = '<tr><td colspan="1" class="text-center text-muted">{{ __('common.empty_file') }}</td></tr>';
                                        tableBody.innerHTML = '';
                                        loading.style.display = 'none';
                                        content.style.display = 'block';
                                        return;
                                    }
                                    
                                    // Parse CSV (simple parser - handles basic CSV)
                                    function parseCSVLine(line) {
                                        const result = [];
                                        let current = '';
                                        let inQuotes = false;
                                        
                                        for(let i = 0; i < line.length; i++) {
                                            const char = line[i];
                                            if(char === '"') {
                                                inQuotes = !inQuotes;
                                            } else if(char === ',' && !inQuotes) {
                                                result.push(current.trim());
                                                current = '';
                                            } else {
                                                current += char;
                                            }
                                        }
                                        result.push(current.trim());
                                        return result;
                                    }
                                    
                                    const rows = lines.map(parseCSVLine);
                                    const maxCols = Math.max(...rows.map(row => row.length));
                                    
                                    // First row as header
                                    const headers = rows[0] || [];
                                    let headerHtml = '<tr>';
                                    for(let i = 0; i < maxCols; i++) {
                                        const header = headers[i] || 'Column ' + (i + 1);
                                        headerHtml += `<th style="background: #f8f9fa; position: sticky; top: 0; z-index: 10; font-weight: 600; white-space: nowrap; padding: 8px;">${header}</th>`;
                                    }
                                    headerHtml += '</tr>';
                                    tableHead.innerHTML = headerHtml;
                                    
                                    // Rest as body
                                    let bodyHtml = '';
                                    for(let rowIndex = 1; rowIndex < rows.length; rowIndex++) {
                                        const row = rows[rowIndex] || [];
                                        bodyHtml += '<tr>';
                                        for(let colIndex = 0; colIndex < maxCols; colIndex++) {
                                            const cell = row[colIndex] !== undefined ? row[colIndex] : '';
                                            bodyHtml += `<td style="white-space: nowrap; padding: 6px 8px; border: 1px solid #dee2e6;">${cell}</td>`;
                                        }
                                        bodyHtml += '</tr>';
                                    }
                                    tableBody.innerHTML = bodyHtml;
                                    
                                    loading.style.display = 'none';
                                    content.style.display = 'block';
                                }).catch(err => {
                                    console.error('Error loading CSV:', err);
                                    loading.innerHTML = '<div class="text-danger"><i class="ri-error-warning-line"></i> {{ __('common.unable_to_load_file') }}</div>';
                                });
                            })();
                        </script>
                        <style>
                            #csvTable {
                                font-size: 13px;
                            }
                            #csvTable th {
                                background: #f8f9fa !important;
                                border: 1px solid #dee2e6;
                            }
                            #csvTable td {
                                border: 1px solid #dee2e6;
                            }
                            #csvTableWrapper {
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            }
                        </style>
                        @endpush
                    @elseif($isText)
                        <iframe src="{{ $fileUrl }}" style="width:100%; height:75vh; border:none; background:#fff;" title="Text preview"></iframe>
                    @else
                        <div class="text-center py-5 w-100">
                            <i class="ri-file-3-line font-size-64 text-muted"></i>
                            <h6 class="mt-3">{{ __('common.preview_not_available') }}</h6>
                            <p class="text-muted">{{ __('common.download_or_open_file') }}</p>
                            <a class="btn btn-primary" href="{{ route('admin.files.download', $file->id) }}">
                                <i class="ri-download-2-line"></i> {{ __('common.download') }}
                            </a>
                            <a class="btn btn-light ml-2" href="{{ $fileUrl }}" target="_blank" rel="noopener">
                                <i class="ri-external-link-line"></i> {{ __('common.open_in_new_tab') }}
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center text-muted small">
                    <div>
                        <span class="mr-3"><i class="ri-hashtag"></i> {{ __('common.type') }}: {{ strtoupper($file->extension) }}</span>
                        <span class="mr-3"><i class="ri-database-2-line"></i> {{ __('common.size') }}: {{ $file->formatted_size }}</span>
                        <span class="mr-3"><i class="ri-user-line"></i> {{ __('common.owner') }}: {{ $file->user->name ?? 'Unknown' }}</span>
                        <span><i class="ri-time-line"></i> {{ __('common.updated') }}: {{ $file->updated_at->diffForHumans() }}</span>
                    </div>
                    <div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $fileUrl }}" target="_blank" rel="noopener">
                            <i class="ri-external-link-line"></i> {{ __('common.open_raw') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
