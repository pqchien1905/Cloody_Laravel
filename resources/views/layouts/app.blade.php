<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'CloudBOX - File Management')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/backend-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/profile-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/doc-viewer/include/plugin/viewer.css') }}">
    
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
    
    @stack('styles')
</head>

<body class="sidebar-main-menu">
    <!-- Loading -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    
    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')
        
        <!-- Top Nav -->
        @include('partials.topnav')
        
        <!-- Page Content -->
        <div class="content-page">
            @yield('content')
        </div>
    </div>
    <!-- Wrapper End -->
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- Global Modals (accessible from sidebar) -->
    @include('partials.create-folder-modal')
    @include('partials.upload-modal')
    @include('partials.upload-folder-modal')
    
    <!-- JavaScript Files -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>
    
    <!-- Customizer - Dark Mode Handler -->
    <script src="{{ asset('assets/js/customizer.js') }}"></script>
    
    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
    
    <!-- App JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    <script src="{{ asset('assets/vendor/doc-viewer/include/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/doc-viewer/include/plugin/viewer.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
