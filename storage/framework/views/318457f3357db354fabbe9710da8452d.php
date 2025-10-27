<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo $__env->yieldContent('title', 'CloudBOX - File Management'); ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.ico')); ?>" />
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/backend-bundle.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/backend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dark-mode.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/profile-icon.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/remixicon/fonts/remixicon.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/doc-viewer/include/plugin/viewer.css')); ?>">
    
    <style>
        .bg-primary-light {
            background-color: rgba(85, 110, 230, 0.1) !important;
        }
        .bg-success-light {
            background-color: rgba(40, 199, 111, 0.1) !important;
        }
        .bg-danger-light {
            background-color: rgba(235, 87, 87, 0.1) !important;
        }
        .bg-warning-light {
            background-color: rgba(242, 153, 74, 0.1) !important;
        }
        .bg-info-light {
            background-color: rgba(23, 162, 184, 0.1) !important;
        }
        .icon-small {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .iq-search-suggestion li {
            padding: 8px 0;
        }
        .iq-search-suggestion li a {
            color: inherit;
            text-decoration: none;
        }
        .iq-search-suggestion li a:hover {
            color: #556ee6;
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="sidebar-main-menu">
    <!-- Loading -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    
    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Sidebar -->
        <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Top Nav -->
        <?php echo $__env->make('partials.topnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Page Content -->
        <div class="content-page">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    <!-- Wrapper End -->
    
    <!-- Footer -->
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Global Modals (accessible from sidebar) -->
    <?php echo $__env->make('partials.create-folder-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.upload-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.upload-folder-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- JavaScript Files -->
    <script src="<?php echo e(asset('assets/js/backend-bundle.min.js')); ?>"></script>
    
    <!-- Customizer - Dark Mode Handler -->
    <script src="<?php echo e(asset('assets/js/customizer.js')); ?>"></script>
    
    <!-- Chart Custom JavaScript -->
    <script src="<?php echo e(asset('assets/js/chart-custom.js')); ?>"></script>
    
    <!-- App JavaScript -->
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
    
    <script src="<?php echo e(asset('assets/vendor/doc-viewer/include/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/doc-viewer/include/plugin/viewer.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\cloudbox-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>