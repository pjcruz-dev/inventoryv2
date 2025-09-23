<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Inventory Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap');
        
        :root {
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;
            
            --text-xs: 0.75rem;
            --text-sm: 0.875rem;
            --text-base: 1rem;
            --text-lg: 1.125rem;
            --text-xl: 1.25rem;
            --text-2xl: 1.5rem;
            --text-3xl: 1.875rem;
            --text-4xl: 2.25rem;
        }
        
        body {
            background: linear-gradient(310deg, #f8f9fa 0%, #dee2e6 100%);
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            line-height: 1.6;
            font-size: var(--text-base);
            color: #344767;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            color: #344767;
            margin-bottom: var(--spacing-md);
            line-height: 1.2;
        }
        
        h1 { font-size: var(--text-4xl); margin-bottom: var(--spacing-xl); }
        h2 { font-size: var(--text-3xl); margin-bottom: var(--spacing-lg); }
        h3 { font-size: var(--text-2xl); margin-bottom: var(--spacing-lg); }
        h4 { font-size: var(--text-xl); margin-bottom: var(--spacing-md); }
        h5 { font-size: var(--text-lg); margin-bottom: var(--spacing-md); }
        h6 { font-size: var(--text-base); margin-bottom: var(--spacing-sm); }
        
        .card-title {
            font-weight: 600;
            color: #344767;
            font-size: var(--text-lg);
            margin-bottom: var(--spacing-sm);
        }
        
        .text-muted {
            color: #67748e !important;
            font-size: var(--text-sm);
        }
        
        .text-xs { font-size: var(--text-xs); }
        .text-sm { font-size: var(--text-sm); }
        .text-lg { font-size: var(--text-lg); }
        .text-xl { font-size: var(--text-xl); }
        .text-2xl { font-size: var(--text-2xl); }
        
        .mb-xs { margin-bottom: var(--spacing-xs); }
        .mb-sm { margin-bottom: var(--spacing-sm); }
        .mb-md { margin-bottom: var(--spacing-md); }
        .mb-lg { margin-bottom: var(--spacing-lg); }
        .mb-xl { margin-bottom: var(--spacing-xl); }
        
        .p-xs { padding: var(--spacing-xs); }
        .p-sm { padding: var(--spacing-sm); }
        .p-md { padding: var(--spacing-md); }
        .p-lg { padding: var(--spacing-lg); }
        .p-xl { padding: var(--spacing-xl); }
        
        /* Enhanced Soft UI Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 14px 20px;
            margin: 4px 12px;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            align-items: center;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.08) 100%);
            transform: translateX(8px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.15) 100%);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.3);
            transform: translateX(6px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: linear-gradient(to bottom, #fff, rgba(255,255,255,0.7));
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .sidebar .nav-link:hover i {
            background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.15) 100%);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .sidebar .nav-link.active i {
            background: linear-gradient(135deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.25) 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .sidebar-heading {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.6) !important;
            margin: 24px 0 8px 0 !important;
            padding: 0 20px !important;
            position: relative;
        }
        
        .sidebar-heading::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 20px;
            right: 20px;
            height: 1px;
            background: linear-gradient(to right, rgba(255,255,255,0.3), transparent);
        }
        
        /* Mobile Responsive Sidebar */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1040;
                transition: left 0.3s ease-in-out;
                overflow-y: auto;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
                padding-top: 60px;
            }
            
            /* Backdrop for mobile sidebar */
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1039;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            }
            
            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }
            
            #sidebarMenu.show {
                transform: translateX(0);
            }
            
            body.sidebar-open {
                overflow: hidden;
            }
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        /* Dark Mode Styles */
        [data-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #e9ecef;
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #343a40;
            --bs-dark: #212529;
        }

        [data-theme="dark"] body {
            background: linear-gradient(310deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #e9ecef;
        }

        [data-theme="dark"] .card {
            background: #2d2d2d;
            border: 1px solid #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .card-header {
            background: #343a40;
            border-bottom: 1px solid #404040;
        }

        [data-theme="dark"] .table {
            color: #e9ecef;
        }

        [data-theme="dark"] .table-dark {
            background-color: #343a40;
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.075);
        }

        [data-theme="dark"] .form-control {
            background-color: #2d2d2d;
            border-color: #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .form-control:focus {
            background-color: #2d2d2d;
            border-color: #0d6efd;
            color: #e9ecef;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        [data-theme="dark"] .form-select {
            background-color: #2d2d2d;
            border-color: #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .form-select:focus {
            background-color: #2d2d2d;
            border-color: #0d6efd;
            color: #e9ecef;
        }

        [data-theme="dark"] .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }

        [data-theme="dark"] .btn-outline-primary:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        [data-theme="dark"] .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        [data-theme="dark"] .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        [data-theme="dark"] .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: #198754;
            color: #75b798;
        }

        [data-theme="dark"] .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #f1aeb5;
        }

        [data-theme="dark"] .alert-info {
            background-color: rgba(13, 202, 240, 0.1);
            border-color: #0dcaf0;
            color: #6edff6;
        }

        [data-theme="dark"] .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: #ffc107;
            color: #ffda6a;
        }

        [data-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        [data-theme="dark"] .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        [data-theme="dark"] .bg-light {
            background-color: #343a40 !important;
        }

        [data-theme="dark"] .bg-white {
            background-color: #2d2d2d !important;
        }

        [data-theme="dark"] .border {
            border-color: #404040 !important;
        }

        [data-theme="dark"] .border-bottom {
            border-bottom-color: #404040 !important;
        }

        [data-theme="dark"] .border-top {
            border-top-color: #404040 !important;
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-theme="dark"] .dropdown-item {
            color: #e9ecef;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .modal-content {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        [data-theme="dark"] .modal-header {
            border-bottom-color: #404040;
        }

        [data-theme="dark"] .modal-footer {
            border-top-color: #404040;
        }

        [data-theme="dark"] .pagination .page-link {
            background-color: #2d2d2d;
            border-color: #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .pagination .page-link:hover {
            background-color: #404040;
            border-color: #404040;
            color: #e9ecef;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Dark Mode Sidebar */
        [data-theme="dark"] .sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        [data-theme="dark"] .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
        }

        [data-theme="dark"] .sidebar .nav-link:hover {
            color: #fff;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        }

        [data-theme="dark"] .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
        }

        [data-theme="dark"] .sidebar-heading {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        /* Dark Mode Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .theme-toggle svg {
            width: 20px;
            height: 20px;
        }

        .theme-toggle .moon-icon {
            display: block;
        }

        .theme-toggle .sun-icon {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .moon-icon {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .sun-icon {
            display: block;
        }
        .card {
            border: none;
            border-radius: 1.5rem;
            background: #f8f9fa;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 
                12px 12px 20px rgba(163, 177, 198, 0.7),
                -12px -12px 20px rgba(255, 255, 255, 0.6),
                0 8px 32px rgba(31, 38, 135, 0.15);
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1.5rem;
            background: linear-gradient(145deg, rgba(255,255,255,0.1), rgba(0,0,0,0.05));
            pointer-events: none;
        }
        .btn-primary {
            background: linear-gradient(310deg, #cb0c9f 0%, #ad1457 100%);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 
                0 4px 7px -1px rgba(203, 12, 159, 0.11),
                0 2px 4px -1px rgba(203, 12, 159, 0.07);
            transition: all 0.15s ease-in;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            background: linear-gradient(310deg, #ad1457 0%, #880e4f 100%);
            transform: translateY(-1px);
            box-shadow: 
                0 7px 14px -3px rgba(203, 12, 159, 0.15),
                0 4px 6px -2px rgba(203, 12, 159, 0.1);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                inset 0 3px 5px rgba(0,0,0,0.125),
                0 2px 4px rgba(203, 12, 159, 0.1);
        }
        
        .btn {
            border-radius: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            text-transform: none;
            transition: all 0.15s ease-in;
        }
        
        .btn-outline-primary {
            border: 1.5px solid #cb0c9f;
            color: #cb0c9f;
            background: transparent;
            box-shadow: none;
        }
        
        .btn-outline-primary:hover {
            background: #cb0c9f;
            border-color: #cb0c9f;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(203, 12, 159, 0.2);
        }
        
        /* Enhanced Badge Styling for Better Visibility */
        .badge-enhanced {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid;
            position: relative;
            z-index: 1;
        }
        
        /* High Contrast Badge Variants */
        .badge-enhanced.bg-success {
            background-color: rgba(25, 135, 84, 0.25) !important;
            color: #0d5132 !important;
            border-color: rgba(25, 135, 84, 0.5) !important;
        }
        
        .badge-enhanced.bg-danger {
            background-color: rgba(220, 53, 69, 0.25) !important;
            color: #842029 !important;
            border-color: rgba(220, 53, 69, 0.5) !important;
        }
        
        .badge-enhanced.bg-warning {
            background-color: rgba(255, 193, 7, 0.25) !important;
            color: #664d03 !important;
            border-color: rgba(255, 193, 7, 0.5) !important;
        }
        
        .badge-enhanced.bg-info {
            background-color: rgba(13, 202, 240, 0.25) !important;
            color: #055160 !important;
            border-color: rgba(13, 202, 240, 0.5) !important;
        }
        
        .badge-enhanced.bg-primary {
            background-color: rgba(13, 110, 253, 0.25) !important;
            color: #052c65 !important;
            border-color: rgba(13, 110, 253, 0.5) !important;
        }
        
        .badge-enhanced.bg-secondary {
            background-color: rgba(108, 117, 125, 0.25) !important;
            color: #41464b !important;
            border-color: rgba(108, 117, 125, 0.5) !important;
        }
        
        .badge-enhanced.bg-dark {
            background-color: rgba(33, 37, 41, 0.25) !important;
            color: #000 !important;
            border-color: rgba(33, 37, 41, 0.5) !important;
        }
        
        /* Override low opacity badges for better visibility */
        .badge.bg-opacity-10,
        .badge.bg-opacity-15 {
            background-color: rgba(var(--bs-bg-opacity-rgb), 0.25) !important;
            font-weight: 600 !important;
        }
        
        /* Ensure text contrast for all badge variants */
        .badge.text-success { color: #0d5132 !important; }
        .badge.text-danger { color: #842029 !important; }
        .badge.text-warning { color: #664d03 !important; }
        .badge.text-info { color: #055160 !important; }
        .badge.text-primary { color: #052c65 !important; }
        .badge.text-secondary { color: #41464b !important; }
        .badge.text-dark { color: #000 !important; }
        
        /* Standardized pagination styles */
        .pagination-wrapper {
            margin-top: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid #dee2e6;
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 0;
        }
        
        .pagination .page-link {
            color: #0d6efd;
            border-color: #dee2e6;
            padding: 0.5rem 0.75rem;
        }
        
        .pagination .page-link:hover {
            color: #0a58ca;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        /* Basic Dashboard Styles */
        .dashboard-container {
            padding: 0 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            margin-bottom: 2rem;
        }
        
        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .metric-card.primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .metric-card.success::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        
        .metric-card.info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }
        
        .metric-card.warning::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .metric-card.primary .metric-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .metric-card.success .metric-icon {
            background: linear-gradient(135deg, #10b981, #34d399);
        }
        
        .metric-card.info .metric-icon {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
        }
        
        .metric-card.warning .metric-icon {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }
        
        .metric-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }
        
        .metric-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .metric-change.positive {
            color: #10b981;
        }
        
        .metric-change.neutral {
            color: #6b7280;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: #fafafa;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            color: #667eea;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .empty-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .empty-subtext {
            color: #9ca3af;
        }
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- Chart.js CDN for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @guest
        <!-- Guest layout (login/register pages) -->
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <div class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    </div>
                </div>
            </nav>
            <main class="py-4">
                @yield('content')
            </main>
        </div>
    @else
        <!-- Authenticated layout with sidebar -->
        <div class="container-fluid">
            <div class="row">
                <!-- Mobile Navigation Toggle -->
                <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation" style="position: fixed; top: 10px; left: 10px; z-index: 1050; background: rgba(102, 126, 234, 0.9); border: none; border-radius: 8px; padding: 8px 12px;">
                    <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 30 30%27%3e%3cpath stroke=%27rgba%28255, 255, 255, 1%29%27 stroke-linecap=%27round%27 stroke-miterlimit=%2710%27 stroke-width=%272%27 d=%27M4 7h22M4 15h22M4 23h22%27/%3e%3c/svg%3e'); width: 20px; height: 20px;"></span>
                </button>

                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                    <div class="position-sticky pt-3">
                        <div class="text-center mb-4">
                            <h4 class="navbar-brand">{{ config('app.name', 'Inventory') }}</h4>
                            <small class="text-white-50">Management System</small>
                        </div>
                        
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            
                            @if(auth()->user()->can('view_assets'))
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>ASSET MANAGEMENT</span>
                                </h6>
                            </li>
                            
                            @can('view_assets')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                                    <i class="fas fa-boxes"></i>
                                    All Assets
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_computers')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('computers.*') ? 'active' : '' }}" href="{{ route('computers.index') }}">
                                    <i class="fas fa-desktop"></i>
                                    Computers
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_monitors')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('monitors.*') ? 'active' : '' }}" href="{{ route('monitors.index') }}">
                                    <i class="fas fa-tv"></i>
                                    Monitors
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_printers')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('printers.*') ? 'active' : '' }}" href="{{ route('printers.index') }}">
                                    <i class="fas fa-print"></i>
                                    Printers
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_peripherals')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('peripherals.*') ? 'active' : '' }}" href="{{ route('peripherals.index') }}">
                                    <i class="fas fa-mouse"></i>
                                    Peripherals
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_asset_categories')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('asset-categories.*') ? 'active' : '' }}" href="{{ route('asset-categories.index') }}">
                                    <i class="fas fa-tags"></i>
                                    Asset Categories
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_asset_assignments')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('asset-assignments.*') ? 'active' : '' }}" href="{{ route('asset-assignments.index') }}">
                                    <i class="fas fa-user-check"></i>
                                    Asset Assignments
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_assignment_confirmations')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('asset-assignment-confirmations.*') ? 'active' : '' }}" href="{{ route('asset-assignment-confirmations.index') }}">
                                    <i class="fas fa-clipboard-check"></i>
                                    Assignment Confirmations
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_accountability_forms')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('accountability.*') ? 'active' : '' }}" href="{{ route('accountability.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    Accountability Forms
                                </a>
                            </li>
                            @endcan
                            @endif
                            
                            @if(auth()->user()->can('view_maintenance') || auth()->user()->can('view_disposal'))
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>ASSET LIFECYCLE</span>
                                </h6>
                            </li>
                            @endif
                            
                            @can('view_maintenance')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                                    <i class="fas fa-tools"></i>
                                    Maintenance
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_disposal')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('disposal.*') ? 'active' : '' }}" href="{{ route('disposal.index') }}">
                                    <i class="fas fa-trash-alt"></i>
                                    Disposal
                                </a>
                            </li>
                            @endcan
                            
                            @if(auth()->user()->can('view_users') || auth()->user()->can('view_assets'))
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>ORGANIZATION</span>
                                </h6>
                            </li>
                            @endif
                            
                            @can('view_users')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i>
                                    Users
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_departments')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                    <i class="fas fa-building"></i>
                                    Departments
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_vendors')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                                    <i class="fas fa-truck"></i>
                                    Vendors
                                </a>
                            </li>
                            @endcan
                            
                            @if(auth()->user()->can('view_logs') || auth()->user()->can('view_assets') || auth()->user()->can('view_roles') || auth()->user()->can('view_permissions'))
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>SYSTEM</span>
                                </h6>
                            </li>
                            @endif
                            
                            @can('view_logs')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="{{ route('logs.index') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    Activity Logs
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_assets')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.interface') }}">
                                    <i class="fas fa-file-import"></i>
                                    Import/Export
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_assets')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}" href="{{ route('timeline.index') }}">
                                    <i class="fas fa-history"></i>
                                    Asset Timeline
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_assets')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('qr-scanner') ? 'active' : '' }}" href="{{ route('qr-scanner') }}">
                                    <i class="fas fa-qrcode"></i>
                                    QR Scanner
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_roles')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                    <i class="fas fa-user-tag"></i>
                                    Roles
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_permissions')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                                    <i class="fas fa-key"></i>
                                    Permissions
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_notifications')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                                    <i class="fas fa-bell"></i>
                                    Notifications
                                </a>
                            </li>
                            @endcan
                        </ul>
                        
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
                
                <!-- Main content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                    <!-- Top navbar -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <div class="d-flex flex-column">
                            <!-- Breadcrumbs replace page titles -->
                            <x-breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
                        </div>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <!-- Global Search Bar -->
                            <div class="position-relative me-3" style="max-width: 300px;">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           placeholder="Search everything..." 
                                           id="globalSearchInput"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="globalSearchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-outline-primary" type="button" id="advancedSearchBtn" title="Advanced Search">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                </div>
                                <!-- Global Search Suggestions -->
                                <div id="globalSearchSuggestions" class="dropdown-menu w-100 position-absolute" style="display: none; z-index: 1050;">
                                    <div id="globalSuggestionsContent">
                                        <!-- Suggestions will be loaded here -->
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group me-2">
                                @yield('page-actions')
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Notification Bell - Visible to all authenticated users -->
                                @auth
                                <div class="dropdown me-3">
                                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                        <i class="fas fa-bell" style="font-size: 1.2rem; color: #6c757d;"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none; font-size: 0.7rem;">0</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;" id="notificationDropdown">
                                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                                            <span>Notifications</span>
                                            <button class="btn btn-sm btn-link text-primary p-0" id="markAllRead" style="display: none;">Mark all read</button>
                                        </div>
                                        <div id="notificationList">
                                            <div class="dropdown-item text-center text-muted py-3">
                                                <i class="fas fa-bell-slash me-2"></i>No notifications
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endauth
                                
                                <!-- User Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        <i class="fas fa-user"></i> {{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="z-index:1060;">
                                        <li><a class="dropdown-item" href="{{ route('password.edit') }}">Change Password</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                                Logout
                                            </a>
                                            <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Flash messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Page content -->
                    @yield('content')
                </main>
            </div>
        </div>
        
        <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop d-md-none" id="sidebarBackdrop"></div>
    @endguest
    
    <!-- Dark Mode Toggle Button -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
        <svg class="moon-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <svg class="sun-icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        // Ensure all Bootstrap dropdowns are initialized
        document.addEventListener('DOMContentLoaded', function () {
            if (window.bootstrap) {
                document.querySelectorAll('.dropdown-toggle').forEach(function (el) {
                    try { new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } }); } catch (e) { /* noop */ }
                });
                // Prevent clicks from being swallowed by other handlers
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });
                });
            }
        });
    </script>
    
    <!-- Dark Mode Toggle Script -->
    <script>
        // Dark mode functionality
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        
        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', currentTheme);
        
        // Update toggle button state
        function updateToggleButton(theme) {
            const moonIcon = themeToggle.querySelector('.moon-icon');
            const sunIcon = themeToggle.querySelector('.sun-icon');
            
            if (theme === 'dark') {
                moonIcon.style.display = 'none';
                sunIcon.style.display = 'block';
                themeToggle.setAttribute('aria-label', 'Switch to light mode');
            } else {
                moonIcon.style.display = 'block';
                sunIcon.style.display = 'none';
                themeToggle.setAttribute('aria-label', 'Switch to dark mode');
            }
        }
        
        // Initialize button state
        updateToggleButton(currentTheme);
        
        // Theme toggle event listener
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleButton(newTheme);
            
            // Add a subtle animation feedback
            themeToggle.style.transform = 'scale(0.95)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });
        
        // Keyboard accessibility
        themeToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                themeToggle.click();
            }
        });
        
        // System theme preference detection
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Only apply system preference if no saved preference exists
            if (!localStorage.getItem('theme')) {
                const systemTheme = mediaQuery.matches ? 'dark' : 'light';
                htmlElement.setAttribute('data-theme', systemTheme);
                updateToggleButton(systemTheme);
            }
            
            // Listen for system theme changes
            mediaQuery.addEventListener('change', function(e) {
                // Only apply if no manual preference is saved
                if (!localStorage.getItem('theme')) {
                    const systemTheme = e.matches ? 'dark' : 'light';
                    htmlElement.setAttribute('data-theme', systemTheme);
                    updateToggleButton(systemTheme);
                }
            });
        }
        
        // Smooth transition on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        });
    </script>
    
    <!-- Notification System JavaScript - For all authenticated users -->
    @auth
    <script>
        class NotificationSystem {
            constructor() {
                this.notificationBell = document.getElementById('notificationBell');
                this.notificationCount = document.getElementById('notificationCount');
                this.notificationList = document.getElementById('notificationList');
                this.markAllReadBtn = document.getElementById('markAllRead');
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                this.init();
            }
            
            init() {
                // Load notifications on page load
                this.loadNotifications();
                
                // Set up periodic refresh
                setInterval(() => this.loadNotifications(), 30000); // Every 30 seconds
                
                // Mark all as read button
                this.markAllReadBtn?.addEventListener('click', () => this.markAllAsRead());
                
                // Ensure dropdown functionality works
                if (this.notificationBell) {
                    // Initialize Bootstrap dropdown explicitly
                    const dropdown = new bootstrap.Dropdown(this.notificationBell);
                    
                    // Add click event listener to ensure dropdown opens
                    this.notificationBell.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropdown.toggle();
                    });
                }
            }
            
            async loadNotifications() {
                try {
                    const response = await fetch('/notifications/unread');
                    const data = await response.json();
                    
                    this.updateNotificationUI(data.notifications, data.count);
                } catch (error) {
                    console.error('Error loading notifications:', error);
                }
            }
            
            updateNotificationUI(notifications, count) {
                // Update count badge
                if (count > 0) {
                    this.notificationCount.textContent = count > 99 ? '99+' : count;
                    this.notificationCount.style.display = 'block';
                    this.markAllReadBtn.style.display = 'block';
                } else {
                    this.notificationCount.style.display = 'none';
                    this.markAllReadBtn.style.display = 'none';
                }
                
                // Update notification list
                if (notifications.length === 0) {
                    this.notificationList.innerHTML = `
                        <div class="dropdown-item text-center text-muted py-3">
                            <i class="fas fa-bell-slash me-2"></i>No notifications
                        </div>
                    `;
                } else {
                    this.notificationList.innerHTML = notifications.map(notification => 
                        this.createNotificationHTML(notification)
                    ).join('');
                    
                    // Add click handlers for individual notifications
                    this.notificationList.querySelectorAll('.notification-item').forEach(item => {
                        item.addEventListener('click', (e) => {
                            const notificationId = e.currentTarget.dataset.notificationId;
                            this.markAsRead(notificationId);
                        });
                    });
                }
            }
            
            createNotificationHTML(notification) {
                const timeAgo = this.timeAgo(new Date(notification.created_at));
                const iconClass = notification.type === 'asset_confirmed' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                
                return `
                    <div class="dropdown-item notification-item" data-notification-id="${notification.id}" style="cursor: pointer; border-left: 3px solid ${notification.type === 'asset_confirmed' ? '#28a745' : '#dc3545'}; white-space: normal;">
                        <div class="d-flex align-items-start">
                            <i class="fas ${iconClass} me-2 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">${notification.title}</div>
                                <div class="text-muted small">${notification.message}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">${timeAgo}</div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            async markAsRead(notificationId) {
                try {
                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        this.loadNotifications(); // Refresh notifications
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            }
            
            async markAllAsRead() {
                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        this.loadNotifications(); // Refresh notifications
                    }
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                }
            }
            
            timeAgo(date) {
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
                if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
                return `${Math.floor(diffInSeconds / 86400)}d ago`;
            }
        }
        
        // Initialize notification system when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new NotificationSystem();
        });
    </script>
    @endauth
    
    <!-- Mobile Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebarMenu');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            if (sidebarToggle && sidebar && backdrop) {
                // Toggle sidebar on button click
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                    document.body.classList.toggle('sidebar-open');
                });
                
                // Close sidebar when clicking backdrop
                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                });
                
                // Close sidebar on window resize if screen becomes larger
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove('show');
                        backdrop.classList.remove('show');
                        document.body.classList.remove('sidebar-open');
                    }
                });
                
                // Close sidebar on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        backdrop.classList.remove('show');
                        document.body.classList.remove('sidebar-open');
                    }
                });
            }
        });
    </script>
    
    <script src="{{ asset('js/searchable-dropdown.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Keyboard Shortcuts -->
    <script src="{{ asset('js/keyboard-shortcuts.js') }}"></script>
    
    <!-- Global Search JavaScript -->
    <script>
        // Global Search Functionality
        class GlobalSearch {
            constructor() {
                this.searchInput = document.getElementById('globalSearchInput');
                this.searchBtn = document.getElementById('globalSearchBtn');
                this.advancedBtn = document.getElementById('advancedSearchBtn');
                this.suggestionsDropdown = document.getElementById('globalSearchSuggestions');
                this.suggestionsContent = document.getElementById('globalSuggestionsContent');
                
                this.init();
            }
            
            init() {
                if (this.searchInput) {
                    this.setupEventListeners();
                }
            }
            
            setupEventListeners() {
                // Search input events
                this.searchInput.addEventListener('input', (e) => {
                    this.handleSearchInput(e.target.value);
                });
                
                this.searchInput.addEventListener('focus', () => {
                    if (this.searchInput.value.trim().length >= 2) {
                        this.loadSuggestions(this.searchInput.value.trim());
                    }
                });
                
                this.searchInput.addEventListener('blur', () => {
                    setTimeout(() => this.hideSuggestions(), 200);
                });
                
                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.performSearch();
                    } else if (e.key === 'Escape') {
                        this.hideSuggestions();
                    }
                });
                
                // Button events
                if (this.searchBtn) {
                    this.searchBtn.addEventListener('click', () => this.performSearch());
                }
                if (this.advancedBtn) {
                    this.advancedBtn.addEventListener('click', () => {
                        window.location.href = '{{ route("search.index") }}';
                    });
                }
            }
            
            handleSearchInput(query) {
                if (query.length >= 2) {
                    this.loadSuggestions(query);
                } else {
                    this.hideSuggestions();
                }
            }
            
            async loadSuggestions(query) {
                try {
                    const response = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.displaySuggestions(data.suggestions);
                    }
                } catch (error) {
                    console.error('Error loading suggestions:', error);
                }
            }
            
            displaySuggestions(suggestions) {
                if (suggestions.length === 0) {
                    this.hideSuggestions();
                    return;
                }
                
                this.suggestionsContent.innerHTML = suggestions.map(suggestion => `
                    <div class="dropdown-item d-flex align-items-center" onclick="selectGlobalSuggestion('${suggestion.text}', '${suggestion.url}')">
                        <i class="${suggestion.icon} me-2 text-muted"></i>
                        <div>
                            <div class="fw-medium">${suggestion.text}</div>
                            <small class="text-muted">${suggestion.subtext}</small>
                        </div>
                    </div>
                `).join('');
                
                this.suggestionsDropdown.style.display = 'block';
            }
            
            hideSuggestions() {
                this.suggestionsDropdown.style.display = 'none';
            }
            
            performSearch() {
                const query = this.searchInput.value.trim();
                if (query) {
                    window.location.href = `{{ route('search.results') }}?q=${encodeURIComponent(query)}`;
                }
            }
        }
        
        // Initialize global search when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const globalSearch = new GlobalSearch();
            
            // Make it globally available
            window.globalSearch = globalSearch;
        });
        
        // Global functions
        function selectGlobalSuggestion(text, url) {
            if (url) {
                window.location.href = url;
            } else {
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.value = text;
                    if (window.globalSearch) {
                        window.globalSearch.performSearch();
                    }
                }
            }
        }
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
