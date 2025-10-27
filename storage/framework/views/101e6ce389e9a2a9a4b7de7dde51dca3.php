

<?php $__env->startSection('title', 'Preview: ' . $file->original_name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-sm btn-outline-secondary mr-2">
                            <i class="ri-arrow-left-line"></i> Back
                        </a>
                        <h5 class="mb-0 text-truncate" title="<?php echo e($file->original_name); ?>">
                            <i class="ri-file-2-line mr-2"></i> <?php echo e($file->original_name); ?>

                        </h5>
                    </div>
                    <div class="d-flex align-items-center">
                        <?php if($file->folder): ?>
                            <a href="<?php echo e(route('cloudbox.folders.show', $file->folder->id)); ?>" class="btn btn-sm btn-light mr-2">
                                <i class="ri-folder-3-line"></i> Open folder
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>" class="btn btn-sm btn-primary">
                            <i class="ri-download-2-line"></i> Download
                        </a>
                    </div>
                </div>
                <div class="card-body" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
                    <?php
                        $mime = strtolower($file->mime_type ?? '');
                        $isImage = Str::startsWith($mime, 'image/');
                        $isPdf = Str::contains($mime, 'pdf');
                        $isVideo = Str::startsWith($mime, 'video/');
                        $isAudio = Str::startsWith($mime, 'audio/');
                        $isText = Str::startsWith($mime, 'text/') || in_array(strtolower($file->extension), ['txt','md','json','csv','log']);
                        $isDocx = Str::contains($mime, 'officedocument.wordprocessingml.document') || strtolower($file->extension) === 'docx';
                    ?>

                    <?php if($isImage): ?>
                        <img src="<?php echo e($fileUrl); ?>" alt="<?php echo e($file->original_name); ?>" class="img-fluid" style="max-height: 75vh;">
                    <?php elseif($isPdf): ?>
                        <iframe src="<?php echo e($fileUrl); ?>#toolbar=1" style="width:100%; height:75vh; border: none;" title="PDF preview"></iframe>
                    <?php elseif($isVideo): ?>
                        <video controls style="width:100%; max-height:75vh; background:#000;">
                            <source src="<?php echo e($fileUrl); ?>" type="<?php echo e($file->mime_type); ?>" />
                            Your browser does not support the video tag.
                        </video>
                    <?php elseif($isAudio): ?>
                        <audio controls style="width:100%;">
                            <source src="<?php echo e($fileUrl); ?>" type="<?php echo e($file->mime_type); ?>" />
                            Your browser does not support the audio element.
                        </audio>
                    <?php elseif($isDocx): ?>
                        <div id="docxContainer" class="w-100" style="max-width: 900px; margin: 0 auto;">
                            <div id="docxLoading" class="text-center text-muted">
                                <i class="ri-loader-4-line ri-spin"></i> Loading document...
                            </div>
                        </div>
                        <?php $__env->startPush('scripts'); ?>
                        <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>
                        <script>
                            (function(){
                                const url = <?php echo json_encode($fileUrl, 15, 512) ?>;
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
                        <?php $__env->stopPush(); ?>
                    <?php elseif($isText): ?>
                        <iframe src="<?php echo e($fileUrl); ?>" style="width:100%; height:75vh; border:none; background:#fff;" title="Text preview"></iframe>
                    <?php else: ?>
                        <div class="text-center py-5 w-100">
                            <i class="ri-file-3-line font-size-64 text-muted"></i>
                            <h6 class="mt-3">Preview not available</h6>
                            <p class="text-muted">You can download or open the file instead.</p>
                            <a class="btn btn-primary" href="<?php echo e(route('cloudbox.files.download', $file->id)); ?>">
                                <i class="ri-download-2-line"></i> Download
                            </a>
                            <a class="btn btn-light ml-2" href="<?php echo e($fileUrl); ?>" target="_blank" rel="noopener">
                                <i class="ri-external-link-line"></i> Open in new tab
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center text-muted small">
                    <div>
                        <span class="mr-3"><i class="ri-hashtag"></i> Type: <?php echo e(strtoupper($file->extension)); ?></span>
                        <span class="mr-3"><i class="ri-database-2-line"></i> Size: <?php echo e($file->formatted_size); ?></span>
                        <span><i class="ri-time-line"></i> Updated: <?php echo e($file->updated_at->diffForHumans()); ?></span>
                    </div>
                    <div>
                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo e($fileUrl); ?>" target="_blank" rel="noopener">
                            <i class="ri-external-link-line"></i> Open raw
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel\resources\views/pages/viewer.blade.php ENDPATH**/ ?>