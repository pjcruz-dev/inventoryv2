<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#007bff">
    
    <title>@yield('title', 'Inventory Management') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Mobile Styles -->
    <style>
        :root {
            --mobile-primary: #007bff;
            --mobile-secondary: #6c757d;
            --mobile-success: #28a745;
            --mobile-danger: #dc3545;
            --mobile-warning: #ffc107;
            --mobile-info: #17a2b8;
            --mobile-light: #f8f9fa;
            --mobile-dark: #343a40;
            --mobile-border-radius: 12px;
            --mobile-shadow: 0 2px 10px rgba(0,0,0,0.1);
            --mobile-touch-target: 44px;
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        input, textarea, select {
            -webkit-user-select: text;
            -khtml-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-bottom: 80px; /* Space for bottom navigation */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Mobile Navigation */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 8px 0;
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 12px;
            text-decoration: none;
            color: #6c757d;
            transition: all 0.2s ease;
            min-height: var(--mobile-touch-target);
            justify-content: center;
        }
        
        .mobile-nav-item.active {
            color: var(--mobile-primary);
        }
        
        .mobile-nav-item i {
            font-size: 20px;
            margin-bottom: 4px;
        }
        
        .mobile-nav-item span {
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Mobile Header */
        .mobile-header {
            background: linear-gradient(135deg, var(--mobile-primary) 0%, #0056b3 100%);
            color: white;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--mobile-shadow);
        }
        
        .mobile-header h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .mobile-header .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 4px;
        }
        
        /* Mobile Cards */
        .mobile-card {
            background: white;
            border-radius: var(--mobile-border-radius);
            box-shadow: var(--mobile-shadow);
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        .mobile-card:active {
            transform: scale(0.98);
        }
        
        .mobile-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }
        
        .mobile-card-body {
            padding: 20px;
        }
        
        .mobile-card-footer {
            padding: 16px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        /* Mobile Buttons */
        .btn-mobile {
            min-height: var(--mobile-touch-target);
            padding: 12px 20px;
            font-weight: 500;
            border-radius: var(--mobile-border-radius);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-mobile:active {
            transform: scale(0.98);
        }
        
        .btn-mobile-primary {
            background: var(--mobile-primary);
            border: none;
            color: white;
        }
        
        .btn-mobile-primary:hover {
            background: #0056b3;
            color: white;
        }
        
        /* Mobile Forms */
        .form-control-mobile {
            min-height: var(--mobile-touch-target);
            border-radius: var(--mobile-border-radius);
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 16px; /* Prevents zoom on iOS */
            transition: all 0.2s ease;
        }
        
        .form-control-mobile:focus {
            border-color: var(--mobile-primary);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        /* Mobile Lists */
        .mobile-list {
            background: white;
            border-radius: var(--mobile-border-radius);
            box-shadow: var(--mobile-shadow);
            overflow: hidden;
        }
        
        .mobile-list-item {
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s ease;
            min-height: var(--mobile-touch-target);
        }
        
        .mobile-list-item:last-child {
            border-bottom: none;
        }
        
        .mobile-list-item:active {
            background-color: #f8f9fa;
        }
        
        /* Mobile Modals */
        .modal-mobile {
            margin: 0;
            max-width: 100%;
        }
        
        .modal-mobile .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }
        
        .modal-mobile .modal-content {
            border: none;
            border-radius: 0;
            height: 100vh;
        }
        
        /* Mobile Search */
        .mobile-search {
            position: sticky;
            top: 0;
            z-index: 998;
            background: white;
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .mobile-search .input-group {
            position: relative;
        }
        
        .mobile-search .form-control {
            border-radius: 25px;
            padding: 12px 20px 12px 45px;
            border: 2px solid #e9ecef;
            font-size: 16px;
        }
        
        .mobile-search .input-group-text {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            z-index: 3;
            color: #6c757d;
        }
        
        /* Mobile Pagination */
        .mobile-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 20px;
        }
        
        .mobile-pagination .page-link {
            min-width: var(--mobile-touch-target);
            min-height: var(--mobile-touch-target);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid #e9ecef;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .mobile-pagination .page-link:hover {
            background: var(--mobile-primary);
            color: white;
            border-color: var(--mobile-primary);
        }
        
        .mobile-pagination .page-item.active .page-link {
            background: var(--mobile-primary);
            color: white;
            border-color: var(--mobile-primary);
        }
        
        /* Mobile Loading States */
        .mobile-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .mobile-spinner {
            width: 24px;
            height: 24px;
            border: 2px solid #e9ecef;
            border-top: 2px solid var(--mobile-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 12px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Mobile Empty States */
        .mobile-empty {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .mobile-empty i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .mobile-empty h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        
        .mobile-empty p {
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        /* Dark Mode Mobile */
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #e9ecef;
        }
        
        [data-theme="dark"] .mobile-nav {
            background: rgba(45, 45, 45, 0.95);
            border-top-color: #404040;
        }
        
        [data-theme="dark"] .mobile-card {
            background: #2d2d2d;
            color: #e9ecef;
        }
        
        [data-theme="dark"] .mobile-card-header,
        [data-theme="dark"] .mobile-card-footer {
            background: #343a40;
            border-color: #404040;
        }
        
        [data-theme="dark"] .mobile-list {
            background: #2d2d2d;
        }
        
        [data-theme="dark"] .mobile-list-item {
            border-color: #404040;
            color: #e9ecef;
        }
        
        [data-theme="dark"] .mobile-search {
            background: #2d2d2d;
            border-color: #404040;
        }
        
        [data-theme="dark"] .form-control-mobile {
            background: #343a40;
            border-color: #404040;
            color: #e9ecef;
        }
        
        [data-theme="dark"] .form-control-mobile:focus {
            background: #343a40;
            border-color: var(--mobile-primary);
            color: #e9ecef;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .mobile-header {
                padding: 12px 16px;
            }
            
            .mobile-header h1 {
                font-size: 18px;
            }
            
            .mobile-card-body {
                padding: 16px;
            }
            
            .btn-mobile {
                padding: 10px 16px;
            }
        }
        
        /* Landscape orientation */
        @media (orientation: landscape) and (max-height: 500px) {
            .mobile-nav {
                display: none;
            }
            
            body {
                padding-bottom: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>

<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1>@yield('page-title', 'Inventory Management')</h1>
                @hasSection('page-subtitle')
                    <div class="subtitle">@yield('page-subtitle')</div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                @yield('page-actions')
                <!-- Theme Toggle -->
                <button class="btn btn-outline-light btn-sm" id="mobileThemeToggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Content -->
    <main class="container-fluid px-0">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
        <div class="row g-0">
            <div class="col">
                <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('assets.mobile') }}" class="mobile-nav-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <i class="fas fa-desktop"></i>
                    <span>Assets</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('users.index') }}" class="mobile-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('qr-scanner.mobile') }}" class="mobile-nav-item {{ request()->routeIs('qr-scanner.*') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>Scanner</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('search.index') }}" class="mobile-nav-item {{ request()->routeIs('search.*') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Theme Toggle -->
    <script>
        // Mobile theme toggle
        document.getElementById('mobileThemeToggle').addEventListener('click', function() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('inventory-theme', newTheme);
            
            // Update icon
            const icon = this.querySelector('i');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
        
        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('inventory-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const icon = document.getElementById('mobileThemeToggle').querySelector('i');
            icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
    </script>
    
    @stack('scripts')
</body>
</html>
