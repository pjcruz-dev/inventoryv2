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
    
    <!-- Dashboard Enhanced Styles - High Priority -->
    @if(request()->routeIs('dashboard'))
    <style>
        /* Dashboard Theme Colors - Matching Sidebar */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            --primary-color: #667eea !important;
            --secondary-color: #764ba2 !important;
            --success-color: #10b981 !important;
            --warning-color: #f59e0b !important;
            --danger-color: #ef4444 !important;
            --info-color: #06b6d4 !important;
            --text-primary: #344767 !important;
            --text-secondary: #67748e !important;
            --bg-light: #f8f9fa !important;
            --bg-white: #ffffff !important;
            --border-light: #e9ecef !important;
        }


        /* Enhanced Dashboard Cards */
        .dashboard-card {
            background: var(--bg-white) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
            margin-bottom: 1.5rem !important;
        }

        .dashboard-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: var(--primary-gradient) !important;
        }

        .dashboard-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        }

        /* Dashboard Hero Section */
        .dashboard-hero {
            background: var(--primary-gradient) !important;
            border-radius: 20px !important;
            padding: 2rem !important;
            margin-bottom: 2rem !important;
            color: white !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .dashboard-hero::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') !important;
            opacity: 0.3 !important;
        }

        .dashboard-hero-content {
            position: relative !important;
            z-index: 2 !important;
        }

        /* Metric Cards */
        .metric-card {
            background: var(--bg-white) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
            text-align: center !important;
        }

        .metric-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: var(--primary-gradient) !important;
        }

        .metric-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .metric-icon {
            width: 60px !important;
            height: 60px !important;
            border-radius: 50% !important;
            background: var(--primary-gradient) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 auto 1rem auto !important;
            color: white !important;
            font-size: 1.5rem !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }

        .metric-value {
            font-size: 2rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.5rem !important;
        }

        .metric-label {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            margin-bottom: 0.5rem !important;
            font-weight: 500 !important;
        }

        .metric-change {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: var(--text-secondary) !important;
        }

        .metric-change.positive {
            color: var(--success-color) !important;
        }

        .metric-change.negative {
            color: var(--danger-color) !important;
        }

        /* Quick Action Buttons */
        .quick-action-btn {
            background: var(--bg-white) !important;
            border-radius: 12px !important;
            padding: 1.5rem 1rem !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            color: var(--text-primary) !important;
            display: block !important;
            text-align: center !important;
            position: relative !important;
            overflow: visible !important;
        }

        .quick-action-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
            color: var(--text-primary) !important;
            text-decoration: none !important;
        }

        .quick-action-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: var(--primary-gradient) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 auto 1rem auto !important;
            color: white !important;
            font-size: 1.25rem !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }

        .quick-action-text {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.5rem !important;
        }

        /* Enhanced Quick Action Badges */
        .quick-action-badge {
            position: absolute !important;
            top: -5px !important;
            right: -5px !important;
            background: var(--primary-color) !important;
            color: white !important;
            border-radius: 50% !important;
            width: 20px !important;
            height: 20px !important;
            font-size: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 600 !important;
            border: 2px solid white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .quick-action-btn:hover .quick-action-badge {
            transform: scale(1.1) !important;
            transition: transform 0.2s ease !important;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .status-badge.deployed {
            background: rgba(16, 185, 129, 0.1) !important;
            color: var(--success-color) !important;
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1) !important;
            color: var(--warning-color) !important;
        }

        .status-badge.problematic {
            background: rgba(239, 68, 68, 0.1) !important;
            color: var(--danger-color) !important;
        }

        /* Recent Asset Icons */
        .recent-asset-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            background: var(--primary-gradient) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }

        /* System Status Indicators */
        .status-indicator {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            display: inline-block !important;
            animation: pulse 2s infinite !important;
        }

        /* Enhanced Monthly Status Cards */
        .monthly-analysis .badge {
            font-size: 0.75rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        /* Weekly Movement Chart Toggle */
        .collapse {
            transition: all 0.3s ease !important;
        }

        /* Enhanced Empty States */
        .text-center.py-4 i {
            opacity: 0.3 !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .metric-card, .dashboard-card {
            animation: fadeInUp 0.6s ease-out !important;
        }

        /* Clickable Numbers */
        .clickable-number {
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .clickable-number:hover {
            color: var(--primary-color) !important;
            transform: scale(1.05) !important;
        }

        /* Enhanced Deployment Status Styles */
        .deployment-status-card {
            position: relative !important;
            overflow: hidden !important;
        }

        .deployment-status-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .deployment-status-badge {
            padding: 0.25rem 0.75rem !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
        }

        .deployment-status-badge.high {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }

        .deployment-status-badge.medium {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .deployment-status-badge.low {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        .deployment-progress-container {
            display: flex !important;
            justify-content: center !important;
            margin: 2rem 0 !important;
        }

        .progress-ring-wrapper {
            position: relative !important;
        }

        .progress-ring-content {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            text-align: center !important;
        }

        .deployment-percentage {
            font-size: 2rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            line-height: 1 !important;
        }

        .deployment-label {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            margin-bottom: 0.25rem !important;
        }

        .deployment-trend {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.25rem !important;
        }

        .deployment-stats-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 1rem !important;
            margin: 1.5rem 0 !important;
        }

        .stat-item {
            display: flex !important;
            align-items: center !important;
            padding: 1rem !important;
            border-radius: 12px !important;
            background: rgba(102, 126, 234, 0.05) !important;
            border: 1px solid rgba(102, 126, 234, 0.1) !important;
            transition: all 0.2s ease !important;
        }

        .stat-item:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15) !important;
        }

        .stat-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-right: 0.75rem !important;
            font-size: 1rem !important;
        }

        .stat-item.deployed .stat-icon {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }

        .stat-item.pending .stat-icon {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .stat-item.new-arrival {
            background: rgba(6, 182, 212, 0.1) !important;
            border: 1px solid rgba(6, 182, 212, 0.2) !important;
        }

        .stat-item.new-arrival .stat-icon {
            background: #06b6d4 !important;
            color: white !important;
        }

        .stat-content {
            flex: 1 !important;
        }

        .stat-number {
            font-size: 1.5rem !important;
            font-weight: 800 !important;
            line-height: 1 !important;
        }

        .stat-item.deployed .stat-number {
            color: #10b981 !important;
        }

        .stat-item.new-arrival .stat-number {
            color: #06b6d4 !important;
        }

        .stat-item.pending .stat-number {
            color: #f59e0b !important;
        }

        .stat-label {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.25rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .stat-item.deployed .stat-label {
            color: #10b981 !important;
        }

        .stat-item.new-arrival .stat-label {
            color: #06b6d4 !important;
        }

        .stat-item.pending .stat-label {
            color: #f59e0b !important;
        }

        .stat-change {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }

        .stat-change.positive {
            color: #10b981 !important;
        }

        .stat-change.neutral {
            color: #6b7280 !important;
        }

        .stat-change.info {
            color: #06b6d4 !important;
        }

        .deployment-action {
            margin-top: 1.5rem !important;
        }

        /* Enhanced Recent Assets Styles */
        .recent-assets-card {
            position: relative !important;
        }

        .recent-assets-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .recent-assets-count {
            display: flex !important;
            align-items: center !important;
        }

        .recent-asset-item {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 1.25rem !important;
            margin-bottom: 1rem !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.7) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }

        .recent-asset-item:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            background: white !important;
        }

        .asset-main-info {
            display: flex !important;
            align-items: center !important;
            flex: 1 !important;
        }

        .asset-icon-wrapper {
            position: relative !important;
            margin-right: 1rem !important;
        }

        .asset-type-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.25rem !important;
            color: white !important;
            position: relative !important;
        }

        .asset-type-icon.computer {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        }

        .asset-type-icon.monitor {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        }

        .asset-type-icon.printer {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .asset-type-icon.phone {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .asset-type-icon.tablet {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .asset-type-icon.default {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        }

        .asset-priority-indicator {
            position: absolute !important;
            top: -2px !important;
            right: -2px !important;
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            border: 2px solid white !important;
        }

        .asset-priority-indicator.high {
            background: #ef4444 !important;
        }

        .asset-priority-indicator.medium {
            background: #f59e0b !important;
        }

        .asset-priority-indicator.low {
            background: #10b981 !important;
        }

        .asset-details {
            flex: 1 !important;
        }

        .asset-name {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.25rem !important;
            line-height: 1.3 !important;
        }

        .asset-meta {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-size: 0.8rem !important;
            color: var(--text-secondary) !important;
            margin-bottom: 0.5rem !important;
        }

        .asset-divider {
            opacity: 0.5 !important;
        }

        .asset-status-row {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }

        .asset-status-badge {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.25rem 0.75rem !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
        }

        .asset-status-badge.deployed {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }

        .asset-status-badge.pending {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .asset-status-badge.problematic {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        .asset-assignment {
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
            font-size: 0.75rem !important;
            color: var(--text-secondary) !important;
        }

        .asset-actions {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            gap: 0.5rem !important;
        }

        .asset-actions-group {
            display: flex !important;
            gap: 0.5rem !important;
        }

        .recent-assets-footer {
            margin-top: 1rem !important;
            padding-top: 1rem !important;
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .recent-assets-empty {
            text-align: center !important;
            padding: 3rem 1rem !important;
        }

        .empty-state-icon {
            width: 80px !important;
            height: 80px !important;
            border-radius: 50% !important;
            background: rgba(102, 126, 234, 0.1) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 auto 1.5rem auto !important;
            font-size: 2rem !important;
            color: #667eea !important;
        }

        .empty-state-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.5rem !important;
        }

        .empty-state-description {
            color: var(--text-secondary) !important;
            margin-bottom: 1.5rem !important;
        }

        /* Enhanced Monthly Overview Styles */
        .monthly-overview-card {
            position: relative !important;
        }

        .monthly-overview-icon {
            width: 48px !important;
            height: 48px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.25rem !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }

        .monthly-filter-btn {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .monthly-filter-btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3) !important;
        }

        .monthly-period-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%) !important;
            border-radius: 20px !important;
            padding: 2rem !important;
            margin-bottom: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .monthly-period-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb) !important;
            border-radius: 20px 20px 0 0 !important;
        }

        .monthly-period-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .month-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 1rem !important;
        }

        .month-name {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            margin: 0 !important;
        }

        .month-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 12px !important;
            text-align: center !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        }

        .month-badge .total-activities {
            display: block !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
        }

        .month-badge small {
            font-size: 0.75rem !important;
            opacity: 0.9 !important;
        }

        .monthly-summary {
            margin-bottom: 1.5rem !important;
        }

        .monthly-chart-section {
            margin-bottom: 2rem !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .chart-container {
            position: relative !important;
            height: 300px !important;
            margin-bottom: 1.5rem !important;
            background: white !important;
            border-radius: 12px !important;
            padding: 1rem !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
        }

        .monthly-chart-canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .chart-legend {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 1rem !important;
            justify-content: center !important;
        }

        .legend-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            background: white !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .legend-item:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15) !important;
        }

        .legend-color {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            flex-shrink: 0 !important;
        }

        .legend-item i {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
        }

        .legend-item span:first-of-type {
            color: #374151 !important;
            min-width: 80px !important;
        }

        .legend-count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 12px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            min-width: 24px !important;
            text-align: center !important;
        }

        .health-indicator {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            margin-bottom: 1rem !important;
        }

        .health-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1rem !important;
            animation: heartbeat 2s ease-in-out infinite !important;
        }

        .health-icon.excellent {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
        }

        .health-icon.good {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3) !important;
        }

        .health-icon.moderate {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3) !important;
        }

        .health-icon.poor {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .health-info {
            display: flex !important;
            flex-direction: column !important;
        }

        .health-score {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            line-height: 1 !important;
        }

        .health-label {
            font-size: 0.75rem !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .activity-bar {
            width: 100% !important;
            height: 6px !important;
            background: rgba(0, 0, 0, 0.1) !important;
            border-radius: 3px !important;
            overflow: hidden !important;
            position: relative !important;
        }

        .activity-fill {
            height: 100% !important;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb) !important;
            border-radius: 3px !important;
            transition: width 0.8s ease !important;
            position: relative !important;
        }

        .activity-pulse {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent) !important;
            animation: shimmer 2s infinite !important;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .monthly-status-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
            gap: 1rem !important;
        }

        .status-item {
            background: white !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .status-item:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .status-item.success {
            border-left: 4px solid #10b981 !important;
        }

        .status-item.primary {
            border-left: 4px solid #3b82f6 !important;
        }

        .status-item.info {
            border-left: 4px solid #06b6d4 !important;
        }

        .status-item.warning {
            border-left: 4px solid #f59e0b !important;
        }

        .status-item.danger {
            border-left: 4px solid #ef4444 !important;
        }

        .status-icon-wrapper {
            position: relative !important;
            display: inline-block !important;
            margin-bottom: 1rem !important;
        }

        .status-icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.25rem !important;
            position: relative !important;
            z-index: 2 !important;
        }

        .status-item.success .status-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .status-item.primary .status-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        }

        .status-item.info .status-icon {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
        }

        .status-item.warning .status-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .status-item.danger .status-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .status-glow {
            position: absolute !important;
            top: -2px !important;
            left: -2px !important;
            right: -2px !important;
            bottom: -2px !important;
            border-radius: 14px !important;
            opacity: 0.3 !important;
            filter: blur(4px) !important;
            z-index: 1 !important;
        }

        .status-item.success .status-glow {
            background: #10b981 !important;
        }

        .status-item.primary .status-glow {
            background: #3b82f6 !important;
        }

        .status-item.info .status-glow {
            background: #06b6d4 !important;
        }

        .status-item.warning .status-glow {
            background: #f59e0b !important;
        }

        .status-item.danger .status-glow {
            background: #ef4444 !important;
        }

        .status-content {
            margin-bottom: 1rem !important;
        }

        .status-count {
            font-size: 2rem !important;
            font-weight: 800 !important;
            color: #1f2937 !important;
            line-height: 1 !important;
            margin-bottom: 0.25rem !important;
        }

        .status-label {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 0.25rem !important;
        }

        .status-percentage {
            font-size: 0.75rem !important;
            color: #9ca3af !important;
        }

        .status-progress {
            margin-bottom: 0.75rem !important;
        }

        .progress-track {
            width: 100% !important;
            height: 4px !important;
            background: rgba(0, 0, 0, 0.1) !important;
            border-radius: 2px !important;
            overflow: hidden !important;
        }

        .progress-fill {
            height: 100% !important;
            border-radius: 2px !important;
            transition: width 0.8s ease !important;
        }

        .status-item.success .progress-fill {
            background: linear-gradient(90deg, #10b981, #059669) !important;
        }

        .status-item.primary .progress-fill {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8) !important;
        }

        .status-item.info .progress-fill {
            background: linear-gradient(90deg, #06b6d4, #0891b2) !important;
        }

        .status-item.warning .progress-fill {
            background: linear-gradient(90deg, #f59e0b, #d97706) !important;
        }

        .status-item.danger .progress-fill {
            background: linear-gradient(90deg, #ef4444, #dc2626) !important;
        }

        .status-trend {
            display: flex !important;
            justify-content: flex-end !important;
            font-size: 0.875rem !important;
        }

        .monthly-empty-state {
            text-align: center !important;
            padding: 3rem 2rem !important;
        }

        .monthly-empty-state.global {
            padding: 4rem 2rem !important;
        }

        .empty-icon {
            width: 80px !important;
            height: 80px !important;
            margin: 0 auto 1.5rem !important;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #9ca3af !important;
            font-size: 2rem !important;
            position: relative !important;
        }

        .empty-pulse {
            position: absolute !important;
            top: -4px !important;
            left: -4px !important;
            right: -4px !important;
            bottom: -4px !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 50% !important;
            animation: pulse 2s infinite !important;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.1);
                opacity: 0;
            }
        }

        .empty-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 0.5rem !important;
        }

        .empty-description {
            color: #6b7280 !important;
            margin-bottom: 2rem !important;
            font-size: 0.875rem !important;
        }

        .empty-actions {
            display: flex !important;
            gap: 1rem !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .monthly-status-grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }

            .monthly-period-card {
                padding: 1.5rem !important;
            }

            .month-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
            }

            .health-indicator {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.5rem !important;
            }

            .empty-actions {
                flex-direction: column !important;
                align-items: center !important;
            }

            .chart-container {
                height: 250px !important;
                padding: 0.75rem !important;
            }

            .chart-legend {
                gap: 0.5rem !important;
            }

            .legend-item {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.8rem !important;
            }

            .legend-item span:first-of-type {
                min-width: 60px !important;
            }
        }

        @media (max-width: 480px) {
            .chart-container {
                height: 200px !important;
            }

            .monthly-chart-section {
                padding: 1rem !important;
            }

            .legend-item {
                flex-direction: column !important;
                text-align: center !important;
                gap: 0.25rem !important;
                padding: 0.5rem !important;
            }

            .legend-item span:first-of-type {
                min-width: auto !important;
            }
        }

        .monthly-overview-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .monthly-filter-btn {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .monthly-filter-menu {
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            padding: 0.5rem !important;
        }

        .monthly-filter-menu .dropdown-item {
            border-radius: 8px !important;
            padding: 0.75rem 1rem !important;
            margin-bottom: 0.25rem !important;
            transition: all 0.2s ease !important;
        }

        .monthly-filter-menu .dropdown-item:hover,
        .monthly-filter-menu .dropdown-item.active {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
        }

        .monthly-period-card {
            background: rgba(255, 255, 255, 0.7) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }

        .monthly-period-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            background: white !important;
        }

        .monthly-period-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            margin-bottom: 1.5rem !important;
        }

        .month-name {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.5rem !important;
        }

        .monthly-summary {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.25rem !important;
        }

        .total-activities {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            font-weight: 500 !important;
        }

        .activity-trend {
            display: flex !important;
            align-items: center !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
        }

        .health-score.good {
            color: #10b981 !important;
        }

        .health-score.moderate {
            color: #f59e0b !important;
        }

        .health-score.poor {
            color: #ef4444 !important;
        }

        .monthly-visual-indicator {
            display: flex !important;
            align-items: flex-end !important;
        }

        .activity-bar {
            width: 100px !important;
            height: 8px !important;
            background: rgba(102, 126, 234, 0.1) !important;
            border-radius: 4px !important;
            overflow: hidden !important;
        }

        .activity-fill {
            height: 100% !important;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 4px !important;
            transition: width 0.8s ease !important;
        }

        .monthly-status-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)) !important;
            gap: 1rem !important;
        }

        .status-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            padding: 1rem !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .status-item:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .status-item.success {
            background: rgba(16, 185, 129, 0.05) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        .status-item.danger {
            background: rgba(239, 68, 68, 0.05) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
        }

        .status-item.warning {
            background: rgba(245, 158, 11, 0.05) !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        .status-item.info {
            background: rgba(6, 182, 212, 0.05) !important;
            border-color: rgba(6, 182, 212, 0.2) !important;
        }

        .status-item.secondary {
            background: rgba(108, 117, 125, 0.05) !important;
            border-color: rgba(108, 117, 125, 0.2) !important;
        }

        .status-item.primary {
            background: rgba(13, 110, 253, 0.05) !important;
            border-color: rgba(13, 110, 253, 0.2) !important;
        }

        .status-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 0.75rem !important;
            font-size: 1rem !important;
        }

        .status-item.success .status-icon {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }

        .status-item.danger .status-icon {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        .status-item.warning .status-icon {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .status-item.info .status-icon {
            background: rgba(6, 182, 212, 0.1) !important;
            color: #06b6d4 !important;
        }

        .status-item.secondary .status-icon {
            background: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
        }

        .status-item.primary .status-icon {
            background: rgba(13, 110, 253, 0.1) !important;
            color: #0d6efd !important;
        }

        .status-content {
            text-align: center !important;
            flex: 1 !important;
        }

        .status-count {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            line-height: 1 !important;
            margin-bottom: 0.25rem !important;
        }

        .status-label {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: var(--text-secondary) !important;
            margin-bottom: 0.25rem !important;
        }

        .status-percentage {
            font-size: 0.7rem !important;
            color: var(--text-secondary) !important;
        }

        .status-progress {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 3px !important;
            background: rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
        }

        .progress-bar {
            height: 100% !important;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%) !important;
            transition: width 0.8s ease !important;
        }

        .monthly-empty-state {
            text-align: center !important;
            padding: 3rem 1rem !important;
        }

        /* Enhanced Weekly Analysis Styles */
        .weekly-analysis-card {
            position: relative !important;
        }

        .weekly-analysis-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .weekly-chart-toggle {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .weekly-chart-container {
            background: rgba(255, 255, 255, 0.8) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .chart-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 1rem !important;
        }

        .chart-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin: 0 !important;
        }

        .chart-legend {
            display: flex !important;
            gap: 1rem !important;
        }

        .legend-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-size: 0.75rem !important;
            color: var(--text-secondary) !important;
        }

        .chart-wrapper {
            height: 250px !important;
            position: relative !important;
        }

        .weekly-period-card {
            background: rgba(255, 255, 255, 0.7) !important;
            border-radius: 16px !important;
            padding: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease !important;
        }

        .weekly-period-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            background: white !important;
        }

        .weekly-period-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            margin-bottom: 1.5rem !important;
        }

        .week-month-name {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.5rem !important;
        }

        .weekly-summary {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.25rem !important;
        }

        .total-movements {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            font-weight: 500 !important;
        }

        .movement-intensity {
            display: flex !important;
            align-items: center !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
        }

        .intensity-level.high {
            color: #ef4444 !important;
        }

        .intensity-level.medium {
            color: #f59e0b !important;
        }

        .intensity-level.low {
            color: #10b981 !important;
        }

        .movement-sparkline {
            display: flex !important;
            align-items: flex-end !important;
            gap: 2px !important;
            height: 40px !important;
            width: 120px !important;
        }

        .spark-bar {
            flex: 1 !important;
            background: linear-gradient(to top, #8b5cf6, #a855f7) !important;
            border-radius: 2px 2px 0 0 !important;
            min-height: 4px !important;
            transition: height 0.3s ease !important;
        }

        .weekly-table-container {
            overflow-x: auto !important;
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .weekly-table-wrapper {
            min-width: 100% !important;
        }

        .weekly-table {
            width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
        }

        .weekly-table th,
        .weekly-table td {
            padding: 0.75rem 0.5rem !important;
            border: none !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        .week-header {
            background: rgba(102, 126, 234, 0.05) !important;
            color: var(--text-primary) !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            text-align: left !important;
            position: sticky !important;
            left: 0 !important;
            z-index: 10 !important;
        }

        .status-header {
            background: rgba(102, 126, 234, 0.05) !important;
            color: var(--text-primary) !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .status-header.success {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }

        .status-header.danger {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        .status-header.warning {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .status-header.info {
            background: rgba(6, 182, 212, 0.1) !important;
            color: #06b6d4 !important;
        }

        .status-header.secondary {
            background: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
        }

        .status-header.primary {
            background: rgba(13, 110, 253, 0.1) !important;
            color: #0d6efd !important;
        }

        .status-header-content {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }

        .status-header-content i {
            font-size: 0.875rem !important;
        }

        .status-header-content small {
            font-size: 0.65rem !important;
            opacity: 0.8 !important;
        }

        .weekly-row {
            transition: all 0.2s ease !important;
        }

        .weekly-row.has-activity {
            background: rgba(255, 255, 255, 0.5) !important;
        }

        .weekly-row.has-activity:hover {
            background: rgba(102, 126, 234, 0.05) !important;
        }

        .weekly-row.no-activity {
            opacity: 0.6 !important;
        }

        .week-cell {
            text-align: left !important;
            background: rgba(102, 126, 234, 0.02) !important;
            position: sticky !important;
            left: 0 !important;
            z-index: 5 !important;
        }

        .week-info {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .week-name {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
        }

        .week-badge {
            background: #667eea !important;
            color: white !important;
            border-radius: 10px !important;
            padding: 0.125rem 0.5rem !important;
            font-size: 0.65rem !important;
            font-weight: 600 !important;
        }

        .status-cell {
            position: relative !important;
        }

        .movement-link {
            display: inline-block !important;
            padding: 0.25rem 0.75rem !important;
            border-radius: 12px !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            transition: all 0.2s ease !important;
            border: 1px solid transparent !important;
        }

        .status-cell.success .movement-link {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        .status-cell.danger .movement-link {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
        }

        .status-cell.warning .movement-link {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        .status-cell.info .movement-link {
            background: rgba(6, 182, 212, 0.1) !important;
            color: #06b6d4 !important;
            border-color: rgba(6, 182, 212, 0.2) !important;
        }

        .status-cell.secondary .movement-link {
            background: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
            border-color: rgba(108, 117, 125, 0.2) !important;
        }

        .status-cell.primary .movement-link {
            background: rgba(13, 110, 253, 0.1) !important;
            color: #0d6efd !important;
            border-color: rgba(13, 110, 253, 0.2) !important;
        }

        .movement-link:hover {
            transform: scale(1.05) !important;
            text-decoration: none !important;
        }

        .no-movement {
            color: #d1d5db !important;
            font-size: 0.875rem !important;
        }

        .weekly-empty-state {
            text-align: center !important;
            padding: 3rem 1rem !important;
        }

        /* Enhanced Quick Actions Styles */
        .quick-actions-card {
            position: relative !important;
        }

        .quick-actions-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .quick-actions-customize-btn {
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
        }

        .quick-actions-menu {
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            padding: 0.5rem !important;
        }

        .quick-actions-menu .dropdown-item {
            border-radius: 8px !important;
            padding: 0.75rem 1rem !important;
            margin-bottom: 0.25rem !important;
            transition: all 0.2s ease !important;
        }

        .quick-actions-menu .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
        }

        .quick-actions-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
            gap: 1.5rem !important;
            margin-bottom: 2rem !important;
        }

        .quick-action-item {
            position: relative !important;
        }

        .enhanced-quick-action-btn {
            display: flex !important;
            align-items: center !important;
            padding: 1.5rem !important;
            background: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 16px !important;
            text-decoration: none !important;
            color: var(--text-primary) !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
            height: 100% !important;
        }

        .enhanced-quick-action-btn:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
            background: white !important;
            color: var(--text-primary) !important;
            text-decoration: none !important;
        }

        .enhanced-quick-action-btn::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%) !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
        }

        .enhanced-quick-action-btn:hover::before {
            opacity: 1 !important;
        }

        .action-icon-wrapper {
            position: relative !important;
            margin-right: 1rem !important;
            flex-shrink: 0 !important;
        }

        .action-icon {
            width: 60px !important;
            height: 60px !important;
            border-radius: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            color: white !important;
            position: relative !important;
            z-index: 2 !important;
        }

        .action-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .action-icon.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .action-icon.info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
        }

        .action-icon.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .action-icon.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .action-icon.secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        }

        .action-glow {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 80px !important;
            height: 80px !important;
            border-radius: 50% !important;
            background: rgba(102, 126, 234, 0.1) !important;
            opacity: 0 !important;
            transition: all 0.3s ease !important;
            z-index: 1 !important;
        }

        .enhanced-quick-action-btn:hover .action-glow {
            opacity: 1 !important;
            transform: translate(-50%, -50%) scale(1.2) !important;
        }

        .action-content {
            flex: 1 !important;
            margin-right: 1rem !important;
        }

        .action-title {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.25rem !important;
        }

        .action-description {
            font-size: 0.8rem !important;
            color: var(--text-secondary) !important;
            margin: 0 !important;
        }

        .action-badge {
            position: absolute !important;
            top: -5px !important;
            right: -5px !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 12px !important;
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            color: white !important;
            z-index: 3 !important;
        }

        .action-badge.new {
            background: #10b981 !important;
        }

        .action-badge.count {
            background: #667eea !important;
        }

        .action-badge.pdf {
            background: #ef4444 !important;
        }

        .action-badge.urgent {
            background: #f59e0b !important;
        }

        .action-badge.pending {
            background: #06b6d4 !important;
        }

        .action-arrow {
            color: var(--text-secondary) !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
        }

        .enhanced-quick-action-btn:hover .action-arrow {
            color: #667eea !important;
            transform: translateX(5px) !important;
        }

        .quick-actions-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
            padding-top: 1.5rem !important;
        }

        .footer-content {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }

        .footer-stats .stat-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
        }

        .stat-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            background: rgba(102, 126, 234, 0.1) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #667eea !important;
            font-size: 1rem !important;
        }

        .stat-info {
            display: flex !important;
            flex-direction: column !important;
        }

        .stat-label {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
        }

        .stat-value {
            font-size: 0.75rem !important;
            color: var(--text-secondary) !important;
        }

        .footer-actions {
            display: flex !important;
            gap: 0.5rem !important;
        }

        /* Enhanced System Status Styles */
        .system-status-card {
            position: relative !important;
        }

        .system-status-icon {
            width: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.2rem !important;
            margin-right: 1rem !important;
        }

        .system-refresh-btn {
            border-radius: 8px !important;
            padding: 0.5rem !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .system-refresh-btn:hover {
            animation: spin 1s linear infinite !important;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .system-status-overview {
            margin-bottom: 2rem !important;
        }

        .status-summary {
            margin-bottom: 2rem !important;
        }

        .overall-status {
            display: flex !important;
            align-items: center !important;
            padding: 1.5rem !important;
            border-radius: 16px !important;
            background: rgba(16, 185, 129, 0.05) !important;
            border: 1px solid rgba(16, 185, 129, 0.2) !important;
        }

        .overall-status.healthy {
            background: rgba(16, 185, 129, 0.05) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        .status-indicator-large {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.5rem !important;
            margin-right: 1rem !important;
            animation: pulse 2s infinite !important;
        }

        .status-info {
            flex: 1 !important;
        }

        .status-title {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.25rem !important;
        }

        .status-description {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            margin: 0 !important;
        }

        .system-components {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .component-item {
            display: flex !important;
            align-items: center !important;
            padding: 1rem !important;
            background: rgba(255, 255, 255, 0.7) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
        }

        .component-item:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
            background: white !important;
        }

        .component-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            background: rgba(102, 126, 234, 0.1) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #667eea !important;
            font-size: 1rem !important;
            margin-right: 1rem !important;
        }

        .component-info {
            flex: 1 !important;
        }

        .component-name {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.125rem !important;
        }

        .component-description {
            font-size: 0.75rem !important;
            color: var(--text-secondary) !important;
            margin: 0 !important;
        }

        .component-status {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .status-indicator {
            width: 10px !important;
            height: 10px !important;
            border-radius: 50% !important;
            animation: pulse 2s infinite !important;
        }

        .status-indicator.healthy {
            background: #10b981 !important;
        }

        .status-indicator.warning {
            background: #f59e0b !important;
        }

        .status-indicator.danger {
            background: #ef4444 !important;
        }

        .status-text {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
        }

        .system-status-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
            padding-top: 1.5rem !important;
        }

        .performance-metrics {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 1rem !important;
            margin-bottom: 1rem !important;
        }

        .metric-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem !important;
            background: rgba(255, 255, 255, 0.7) !important;
            border-radius: 10px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .metric-icon {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            background: rgba(102, 126, 234, 0.1) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #667eea !important;
            font-size: 0.875rem !important;
        }

        .metric-info {
            display: flex !important;
            flex-direction: column !important;
        }

        .metric-label {
            font-size: 0.7rem !important;
            color: var(--text-secondary) !important;
            margin-bottom: 0.125rem !important;
        }

        .metric-value {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: var(--text-primary) !important;
        }

        .last-updated {
            text-align: center !important;
            padding-top: 1rem !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        /* Enhanced Dashboard Hero Styles */
        .enhanced-dashboard-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
            border-radius: 24px !important;
            padding: 3rem !important;
            margin-bottom: 2rem !important;
            position: relative !important;
            overflow: hidden !important;
            min-height: 280px !important;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3) !important;
        }

        .hero-background-pattern {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="hero-grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23hero-grid)"/></svg>') !important;
            opacity: 0.3 !important;
            z-index: 1 !important;
        }

        .hero-content-wrapper {
            position: relative !important;
            z-index: 2 !important;
        }

        .hero-text-section {
            display: flex !important;
            align-items: flex-start !important;
            gap: 1.5rem !important;
        }

        .hero-icon-wrapper {
            width: 80px !important;
            height: 80px !important;
            border-radius: 20px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 2rem !important;
            flex-shrink: 0 !important;
        }

        .hero-text-content {
            flex: 1 !important;
        }

        .hero-title {
            font-size: 3rem !important;
            font-weight: 800 !important;
            margin-bottom: 1rem !important;
            line-height: 1.1 !important;
        }

        .title-main {
            color: white !important;
            display: block !important;
        }

        .title-accent {
            color: rgba(255, 255, 255, 0.8) !important;
            display: block !important;
            font-size: 0.8em !important;
            margin-top: 0.25rem !important;
        }

        .hero-description {
            font-size: 1.125rem !important;
            color: rgba(255, 255, 255, 0.9) !important;
            margin-bottom: 2rem !important;
            line-height: 1.6 !important;
        }

        .hero-stats {
            display: flex !important;
            gap: 2rem !important;
            flex-wrap: wrap !important;
        }

        .stat-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease !important;
        }

        .stat-item:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px) !important;
        }

        .stat-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 0.875rem !important;
        }

        .stat-content {
            display: flex !important;
            flex-direction: column !important;
        }

        .stat-label {
            font-size: 0.75rem !important;
            color: rgba(255, 255, 255, 0.7) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 0.125rem !important;
        }

        .stat-value {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: white !important;
        }

        .hero-deployment-section {
            display: flex !important;
            justify-content: flex-end !important;
        }

        .deployment-card {
            background: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 2rem !important;
            text-align: center !important;
            width: 280px !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .deployment-card::before {
            content: '' !important;
            position: absolute !important;
            top: -50% !important;
            left: -50% !important;
            width: 200% !important;
            height: 200% !important;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transform: rotate(45deg) !important;
            animation: shimmer-hero 3s ease-in-out infinite !important;
        }

        @keyframes shimmer-hero {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .deployment-header {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.75rem !important;
            margin-bottom: 1rem !important;
        }

        .deployment-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: white !important;
            font-size: 1.125rem !important;
        }

        .deployment-label {
            font-size: 0.875rem !important;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500 !important;
        }

        .deployment-value {
            font-size: 4rem !important;
            font-weight: 900 !important;
            color: white !important;
            margin-bottom: 1.5rem !important;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
        }

        .deployment-progress {
            margin-bottom: 1.5rem !important;
        }

        .progress-track {
            width: 100% !important;
            height: 12px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            margin-bottom: 0.5rem !important;
            position: relative !important;
        }

        .progress-fill {
            height: 100% !important;
            background: linear-gradient(90deg, #00f5ff 0%, #00d4aa 100%) !important;
            border-radius: 6px !important;
            position: relative !important;
            transition: width 1s ease-in-out !important;
        }

        .progress-glow {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent) !important;
            animation: progress-glow 2s ease-in-out infinite !important;
        }

        @keyframes progress-glow {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .progress-label {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            font-size: 0.75rem !important;
        }

        .current {
            color: white !important;
            font-weight: 600 !important;
        }

        .target {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .deployment-status {
            margin-top: 1rem !important;
        }

        .status-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .status-badge.success {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #10b981 !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
        }

        .status-badge.warning {
            background: rgba(245, 158, 11, 0.2) !important;
            color: #f59e0b !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
        }

        .status-badge.danger {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
        }

        .hero-floating-elements {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            pointer-events: none !important;
            z-index: 0 !important;
        }

        .floating-icon {
            position: absolute !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 1rem !important;
            animation: float 6s ease-in-out infinite !important;
        }

        .floating-icon.icon-1 {
            top: 20% !important;
            right: 15% !important;
            animation-delay: 0s !important;
        }

        .floating-icon.icon-2 {
            top: 60% !important;
            right: 25% !important;
            animation-delay: 2s !important;
        }

        .floating-icon.icon-3 {
            top: 40% !important;
            left: 10% !important;
            animation-delay: 4s !important;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Enhanced Metric Cards Styles */
        .enhanced-metric-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.95) 100%) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 20px !important;
            padding: 1.5rem !important;
            position: relative !important;
            overflow: hidden !important;
            transition: all 0.4s ease !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            height: 100% !important;
        }

        .enhanced-metric-card:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
            border-color: rgba(102, 126, 234, 0.2) !important;
        }

        .enhanced-metric-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 4px !important;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%) !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
        }

        .enhanced-metric-card:hover::before {
            opacity: 1 !important;
        }

        .metric-card-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            margin-bottom: 1.5rem !important;
        }

        .metric-icon-wrapper {
            width: 60px !important;
            height: 60px !important;
            border-radius: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            color: white !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .metric-icon-wrapper::before {
            content: '' !important;
            position: absolute !important;
            top: -50% !important;
            left: -50% !important;
            width: 200% !important;
            height: 200% !important;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transform: rotate(45deg) !important;
            transition: all 0.6s ease !important;
            opacity: 0 !important;
        }

        .enhanced-metric-card:hover .metric-icon-wrapper::before {
            opacity: 1 !important;
            animation: shimmer 1.5s ease-in-out !important;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .metric-icon-wrapper.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .metric-icon-wrapper.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .metric-icon-wrapper.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .metric-icon-wrapper.info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
        }

        .metric-trend-indicator {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.875rem !important;
            color: white !important;
            position: relative !important;
        }

        .metric-trend-indicator.positive {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            animation: pulse-green 2s infinite !important;
        }

        .metric-trend-indicator.negative {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            animation: pulse-red 2s infinite !important;
        }

        .metric-trend-indicator.neutral {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        }

        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        }

        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        }

        .metric-card-body {
            margin-bottom: 1.5rem !important;
        }

        .metric-value {
            font-size: 2.5rem !important;
            font-weight: 800 !important;
            color: var(--text-primary) !important;
            line-height: 1 !important;
            margin-bottom: 0.5rem !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }

        .metric-label {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: var(--text-primary) !important;
            margin-bottom: 0.25rem !important;
        }

        .metric-description {
            font-size: 0.875rem !important;
            color: var(--text-secondary) !important;
            opacity: 0.8 !important;
        }

        .metric-card-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding-top: 1rem !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .metric-change {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }

        .metric-change.positive {
            color: #10b981 !important;
        }

        .metric-change.negative {
            color: #ef4444 !important;
        }

        .metric-change.neutral {
            color: #6b7280 !important;
        }

        .metric-action .btn {
            border-radius: 8px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .metric-action .btn:hover {
            transform: scale(1.1) !important;
        }

        .metric-card-bg-pattern {
            position: absolute !important;
            top: -20px !important;
            right: -20px !important;
            width: 100px !important;
            height: 100px !important;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(102, 126, 234, 0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>') !important;
            opacity: 0.3 !important;
            z-index: 0 !important;
        }

        .enhanced-metric-card:hover .metric-card-bg-pattern {
            opacity: 0.5 !important;
            transform: rotate(10deg) scale(1.1) !important;
            transition: all 0.4s ease !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .quick-action-badge {
                width: 18px !important;
                height: 18px !important;
                font-size: 9px !important;
            }
            
            .status-indicator {
                width: 10px !important;
                height: 10px !important;
            }

            .deployment-stats-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 0.75rem !important;
            }

            .recent-asset-item {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
            }

            .asset-actions {
                align-items: flex-start !important;
                width: 100% !important;
            }

            .asset-actions-group {
                width: 100% !important;
                justify-content: space-between !important;
            }

            /* Enhanced Quick Actions Responsive */
            .quick-actions-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            .enhanced-quick-action-btn {
                padding: 1rem !important;
            }

            .action-icon {
                width: 50px !important;
                height: 50px !important;
                font-size: 1.25rem !important;
            }

            .footer-content {
                flex-direction: column !important;
                gap: 1rem !important;
                align-items: flex-start !important;
            }

            .footer-actions {
                width: 100% !important;
                justify-content: space-between !important;
            }

            /* Enhanced System Status Responsive */
            .performance-metrics {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }

            .overall-status {
                flex-direction: column !important;
                text-align: center !important;
                gap: 1rem !important;
            }

            .status-indicator-large {
                margin-right: 0 !important;
                margin-bottom: 0.5rem !important;
            }

            .component-item {
                padding: 0.75rem !important;
            }

            .component-icon {
                width: 35px !important;
                height: 35px !important;
                font-size: 0.875rem !important;
            }

            /* Enhanced Metric Cards Responsive */
            .enhanced-metric-card {
                padding: 1rem !important;
            }

            .metric-icon-wrapper {
                width: 50px !important;
                height: 50px !important;
                font-size: 1.25rem !important;
            }

            .metric-value {
                font-size: 2rem !important;
            }

            .metric-label {
                font-size: 1rem !important;
            }

            .metric-description {
                font-size: 0.8rem !important;
            }

            .metric-trend-indicator {
                width: 28px !important;
                height: 28px !important;
                font-size: 0.75rem !important;
            }

            .metric-card-footer {
                flex-direction: column !important;
                gap: 0.75rem !important;
                align-items: flex-start !important;
            }

            .metric-action {
                align-self: flex-end !important;
            }

            /* Enhanced Dashboard Hero Responsive */
            .enhanced-dashboard-hero {
                padding: 2rem !important;
                min-height: auto !important;
            }

            .hero-text-section {
                flex-direction: column !important;
                gap: 1rem !important;
                align-items: center !important;
                text-align: center !important;
            }

            .hero-icon-wrapper {
                width: 60px !important;
                height: 60px !important;
                font-size: 1.5rem !important;
            }

            .hero-title {
                font-size: 2rem !important;
            }

            .hero-description {
                font-size: 1rem !important;
                margin-bottom: 1.5rem !important;
            }

            .hero-stats {
                flex-direction: column !important;
                gap: 1rem !important;
                width: 100% !important;
            }

            .stat-item {
                width: 100% !important;
                justify-content: center !important;
            }

            .deployment-card {
                width: 100% !important;
                margin-top: 2rem !important;
            }

            .deployment-value {
                font-size: 3rem !important;
            }

            .floating-icon {
                display: none !important;
            }
        }
    </style>
    @endif
    
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

