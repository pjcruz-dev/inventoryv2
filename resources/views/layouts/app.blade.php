<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Inventory Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
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
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
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
                            
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>ASSET MANAGEMENT</span>
                                </h6>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                                    <i class="fas fa-boxes"></i>
                                    All Assets
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('computers.*') ? 'active' : '' }}" href="{{ route('computers.index') }}">
                                    <i class="fas fa-desktop"></i>
                                    Computers
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('monitors.*') ? 'active' : '' }}" href="{{ route('monitors.index') }}">
                                    <i class="fas fa-tv"></i>
                                    Monitors
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('printers.*') ? 'active' : '' }}" href="{{ route('printers.index') }}">
                                    <i class="fas fa-print"></i>
                                    Printers
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>ORGANIZATION</span>
                                </h6>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i>
                                    Users
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                    <i class="fas fa-building"></i>
                                    Departments
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
                                    <i class="fas fa-truck"></i>
                                    Vendors
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">
                                    <span>SYSTEM</span>
                                </h6>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="{{ route('logs.index') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    Activity Logs
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}" href="{{ route('timeline.index') }}">
                                    <i class="fas fa-history"></i>
                                    Asset Timeline
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                    <i class="fas fa-user-tag"></i>
                                    Roles
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                                    <i class="fas fa-key"></i>
                                    Permissions
                                </a>
                            </li>
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
                        <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                @yield('page-actions')
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> {{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><a class="dropdown-item" href="#">Settings</a></li>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
    
    @yield('scripts')
</body>
</html>
