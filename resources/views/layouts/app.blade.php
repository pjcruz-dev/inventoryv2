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
        
        /* Enhanced Global Search Styles */
        .global-search-container {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
            z-index: 1000;
        }
        
        #globalSearchInput {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 12px 20px 12px 45px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        #globalSearchInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
            background: #fff;
        }
        
        #globalSearchInput::placeholder {
            color: #6c757d;
            font-weight: 400;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
            pointer-events: none;
            z-index: 10;
        }
        
        #clearGlobalSearch {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6c757d;
            font-size: 14px;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        #clearGlobalSearch:hover {
            background: #f8f9fa;
            color: #495057;
        }
        
        /* Enhanced Search Results Dropdown */
        #globalSearchResults {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background: #fff;
            overflow: hidden;
            animation: slideDown 0.2s ease-out;
            max-height: 450px;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .search-result-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .search-result-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(4px);
            text-decoration: none;
            color: inherit;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .search-result-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .search-result-icon.asset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .search-result-icon.user {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .search-result-icon.category {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .search-result-icon.department {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        
        .search-result-icon.vendor {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .search-result-icon.assignment {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
        
        .search-result-icon.maintenance {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #333;
        }
        
        .search-result-icon.disposal {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #333;
        }
        
        .search-result-content {
            flex: 1;
            min-width: 0;
        }
        
        .search-result-title {
            font-weight: 600;
            font-size: 14px;
            color: #2d3748;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-result-description {
            font-size: 12px;
            color: #718096;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-result-arrow {
            color: #cbd5e0;
            font-size: 12px;
            margin-left: 8px;
            transition: all 0.2s ease;
        }
        
        .search-result-item:hover .search-result-arrow {
            color: #667eea;
            transform: translateX(2px);
        }
        
        /* Search Loading State */
        .search-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .search-loading i {
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* No Results State */
        .search-no-results {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .search-no-results i {
            font-size: 24px;
            margin-bottom: 8px;
            opacity: 0.5;
        }
        
        /* View All Results Link */
        .search-view-all {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 12px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        
        .search-view-all:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            text-decoration: none;
        }
        
        /* Enhanced Header Styles */
        .enhanced-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 8px 16px;
            margin-bottom: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Breadcrumb Navigation */
        .breadcrumb-section {
            background: transparent;
            border-radius: 8px;
            padding: 4px 10px;
            flex-shrink: 0;
        }

        .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #5a6fd8;
        }

        .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 500;
        }


        /* Enhanced Search with Keyboard Shortcut */
        .search-shortcut-hint {
            position: absolute;
            right: 60px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color:rgb(124, 126, 129);
            opacity: 1;
            z-index: 10;
            font-weight: 500;
            background: transparent;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .search-shortcut-hint kbd {
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 3px;
            padding: 2px 5px;
            font-size: 11px;
            font-family: monospace;
            color: #495057;
            font-weight: 600;
        }

        /* Header Actions */
        .header-actions {
            gap: 12px;
        }

        /* Enhanced Notification Bell */
        .notification-bell {
            position: relative;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
            color: #6c757d;
        }

        .notification-bell:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .notification-badge {
            font-size: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-dropdown {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced User Dropdown */
        .user-dropdown-btn {
            border: none;
            border-radius: 8px;
            padding: 6px 8px;
            background: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            height: auto;
            min-width: auto;
            cursor: pointer;
        }

        .user-dropdown-btn:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 50%;
            display: flex !important;
            align-items: center;
            justify-content: center;
            color: white !important;
            font-size: 12px;
            flex-shrink: 0;
        }

        .user-info {
            text-align: left;
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #2d3748;
            line-height: 1.2;
            white-space: nowrap;
            overflow: visible;
            text-overflow: visible;
            display: block;
        }

        .user-role {
            font-size: 11px;
            color: #718096;
            line-height: 1.2;
            white-space: nowrap;
            overflow: visible;
            text-overflow: visible;
            display: block;
        }

        .user-dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 8px 0;
            min-width: 280px;
        }

        .user-profile-header {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: -8px -8px 8px -8px;
            border-radius: 12px 12px 0 0;
            color: white;
        }

        .user-avatar-large {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 20px;
        }

        .user-details {
            flex: 1;
        }

        .user-name-large {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 2px;
        }

        .user-email {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .user-role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            display: inline-block;
        }

        .user-dropdown-menu .dropdown-item {
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .user-dropdown-menu .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .user-dropdown-menu .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        /* Glassmorphism Effects */
        .enhanced-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 12px;
            pointer-events: none;
        }

        /* Micro-interactions */
        .quick-action-btn,
        .notification-bell,
        .user-dropdown-btn {
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn::before,
        .notification-bell::before,
        .user-dropdown-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .quick-action-btn:hover::before,
        .notification-bell:hover::before,
        .user-dropdown-btn:hover::before {
            width: 100%;
            height: 100%;
        }

        /* Search Container */
        .search-container {
            max-width: 700px;
            min-width: 400px;
            flex: 0 0 auto;
        }

        /* Ensure main content has lower z-index */
        .main-content {
            position: relative;
            z-index: 1;
        }

        /* Ensure dashboard cards have lower z-index */
        .card {
            position: relative;
            z-index: 1;
        }

        /* Global Search Results Portal */
        #globalSearchResultsPortal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999999 !important;
            pointer-events: none !important;
        }

        #globalSearchResults {
            position: absolute !important;
            z-index: 9999999 !important;
            pointer-events: auto !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            background: white !important;
            max-width: 500px !important;
            min-width: 300px !important;
        }

        #searchResultsContent {
            background: white !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .enhanced-header {
                padding: 8px 10px;
                margin-bottom: 10px;
            }

            .breadcrumb-section {
                padding: 4px 8px;
                margin-bottom: 0;
            }

            .breadcrumb {
                font-size: 14px;
            }

            .breadcrumb-item {
                padding: 0.25rem 0.5rem;
            }

            /* Stack elements vertically on mobile */
            .d-flex.justify-content-between {
                flex-direction: column !important;
                gap: 12px;
            }

            .search-container {
                width: 100%;
                max-width: 100%;
                min-width: auto;
                margin: 0;
                order: 2;
            }

            .global-search-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }
            
            #globalSearchInput {
                font-size: 16px; /* Prevents zoom on iOS */
                padding: 12px 16px 12px 45px;
                width: 100%;
            }

            .search-icon {
                left: 16px;
                font-size: 16px;
            }

            .search-shortcut-hint {
                display: block;
                right: 10px;
                font-size: 11px;
                opacity: 1;
                background: transparent;
            }

            .header-actions {
                order: 1;
                width: 100%;
                justify-content: flex-end;
                margin-bottom: 8px;
            }

            .user-info {
                display: block !important;
            }

            .user-name {
                font-size: 13px;
            }

            .user-role {
                font-size: 11px;
            }

            .user-dropdown-btn {
                padding: 4px 6px;
                font-size: 13px;
                height: auto;
            }

            .user-dropdown-btn .user-avatar {
                width: 24px;
                height: 24px;
                font-size: 11px;
            }

            /* Notification bell adjustments */
            .notification-bell {
                padding: 8px 12px;
                font-size: 16px;
            }
        }

        @media (max-width: 576px) {
            .enhanced-header {
                padding: 8px 10px;
            }

            .search-container {
                min-width: 200px;
                margin: 8px 0;
            }


            .header-actions {
                gap: 8px;
            }
        }
        
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
        
        /* Enhanced Mobile Responsive Design */
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
                -webkit-overflow-scrolling: touch;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
                padding-top: 70px;
                padding-left: 15px;
                padding-right: 15px;
            }
            
            /* Enhanced mobile navigation toggle */
            .navbar-toggler {
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1050;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 12px;
                padding: 12px 16px;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                transition: all 0.3s ease;
            }
            
            .navbar-toggler:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
            }
            
            .navbar-toggler:active {
                transform: translateY(0);
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
                backdrop-filter: blur(2px);
            }
            
            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }
            
            #sidebarMenu.show {
                transform: translateX(0);
            }
            
            /* Mobile-optimized cards */
            .card {
                margin-bottom: 1rem;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .card-header {
                padding: 1rem;
                border-radius: 12px 12px 0 0;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            /* Mobile-optimized tables */
            .table-responsive {
                border-radius: 8px;
                overflow: hidden;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
                vertical-align: middle;
            }
            
            /* Mobile-optimized buttons */
            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                border-radius: 8px;
                min-height: 44px; /* Touch-friendly minimum size */
            }
            
            .btn-sm {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
                min-height: 36px;
            }
            
            /* Mobile-optimized forms */
            .form-control {
                padding: 0.75rem;
                font-size: 1rem;
                border-radius: 8px;
                min-height: 44px;
            }
            
            .form-select {
                padding: 0.75rem;
                font-size: 1rem;
                border-radius: 8px;
                min-height: 44px;
            }
            
            /* Mobile-optimized action buttons */
            .action-btn {
                min-width: 44px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            /* Mobile-optimized search bar */
            .search-container {
                margin-bottom: 1rem;
            }
            
            .search-container .form-control {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }
            
            .search-container .btn {
                padding: 0.75rem 1rem;
            }
            
            /* Mobile-optimized pagination */
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            
            .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                min-width: 44px;
                text-align: center;
            }
            
            /* Mobile-optimized modals */
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
            
            .modal-content {
                border-radius: 12px;
            }
            
            .modal-header {
                padding: 1rem;
                border-radius: 12px 12px 0 0;
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .modal-footer {
                padding: 1rem;
                border-radius: 0 0 12px 12px;
            }
            
            /* Mobile-optimized dropdowns */
            .dropdown-menu {
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: none;
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
            
            /* Mobile-optimized alerts */
            .alert {
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            /* Mobile-optimized badges */
            .badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
            
            /* Mobile-optimized spacing */
            .mb-3 { margin-bottom: 1rem !important; }
            .mb-4 { margin-bottom: 1.5rem !important; }
            .mb-5 { margin-bottom: 2rem !important; }
            
            .mt-3 { margin-top: 1rem !important; }
            .mt-4 { margin-top: 1.5rem !important; }
            .mt-5 { margin-top: 2rem !important; }
            
            /* Mobile-optimized text */
            h1 { font-size: 1.75rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            h4 { font-size: 1.125rem; }
            h5 { font-size: 1rem; }
            h6 { font-size: 0.875rem; }
            
            /* Mobile-optimized grid */
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
            .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
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
        
        /* User Dropdown Fixes */
        .dropdown-menu {
            z-index: 1060 !important;
            min-width: 200px;
        }
        
        .dropdown-toggle::after {
            margin-left: 0.5em;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.15s ease-in-out;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item:focus {
            background-color: #e9ecef;
            outline: none;
        }
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <!-- Global Search Results Portal -->
    <div id="globalSearchResultsPortal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999999; pointer-events: none;">
        <div id="globalSearchResults" style="position: absolute; z-index: 9999999; pointer-events: auto;">
            <div id="searchResultsContent">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- User Dropdown Portal -->
    <div id="userDropdownPortal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999999; pointer-events: none;">
        <div id="userDropdownContent" style="position: absolute; z-index: 999999; pointer-events: auto;">
            <!-- User dropdown content will be dynamically inserted here -->
        </div>
    </div>
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
                            @can('view_dashboard')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            @endcan
                            
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
                                    {{ auth()->user()->hasRole('User') && !auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']) ? 'My Assets' : 'All Assets' }}
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
                            
                            @if(auth()->user()->can('view_logs') || auth()->user()->can('view_assets') || auth()->user()->can('view_roles') || auth()->user()->can('view_permissions') || auth()->user()->can('view_security_audit'))
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
                            
                            @can('import_export_access')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.interface') }}">
                                    <i class="fas fa-file-import"></i>
                                    Import/Export
                                </a>
                            </li>
                            @endcan
                            
                            @can('view_timeline')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}" href="{{ route('timeline.index') }}">
                                    <i class="fas fa-history"></i>
                                    Asset Timeline
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
                            
                            @can('view_security_audit')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('security.*') ? 'active' : '' }}" href="{{ route('security.audit.index') }}">
                                    <i class="fas fa-shield-alt"></i>
                                    Security Audit
                                </a>
                            </li>
                            @endcan
                            
        @can('view_reports')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <i class="fas fa-chart-bar"></i>
                Reports & Analytics
            </a>
        </li>
        @endcan
        
        @can('view_system_health')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('system.*') ? 'active' : '' }}" href="{{ route('system.health') }}">
                <i class="fas fa-heartbeat"></i>
                System Health
            </a>
        </li>
        @endcan
        
        @can('view_security_audit')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('security.monitoring.*') ? 'active' : '' }}" href="{{ route('security.monitoring.index') }}">
                <i class="fas fa-shield-alt"></i>
                Security Monitoring
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
                    <!-- Enhanced Top Header -->
                    <div class="enhanced-header">
                        <!-- Top Header Row with Breadcrumbs, Search, and User -->
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-1 mb-1" style="position: relative;">
                            <!-- Breadcrumb Navigation -->
                            <div class="breadcrumb-section">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('dashboard') }}">
                                                <i class="fas fa-home"></i>
                                            </a>
                                        </li>
                                        @php
                                            $currentRoute = request()->route()->getName();
                                            $routeSegments = explode('.', $currentRoute);
                                            $breadcrumbs = [];
                                            
                                            // Build breadcrumbs based on route
                                            if ($currentRoute === 'dashboard') {
                                                // Dashboard - no additional breadcrumbs
                                            } elseif (isset($routeSegments[0]) && in_array($routeSegments[0], ['assets', 'users', 'asset-categories', 'departments', 'vendors', 'monitors', 'computers', 'printers', 'peripherals', 'asset-assignments', 'asset-assignment-confirmations', 'maintenance', 'disposal', 'transfers', 'accountability'])) {
                                                $moduleName = ucfirst(str_replace('-', ' ', $routeSegments[0]));
                                                
                                                // Add module index breadcrumb
                                                try {
                                                    $moduleUrl = route($routeSegments[0] . '.index');
                                                    $breadcrumbs[] = [
                                                        'title' => $moduleName,
                                                        'url' => $moduleUrl,
                                                        'active' => false
                                                    ];
                                                } catch (Exception $e) {
                                                    $breadcrumbs[] = [
                                                        'title' => $moduleName,
                                                        'url' => null,
                                                        'active' => false
                                                    ];
                                                }
                                                
                                                // Add specific page breadcrumb
                                                if (count($routeSegments) > 1) {
                                                    $action = $routeSegments[1];
                                                    $actionTitle = ucfirst($action);
                                                    
                                                    if ($action === 'create') {
                                                        $breadcrumbs[] = [
                                                            'title' => 'Add ' . substr($moduleName, 0, -1),
                                                            'url' => null,
                                                            'active' => true
                                                        ];
                                                    } elseif ($action === 'edit') {
                                                        $breadcrumbs[] = [
                                                            'title' => 'Edit ' . substr($moduleName, 0, -1),
                                                            'url' => null,
                                                            'active' => true
                                                        ];
                                                    } elseif ($action === 'show') {
                                                        $breadcrumbs[] = [
                                                            'title' => 'View ' . substr($moduleName, 0, -1),
                                                            'url' => null,
                                                            'active' => true
                                                        ];
                                                    } else {
                                                        $breadcrumbs[] = [
                                                            'title' => $actionTitle,
                                                            'url' => null,
                                                            'active' => true
                                                        ];
                                                    }
                                                } else {
                                                    if (!empty($breadcrumbs)) {
                                                        $breadcrumbs[0]['active'] = true;
                                                    }
                                                }
                                            } else {
                                                // Fallback for other routes
                                                $pageTitle = 'Page';
                                                if (isset($routeSegments[0])) {
                                                    $pageTitle = ucfirst(str_replace('-', ' ', $routeSegments[0]));
                                                }
                                                $breadcrumbs[] = [
                                                    'title' => $pageTitle,
                                                    'url' => null,
                                                    'active' => true
                                                ];
                                            }
                                        @endphp
                                        
                                        @foreach($breadcrumbs as $breadcrumb)
                                            <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}" {{ $breadcrumb['active'] ? 'aria-current="page"' : '' }}>
                                                @if($breadcrumb['url'])
                                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                                @else
                                                    {{ $breadcrumb['title'] }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ol>
                                </nav>
                            </div>
                            
                            <!-- Enhanced Global Search (Centered) -->
                            <div class="search-container d-flex justify-content-center">
                                <div class="global-search-container">
                                    <form action="{{ route('search.results') }}" method="GET" id="globalSearchForm">
                                        <div class="position-relative">
                                            <i class="fas fa-search search-icon"></i>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="globalSearchInput"
                                                   name="q"
                                                   placeholder="Search"
                                                   autocomplete="off">
                                            <button class="btn btn-link" type="button" id="clearGlobalSearch" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <div class="search-shortcut-hint">
                                                <kbd>Ctrl</kbd> + <kbd>K</kbd>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <!-- Search Results will be dynamically inserted here -->
                                </div>
                            </div>
                            
                            <!-- Header Actions -->
                            <div class="header-actions d-flex align-items-center">
                                <!-- Enhanced Notification Bell -->
                                @if(auth()->user()->department && auth()->user()->department->name === 'Information Technology')
                                <div class="dropdown me-3">
                                    <a class="nav-link position-relative notification-bell" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                        <i class="fas fa-bell"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" id="notificationCount" style="display: none;">0</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" id="notificationDropdown">
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
                                @endif
                                
                                <!-- Enhanced User Dropdown -->
                                <div class="user-dropdown-container">
                                    <div class="user-dropdown-btn" id="userDropdownBtn">
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}</div>
                                        </div>
                                        <i class="fas fa-chevron-down ms-1"></i>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // Ensure Bootstrap is loaded before initializing dropdowns
        function initializeDropdowns() {
            if (typeof bootstrap !== 'undefined') {
                // Initialize all dropdowns
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl, {
                        popperConfig: { strategy: 'fixed' }
                    });
                });
                
                // Ensure dropdowns work properly
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Manually toggle dropdown
                        var dropdown = bootstrap.Dropdown.getInstance(this);
                        if (dropdown) {
                            dropdown.toggle();
                        } else {
                            dropdown = new bootstrap.Dropdown(this);
                            dropdown.toggle();
                        }
                    });
                });
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.dropdown')) {
                        dropdownList.forEach(function (dropdown) {
                            dropdown.hide();
                        });
                    }
                });
            } else {
                // Retry after a short delay if Bootstrap isn't loaded yet
                setTimeout(initializeDropdowns, 100);
            }
        }
    </script>
    <script>
        // Initialize dropdowns when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            initializeDropdowns();
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
    
    <!-- Notification System JavaScript - Only for IT Department -->
    @auth
    @if(auth()->user()->department && auth()->user()->department->name === 'Information Technology')
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
        
        // Global Loading State System
        class LoadingStateManager {
            constructor() {
                this.loadingOverlay = null;
                this.createLoadingOverlay();
            }
            
            createLoadingOverlay() {
                this.loadingOverlay = document.createElement('div');
                this.loadingOverlay.id = 'global-loading-overlay';
                this.loadingOverlay.innerHTML = `
                    <div class="loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="loading-text">Processing...</div>
                    </div>
                `;
                this.loadingOverlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: none;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    backdrop-filter: blur(2px);
                `;
                document.body.appendChild(this.loadingOverlay);
            }
            
            show(message = 'Processing...') {
                const textElement = this.loadingOverlay.querySelector('.loading-text');
                if (textElement) textElement.textContent = message;
                this.loadingOverlay.style.display = 'flex';
            }
            
            hide() {
                this.loadingOverlay.style.display = 'none';
            }
        }
        
        // Global Toast Notification System
        class ToastNotificationSystem {
            constructor() {
                this.container = null;
                this.createContainer();
            }
            
            createContainer() {
                this.container = document.createElement('div');
                this.container.id = 'toast-container';
                this.container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10000;
                    max-width: 400px;
                `;
                document.body.appendChild(this.container);
            }
            
            show(message, type = 'info', duration = 5000) {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <div class="toast-icon">
                            <i class="fas fa-${this.getIcon(type)}"></i>
                        </div>
                        <div class="toast-message">${message}</div>
                        <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                toast.style.cssText = `
                    background: white;
                    border-left: 4px solid ${this.getColor(type)};
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    padding: 16px;
                    margin-bottom: 8px;
                    opacity: 0;
                    transform: translateX(100%);
                    transition: all 0.3s ease;
                    position: relative;
                `;
                
                this.container.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(0)';
                }, 100);
                
                // Auto remove
                if (duration > 0) {
                    setTimeout(() => {
                        this.remove(toast);
                    }, duration);
                }
            }
            
            remove(toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
            
            getIcon(type) {
                const icons = {
                    success: 'check-circle',
                    error: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };
                return icons[type] || 'info-circle';
            }
            
            getColor(type) {
                const colors = {
                    success: '#10b981',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#3b82f6'
                };
                return colors[type] || '#3b82f6';
            }
        }
        
        // Initialize global systems
        window.loadingManager = new LoadingStateManager();
        window.toastManager = new ToastNotificationSystem();
        
        // Add CSS for loading states
        const loadingStyles = document.createElement('style');
        loadingStyles.textContent = `
            .loading-content {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            }
            
            .loading-text {
                margin-top: 1rem;
                font-weight: 500;
                color: #374151;
            }
            
            .toast-content {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .toast-icon {
                font-size: 1.2rem;
                color: #6b7280;
            }
            
            .toast-message {
                flex: 1;
                font-weight: 500;
                color: #374151;
            }
            
            .toast-close {
                background: none;
                border: none;
                color: #9ca3af;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: all 0.2s ease;
            }
            
            .toast-close:hover {
                background: #f3f4f6;
                color: #374151;
            }
            
            /* Skeleton loading states */
            .skeleton {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite;
            }
            
            @keyframes loading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
            
            .skeleton-text {
                height: 1rem;
                border-radius: 4px;
                margin-bottom: 0.5rem;
            }
            
            .skeleton-text.short { width: 60%; }
            .skeleton-text.medium { width: 80%; }
            .skeleton-text.long { width: 100%; }
            
            .skeleton-button {
                height: 2.5rem;
                width: 6rem;
                border-radius: 6px;
            }
            
            .skeleton-card {
                height: 200px;
                border-radius: 8px;
                margin-bottom: 1rem;
            }
        `;
        document.head.appendChild(loadingStyles);
        
        // Enhanced Mobile Navigation System
        class MobileNavigationManager {
            constructor() {
                this.sidebar = document.getElementById('sidebarMenu');
                this.toggler = document.querySelector('.navbar-toggler');
                this.backdrop = null;
                this.init();
            }
            
            init() {
                if (this.toggler && this.sidebar) {
                    this.createBackdrop();
                    this.bindEvents();
                }
            }
            
            createBackdrop() {
                this.backdrop = document.createElement('div');
                this.backdrop.className = 'sidebar-backdrop';
                this.backdrop.addEventListener('click', () => this.closeSidebar());
                document.body.appendChild(this.backdrop);
            }
            
            bindEvents() {
                this.toggler.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleSidebar();
                });
                
                // Close sidebar when clicking on links
                const sidebarLinks = this.sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        this.closeSidebar();
                    });
                });
                
                // Close sidebar on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isSidebarOpen()) {
                        this.closeSidebar();
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 768) {
                        this.closeSidebar();
                    }
                });
            }
            
            toggleSidebar() {
                if (this.isSidebarOpen()) {
                    this.closeSidebar();
                } else {
                    this.openSidebar();
                }
            }
            
            openSidebar() {
                this.sidebar.classList.add('show');
                this.backdrop.classList.add('show');
                document.body.classList.add('sidebar-open');
                this.toggler.setAttribute('aria-expanded', 'true');
            }
            
            closeSidebar() {
                this.sidebar.classList.remove('show');
                this.backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                this.toggler.setAttribute('aria-expanded', 'false');
            }
            
            isSidebarOpen() {
                return this.sidebar.classList.contains('show');
            }
        }
        
        // Enhanced Touch Interactions
        class TouchInteractionManager {
            constructor() {
                this.init();
            }
            
            init() {
                // Add touch feedback to buttons
                this.addTouchFeedback();
                
                // Add swipe gestures for mobile
                this.addSwipeGestures();
                
                // Add pull-to-refresh functionality
                this.addPullToRefresh();
            }
            
            addTouchFeedback() {
                const buttons = document.querySelectorAll('.btn, .action-btn, .navbar-toggler');
                buttons.forEach(button => {
                    button.addEventListener('touchstart', (e) => {
                        button.style.transform = 'scale(0.95)';
                        button.style.transition = 'transform 0.1s ease';
                    });
                    
                    button.addEventListener('touchend', (e) => {
                        button.style.transform = 'scale(1)';
                    });
                    
                    button.addEventListener('touchcancel', (e) => {
                        button.style.transform = 'scale(1)';
                    });
                });
            }
            
            addSwipeGestures() {
                let startX = 0;
                let startY = 0;
                let endX = 0;
                let endY = 0;
                
                document.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                });
                
                document.addEventListener('touchend', (e) => {
                    endX = e.changedTouches[0].clientX;
                    endY = e.changedTouches[0].clientY;
                    
                    const diffX = startX - endX;
                    const diffY = startY - endY;
                    
                    // Swipe left to close sidebar
                    if (diffX > 50 && Math.abs(diffY) < 50 && window.innerWidth < 768) {
                        const mobileNav = window.mobileNavigationManager;
                        if (mobileNav && mobileNav.isSidebarOpen()) {
                            mobileNav.closeSidebar();
                        }
                    }
                });
            }
            
            addPullToRefresh() {
                let startY = 0;
                let currentY = 0;
                let isPulling = false;
                let pullDistance = 0;
                
                document.addEventListener('touchstart', (e) => {
                    if (window.scrollY === 0) {
                        startY = e.touches[0].clientY;
                        isPulling = true;
                    }
                });
                
                document.addEventListener('touchmove', (e) => {
                    if (isPulling) {
                        currentY = e.touches[0].clientY;
                        pullDistance = currentY - startY;
                        
                        if (pullDistance > 0) {
                            e.preventDefault();
                            document.body.style.transform = `translateY(${Math.min(pullDistance * 0.5, 100)}px)`;
                        }
                    }
                });
                
                document.addEventListener('touchend', (e) => {
                    if (isPulling && pullDistance > 100) {
                        // Trigger refresh
                        window.location.reload();
                    }
                    
                    document.body.style.transform = '';
                    isPulling = false;
                    pullDistance = 0;
                });
            }
        }
        
        // Initialize all systems when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new NotificationSystem();
            window.mobileNavigationManager = new MobileNavigationManager();
            new TouchInteractionManager();
            
            // Add loading states to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    }
                });
            });
            
            // Add loading states to action buttons
            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    if (button.tagName === 'A' || button.type === 'submit') {
                        button.classList.add('loading');
                        button.disabled = true;
                    }
                });
            });
        });
    </script>
    @endif
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
    
    <!-- Enhanced Header JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global Search functionality
            const searchInput = document.getElementById('globalSearchInput');
            const searchResultsPortal = document.getElementById('globalSearchResultsPortal');
            const searchResults = document.getElementById('globalSearchResults');
            const searchResultsContent = document.getElementById('searchResultsContent');
            const clearButton = document.getElementById('clearGlobalSearch');
            let searchTimeout;

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+K or Cmd+K for search focus
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
                
                // Escape to close search results
                if (e.key === 'Escape') {
                    hideSearchResults();
                    searchInput.blur();
                }
            });

            // Show user profile function
            window.showUserProfile = function() {
                // Placeholder for user profile modal
                alert('User profile editing feature coming soon!');
            };

            // User dropdown functionality
            const userDropdownBtn = document.getElementById('userDropdownBtn');
            const userDropdownPortal = document.getElementById('userDropdownPortal');
            const userDropdownContent = document.getElementById('userDropdownContent');
            let userDropdownOpen = false;

            if (userDropdownBtn) {
                // User dropdown click handler
                userDropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('User dropdown clicked, current state:', userDropdownOpen);
                    
                    if (userDropdownOpen) {
                        hideUserDropdown();
                    } else {
                        showUserDropdown();
                    }
                });

                // Hide user dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (userDropdownOpen) {
                        const isInsideDropdown = e.target.closest('.user-dropdown-menu-container');
                        const isInsideButton = e.target.closest('#userDropdownBtn');
                        
                        if (!isInsideDropdown && !isInsideButton) {
                            console.log('Clicking outside, hiding dropdown');
                            hideUserDropdown();
                        }
                    }
                });

                // Prevent dropdown from closing when clicking inside it
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.user-dropdown-menu-container')) {
                        console.log('Clicking inside dropdown, preventing close');
                        e.stopPropagation();
                    }
                });

                // Show user dropdown
                function showUserDropdown() {
                    console.log('Showing user dropdown...');
                    const userDropdownBtnRect = userDropdownBtn.getBoundingClientRect();
                    console.log('Button rect:', userDropdownBtnRect);
                    
                    // Create dropdown content
                    const dropdownHTML = `
                        <div class="dropdown-menu dropdown-menu-end user-dropdown-menu" style="display: block; position: absolute; top: 0; right: 0; z-index: 9999; min-width: 280px;">
                            <div class="dropdown-header">
                                <div class="user-profile-header">
                                    <div class="user-avatar-large">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name-large">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}</div>
                                        <div class="user-email">{{ Auth::user()->email }}</div>
                                        <div class="user-role-badge">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('password.edit') }}">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                            <a class="dropdown-item" href="#" onclick="showUserProfile()">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-2').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form-2" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    `;
                    
                    // Create and position the dropdown
                    const existingDropdown = document.querySelector('.user-dropdown-menu');
                    if (existingDropdown) {
                        existingDropdown.remove();
                    }
                    
                    // Create a container div
                    const dropdownContainer = document.createElement('div');
                    dropdownContainer.style.position = 'fixed';
                    // Position below the button (top edge of dropdown = bottom edge of button)
                    dropdownContainer.style.top = (userDropdownBtnRect.bottom + window.scrollY + 8) + 'px';
                    // Align right edge with button's right edge (right-aligned)
                    dropdownContainer.style.right = (window.innerWidth - userDropdownBtnRect.right - window.scrollX) + 'px';
                    dropdownContainer.style.zIndex = '99999';
                    dropdownContainer.innerHTML = dropdownHTML;
                    dropdownContainer.className = 'user-dropdown-menu-container';
                    
                    document.body.appendChild(dropdownContainer);
                    userDropdownOpen = true;
                    
                    console.log('Dropdown created and appended to body');
                }

                // Hide user dropdown
                function hideUserDropdown() {
                    console.log('Hiding user dropdown...');
                    const existingDropdown = document.querySelector('.user-dropdown-menu-container');
                    if (existingDropdown) {
                        existingDropdown.remove();
                    }
                    userDropdownOpen = false;
                    
                    // Debug: Check if button is still visible
                    const button = document.getElementById('userDropdownBtn');
                    if (button) {
                        console.log('Button still exists:', button.offsetWidth > 0 && button.offsetHeight > 0);
                        console.log('Button display:', window.getComputedStyle(button).display);
                        console.log('Button visibility:', window.getComputedStyle(button).visibility);
                    } else {
                        console.log('Button element not found!');
                    }
                }

                // Update position on scroll and resize
                window.addEventListener('scroll', function() {
                    if (userDropdownOpen) {
                        showUserDropdown(); // Recalculate position
                    }
                });

                window.addEventListener('resize', function() {
                    if (userDropdownOpen) {
                        showUserDropdown(); // Recalculate position
                    }
                });
            }

            // Search input event listener
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                const shortcutHint = document.querySelector('.search-shortcut-hint');
                
                // Show/hide clear button and shortcut hint
                if (query.length > 0) {
                    clearButton.style.display = 'block';
                    if (shortcutHint) {
                        shortcutHint.style.display = 'none';
                    }
                } else {
                    clearButton.style.display = 'none';
                    if (shortcutHint) {
                        shortcutHint.style.display = 'block';
                    }
                }
                
                // Update position if dropdown is visible
                if (searchResultsPortal && searchResultsPortal.style.display === 'block') {
                    updateSearchResultsPosition();
                }
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    hideSearchResults();
                    return;
                }

                // Show loading state
                showLoadingState();

                // Debounce search requests
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Clear search button
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                clearButton.style.display = 'none';
                const shortcutHint = document.querySelector('.search-shortcut-hint');
                if (shortcutHint) {
                    shortcutHint.style.display = 'block';
                }
                hideSearchResults();
                searchInput.focus();
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.global-search-container') && !e.target.closest('#globalSearchResultsPortal')) {
                    hideSearchResults();
                }
            });

            // Update search results position on scroll and resize
            window.addEventListener('scroll', function() {
                if (searchResultsPortal && searchResultsPortal.style.display === 'block') {
                    updateSearchResultsPosition();
                }
            });

            window.addEventListener('resize', function() {
                if (searchResultsPortal && searchResultsPortal.style.display === 'block') {
                    updateSearchResultsPosition();
                }
            });

            // Handle Enter key in search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.value.trim()) {
                        document.getElementById('globalSearchForm').submit();
                    }
                }
            });

            // Show search results dropdown
            function showSearchResults() {
                if (searchResultsPortal && searchResults) {
                    updateSearchResultsPosition();
                    searchResultsPortal.style.display = 'block';
                }
            }

            // Update search results position
            function updateSearchResultsPosition() {
                if (searchResultsPortal && searchResults && searchInput) {
                    const searchInputRect = searchInput.getBoundingClientRect();
                    searchResults.style.top = (searchInputRect.bottom + window.scrollY + 8) + 'px';
                    searchResults.style.left = (searchInputRect.left + window.scrollX) + 'px';
                    searchResults.style.width = searchInputRect.width + 'px';
                }
            }

            // Hide search results dropdown
            function hideSearchResults() {
                if (searchResultsPortal) {
                    searchResultsPortal.style.display = 'none';
                }
            }

            // Perform AJAX search
            function performSearch(query) {
                console.log('Performing search for:', query);
                
                fetch(`/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Search response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Search response data:', data);
                    if (data.success) {
                        displaySearchResults(data.results);
                        showSearchResults();
                    } else {
                        displayNoResults();
                        showSearchResults();
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    displayError();
                    showSearchResults();
                });
            }

            // Show loading state
            function showLoadingState() {
                searchResultsContent.innerHTML = `
                    <div class="search-loading">
                        <i class="fas fa-spinner"></i>
                        Searching...
                    </div>
                `;
                showSearchResults();
            }

            // Display search results
            function displaySearchResults(results) {
                if (results.length === 0) {
                    displayNoResults();
                    return;
                }

                let html = '';
                
                // Show top 8 results for better UI
                const topResults = results.slice(0, 8);
                
                topResults.forEach(result => {
                    const iconClass = getIconClass(result.type);
                    html += `
                        <a href="${result.url}" class="search-result-item">
                            <div class="search-result-icon ${iconClass}">
                                <i class="${result.icon}"></i>
                            </div>
                            <div class="search-result-content">
                                <div class="search-result-title">${result.title}</div>
                                <div class="search-result-description">${result.description}</div>
                            </div>
                            <div class="search-result-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    `;
                });

                // Add "View all results" link if there are more results
                if (results.length > 8) {
                    html += `
                        <a href="/search/results?q=${encodeURIComponent(searchInput.value)}" class="search-view-all">
                            <i class="fas fa-search me-1"></i>
                            View all ${results.length} results
                        </a>
                    `;
                }

                searchResultsContent.innerHTML = html;
            }

            // Display no results message
            function displayNoResults() {
                searchResultsContent.innerHTML = `
                    <div class="search-no-results">
                        <i class="fas fa-search"></i>
                        <div>No results found for "${searchInput.value}"</div>
                        <small>Try a different search term</small>
                    </div>
                `;
            }

            // Display error message
            function displayError() {
                searchResultsContent.innerHTML = `
                    <div class="search-no-results">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Search failed</div>
                        <small>Please try again</small>
                    </div>
                `;
            }

            // Get icon class based on result type
            function getIconClass(type) {
                const iconMap = {
                    'asset': 'asset',
                    'user': 'user',
                    'category': 'category',
                    'department': 'department',
                    'vendor': 'vendor',
                    'assignment': 'assignment',
                    'maintenance': 'maintenance',
                    'disposal': 'disposal'
                };
                return iconMap[type] || 'asset';
            }
        });
    </script>

    <script src="{{ asset('js/searchable-dropdown.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>

