

<?php $__env->startSection('title', 'Admin Dashboard - CloudBOX'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card-transparent card-block card-stretch card-height">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Admin Dashboard</h4>
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

        <!-- Top Stats -->
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">Total Users</h6>
                            <h3 class="mb-0"><?php echo e($totalUsers); ?></h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">Total Files</h6>
                            <h3 class="mb-0"><?php echo e($totalFiles); ?></h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-file text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">Total Folders</h6>
                            <h3 class="mb-0"><?php echo e($totalFolders); ?></h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-folder text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">Total Shares</h6>
                            <h3 class="mb-0"><?php echo e($totalFileShares + $totalFolderShares); ?></h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-2">
                            <i class="las la-share-alt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Overview -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Storage Usage</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="mb-3"><?php echo e(number_format($storageUsed / 1024 / 1024 / 1024, 2)); ?> GB</h2>
                        <p class="mb-4 text-muted">of 100 GB Used</p>
                        <div class="iq-progress-bar mb-3">
                            <?php ($percent = min(($storageUsed / 1024 / 1024 / 1024) / 100 * 100, 100)); ?>
                            <span class="bg-primary iq-progress progress-1 admin-storage-bar" data-percent="<?php echo e(number_format($percent, 2)); ?>" style="width: 0%; transition: width 1s ease;"></span>
                        </div>
                        <p class="mb-0">~ <?php echo e(number_format(100 - ($storageUsed / 1024 / 1024 / 1024), 2)); ?> GB Free</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Type Breakdown Chart -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Files by Type</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div id="admin-file-type-chart" style="min-height: 260px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Quick Links</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="<?php echo e(route('cloudbox.user.list')); ?>" class="btn btn-outline-primary btn-block"><i class="las la-th-list mr-2"></i>Manage Users</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="<?php echo e(route('cloudbox.files')); ?>" class="btn btn-outline-secondary btn-block"><i class="las la-file mr-2"></i>All Files</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="<?php echo e(route('cloudbox.folders.index')); ?>" class="btn btn-outline-info btn-block"><i class="las la-folder mr-2"></i>All Folders</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="<?php echo e(route('cloudbox.shared')); ?>" class="btn btn-outline-success btn-block"><i class="las la-share-alt mr-2"></i>Shared</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const bar = document.querySelector('.admin-storage-bar');
        if (bar) {
            const percent = parseFloat(bar.getAttribute('data-percent')) || 0;
            bar.style.width = percent + '%';
        }
    }, 100);

    // Render file type chart (requires ApexCharts via template bundle)
    try {
        if (window.ApexCharts) {
            const options = {
                chart: { type: 'donut', height: 260 },
                labels: ['Images', 'Videos', 'Audio', 'PDF', 'Docs', 'Sheets', 'Others'],
                series: [
                    <?php echo e($byType['images'] ?? 0); ?>,
                    <?php echo e($byType['videos'] ?? 0); ?>,
                    <?php echo e($byType['audio'] ?? 0); ?>,
                    <?php echo e($byType['pdf'] ?? 0); ?>,
                    <?php echo e($byType['docs'] ?? 0); ?>,
                    <?php echo e($byType['sheets'] ?? 0); ?>,
                    <?php echo e($byType['others'] ?? 0); ?>

                ],
                dataLabels: { enabled: false },
                legend: { position: 'bottom' },
            };
            const el = document.querySelector('#admin-file-type-chart');
            if (el) {
                const chart = new window.ApexCharts(el, options);
                chart.render();
            }
        }
    } catch (e) { console.warn('Chart init failed', e); }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/pages/admin/dashboard.blade.php ENDPATH**/ ?>