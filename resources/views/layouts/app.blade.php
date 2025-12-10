<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="ngrok-skip-browser-warning" content="true">
    
    <title>@yield('title', 'Cloody - File Management')</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/cloud.png') }}" type="image/png" />
    <link rel="icon" href="{{ asset('assets/images/cloud.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('assets/images/cloud.png') }}" />
    
    <!-- Vietnamese-friendly Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
    
    <style>
        /* Global Vietnamese-friendly font with Be Vietnam Pro */
        :root {
            --font-family-base: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-family-fallback: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        html, body {
            font-family: var(--font-family-base);
            font-feature-settings: 'kern' 1, 'liga' 1, 'calt' 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        
        /* Apply Inter to all text elements */
        body, button, input, textarea, select, .card, .modal, .dropdown-menu, 
        .nav, .navbar, .sidebar, .table, .form-control, .btn, 
        h1, h2, h3, h4, h5, h6, p, span, a, li, label {
            font-family: var(--font-family-base);
        }
        
        /* Optimize line height for Vietnamese text */
        body {
            line-height: 1.6;
            letter-spacing: -0.011em;
        }
        
        /* Headings optimization */
        h1, h2, h3, h4, h5, h6, .card-title, .modal-title {
            font-weight: 600;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }
        
        h1 { font-size: 2.25rem; font-weight: 700; }
        h2 { font-size: 1.875rem; font-weight: 700; }
        h3 { font-size: 1.5rem; }
        h4 { font-size: 1.25rem; }
        h5 { font-size: 1.125rem; }
        h6 { font-size: 1rem; }
        
        /* Paragraphs and text */
        p, .text-muted, small {
            line-height: 1.65;
        }
        
        /* Better Vietnamese diacritics spacing */
        .card-body, .modal-body, .table td, .table th {
            line-height: 1.6;
        }
        
        /* Form elements */
        .form-control, .form-control::placeholder {
            font-size: 0.9375rem;
            line-height: 1.5;
        }
        
        /* Buttons */
        .btn {
            font-weight: 500;
            letter-spacing: 0.025em;
        }
        
        /* Tables */
        .table {
            font-size: 0.9375rem;
        }
        
        .table th {
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        
        /* Navigation */
        .navbar, .sidebar {
            font-size: 0.9375rem;
        }
        
        .nav-link {
            font-weight: 500;
        }
        
        /* Badges and labels */
        .badge {
            font-weight: 500;
            letter-spacing: 0.025em;
        }
        
        /* Dropdowns */
        .dropdown-item {
            font-size: 0.9375rem;
            line-height: 1.5;
        }
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
        
        /* ===== Sidebar Scroll Fix ===== */
        .iq-sidebar {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        
        .iq-sidebar .iq-sidebar-logo {
            flex-shrink: 0;
        }
        
        .iq-sidebar .data-scrollbar {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
            min-height: 0;
            height: calc(100vh - 70px);
        }
        
        .iq-sidebar .sidebar-top-section {
            flex-shrink: 0;
            padding: 15px;
        }
        
        /* ===== Create New Button Styling ===== */
        .iq-sidebar .new-create {
            padding-top: 0;
            border-top: none;
            margin: 0;
            width: 100%;
        }
        
        .iq-sidebar .new-create.select-dropdown {
            width: 100%;
            font-size: 14px;
        }
        
        .iq-sidebar .new-create .btn-group {
            width: 100%;
        }
        
        .iq-sidebar .new-create .btn-group > div[data-toggle="dropdown"] {
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .iq-sidebar .new-create .selet-caption {
            width: 100%;
            display: block;
            padding: 12px 16px;
            text-align: center;
            font-weight: 500;
            font-size: 14px;
            color: #556ee6;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(85, 110, 230, 0.1) 0%, rgba(85, 110, 230, 0.05) 100%);
            border: 1px solid rgba(85, 110, 230, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .iq-sidebar .new-create .selet-caption:hover {
            background: linear-gradient(135deg, rgba(85, 110, 230, 0.15) 0%, rgba(85, 110, 230, 0.1) 100%);
            border-color: rgba(85, 110, 230, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(85, 110, 230, 0.15);
        }
        
        .iq-sidebar .new-create .selet-caption i {
            font-size: 16px;
        }
        
        .iq-sidebar .new-create .btn-group .dropdown-menu {
            width: 100%;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 4px 0;
        }
        
        .iq-sidebar .new-create .btn-group .dropdown-menu li .item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f1f1;
            color: #535f6b;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .iq-sidebar .new-create .btn-group .dropdown-menu li:last-child .item {
            border-bottom: none;
        }
        
        .iq-sidebar .new-create .btn-group .dropdown-menu li .item:hover {
            background-color: #f8f9fa;
            color: #556ee6;
            padding-left: 20px;
        }
        
        .iq-sidebar .new-create .btn-group .dropdown-menu li .item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        
        .iq-sidebar .new-create .caret {
            display: none;
        }
        
        .iq-sidebar .iq-sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            min-height: 0;
            padding: 0 15px;
        }
        
        .iq-sidebar .sidebar-bottom {
            flex-shrink: 0;
            margin-top: auto;
            padding: 15px;
            background: #fff;
            border-top: 1px solid #e5e5e5;
            z-index: 10;
        }
        
        .iq-sidebar .sidebar-bottom .btn {
            width: 100%;
            white-space: nowrap;
        }
        
        /* ===== Logo Styling Improvements ===== */
        /* Sidebar Logo */
        .iq-sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        
        .iq-sidebar-logo .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            width: 100%;
            outline: none !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .iq-sidebar-logo .header-logo:hover,
        .iq-sidebar-logo .header-logo:focus,
        .iq-sidebar-logo .header-logo:active {
            transform: translateY(-2px);
            box-shadow: none !important;
            background: transparent;
            outline: none !important;
            border: none !important;
        }
        
        .iq-sidebar-logo .header-logo img {
            height: auto;
            max-height: 50px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
            background: transparent;
        }
        
        .iq-sidebar-logo .header-logo:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.2));
        }
        
        /* Top Navbar Logo */
        .iq-navbar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .iq-navbar-logo .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            outline: none !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .iq-navbar-logo .header-logo:hover,
        .iq-navbar-logo .header-logo:focus,
        .iq-navbar-logo .header-logo:active {
            transform: translateY(-1px);
            background: transparent;
            outline: none !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .iq-navbar-logo .header-logo img {
            height: auto;
            max-height: 45px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
            background: transparent;
        }
        
        .iq-navbar-logo .header-logo:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 8px rgba(102, 126, 234, 0.2));
        }
        
        /* Auth Pages Logo (Login, Register) */
        .sign-user_card .logo,
        .sign-user_card img.logo {
            max-width: 220px !important;
            width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 4px 12px rgba(102, 126, 234, 0.15));
            animation: logoFadeIn 0.6s ease-out;
        }
        
        .sign-user_card .logo:hover,
        .sign-user_card img.logo:hover {
            transform: scale(1.05) translateY(-3px);
            filter: drop-shadow(0 8px 20px rgba(102, 126, 234, 0.25));
        }
        
        /* Auth Pages with Light/Dark Logo */
        .sign-user_card .light-logo,
        .sign-user_card .darkmode-logo {
            max-width: 200px !important;
            width: 100%;
            height: auto;
            margin: 0 auto 15px;
            display: block;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 4px 12px rgba(102, 126, 234, 0.15));
            animation: logoFadeIn 0.6s ease-out;
        }
        
        .sign-user_card .darkmode-logo {
            display: none;
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .sign-user_card .light-logo {
                display: none;
            }
            .sign-user_card .darkmode-logo {
                display: block;
            }
        }
        
        /* Application Logo Component */
        x-application-logo img,
        [x-application-logo] img,
        .application-logo {
            max-height: 100%;
            max-width: 100%;
            height: auto;
            width: auto;
            object-fit: contain;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.1));
        }
        
        /* Logo Animation */
        @keyframes logoFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes logoPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }
        
        /* Responsive Logo Sizing */
        @media (max-width: 768px) {
            .iq-sidebar-logo .header-logo img {
                max-height: 40px;
                max-width: 140px;
            }
            
            .iq-navbar-logo .header-logo img {
                max-height: 38px;
                max-width: 130px;
            }
            
            .sign-user_card .logo,
            .sign-user_card img.logo {
                max-width: 180px !important;
            }
            
            .sign-user_card .light-logo,
            .sign-user_card .darkmode-logo {
                max-width: 160px !important;
            }
        }
        
        @media (max-width: 576px) {
            .iq-sidebar-logo .header-logo img {
                max-height: 35px;
                max-width: 120px;
            }
            
            .iq-navbar-logo .header-logo img {
                max-height: 32px;
                max-width: 110px;
            }
            
            .sign-user_card .logo,
            .sign-user_card img.logo {
                max-width: 150px !important;
            }
        }
        
        /* Logo Container Improvements */
        .iq-sidebar-logo {
            padding: 15px 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .iq-navbar-logo {
            padding: 8px 12px;
        }
        
        /* Smooth loading effect */
        .header-logo img {
            opacity: 0;
            animation: logoFadeIn 0.5s ease-out 0.2s forwards;
        }
        
        /* Focus state for accessibility */
        .header-logo:focus {
            outline: 2px solid rgba(102, 126, 234, 0.5);
            outline-offset: 4px;
            border-radius: 12px;
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
    
    <!-- Error Fix Scripts (Load FIRST to prevent errors) -->
    <script>
    // Fix timer variable error - MUST run before any other scripts
    // This fixes: "Uncaught ReferenceError: timer is not defined"
    (function() {
        'use strict';
        
        // Define timer variable globally IMMEDIATELY to prevent errors
            window.timer = null;
        
        // Wrap updateTime after scripts load to ensure timer exists
        // This will run after DOMContentLoaded
        function wrapUpdateTime() {
            if (typeof window.updateTime === 'function') {
                var originalUpdateTime = window.updateTime;
            window.updateTime = function() {
                try {
                        // Ensure timer exists before calling original function
                        if (typeof window.timer === 'undefined') {
                            window.timer = null;
                    }
                    return originalUpdateTime.apply(this, arguments);
                } catch (e) {
                        // If timer is not defined in the function's scope, suppress error
                        if (e.message && e.message.includes('timer is not defined')) {
                            console.info('ℹ️ timer error suppressed in updateTime');
                            return;
                        }
                        throw e; // Re-throw if it's a different error
                    }
                };
            }
        }
        
        // Try to wrap immediately
        wrapUpdateTime();
        
        // Also wrap after DOM is ready (in case custom.min.js loads later)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', wrapUpdateTime);
        } else {
            setTimeout(wrapUpdateTime, 0);
        }
        
        // Wrap again after a short delay to catch late-loading scripts
        setTimeout(wrapUpdateTime, 100);
        setTimeout(wrapUpdateTime, 500);
        
    })();
    </script>
    <script src="{{ asset('assets/js/share-modal-fix.js') }}?v={{ time() }}"></script>
    
    <!-- JavaScript Files -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>
    
    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
    
    <!-- App JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    <!-- Load empty files with cache busting (share-modal-fix.js already handles protection) -->
    <script src="{{ asset('assets/js/share-modal.js') }}?v={{ time() }}&nocache={{ uniqid() }}"></script>
    <script src="{{ asset('assets/js/reload.js') }}?v={{ time() }}&nocache={{ uniqid() }}"></script>
    
    <!-- AI Chat Widget -->
    @include('components.ai-chat-widget')
    <script src="{{ asset('assets/js/ai-chat.js') }}?v={{ time() }}"></script>
    
    @stack('scripts')
</body>
</html>
