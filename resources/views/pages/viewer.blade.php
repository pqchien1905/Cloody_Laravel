@extends('layouts.app')

@section('title', 'Preview: ' . $file->original_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mr-2">
                            <i class="ri-arrow-left-line"></i> Back
                        </a>
                        <h5 class="mb-0 text-truncate" title="{{ $file->original_name }}">
                            <i class="ri-file-2-line mr-2"></i> {{ $file->original_name }}
                        </h5>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($file->folder)
                            <a href="{{ route('cloudbox.folders.show', $file->folder->id) }}" class="btn btn-sm btn-light mr-2">
                                <i class="ri-folder-3-line"></i> Open folder
                            </a>
                        @endif
                        <a href="{{ route('cloudbox.files.download', $file->id) }}" class="btn btn-sm btn-primary">
                            <i class="ri-download-2-line"></i> Download
                        </a>
                    </div>
                </div>
                <div class="card-body" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
                    @php
                        $mime = strtolower($file->mime_type ?? '');
                        $isImage = Str::startsWith($mime, 'image/');
                        $isPdf = Str::contains($mime, 'pdf');
                        $isVideo = Str::startsWith($mime, 'video/');
                        $isAudio = Str::startsWith($mime, 'audio/');
                        $isText = Str::startsWith($mime, 'text/') || in_array(strtolower($file->extension), ['txt','md','json','csv','log']);
                        $isDocx = Str::contains($mime, 'officedocument.wordprocessingml.document') || strtolower($file->extension) === 'docx';
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
                                <i class="ri-loader-4-line ri-spin"></i> Loading document...
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
                                    loading && (loading.innerHTML = 'Preview not available. You can download the file instead.');
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
                    @elseif($isText)
                        <iframe src="{{ $fileUrl }}" style="width:100%; height:75vh; border:none; background:#fff;" title="Text preview"></iframe>
                    @else
                        <div class="text-center py-5 w-100">
                            <i class="ri-file-3-line font-size-64 text-muted"></i>
                            <h6 class="mt-3">Preview not available</h6>
                            <p class="text-muted">You can download or open the file instead.</p>
                            <a class="btn btn-primary" href="{{ route('cloudbox.files.download', $file->id) }}">
                                <i class="ri-download-2-line"></i> Download
                            </a>
                            <a class="btn btn-light ml-2" href="{{ $fileUrl }}" target="_blank" rel="noopener">
                                <i class="ri-external-link-line"></i> Open in new tab
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center text-muted small">
                    <div>
                        <span class="mr-3"><i class="ri-hashtag"></i> Type: {{ strtoupper($file->extension) }}</span>
                        <span class="mr-3"><i class="ri-database-2-line"></i> Size: {{ $file->formatted_size }}</span>
                        <span><i class="ri-time-line"></i> Updated: {{ $file->updated_at->diffForHumans() }}</span>
                    </div>
                    <div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $fileUrl }}" target="_blank" rel="noopener">
                            <i class="ri-external-link-line"></i> Open raw
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
