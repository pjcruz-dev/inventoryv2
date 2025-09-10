@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap');
    
    :root {
        /* Professional Color Palette - Enhanced for Better Accessibility */
        --primary-50: #f0f4ff;
        --primary-100: #e0e7ff;
        --primary-200: #c7d2fe;
        --primary-300: #a5b4fc;
        --primary-400: #818cf8;
        --primary-500: #6366f1;
        --primary-600: #4f46e5;
        --primary-700: #4338ca;
        --primary-800: #3730a3;
        --primary-900: #312e81;
        --primary-950: #1e1b4b;
        
        /* Secondary Colors */
        --secondary-50: #f8fafc;
        --secondary-100: #f1f5f9;
        --secondary-200: #e2e8f0;
        --secondary-300: #cbd5e1;
        --secondary-400: #94a3b8;
        --secondary-500: #64748b;
        --secondary-600: #475569;
        --secondary-700: #334155;
        --secondary-800: #1e293b;
        --secondary-900: #0f172a;
        
        /* Semantic Colors */
        --success-50: #f0fdf4;
        --success-100: #dcfce7;
        --success-200: #bbf7d0;
        --success-300: #86efac;
        --success-400: #4ade80;
        --success-500: #22c55e;
        --success-600: #16a34a;
        --success-700: #15803d;
        --success-800: #166534;
        --success-900: #14532d;
        
        --warning-50: #fffbeb;
        --warning-100: #fef3c7;
        --warning-200: #fde68a;
        --warning-300: #fcd34d;
        --warning-400: #fbbf24;
        --warning-500: #f59e0b;
        --warning-600: #d97706;
        --warning-700: #b45309;
        --warning-800: #92400e;
        --warning-900: #78350f;
        
        --danger-50: #fef2f2;
        --danger-100: #fee2e2;
        --danger-200: #fecaca;
        --danger-300: #fca5a5;
        --danger-400: #f87171;
        --danger-500: #ef4444;
        --danger-600: #dc2626;
        --danger-700: #b91c1c;
        --danger-800: #991b1b;
        --danger-900: #7f1d1d;
        
        --info-50: #eff6ff;
        --info-100: #dbeafe;
        --info-200: #bfdbfe;
        --info-300: #93c5fd;
        --info-400: #60a5fa;
        --info-500: #3b82f6;
        --info-600: #2563eb;
        --info-700: #1d4ed8;
        --info-800: #1e40af;
        --info-900: #1e3a8a;
        
        /* Neutral Colors */
        --neutral-50: #fafafa;
        --neutral-100: #f5f5f5;
        --neutral-200: #e5e5e5;
        --neutral-300: #d4d4d4;
        --neutral-400: #a3a3a3;
        --neutral-500: #737373;
        --neutral-600: #525252;
        --neutral-700: #404040;
        --neutral-800: #262626;
        --neutral-900: #171717;
        --neutral-950: #0a0a0a;
        
        /* Legacy Variables for Backward Compatibility */
        --soft-primary: var(--primary-600);
        --soft-primary-dark: var(--primary-700);
        --soft-primary-light: var(--primary-300);
        --soft-secondary: var(--secondary-500);
        --soft-secondary-light: var(--secondary-400);
        --soft-success: var(--success-600);
        --soft-success-light: var(--success-400);
        --soft-info: var(--info-600);
        --soft-info-light: var(--info-400);
        --soft-warning: var(--warning-600);
        --soft-warning-light: var(--warning-400);
        --soft-danger: var(--danger-600);
        --soft-danger-light: var(--danger-400);
        --soft-light: var(--neutral-50);
        --soft-dark: var(--neutral-800);
        --soft-dark-light: var(--neutral-600);
        --soft-white: #ffffff;
        
        /* Gray Scale Mapping */
        --soft-gray-50: var(--neutral-50);
        --soft-gray-100: var(--neutral-100);
        --soft-gray-200: var(--neutral-200);
        --soft-gray-300: var(--neutral-300);
        --soft-gray-400: var(--neutral-400);
        --soft-gray-500: var(--neutral-500);
        --soft-gray-600: var(--neutral-600);
        --soft-gray-700: var(--neutral-700);
        --soft-gray-800: var(--neutral-800);
        --soft-gray-900: var(--neutral-900);
        
        /* Background Colors */
        --soft-bg: var(--neutral-50);
        --soft-bg-secondary: var(--neutral-100);
        --soft-card-bg: #ffffff;
        
        /* Typography System */
        --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        --font-family-mono: 'JetBrains Mono', 'SF Mono', Monaco, Inconsolata, 'Roboto Mono', 'Source Code Pro', monospace;
        
        /* Font Sizes */
        --text-xs: 0.75rem;     /* 12px */
        --text-sm: 0.875rem;    /* 14px */
        --text-base: 1rem;      /* 16px */
        --text-lg: 1.125rem;    /* 18px */
        --text-xl: 1.25rem;     /* 20px */
        --text-2xl: 1.5rem;     /* 24px */
        --text-3xl: 1.875rem;   /* 30px */
        --text-4xl: 2.25rem;    /* 36px */
        --text-5xl: 3rem;       /* 48px */
        --text-6xl: 3.75rem;    /* 60px */
        
        /* Line Heights */
        --leading-none: 1;
        --leading-tight: 1.25;
        --leading-snug: 1.375;
        --leading-normal: 1.5;
        --leading-relaxed: 1.625;
        --leading-loose: 2;
        
        /* Font Weights */
        --font-light: 300;
        --font-normal: 400;
        --font-medium: 500;
        --font-semibold: 600;
        --font-bold: 700;
        --font-extrabold: 800;
        --font-black: 900;
        
        /* Letter Spacing */
        --tracking-tighter: -0.05em;
        --tracking-tight: -0.025em;
        --tracking-normal: 0em;
        --tracking-wide: 0.025em;
        --tracking-wider: 0.05em;
        --tracking-widest: 0.1em;
        
        /* Enhanced Soft Shadow System */
        --soft-shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --soft-shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --soft-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --soft-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --soft-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --soft-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --soft-shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --soft-shadow-3xl: 0 35px 60px -12px rgba(0, 0, 0, 0.35);
        --soft-shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        --soft-shadow-inner-lg: inset 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        
        /* Colored Shadows */
        --soft-shadow-colored: 0 4px 14px 0 rgba(102, 126, 234, 0.15);
        --soft-shadow-primary: 0 4px 14px 0 rgba(102, 126, 234, 0.2);
        --soft-shadow-secondary: 0 4px 14px 0 rgba(240, 147, 251, 0.2);
        --soft-shadow-success: 0 4px 14px 0 rgba(67, 233, 123, 0.2);
        --soft-shadow-warning: 0 4px 14px 0 rgba(255, 193, 7, 0.2);
        --soft-shadow-danger: 0 4px 14px 0 rgba(220, 53, 69, 0.2);
        --soft-shadow-info: 0 4px 14px 0 rgba(79, 172, 254, 0.2);
        
        /* Glow Effects */
        --soft-glow-primary: 0 0 20px rgba(102, 126, 234, 0.3);
        --soft-glow-secondary: 0 0 20px rgba(240, 147, 251, 0.3);
        --soft-glow-success: 0 0 20px rgba(67, 233, 123, 0.3);
        --soft-glow-warning: 0 0 20px rgba(255, 193, 7, 0.3);
        --soft-glow-danger: 0 0 20px rgba(220, 53, 69, 0.3);
        --soft-glow-info: 0 0 20px rgba(79, 172, 254, 0.3);
        
        /* Enhanced Border Radius System */
        --border-radius-xs: 0.125rem;
        --border-radius-sm: 0.25rem;
        --border-radius-md: 0.375rem;
        --border-radius-lg: 0.5rem;
        --border-radius-xl: 0.75rem;
        --border-radius-2xl: 1rem;
        --border-radius-3xl: 1.5rem;
        --border-radius-4xl: 2rem;
        --border-radius-full: 9999px;
    }
    
    body {
        background: var(--soft-bg);
        min-height: 100vh;
        font-family: 'Open Sans', sans-serif;
        color: var(--soft-gray-700);
        opacity: 0;
        animation: pageLoad 0.8s ease-out forwards;
    }
    
    @keyframes pageLoad {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Enhanced transitions for all interactive elements */
    * {
        transition-property: transform, box-shadow, background, border-color, opacity;
        transition-duration: 0.3s;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Disable transitions during page load */
    .preload * {
        transition: none !important;
    }
    
    /* Mobile-First Responsive Design */
    @media (max-width: 576px) {
        .dashboard-header {
            padding: 1rem !important;
            text-align: center;
        }
        
        .dashboard-title {
            font-size: 1.75rem !important;
        }
        
        .dashboard-subtitle {
            font-size: 0.875rem !important;
        }
        
        .dashboard-card {
            margin-bottom: 1rem;
        }
        
        .stat-card {
            padding: 1rem !important;
        }
        
        .display-6 {
            font-size: 1.5rem !important;
        }
        
        .quick-filter {
            padding: 0.5rem 1rem !important;
            font-size: 0.75rem !important;
            margin-bottom: 0.5rem;
        }
        
        .modern-select, .modern-btn {
            padding: 0.625rem 1rem !important;
            font-size: 0.8rem !important;
        }
        
        .btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .btn-group .quick-filter {
            border-radius: 0.5rem !important;
            margin-bottom: 0.25rem;
        }
    }
    
    @media (max-width: 768px) {
        .row.g-3 > .col-md-3 {
            margin-bottom: 1rem;
        }
        
        .filter-container {
            padding: 1rem;
        }
        
        .dashboard-card:hover {
            transform: translateY(-4px) scale(1.01) !important;
        }
        
        .clickable-card:hover {
            transform: translateY(-6px) scale(1.02) !important;
        }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
        .dashboard-title {
            font-size: 2.25rem;
        }
        
        .stat-card {
            padding: 1.25rem;
        }
    }
    
    @media (min-width: 1025px) {
        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.02);
        }
        
        .clickable-card:hover {
            transform: translateY(-12px) scale(1.03);
        }
    }
    
    /* Touch-friendly interactions */
    @media (hover: none) and (pointer: coarse) {
        .dashboard-card:hover,
        .clickable-card:hover,
        .quick-filter:hover,
        .modern-btn:hover {
            transform: none !important;
        }
        
        .dashboard-card:active,
        .clickable-card:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }
        
        .quick-filter,
        .modern-btn {
            min-height: 44px; /* Touch target size */
        }
    }
    
    /* Responsive grid improvements */
    @media (max-width: 576px) {
        .col-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
    }
    
    @media (max-width: 768px) {
        .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    /* Improved spacing for mobile */
    @media (max-width: 576px) {
        .mb-xl {
            margin-bottom: 1.5rem !important;
        }
        
        .p-xl {
            padding: 1rem !important;
        }
        
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
    
    /* Dashboard Header Styles */
    .dashboard-header {
        padding: 2rem 0;
        margin-bottom: 1rem;
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--soft-dark);
        margin-bottom: 0.5rem;
        background: linear-gradient(310deg, var(--soft-primary), var(--soft-primary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .dashboard-subtitle {
        font-size: 1.1rem;
        font-weight: 400;
        color: var(--soft-gray-600);
        max-width: 600px;
        margin: 0 auto;
    }
    
    .dashboard-card {
        border: 1px solid var(--soft-gray-100);
        border-radius: var(--border-radius-3xl);
        box-shadow: var(--soft-shadow-lg);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        overflow: hidden;
        background: var(--soft-card-bg);
        position: relative;
        backdrop-filter: blur(10px);
    }
    
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s;
        z-index: 1;
        border-radius: 1.5rem;
    }
    
    .dashboard-card:hover::before {
        left: 100%;
    }
    
    .dashboard-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: var(--soft-shadow-xl), var(--soft-glow-primary);
        border-color: var(--soft-primary-light);
    }
    
    /* Staggered Animation for Cards */
    .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
    .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
    .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
    .dashboard-card:nth-child(4) { animation-delay: 0.4s; }
    
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
    
    .dashboard-card {
        animation: fadeInUp 0.6s ease-out both;
    }
    
    .dashboard-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: var(--soft-shadow-xl), var(--soft-glow-primary);
        border-color: var(--soft-primary-light);
    }
    
    .stat-card {
        background: linear-gradient(135deg, var(--soft-primary) 0%, var(--soft-primary-dark) 100%);
        color: white;
        position: relative;
        border-radius: var(--border-radius-3xl);
        box-shadow: var(--soft-shadow-lg), var(--soft-shadow-primary);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        pointer-events: none;
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, var(--soft-success) 0%, var(--soft-success-light) 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, var(--soft-info) 0%, var(--soft-primary-light) 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, var(--soft-warning) 0%, #f57c00 100%);
    }
    
    .stat-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.3;
        font-size: 3rem;
    }
    
    .modern-select {
        border: 1px solid var(--soft-gray-300);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--soft-white);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 400;
        box-shadow: var(--soft-shadow);
        position: relative;
    }
    
    .modern-select:focus {
        border-color: var(--soft-primary);
        box-shadow: 0 0 0 2px rgba(203, 12, 159, 0.25), var(--soft-shadow-lg);
        outline: none;
        transform: translateY(-1px) scale(1.01);
        background: linear-gradient(135deg, var(--soft-white) 0%, rgba(203, 12, 159, 0.02) 100%);
    }
    
    .modern-select:hover {
        border-color: var(--soft-primary);
        transform: translateY(-1px);
        box-shadow: var(--soft-shadow-lg);
    }
    
    .modern-btn {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0.025rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: var(--soft-shadow);
        background: linear-gradient(310deg, var(--soft-primary) 0%, #ad1457 100%);
        color: var(--soft-white);
        position: relative;
        overflow: hidden;
    }
    
    .modern-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
        transition: all 0.4s ease;
        transform: translate(-50%, -50%);
        border-radius: 50%;
    }
    
    .modern-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .modern-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: var(--soft-shadow-xl);
    }
    
    .modern-btn:active {
        transform: translateY(-1px) scale(1.01);
        transition: all 0.1s ease;
    }
    
    .modern-btn i {
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }
    
    .modern-btn:hover i {
        transform: scale(1.1);
    }
    
    .section-header {
        background: linear-gradient(310deg, var(--soft-dark) 0%, #263238 100%);
        color: white;
        border-radius: 1rem 1rem 0 0;
        padding: 1.5rem;
        box-shadow: var(--soft-shadow);
    }
    
    .table-modern {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: var(--soft-shadow);
        background: var(--soft-white);
    }
    
    .table-modern thead {
        background: linear-gradient(310deg, var(--soft-gray-100) 0%, var(--soft-gray-200) 100%);
    }
    
    .table-modern th {
        border: none;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0.025rem;
        font-size: 0.875rem;
        color: var(--soft-dark);
        padding: 1rem;
    }
    
    .table-modern td {
        border: none;
        border-bottom: 1px solid var(--soft-gray-200);
        vertical-align: middle;
        padding: 1rem;
        color: var(--soft-gray-700);
    }
    
    /* Enhanced Gradient Status Badges */
    .status-badge {
        padding: 8px 16px;
        border-radius: var(--border-radius-3xl);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: none;
        letter-spacing: 0.025rem;
        box-shadow: var(--soft-shadow-md), var(--soft-glow-primary);
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .status-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .status-badge:hover::before {
        left: 100%;
    }
    
    .status-badge:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: var(--soft-shadow-xl), var(--soft-glow-primary);
    }
    
    /* Enhanced Structured Chips */
    .status-badge.badge-success {
        background: linear-gradient(135deg, var(--success-500), var(--success-600)) !important;
        color: white !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(34, 197, 94, 0.3);
    }
    
    .status-badge.badge-danger {
        background: linear-gradient(135deg, var(--danger-500), var(--danger-600)) !important;
        color: white !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(239, 68, 68, 0.3);
    }
    
    .status-badge.badge-warning {
        background: linear-gradient(135deg, var(--warning-400), var(--warning-500)) !important;
        color: var(--neutral-900) !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(245, 158, 11, 0.3);
    }
    
    .status-badge.badge-info {
        background: linear-gradient(135deg, var(--info-500), var(--info-600)) !important;
        color: white !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(59, 130, 246, 0.3);
    }
    
    .status-badge.badge-primary {
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600)) !important;
        color: white !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(99, 102, 241, 0.3);
    }
    
    .status-badge.badge-secondary {
        background: linear-gradient(135deg, var(--neutral-500), var(--neutral-600)) !important;
        color: white !important;
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(107, 114, 128, 0.3);
    }
    
    .filter-container {
        background: var(--soft-white);
        border-radius: var(--border-radius-2xl);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--soft-shadow-md);
    }
    
    .card-body-modern {
        padding: 0;
    }
    
    .quick-filter {
        border-radius: var(--border-radius-xl);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--soft-gray-300);
        background: var(--soft-white);
        color: var(--soft-gray-700);
        box-shadow: var(--soft-shadow-sm);
        position: relative;
        overflow: hidden;
    }
    
    .quick-filter::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: radial-gradient(circle, rgba(203, 12, 159, 0.2) 0%, transparent 70%);
        transition: all 0.4s ease;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        z-index: 0;
    }
    
    .quick-filter span, .quick-filter i {
        position: relative;
        z-index: 1;
    }
    
    .quick-filter:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .quick-filter:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: var(--soft-shadow-lg);
        border-color: var(--soft-primary);
        background: linear-gradient(135deg, var(--soft-white) 0%, rgba(203, 12, 159, 0.05) 100%);
    }
    
    .quick-filter:active {
        transform: translateY(-1px) scale(1.01);
        transition: all 0.1s ease;
    }
    
    .quick-filter.active {
        background: linear-gradient(310deg, var(--soft-primary) 0%, #ad1457 100%);
        border-color: var(--soft-primary);
        color: var(--soft-white);
        box-shadow: var(--soft-shadow-lg);
        transform: scale(1.02);
    }
    
    .quick-filter.active::before {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }
    
    .progress-mini {
        height: 6px;
        border-radius: 3px;
        background: var(--soft-gray-200);
        overflow: hidden;
        margin-top: 4px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .progress-mini .progress-bar {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }
    
    .collapsible-header {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 1rem 1rem 0 0;
    }
    
    .collapsible-header:hover {
        background: linear-gradient(310deg, var(--soft-dark) 0%, #263238 100%);
        transform: translateY(-2px);
        box-shadow: var(--soft-shadow-lg);
    }
    
    .tooltip-status {
        cursor: help;
        border-bottom: 1px dotted var(--soft-gray-500);
    }
    
    .mobile-card {
        display: none;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }
        
        .mobile-card {
            display: block;
        }
        
        .filter-container {
            padding: 15px;
        }
        
        .btn-group {
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .btn-group .btn {
            flex: 1;
            min-width: calc(50% - 4px);
        }
        
        .stat-card {
            margin-bottom: 15px;
        }
    }
    
    .drill-down {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        padding: 0.5rem;
    }
    
    .drill-down:hover {
        background-color: var(--soft-gray-100);
        border-radius: var(--border-radius-lg);
        transform: scale(1.02);
        box-shadow: var(--soft-shadow-md);
    }
    
    .transition-icon {
        transition: transform 0.3s ease;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .pulse {
        animation: pulse 0.6s ease-in-out;
    }
    
    /* Enhanced KPI Card Styles */
    .clickable-card {
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border-radius: var(--border-radius-2xl);
        background: var(--soft-white);
        box-shadow: var(--soft-shadow-md);
    }
    
    .clickable-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .clickable-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: var(--soft-shadow-2xl), var(--soft-glow-primary);
    }
    
    .clickable-card:hover::after {
        opacity: 1;
    }
    
    .clickable-card:active {
        transform: translateY(-6px) scale(1.01);
        transition: all 0.1s ease;
    }
    
    /* Stat Icon Animation */
    .stat-icon {
        transition: all 0.3s ease;
        transform-origin: center;
    }
    
    .clickable-card:hover .stat-icon {
        transform: rotate(10deg) scale(1.1);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    }
    
    /* Number Counter Animation */
    .display-6 {
        transition: all 0.3s ease;
    }
    
    .clickable-card:hover .display-6 {
        transform: scale(1.05);
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
    }
    
    .stat-card:hover::before {
        left: 100%;
    }
    
    /* Enhanced Typography with Gradient Effects */
    .display-6 {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .section-header h4 {
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        color: white !important;
        background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,0.9));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .section-header h5 {
        font-weight: 700;
        color: white !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,0.85));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Enhanced Heading Styles */
    h1, .h1 {
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800), var(--secondary-600));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    h2, .h2 {
        background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 600;
    }
    
    h3, .h3 {
        background: linear-gradient(135deg, var(--secondary-600), var(--secondary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 600;
    }
    
    h4, .h4 {
        background: linear-gradient(135deg, var(--info-600), var(--info-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 600;
    }
    
    /* Improved Text Visibility */
    .text-white {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    
    .fw-bold {
        font-weight: 700 !important;
    }
    
    .h5, h5 {
        font-weight: 600;
        background: linear-gradient(135deg, var(--success-600), var(--success-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .h6, h6 {
        font-weight: 600;
        background: linear-gradient(135deg, var(--warning-600), var(--warning-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .small {
        font-weight: 500;
        color: #6c757d;
    }
    
    /* Card Text Improvements */
    .card-header {
        color: white !important;
    }
    
    .card-header h6 {
        color: white !important;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,0.9)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
    }
    
    .text-white-50 {
        color: rgba(255,255,255,0.8) !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    
    /* Enhanced Status Metric Cards */
    .status-metric-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 249, 250, 0.8)) !important;
        border: 2px solid transparent !important;
        border-radius: var(--border-radius-2xl) !important;
        padding: 20px !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        overflow: hidden !important;
        backdrop-filter: blur(10px) !important;
        box-shadow: var(--soft-shadow-lg) !important;
    }
    
    .status-metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
        transition: all 0.3s ease;
    }
    
    .status-metric-card:hover {
        transform: translateY(-4px) scale(1.02) !important;
        box-shadow: var(--soft-shadow-xl), var(--soft-glow-primary) !important;
    }
    
    /* Success (Deployed) Cards */
    .status-metric-card.bg-success-subtle {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.05)) !important;
        border-color: rgba(40, 167, 69, 0.2) !important;
    }
    
    .status-metric-card.bg-success-subtle::before {
        background: linear-gradient(90deg, #28a745, #20c997);
    }
    
    .status-metric-card.bg-success-subtle:hover {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15), rgba(32, 201, 151, 0.1)) !important;
        border-color: rgba(40, 167, 69, 0.3) !important;
    }
    
    /* Danger (Issues) Cards */
    .status-metric-card.bg-danger-subtle {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(253, 126, 20, 0.05)) !important;
        border-color: rgba(220, 53, 69, 0.2) !important;
    }
    
    .status-metric-card.bg-danger-subtle::before {
        background: linear-gradient(90deg, #dc3545, #fd7e14);
    }
    
    .status-metric-card.bg-danger-subtle:hover {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15), rgba(253, 126, 20, 0.1)) !important;
        border-color: rgba(220, 53, 69, 0.3) !important;
    }
    
    /* Warning (Pending) Cards */
    .status-metric-card.bg-warning-subtle {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(253, 126, 20, 0.05)) !important;
        border-color: rgba(255, 193, 7, 0.2) !important;
    }
    
    .status-metric-card.bg-warning-subtle::before {
        background: linear-gradient(90deg, #ffc107, #fd7e14);
    }
    
    .status-metric-card.bg-warning-subtle:hover {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(253, 126, 20, 0.1)) !important;
        border-color: rgba(255, 193, 7, 0.3) !important;
    }
    
    /* Info (Returned) Cards */
    .status-metric-card.bg-info-subtle {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(0, 123, 255, 0.05)) !important;
        border-color: rgba(23, 162, 184, 0.2) !important;
    }
    
    .status-metric-card.bg-info-subtle::before {
        background: linear-gradient(90deg, #17a2b8, #007bff);
    }
    
    .status-metric-card.bg-info-subtle:hover {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.15), rgba(0, 123, 255, 0.1)) !important;
        border-color: rgba(23, 162, 184, 0.3) !important;
    }
    
    /* Enhanced Text Styling */
    .status-metric-card .fw-bold {
        font-weight: 700 !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        position: relative !important;
        z-index: 2 !important;
    }
    
    .status-metric-card .h5 {
        font-size: 1.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .status-metric-card small {
        font-weight: 600 !important;
        opacity: 0.8 !important;
    }
    
    /* Table Text */
    .table-modern th {
        color: #2c3e50 !important;
        font-weight: 700;
    }
    
    .table-modern td {
        color: #34495e;
        font-weight: 500;
    }
    
    /* Button Text */
    .btn {
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    /* Badge Text */
    .badge {
        font-weight: 600;
        text-shadow: none;
    }
    
    .badge.bg-light {
        color: #2c3e50 !important;
        font-weight: 700;
    }
    
    /* Grid Layout Improvements */
    .row.g-4 {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 1.5rem;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .display-6 {
            font-size: 2rem;
        }
        
        .clickable-card:hover {
            transform: translateY(-4px) scale(1.01);
        }
        
        .section-header {
            padding: 15px;
        }
        
        .section-header h4 {
            font-size: 1.25rem;
        }
    }
    
    /* Loading State */
    .loading-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    /* Grid Layout Enhancements */
    .month-section {
        border-left: 4px solid var(--bs-primary);
        padding-left: 1rem;
        margin-bottom: 2rem;
    }

    .week-card, .month-card {
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border: 1px solid var(--soft-gray-100);
        border-radius: 1.5rem;
        background: var(--soft-card-bg);
        box-shadow: var(--soft-shadow);
        backdrop-filter: blur(10px);
        overflow: hidden;
    }
    
    .week-card:hover, .month-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--soft-shadow-colored);
        border-color: var(--soft-primary-light);
    }
    
    .status-metric-card {
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        cursor: pointer;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: 1.2rem;
        background: var(--soft-card-bg);
        box-shadow: var(--soft-shadow);
        padding: 1rem;
        border: 1px solid var(--soft-gray-100);
        backdrop-filter: blur(5px);
    }
    
    .status-metric-card:hover {
        transform: translateY(-4px) scale(1.03);
        box-shadow: var(--soft-shadow-lg);
        border-color: var(--soft-primary-light);
        background: linear-gradient(135deg, var(--soft-card-bg) 0%, rgba(102, 126, 234, 0.05) 100%);
    }

    .status-metric-card .h5 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Card Headers */
    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, var(--soft-primary) 0%, var(--soft-primary-dark) 100%) !important;
        border-radius: var(--border-radius-3xl) var(--border-radius-3xl) 0 0;
        box-shadow: var(--soft-shadow-md);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .card-header.bg-gradient-info {
        background: linear-gradient(135deg, var(--soft-info) 0%, var(--soft-primary-light) 100%) !important;
        border-radius: var(--border-radius-3xl) var(--border-radius-3xl) 0 0;
        box-shadow: var(--soft-shadow-md);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .card-header.bg-gradient-success {
        background: linear-gradient(310deg, var(--soft-success) 0%, #689f38 100%) !important;
        border-radius: var(--border-radius-2xl) var(--border-radius-2xl) 0 0;
        box-shadow: var(--soft-shadow-md);
    }
    
    .card-header.bg-gradient-warning {
        background: linear-gradient(310deg, var(--soft-warning) 0%, #f57c00 100%) !important;
        border-radius: var(--border-radius-2xl) var(--border-radius-2xl) 0 0;
        box-shadow: var(--soft-shadow-md);
    }

    /* Enhanced Soft UI KPI Card Styles */
    .kpi-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 2rem;
        padding: 2rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), 0 4px 16px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.6);
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        height: 100%;
        min-height: 140px;
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 200% 100%;
        animation: gradientShift 3s ease-in-out infinite;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .kpi-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 24px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        border-color: rgba(102, 126, 234, 0.4);
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.9) 100%);
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }

    .kpi-icon {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    
    .kpi-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }
    
    .kpi-card:hover .kpi-icon::before {
        opacity: 1;
        animation: shimmer 1.5s ease-in-out;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    /* Specific icon gradients for different KPI types */
    .kpi-card[data-type="assets"] .kpi-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .kpi-card[data-type="users"] .kpi-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 8px 24px rgba(240, 147, 251, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .kpi-card[data-type="departments"] .kpi-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        box-shadow: 0 8px 24px rgba(79, 172, 254, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .kpi-card[data-type="vendors"] .kpi-icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        box-shadow: 0 8px 24px rgba(67, 233, 123, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .kpi-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .kpi-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--soft-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--soft-dark);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .kpi-change {
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .kpi-change.positive {
        color: var(--soft-success);
    }

    .kpi-change.negative {
        color: var(--soft-danger);
    }

    .kpi-change .percentage {
        font-weight: 700;
        padding: 0.125rem 0.375rem;
        border-radius: 0.375rem;
        background: rgba(72, 187, 120, 0.1);
        color: var(--soft-success);
    }

    .kpi-change.negative .percentage {
        background: rgba(245, 101, 101, 0.1);
        color: var(--soft-danger);
    }

    @media (max-width: 768px) {
        .kpi-card {
            padding: 1rem;
            min-height: 100px;
        }
        
        .kpi-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .kpi-value {
            font-size: 1.5rem;
        }
    }

    /* Enhanced Progress Bars */
    .progress-mini {
        height: 6px;
        background-color: var(--soft-gray-200);
        border-radius: 0.5rem;
        box-shadow: var(--soft-shadow-inner);
        overflow: hidden;
        position: relative;
    }
    
    .progress-mini::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-mini .progress-bar {
        transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .progress-mini .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.2) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, transparent 75%);
        background-size: 8px 8px;
        animation: progressStripes 1s linear infinite;
    }
    
    @keyframes progressStripes {
        0% { background-position: 0 0; }
        100% { background-position: 8px 0; }
    }
    
    /* Enhanced Progress Variants */
    .progress-bar.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }
    
    .progress-bar.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    }
    
    .progress-bar.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    }
    
    .progress-bar.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
    }

    /* Responsive Grid Adjustments */
    @media (max-width: 1200px) {
        .col-xl-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 768px) {
        .col-xl-3, .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .month-section {
            border-left: none;
            border-top: 4px solid var(--bs-primary);
            padding-left: 0;
            padding-top: 1rem;
        }
        
        .status-metric-card {
            min-height: 70px;
        }
        
        .status-metric-card .h5 {
            font-size: 1.25rem;
        }
    }

    /* Empty State Styling */
    .text-center.py-5 {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        border: 2px dashed #dee2e6;
    }

    /* Enhanced Data Chips */
    .badge.bg-primary {
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600)) !important;
        box-shadow: var(--soft-shadow-sm), 0 0 15px rgba(99, 102, 241, 0.3);
        transition: all 0.3s ease;
        border-radius: var(--border-radius-2xl);
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    
    .badge:hover {
        transform: scale(1.05) translateY(-1px);
        box-shadow: var(--soft-shadow-md), 0 0 20px rgba(99, 102, 241, 0.4);
    }
    
    /* Structured Data Chips */
    .data-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius-3xl);
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .data-chip::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .data-chip:hover::before {
        left: 100%;
    }
    
    .data-chip.chip-primary {
        background: linear-gradient(135deg, var(--primary-100), var(--primary-200));
        color: var(--primary-700);
        border-color: var(--primary-300);
    }
    
    .data-chip.chip-success {
        background: linear-gradient(135deg, var(--success-100), var(--success-200));
        color: var(--success-700);
        border-color: var(--success-300);
    }
    
    .data-chip.chip-warning {
        background: linear-gradient(135deg, var(--warning-100), var(--warning-200));
        color: var(--warning-700);
        border-color: var(--warning-300);
    }
    
    .data-chip.chip-danger {
        background: linear-gradient(135deg, var(--danger-100), var(--danger-200));
        color: var(--danger-700);
        border-color: var(--danger-300);
    }
    
    .data-chip.chip-info {
        background: linear-gradient(135deg, var(--info-100), var(--info-200));
        color: var(--info-700);
        border-color: var(--info-300);
    }
    
    /* Enhanced KPI Visualization */
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
        color: rgba(255,255,255,0.9);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    /* Animated Counter Effect */
    .display-6 {
        font-family: 'Segoe UI', system-ui, sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    /* Data Visualization Enhancements */
    .status-metric-card {
        background: linear-gradient(135deg, var(--soft-white) 0%, rgba(248,249,250,0.8) 100%);
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .status-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--soft-shadow-lg);
        background: linear-gradient(135deg, var(--soft-white) 0%, rgba(248,249,250,0.95) 100%);
    }
    
    /* Chart Container Enhancements */
    .chart-container {
        position: relative;
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.8) 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--soft-shadow);
        backdrop-filter: blur(10px);
    }
    
    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        border-radius: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .chart-container:hover::before {
        opacity: 1;
    }
    
    /* Accessibility Enhancements */
    .sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    
    /* Focus Indicators */
    .dashboard-card:focus,
    .clickable-card:focus,
    .quick-filter:focus,
    .modern-btn:focus,
    .modern-select:focus {
        outline: 2px solid var(--soft-primary);
        outline-offset: 2px;
    }
    
    /* High Contrast Mode Support */
    @media (prefers-contrast: high) {
        .dashboard-card,
        .clickable-card {
            border: 2px solid var(--soft-gray-800);
        }
        
        .text-muted {
            color: var(--soft-gray-800) !important;
        }
    }
    
    /* Reduced Motion Support */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
    
    /* Tooltip Styles */
    .tooltip {
        font-size: 0.875rem;
        border-radius: 0.5rem;
        box-shadow: var(--soft-shadow-lg);
    }
    
    .tooltip-inner {
        background: var(--soft-gray-800);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        max-width: 200px;
    }
    
    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: var(--soft-gray-800);
    }
    
    .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
        border-bottom-color: var(--soft-gray-800);
    }
    
    .tooltip.bs-tooltip-start .tooltip-arrow::before {
        border-left-color: var(--soft-gray-800);
    }
    
    .tooltip.bs-tooltip-end .tooltip-arrow::before {
        border-right-color: var(--soft-gray-800);
    }
    
    /* Help Text Styles */
    .help-text {
        font-size: 0.75rem;
        color: var(--soft-gray-600);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .help-text i {
        margin-right: 0.25rem;
        color: var(--soft-primary);
    }
    
    /* Contextual Help Icons */
    .help-icon {
        color: var(--soft-gray-500);
        cursor: help;
        margin-left: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .help-icon:hover {
        color: var(--soft-primary);
        transform: scale(1.1);
    }
    
    /* Skip Links for Accessibility */
    .skip-link {
        position: absolute;
        top: -40px;
        left: 6px;
        background: var(--soft-primary);
        color: white;
        padding: 8px;
        text-decoration: none;
        border-radius: 0.25rem;
        z-index: 1000;
    }
    
    .skip-link:focus {
        top: 6px;
    }
    
    /* Loading States and Skeleton Screens */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: inherit;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--soft-gray-300);
        border-top: 3px solid var(--soft-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Skeleton Loading Animation */
    .skeleton {
        background: linear-gradient(90deg, var(--soft-gray-200) 25%, var(--soft-gray-100) 50%, var(--soft-gray-200) 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 0.375rem;
    }
    
    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .skeleton-text {
        height: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .skeleton-text.large {
        height: 1.5rem;
    }
    
    .skeleton-text.small {
        height: 0.75rem;
        width: 60%;
    }
    
    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    
    .skeleton-card {
        height: 120px;
        margin-bottom: 1rem;
    }
    
    /* Loading States for Cards */
    .stat-card.loading {
        position: relative;
        pointer-events: none;
    }
    
    .stat-card.loading .card-body {
        opacity: 0.3;
    }
    
    /* Pulse Animation for Loading */
    .pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Success/Error Feedback States */
    .feedback-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        color: white;
        font-weight: 500;
        z-index: 1050;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: var(--soft-shadow-lg);
    }
    
    .feedback-message.show {
        transform: translateX(0);
    }
    
    .feedback-message.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .feedback-message.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    .feedback-message.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    
    .feedback-message.info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    
    /* Enhanced Progress Indicators */
    .progress-indicator {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--soft-gray-200);
        z-index: 1060;
        overflow: hidden;
        border-radius: 0 0 var(--border-radius-sm) var(--border-radius-sm);
    }
    
    .progress-indicator .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-500), var(--secondary-500));
        width: 0%;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
    }
    
    .progress-indicator.indeterminate .progress-bar {
        width: 30%;
        animation: indeterminate 2s infinite;
    }
    
    /* Enhanced Progress Mini Bars */
    .progress-mini {
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-full);
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .progress-mini .progress-bar {
        height: 100%;
        border-radius: var(--border-radius-full);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .progress-mini .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes indeterminate {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(400%); }
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Enhanced Search and Filter Card Integration */
    .filter-container {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    
    .filter-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
        border-radius: 20px 20px 0 0;
    }
    
    .search-container {
        position: relative;
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e9ecef;
    }
    
    .search-input-group {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
    }
    
    .search-input-group:focus-within {
        box-shadow: 0 8px 30px rgba(0, 123, 255, 0.2);
        transform: translateY(-3px);
        border-color: #007bff;
        background: white;
    }
    
    .search-icon {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border: none;
        padding: 14px 18px;
        border-radius: 16px 0 0 16px;
        position: relative;
        overflow: hidden;
    }
    
    .search-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .search-icon:hover::before {
        left: 100%;
    }
    
    .search-input {
        border: none;
        padding: 14px 20px;
        font-size: 16px;
        background: transparent;
        font-weight: 500;
        color: #495057;
    }
    
    .search-input:focus {
        box-shadow: none;
        border: none;
        background: transparent;
        outline: none;
    }
    
    .search-input::placeholder {
        color: #6c757d;
        font-weight: 400;
    }
    
    .search-clear, .search-submit {
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }
    
    .search-clear {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        padding: 14px 16px;
        border-radius: 0;
    }
    
    .search-clear:hover {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }
    
    .search-submit {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        padding: 14px 24px;
        color: white;
        border-radius: 0 16px 16px 0;
        font-weight: 600;
    }
    
    .search-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .search-submit:hover {
        background: linear-gradient(135deg, #20c997, #28a745);
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }
    
    .search-submit:hover::before {
        left: 100%;
    }
    
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .suggestion-item:hover {
        background: #f8f9fa;
        transform: translateX(4px);
    }
    
    .suggestion-item:last-child {
        border-bottom: none;
    }
    
    /* Enhanced Pill-Style Filter Buttons */
    .quick-filter {
        border-radius: 25px !important;
        padding: 12px 24px !important;
        font-weight: 500 !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        overflow: hidden !important;
        border: 2px solid transparent !important;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
        color: #495057 !important;
        margin: 0 4px 8px 0 !important;
    }
    
    .quick-filter::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
        z-index: 1;
    }
    
    .quick-filter:hover::before {
        left: 100%;
    }
    
    .quick-filter:hover {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        color: white !important;
        transform: translateY(-3px) scale(1.05) !important;
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3) !important;
        border-color: #007bff !important;
    }
    
    .quick-filter.active,
    .quick-filter[aria-pressed="true"] {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white !important;
        border-color: #28a745 !important;
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
        transform: translateY(-2px) !important;
    }
    
    .quick-filter.active:hover,
    .quick-filter[aria-pressed="true"]:hover {
        background: linear-gradient(135deg, #20c997, #28a745) !important;
        transform: translateY(-4px) scale(1.05) !important;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.5) !important;
    }
    
    .advanced-filter-toggle {
        border-radius: 25px;
        padding: 12px 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #6f42c1, #5a2d91);
        border: none;
        color: white;
        font-weight: 500;
    }
    
    .advanced-filter-toggle::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .advanced-filter-toggle:hover {
        background: linear-gradient(135deg, #5a2d91, #6f42c1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(111, 66, 193, 0.3);
    }
    
    .advanced-filter-toggle:hover::before {
        left: 100%;
    }
    
    .advanced-filter-toggle .transition-icon {
        transition: transform 0.3s ease;
    }
    
    .advanced-filter-toggle[aria-expanded="true"] .transition-icon {
        transform: rotate(180deg);
    }
    
    .advanced-filters-container {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        border: 1px solid #dee2e6;
        backdrop-filter: blur(10px);
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modern-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 8px 12px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .modern-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        transform: translateY(-1px);
    }
    
    .modern-select option {
        padding: 8px;
    }
    
    .filter-reset, .apply-advanced-filters, .clear-advanced-filters {
        border-radius: 25px;
        padding: 8px 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .apply-advanced-filters {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
    }
    
    .apply-advanced-filters:hover {
        background: linear-gradient(135deg, #0056b3, #007bff);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
    
    .clear-advanced-filters:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-2px);
    }
    
    .filter-reset:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Filter Tags */
    .active-filters {
        margin-top: 15px;
    }
    
    .filter-tag {
        display: inline-block;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        margin: 4px;
        font-size: 12px;
        position: relative;
        animation: fadeInScale 0.3s ease;
    }
    
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .filter-tag .remove-filter {
        margin-left: 8px;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    
    .filter-tag .remove-filter:hover {
        opacity: 1;
    }
    
    /* Performance Optimizations */
    .dashboard-card {
        will-change: transform;
        contain: layout style paint;
    }
    
    .stat-card {
        will-change: transform, opacity;
        contain: layout style paint;
    }
    
    /* Progressive Enhancement - Reduced Motion Support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
        
        .loading-spinner {
            animation: none;
        }
        
        .skeleton {
            animation: none;
            background: #e9ecef;
        }
    }
    
    /* Lazy Loading Placeholder */
    .lazy-load {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .lazy-load.loaded {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Critical CSS - Above the fold optimizations */
    .dashboard-header {
        contain: layout style;
    }
    
    .kpi-section {
        contain: layout style;
    }
    
    /* GPU Acceleration for smooth animations */
    .clickable-card,
    .stat-card,
    .dashboard-card {
        transform: translateZ(0);
        backface-visibility: hidden;
        perspective: 1000px;
    }
    
    /* Optimize font loading */
    .dashboard-title,
    .section-header h5 {
        font-display: swap;
    }
    
    /* Intersection Observer optimizations */
    .observe-visibility {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .observe-visibility.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Memory efficient animations */
    @keyframes efficientFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .efficient-animation {
        animation: efficientFadeIn 0.5s ease-out;
    }
    
    /* Optimize repaints */
    .no-repaint {
        transform: translateZ(0);
        will-change: auto;
    }
    
    /* Dashboard Container Styles */
    .dashboard-container {
        min-height: 100vh;
        padding: 2rem 0;
        background: linear-gradient(135deg, var(--soft-bg) 0%, var(--soft-bg-secondary) 100%);
    }
    
    .dashboard-container .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem 0;
        }
        
        .dashboard-container .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
    
    /* Enhanced Typography System */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    body {
        font-family: var(--font-family-sans);
        font-weight: var(--font-normal);
        font-size: var(--text-base);
        line-height: var(--leading-normal);
        color: var(--neutral-800);
        background-color: var(--soft-bg);
        letter-spacing: var(--tracking-normal);
    }
    
    /* Heading Hierarchy */
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-family-sans);
        font-weight: var(--font-bold);
        color: var(--neutral-900);
        line-height: var(--leading-tight);
        letter-spacing: var(--tracking-tight);
        margin-bottom: 0.5em;
    }
    
    h1 {
        font-size: var(--text-4xl);
        font-weight: var(--font-extrabold);
        line-height: var(--leading-none);
        letter-spacing: var(--tracking-tighter);
    }
    
    h2 {
        font-size: var(--text-3xl);
        font-weight: var(--font-bold);
        line-height: var(--leading-tight);
    }
    
    h3 {
        font-size: var(--text-2xl);
        font-weight: var(--font-semibold);
    }
    
    h4 {
        font-size: var(--text-xl);
        font-weight: var(--font-semibold);
    }
    
    h5 {
        font-size: var(--text-lg);
        font-weight: var(--font-medium);
    }
    
    h6 {
        font-size: var(--text-base);
        font-weight: var(--font-medium);
        text-transform: uppercase;
        letter-spacing: var(--tracking-wide);
    }
    
    /* Paragraph and Body Text */
    p {
        font-size: var(--text-base);
        line-height: var(--leading-relaxed);
        color: var(--neutral-700);
        margin-bottom: 1rem;
    }
    
    .lead {
        font-size: var(--text-lg);
        font-weight: var(--font-normal);
        line-height: var(--leading-relaxed);
        color: var(--neutral-600);
    }
    
    /* Text Utilities */
    .text-xs { font-size: var(--text-xs); }
    .text-sm { font-size: var(--text-sm); }
    .text-base { font-size: var(--text-base); }
    .text-lg { font-size: var(--text-lg); }
    .text-xl { font-size: var(--text-xl); }
    .text-2xl { font-size: var(--text-2xl); }
    .text-3xl { font-size: var(--text-3xl); }
    .text-4xl { font-size: var(--text-4xl); }
    
    .font-light { font-weight: var(--font-light); }
    .font-normal { font-weight: var(--font-normal); }
    .font-medium { font-weight: var(--font-medium); }
    .font-semibold { font-weight: var(--font-semibold); }
    .font-bold { font-weight: var(--font-bold); }
    .font-extrabold { font-weight: var(--font-extrabold); }
    
    .leading-tight { line-height: var(--leading-tight); }
    .leading-normal { line-height: var(--leading-normal); }
    .leading-relaxed { line-height: var(--leading-relaxed); }
    
    .tracking-tight { letter-spacing: var(--tracking-tight); }
    .tracking-normal { letter-spacing: var(--tracking-normal); }
    .tracking-wide { letter-spacing: var(--tracking-wide); }
    
    /* Semantic Text Colors */
    .text-muted {
        color: var(--neutral-500) !important;
    }
    
    .text-dark {
        color: var(--neutral-900) !important;
    }
    
    .text-primary {
        color: var(--primary-600) !important;
    }
    
    .text-secondary {
        color: var(--secondary-600) !important;
    }
    
    .text-success {
        color: var(--success-600) !important;
    }
    
    .text-warning {
        color: var(--warning-600) !important;
    }
    
    .text-danger {
        color: var(--danger-600) !important;
    }
    
    .text-info {
        color: var(--info-600) !important;
    }
    
    /* Enhanced Card Typography with Gradients */
    .card-title {
        font-size: var(--text-lg);
        font-weight: var(--font-semibold);
        line-height: var(--leading-tight);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .card-subtitle {
        font-size: var(--text-sm);
        font-weight: var(--font-medium);
        color: var(--neutral-600);
        line-height: var(--leading-normal);
        margin-bottom: 0.75rem;
    }
    
    .card-text {
        font-size: var(--text-sm);
        color: var(--neutral-700);
        line-height: var(--leading-relaxed);
    }
    
    /* KPI Card Typography Enhancements */
    .kpi-value {
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        font-weight: 700 !important;
        font-size: 2.5rem !important;
        line-height: 1.2 !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .kpi-label {
        background: linear-gradient(135deg, var(--secondary-600), var(--secondary-800)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-change {
        font-weight: 500 !important;
        font-size: 0.875rem !important;
    }
    
    .kpi-change.positive {
        background: linear-gradient(135deg, var(--success-600), var(--success-800)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
    }
    
    .kpi-change.negative {
        background: linear-gradient(135deg, var(--danger-600), var(--danger-800)) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
    }
    
    .percentage {
        font-weight: 700 !important;
    }
    
    /* Enhanced Display Text with Gradients */
    .display-1, .display-2, .display-3, .display-4, .display-5, .display-6 {
        font-family: var(--font-family-sans);
        font-weight: var(--font-bold);
        line-height: var(--leading-none);
        letter-spacing: var(--tracking-tight);
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .display-1 { 
        font-size: var(--text-6xl);
        background: linear-gradient(135deg, var(--primary-500), var(--primary-700), var(--secondary-600));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .display-2 { 
        font-size: var(--text-5xl);
        background: linear-gradient(135deg, var(--primary-600), var(--primary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .display-3 { 
        font-size: var(--text-4xl);
        background: linear-gradient(135deg, var(--secondary-600), var(--secondary-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .display-4 { 
        font-size: var(--text-3xl);
        background: linear-gradient(135deg, var(--info-600), var(--info-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .display-5 { 
        font-size: var(--text-2xl);
        background: linear-gradient(135deg, var(--success-600), var(--success-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .display-6 { 
        font-size: var(--text-xl);
        background: linear-gradient(135deg, var(--warning-600), var(--warning-800));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Table Typography */
    .table th {
        font-size: var(--text-sm);
        font-weight: var(--font-semibold);
        color: var(--neutral-700);
        text-transform: uppercase;
        letter-spacing: var(--tracking-wide);
    }
    
    .table td {
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        color: var(--neutral-800);
    }
    
    /* Code Typography */
    code, kbd, pre, samp {
        font-family: var(--font-family-mono);
        font-size: 0.875em;
    }
    
    /* Link Typography */
    a {
        color: var(--primary-600);
        text-decoration: none;
        font-weight: var(--font-medium);
        transition: color 0.15s ease-in-out;
    }
    
    a:hover {
        color: var(--primary-700);
        text-decoration: underline;
    }
    
    /* Badge Typography */
    .badge {
        font-size: var(--text-xs);
        font-weight: var(--font-semibold);
        letter-spacing: var(--tracking-wide);
        text-transform: uppercase;
    }
    
    /* Modern Button System */
    .btn {
        font-family: var(--font-family-sans);
        font-weight: var(--font-medium);
        font-size: var(--text-sm);
        line-height: var(--leading-normal);
        letter-spacing: var(--tracking-normal);
        border-radius: var(--border-radius-md);
        border: 1px solid transparent;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    
    .btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(var(--primary-500-rgb), 0.2);
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Button Sizes */
    .btn-xs {
        padding: 0.375rem 0.75rem;
        font-size: var(--text-xs);
        border-radius: var(--border-radius-sm);
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: var(--text-sm);
        border-radius: var(--border-radius-sm);
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: var(--text-lg);
        border-radius: var(--border-radius-lg);
    }
    
    .btn-xl {
        padding: 1.25rem 2.5rem;
        font-size: var(--text-xl);
        border-radius: var(--border-radius-lg);
    }
    
    /* Primary Button */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
        color: white;
        border-color: var(--primary-600);
        box-shadow: var(--shadow-md);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-800) 100%);
        border-color: var(--primary-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    
    .btn-primary:active {
        transform: translateY(0);
        box-shadow: var(--shadow-sm);
    }
    
    /* Secondary Button */
    .btn-secondary {
        background: linear-gradient(135deg, var(--secondary-600) 0%, var(--secondary-700) 100%);
        color: white;
        border-color: var(--secondary-600);
        box-shadow: var(--shadow-md);
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, var(--secondary-700) 0%, var(--secondary-800) 100%);
        border-color: var(--secondary-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    
    /* Success Button */
    .btn-success {
        background: linear-gradient(135deg, var(--success-600) 0%, var(--success-700) 100%);
        color: white;
        border-color: var(--success-600);
        box-shadow: var(--shadow-md);
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, var(--success-700) 0%, var(--success-800) 100%);
        border-color: var(--success-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    
    /* Warning Button */
    .btn-warning {
        background: linear-gradient(135deg, var(--warning-500) 0%, var(--warning-600) 100%);
        color: var(--neutral-900);
        border-color: var(--warning-500);
        box-shadow: var(--shadow-md);
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, var(--warning-600) 0%, var(--warning-700) 100%);
        border-color: var(--warning-600);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: var(--neutral-900);
    }
    
    /* Danger Button */
    .btn-danger {
        background: linear-gradient(135deg, var(--danger-600) 0%, var(--danger-700) 100%);
        color: white;
        border-color: var(--danger-600);
        box-shadow: var(--shadow-md);
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, var(--danger-700) 0%, var(--danger-800) 100%);
        border-color: var(--danger-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    
    /* Info Button */
    .btn-info {
        background: linear-gradient(135deg, var(--info-600) 0%, var(--info-700) 100%);
        color: white;
        border-color: var(--info-600);
        box-shadow: var(--shadow-md);
    }
    
    .btn-info:hover {
        background: linear-gradient(135deg, var(--info-700) 0%, var(--info-800) 100%);
        border-color: var(--info-700);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    
    /* Outline Buttons */
    .btn-outline-primary {
        background: transparent;
        color: var(--primary-600);
        border-color: var(--primary-600);
        box-shadow: none;
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-600);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-outline-secondary {
        background: transparent;
        color: var(--secondary-600);
        border-color: var(--secondary-600);
        box-shadow: none;
    }
    
    .btn-outline-secondary:hover {
        background: var(--secondary-600);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    /* Ghost Buttons */
    .btn-ghost {
        background: transparent;
        color: var(--neutral-700);
        border: none;
        box-shadow: none;
        padding: 0.75rem 1rem;
    }
    
    .btn-ghost:hover {
        background: var(--neutral-100);
        color: var(--neutral-900);
        transform: none;
        box-shadow: none;
    }
    
    /* Link Buttons */
    .btn-link {
        background: transparent;
        color: var(--primary-600);
        border: none;
        box-shadow: none;
        padding: 0;
        font-weight: var(--font-medium);
        text-decoration: underline;
    }
    
    .btn-link:hover {
        color: var(--primary-700);
        background: transparent;
        transform: none;
        box-shadow: none;
    }
    
    /* Button Groups */
    .btn-group {
        display: inline-flex;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    
    .btn-group .btn {
        border-radius: 0;
        border-right-width: 0;
        box-shadow: none;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: var(--border-radius-md);
        border-bottom-left-radius: var(--border-radius-md);
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: var(--border-radius-md);
        border-bottom-right-radius: var(--border-radius-md);
        border-right-width: 1px;
    }
    
    /* Interactive Elements */
    .form-control {
        font-family: var(--font-family-sans);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        line-height: var(--leading-normal);
        color: var(--neutral-800);
        background-color: white;
        border: 1px solid var(--neutral-300);
        border-radius: var(--border-radius-md);
        padding: 0.75rem 1rem;
        transition: all 0.2s ease-in-out;
        box-shadow: var(--shadow-sm);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(var(--primary-500-rgb), 0.1), var(--shadow-sm);
        background-color: white;
    }
    
    .form-control::placeholder {
        color: var(--neutral-500);
        font-weight: var(--font-normal);
    }
    
    /* Form Labels */
    .form-label {
        font-family: var(--font-family-sans);
        font-size: var(--text-sm);
        font-weight: var(--font-medium);
        color: var(--neutral-700);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    /* Select Elements */
    .form-select {
        font-family: var(--font-family-sans);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        color: var(--neutral-800);
        background-color: white;
        border: 1px solid var(--neutral-300);
        border-radius: var(--border-radius-md);
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        transition: all 0.2s ease-in-out;
        box-shadow: var(--shadow-sm);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1rem;
    }
    
    .form-select:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(var(--primary-500-rgb), 0.1), var(--shadow-sm);
    }
    
    /* Checkbox and Radio */
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--neutral-300);
        border-radius: var(--border-radius-sm);
        background-color: white;
        transition: all 0.2s ease-in-out;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-600);
        border-color: var(--primary-600);
    }
    
    .form-check-input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(var(--primary-500-rgb), 0.2);
    }
    
    .form-check-label {
        font-family: var(--font-family-sans);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        color: var(--neutral-700);
        margin-left: 0.5rem;
    }
    
    /* Switch Toggle */
    .form-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
        border-radius: 1rem;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255,255,255,1%29'/%3e%3c/svg%3e");
        background-position: left center;
        background-repeat: no-repeat;
        background-size: contain;
        transition: all 0.2s ease-in-out;
    }
    
    .form-switch .form-check-input:checked {
        background-position: right center;
        background-color: var(--primary-600);
        border-color: var(--primary-600);
    }
    
    /* Loading States */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 1rem;
        height: 1rem;
        margin: -0.5rem 0 0 -0.5rem;
        border: 2px solid currentColor;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Consistent Spacing System */
    /* Margin Utilities */
    .m-0 { margin: 0 !important; }
    .m-1 { margin: 0.25rem !important; }
    .m-2 { margin: 0.5rem !important; }
    .m-3 { margin: 0.75rem !important; }
    .m-4 { margin: 1rem !important; }
    .m-5 { margin: 1.25rem !important; }
    .m-6 { margin: 1.5rem !important; }
    .m-8 { margin: 2rem !important; }
    .m-10 { margin: 2.5rem !important; }
    .m-12 { margin: 3rem !important; }
    .m-16 { margin: 4rem !important; }
    .m-20 { margin: 5rem !important; }
    .m-auto { margin: auto !important; }
    
    /* Margin Top */
    .mt-0 { margin-top: 0 !important; }
    .mt-1 { margin-top: 0.25rem !important; }
    .mt-2 { margin-top: 0.5rem !important; }
    .mt-3 { margin-top: 0.75rem !important; }
    .mt-4 { margin-top: 1rem !important; }
    .mt-5 { margin-top: 1.25rem !important; }
    .mt-6 { margin-top: 1.5rem !important; }
    .mt-8 { margin-top: 2rem !important; }
    .mt-10 { margin-top: 2.5rem !important; }
    .mt-12 { margin-top: 3rem !important; }
    .mt-16 { margin-top: 4rem !important; }
    .mt-20 { margin-top: 5rem !important; }
    .mt-auto { margin-top: auto !important; }
    
    /* Margin Bottom */
    .mb-0 { margin-bottom: 0 !important; }
    .mb-1 { margin-bottom: 0.25rem !important; }
    .mb-2 { margin-bottom: 0.5rem !important; }
    .mb-3 { margin-bottom: 0.75rem !important; }
    .mb-4 { margin-bottom: 1rem !important; }
    .mb-5 { margin-bottom: 1.25rem !important; }
    .mb-6 { margin-bottom: 1.5rem !important; }
    .mb-8 { margin-bottom: 2rem !important; }
    .mb-10 { margin-bottom: 2.5rem !important; }
    .mb-12 { margin-bottom: 3rem !important; }
    .mb-16 { margin-bottom: 4rem !important; }
    .mb-20 { margin-bottom: 5rem !important; }
    .mb-auto { margin-bottom: auto !important; }
    
    /* Margin Left */
    .ml-0 { margin-left: 0 !important; }
    .ml-1 { margin-left: 0.25rem !important; }
    .ml-2 { margin-left: 0.5rem !important; }
    .ml-3 { margin-left: 0.75rem !important; }
    .ml-4 { margin-left: 1rem !important; }
    .ml-5 { margin-left: 1.25rem !important; }
    .ml-6 { margin-left: 1.5rem !important; }
    .ml-8 { margin-left: 2rem !important; }
    .ml-10 { margin-left: 2.5rem !important; }
    .ml-12 { margin-left: 3rem !important; }
    .ml-16 { margin-left: 4rem !important; }
    .ml-20 { margin-left: 5rem !important; }
    .ml-auto { margin-left: auto !important; }
    
    /* Margin Right */
    .mr-0 { margin-right: 0 !important; }
    .mr-1 { margin-right: 0.25rem !important; }
    .mr-2 { margin-right: 0.5rem !important; }
    .mr-3 { margin-right: 0.75rem !important; }
    .mr-4 { margin-right: 1rem !important; }
    .mr-5 { margin-right: 1.25rem !important; }
    .mr-6 { margin-right: 1.5rem !important; }
    .mr-8 { margin-right: 2rem !important; }
    .mr-10 { margin-right: 2.5rem !important; }
    .mr-12 { margin-right: 3rem !important; }
    .mr-16 { margin-right: 4rem !important; }
    .mr-20 { margin-right: 5rem !important; }
    .mr-auto { margin-right: auto !important; }
    
    /* Margin X (horizontal) */
    .mx-0 { margin-left: 0 !important; margin-right: 0 !important; }
    .mx-1 { margin-left: 0.25rem !important; margin-right: 0.25rem !important; }
    .mx-2 { margin-left: 0.5rem !important; margin-right: 0.5rem !important; }
    .mx-3 { margin-left: 0.75rem !important; margin-right: 0.75rem !important; }
    .mx-4 { margin-left: 1rem !important; margin-right: 1rem !important; }
    .mx-5 { margin-left: 1.25rem !important; margin-right: 1.25rem !important; }
    .mx-6 { margin-left: 1.5rem !important; margin-right: 1.5rem !important; }
    .mx-8 { margin-left: 2rem !important; margin-right: 2rem !important; }
    .mx-10 { margin-left: 2.5rem !important; margin-right: 2.5rem !important; }
    .mx-12 { margin-left: 3rem !important; margin-right: 3rem !important; }
    .mx-16 { margin-left: 4rem !important; margin-right: 4rem !important; }
    .mx-20 { margin-left: 5rem !important; margin-right: 5rem !important; }
    .mx-auto { margin-left: auto !important; margin-right: auto !important; }
    
    /* Margin Y (vertical) */
    .my-0 { margin-top: 0 !important; margin-bottom: 0 !important; }
    .my-1 { margin-top: 0.25rem !important; margin-bottom: 0.25rem !important; }
    .my-2 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
    .my-3 { margin-top: 0.75rem !important; margin-bottom: 0.75rem !important; }
    .my-4 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
    .my-5 { margin-top: 1.25rem !important; margin-bottom: 1.25rem !important; }
    .my-6 { margin-top: 1.5rem !important; margin-bottom: 1.5rem !important; }
    .my-8 { margin-top: 2rem !important; margin-bottom: 2rem !important; }
    .my-10 { margin-top: 2.5rem !important; margin-bottom: 2.5rem !important; }
    .my-12 { margin-top: 3rem !important; margin-bottom: 3rem !important; }
    .my-16 { margin-top: 4rem !important; margin-bottom: 4rem !important; }
    .my-20 { margin-top: 5rem !important; margin-bottom: 5rem !important; }
    .my-auto { margin-top: auto !important; margin-bottom: auto !important; }
    
    /* Padding Utilities */
    .p-0 { padding: 0 !important; }
    .p-1 { padding: 0.25rem !important; }
    .p-2 { padding: 0.5rem !important; }
    .p-3 { padding: 0.75rem !important; }
    .p-4 { padding: 1rem !important; }
    .p-5 { padding: 1.25rem !important; }
    .p-6 { padding: 1.5rem !important; }
    .p-8 { padding: 2rem !important; }
    .p-10 { padding: 2.5rem !important; }
    .p-12 { padding: 3rem !important; }
    .p-16 { padding: 4rem !important; }
    .p-20 { padding: 5rem !important; }
    
    /* Padding Top */
    .pt-0 { padding-top: 0 !important; }
    .pt-1 { padding-top: 0.25rem !important; }
    .pt-2 { padding-top: 0.5rem !important; }
    .pt-3 { padding-top: 0.75rem !important; }
    .pt-4 { padding-top: 1rem !important; }
    .pt-5 { padding-top: 1.25rem !important; }
    .pt-6 { padding-top: 1.5rem !important; }
    .pt-8 { padding-top: 2rem !important; }
    .pt-10 { padding-top: 2.5rem !important; }
    .pt-12 { padding-top: 3rem !important; }
    .pt-16 { padding-top: 4rem !important; }
    .pt-20 { padding-top: 5rem !important; }
    
    /* Padding Bottom */
    .pb-0 { padding-bottom: 0 !important; }
    .pb-1 { padding-bottom: 0.25rem !important; }
    .pb-2 { padding-bottom: 0.5rem !important; }
    .pb-3 { padding-bottom: 0.75rem !important; }
    .pb-4 { padding-bottom: 1rem !important; }
    .pb-5 { padding-bottom: 1.25rem !important; }
    .pb-6 { padding-bottom: 1.5rem !important; }
    .pb-8 { padding-bottom: 2rem !important; }
    .pb-10 { padding-bottom: 2.5rem !important; }
    .pb-12 { padding-bottom: 3rem !important; }
    .pb-16 { padding-bottom: 4rem !important; }
    .pb-20 { padding-bottom: 5rem !important; }
    
    /* Padding Left */
    .pl-0 { padding-left: 0 !important; }
    .pl-1 { padding-left: 0.25rem !important; }
    .pl-2 { padding-left: 0.5rem !important; }
    .pl-3 { padding-left: 0.75rem !important; }
    .pl-4 { padding-left: 1rem !important; }
    .pl-5 { padding-left: 1.25rem !important; }
    .pl-6 { padding-left: 1.5rem !important; }
    .pl-8 { padding-left: 2rem !important; }
    .pl-10 { padding-left: 2.5rem !important; }
    .pl-12 { padding-left: 3rem !important; }
    .pl-16 { padding-left: 4rem !important; }
    .pl-20 { padding-left: 5rem !important; }
    
    /* Padding Right */
    .pr-0 { padding-right: 0 !important; }
    .pr-1 { padding-right: 0.25rem !important; }
    .pr-2 { padding-right: 0.5rem !important; }
    .pr-3 { padding-right: 0.75rem !important; }
    .pr-4 { padding-right: 1rem !important; }
    .pr-5 { padding-right: 1.25rem !important; }
    .pr-6 { padding-right: 1.5rem !important; }
    .pr-8 { padding-right: 2rem !important; }
    .pr-10 { padding-right: 2.5rem !important; }
    .pr-12 { padding-right: 3rem !important; }
    .pr-16 { padding-right: 4rem !important; }
    .pr-20 { padding-right: 5rem !important; }
    
    /* Padding X (horizontal) */
    .px-0 { padding-left: 0 !important; padding-right: 0 !important; }
    .px-1 { padding-left: 0.25rem !important; padding-right: 0.25rem !important; }
    .px-2 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
    .px-3 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
    .px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
    .px-5 { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
    .px-6 { padding-left: 1.5rem !important; padding-right: 1.5rem !important; }
    .px-8 { padding-left: 2rem !important; padding-right: 2rem !important; }
    .px-10 { padding-left: 2.5rem !important; padding-right: 2.5rem !important; }
    .px-12 { padding-left: 3rem !important; padding-right: 3rem !important; }
    .px-16 { padding-left: 4rem !important; padding-right: 4rem !important; }
    .px-20 { padding-left: 5rem !important; padding-right: 5rem !important; }
    
    /* Padding Y (vertical) */
    .py-0 { padding-top: 0 !important; padding-bottom: 0 !important; }
    .py-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
    .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
    .py-4 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .py-5 { padding-top: 1.25rem !important; padding-bottom: 1.25rem !important; }
    .py-6 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
    .py-8 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
    .py-10 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
    .py-12 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
    .py-16 { padding-top: 4rem !important; padding-bottom: 4rem !important; }
    .py-20 { padding-top: 5rem !important; padding-bottom: 5rem !important; }
    
    /* Gap Utilities for Flexbox and Grid */
    .gap-0 { gap: 0 !important; }
    .gap-1 { gap: 0.25rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 0.75rem !important; }
    .gap-4 { gap: 1rem !important; }
    .gap-5 { gap: 1.25rem !important; }
    .gap-6 { gap: 1.5rem !important; }
    .gap-8 { gap: 2rem !important; }
    .gap-10 { gap: 2.5rem !important; }
    .gap-12 { gap: 3rem !important; }
    .gap-16 { gap: 4rem !important; }
    .gap-20 { gap: 5rem !important; }
    
    /* Layout Improvements */
    .container-custom {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    .section-spacing {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }
    
    .card-spacing {
        margin-bottom: 1.5rem;
    }
    
    .grid-auto-fit {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .grid-auto-fill {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    /* Responsive Spacing */
    @media (max-width: 576px) {
        .container-custom {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .section-spacing {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .card-spacing {
            margin-bottom: 1rem;
        }
        
        .grid-auto-fit,
        .grid-auto-fill {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    
    @media (min-width: 577px) and (max-width: 768px) {
        .grid-auto-fit,
        .grid-auto-fill {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
        }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
        .grid-auto-fit,
        .grid-auto-fill {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
     }
     
     /* Dark Mode Theme System */
     [data-theme="dark"] {
         /* Dark Mode Color Palette */
         --primary-50: #1e293b;
         --primary-100: #334155;
         --primary-200: #475569;
         --primary-300: #64748b;
         --primary-400: #94a3b8;
         --primary-500: #cbd5e1;
         --primary-600: #e2e8f0;
         --primary-700: #f1f5f9;
         --primary-800: #f8fafc;
         --primary-900: #ffffff;
         
         --secondary-50: #1a1a2e;
         --secondary-100: #16213e;
         --secondary-200: #0f3460;
         --secondary-300: #533483;
         --secondary-400: #7209b7;
         --secondary-500: #a663cc;
         --secondary-600: #c084fc;
         --secondary-700: #ddd6fe;
         --secondary-800: #ede9fe;
         --secondary-900: #f5f3ff;
         
         --success-50: #0c1f17;
         --success-100: #14532d;
         --success-200: #166534;
         --success-300: #15803d;
         --success-400: #16a34a;
         --success-500: #22c55e;
         --success-600: #4ade80;
         --success-700: #86efac;
         --success-800: #bbf7d0;
         --success-900: #dcfce7;
         
         --warning-50: #1c1917;
         --warning-100: #451a03;
         --warning-200: #78350f;
         --warning-300: #a16207;
         --warning-400: #ca8a04;
         --warning-500: #eab308;
         --warning-600: #facc15;
         --warning-700: #fde047;
         --warning-800: #fef08a;
         --warning-900: #fefce8;
         
         --danger-50: #1f1315;
         --danger-100: #450a0a;
         --danger-200: #7f1d1d;
         --danger-300: #b91c1c;
         --danger-400: #dc2626;
         --danger-500: #ef4444;
         --danger-600: #f87171;
         --danger-700: #fca5a5;
         --danger-800: #fecaca;
         --danger-900: #fef2f2;
         
         --info-50: #0c1426;
         --info-100: #0c4a6e;
         --info-200: #075985;
         --info-300: #0369a1;
         --info-400: #0284c7;
         --info-500: #0ea5e9;
         --info-600: #38bdf8;
         --info-700: #7dd3fc;
         --info-800: #bae6fd;
         --info-900: #e0f2fe;
         
         --neutral-50: #0f172a;
         --neutral-100: #1e293b;
         --neutral-200: #334155;
         --neutral-300: #475569;
         --neutral-400: #64748b;
         --neutral-500: #94a3b8;
         --neutral-600: #cbd5e1;
         --neutral-700: #e2e8f0;
         --neutral-800: #f1f5f9;
         --neutral-900: #f8fafc;
         
         /* Dark Mode Backgrounds */
         --soft-bg: #0f172a;
         --soft-bg-secondary: #1e293b;
         --card-bg: #1e293b;
         --card-bg-hover: #334155;
         
         /* Dark Mode Shadows */
         --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
         --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
         --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
         --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
         --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
         
         /* Dark Mode Text Colors */
         color: var(--neutral-800);
         background-color: var(--soft-bg);
     }
     
     /* Dark Mode Specific Overrides */
     [data-theme="dark"] .card {
         background-color: var(--card-bg);
         border-color: var(--neutral-300);
         color: var(--neutral-800);
     }
     
     [data-theme="dark"] .card:hover {
         background-color: var(--card-bg-hover);
     }
     
     [data-theme="dark"] .kpi-card {
         background: linear-gradient(135deg, var(--card-bg) 0%, var(--card-bg-hover) 100%);
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .kpi-card:hover {
         background: linear-gradient(135deg, var(--card-bg-hover) 0%, var(--neutral-200) 100%);
         transform: translateY(-2px);
     }
     
     [data-theme="dark"] .btn-primary {
         background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
         color: var(--neutral-50);
     }
     
     [data-theme="dark"] .btn-primary:hover {
         background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-800) 100%);
         color: var(--neutral-50);
     }
     
     [data-theme="dark"] .form-control {
         background-color: var(--card-bg);
         border-color: var(--neutral-300);
         color: var(--neutral-800);
     }
     
     [data-theme="dark"] .form-control:focus {
         background-color: var(--card-bg);
         border-color: var(--primary-500);
         color: var(--neutral-800);
     }
     
     [data-theme="dark"] .form-control::placeholder {
         color: var(--neutral-400);
     }
     
     [data-theme="dark"] .table {
         color: var(--neutral-800);
     }
     
     [data-theme="dark"] .table th {
         color: var(--neutral-600);
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .table td {
         color: var(--neutral-700);
         border-color: var(--neutral-300);
     }
     
     /* Dark Mode Toggle Button */
     .theme-toggle {
         position: fixed;
         top: 20px;
         right: 20px;
         z-index: 1000;
         background: var(--card-bg);
         border: 1px solid var(--neutral-300);
         border-radius: var(--border-radius-full);
         width: 3rem;
         height: 3rem;
         display: flex;
         align-items: center;
         justify-content: center;
         cursor: pointer;
         transition: all 0.3s ease;
         box-shadow: var(--shadow-lg);
         color: var(--neutral-700);
     }
     
     .theme-toggle:hover {
         background: var(--card-bg-hover);
         transform: scale(1.05);
         box-shadow: var(--shadow-xl);
     }
     
     .theme-toggle svg {
         width: 1.25rem;
         height: 1.25rem;
         transition: all 0.3s ease;
     }
     
     /* Hide/Show icons based on theme */
     [data-theme="light"] .theme-toggle .moon-icon {
         display: block;
     }
     
     [data-theme="light"] .theme-toggle .sun-icon {
         display: none;
     }
     
     [data-theme="dark"] .theme-toggle .moon-icon {
         display: none;
     }
     
     [data-theme="dark"] .theme-toggle .sun-icon {
         display: block;
     }
     
     /* Smooth theme transition */
     * {
         transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
     }
     
     /* Dark mode navbar and sidebar adjustments */
     [data-theme="dark"] .navbar {
         background-color: var(--card-bg) !important;
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .navbar-brand,
     [data-theme="dark"] .navbar-nav .nav-link {
         color: var(--neutral-800) !important;
     }
     
     [data-theme="dark"] .sidebar {
         background-color: var(--card-bg) !important;
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .sidebar .nav-link {
         color: var(--neutral-700) !important;
     }
     
     [data-theme="dark"] .sidebar .nav-link:hover {
         background-color: var(--card-bg-hover) !important;
         color: var(--neutral-800) !important;
     }
     
     [data-theme="dark"] .sidebar .nav-link.active {
         background-color: var(--primary-600) !important;
         color: var(--neutral-50) !important;
     }
     
     /* Dark mode dropdown menus */
     [data-theme="dark"] .dropdown-menu {
         background-color: var(--card-bg);
         border-color: var(--neutral-300);
         box-shadow: var(--shadow-lg);
     }
     
     [data-theme="dark"] .dropdown-item {
         color: var(--neutral-700);
     }
     
     [data-theme="dark"] .dropdown-item:hover {
         background-color: var(--card-bg-hover);
         color: var(--neutral-800);
     }
     
     /* Dark mode modal adjustments */
     [data-theme="dark"] .modal-content {
         background-color: var(--card-bg);
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .modal-header {
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .modal-footer {
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .modal-title {
         color: var(--neutral-800);
     }
     
     /* Dark mode alert adjustments */
     [data-theme="dark"] .alert {
         border-color: var(--neutral-300);
     }
     
     [data-theme="dark"] .alert-primary {
         background-color: rgba(var(--primary-600-rgb), 0.1);
         border-color: var(--primary-600);
         color: var(--primary-700);
     }
     
     [data-theme="dark"] .alert-success {
         background-color: rgba(var(--success-600-rgb), 0.1);
         border-color: var(--success-600);
         color: var(--success-700);
     }
     
     [data-theme="dark"] .alert-warning {
         background-color: rgba(var(--warning-600-rgb), 0.1);
         border-color: var(--warning-600);
         color: var(--warning-700);
     }
     
     [data-theme="dark"] .alert-danger {
         background-color: rgba(var(--danger-600-rgb), 0.1);
         border-color: var(--danger-600);
         color: var(--danger-700);
     }
</style>
@endpush

@section('content')
<!-- Skip Links for Accessibility -->
<a href="#main-content" class="skip-link">Skip to main content</a>
<a href="#navigation" class="skip-link">Skip to navigation</a>

<!-- Main Dashboard Container -->
<div class="dashboard-container">
    <div class="container-fluid px-4">
        <!-- Dashboard Header -->
        <div class="row mb-xl">
            <div class="col-12">
                <div class="dashboard-header text-center">
                    <h1 class="dashboard-title mb-sm" id="main-heading">Asset Management Dashboard</h1>
                    <p class="dashboard-subtitle text-muted mb-0" aria-describedby="main-heading">Real-time insights and comprehensive asset tracking</p>
                </div>
            </div>
        </div>

<!-- Dashboard Overview KPI Section -->
<div class="row g-4 mb-5" id="main-content">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kpi-card" data-type="assets" role="button" tabindex="0" aria-label="Total Assets: {{ $totalAssets ?? 0 }}. Click to view all assets" data-bs-toggle="tooltip" title="Click to view all assets">
            <div class="kpi-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Total Assets</div>
                <div class="kpi-value">${{ number_format($totalAssets ?? 0) }}</div>
                <div class="kpi-change positive">
                    <span class="percentage">12%</span> from last month
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kpi-card" data-type="users" role="button" tabindex="0" aria-label="Active Users: {{ $totalUsers ?? 0 }}. Click to view all users" data-bs-toggle="tooltip" title="Click to view all users">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Active Users</div>
                <div class="kpi-value">{{ number_format($totalUsers ?? 0) }}</div>
                <div class="kpi-change positive">
                    <span class="percentage">8%</span> from last month
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kpi-card" data-type="departments" role="button" tabindex="0" aria-label="Departments: {{ $totalDepartments ?? 0 }}. Click to view all departments" data-bs-toggle="tooltip" title="Click to view all departments">
            <div class="kpi-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Departments</div>
                <div class="kpi-value">{{ number_format($totalDepartments ?? 0) }}</div>
                <div class="kpi-change positive">
                    <span class="percentage">5%</span> from last month
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kpi-card" data-type="vendors" role="button" tabindex="0" aria-label="Vendors: {{ $totalVendors ?? 0 }}. Click to view all vendors" data-bs-toggle="tooltip" title="Click to view all vendors">
            <div class="kpi-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Vendors</div>
                <div class="kpi-value">{{ number_format($totalVendors ?? 0) }}</div>
                <div class="kpi-change positive">
                    <span class="percentage">15%</span> from last month
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Section 1: Weekly Breakdown -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card" role="region" aria-labelledby="weekly-breakdown-heading">
            <div class="section-header collapsible-header" data-bs-toggle="collapse" data-bs-target="#weeklyBreakdownContent" role="button" aria-expanded="true" aria-controls="weeklyBreakdownContent">
                <h5 class="m-0 fw-bold d-flex align-items-center justify-content-between" id="weekly-breakdown-heading">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line me-3" aria-hidden="true"></i>
                        Section 1: Weekly Breakdown
                        <span class="badge bg-light text-dark ms-3 small" aria-label="Asset Lifecycle Status by Weeks">Asset Lifecycle Status by Weeks</span>
                    </div>
                    <i class="fas fa-chevron-down transition-icon" aria-hidden="true"></i>
                </h5>
            </div>
            <div class="card-body-modern collapse show" id="weeklyBreakdownContent">
                <!-- Enhanced Filter Controls -->
                <div class="filter-container">
                    <!-- Advanced Search Bar -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="search-container">
                                <label for="globalSearch" class="form-label fw-semibold text-muted small text-uppercase mb-2">Global Search</label>
                                <div class="input-group search-input-group">
                                    <span class="input-group-text search-icon">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" id="globalSearch" class="form-control search-input" placeholder="Search assets, users, departments, or any data..." aria-describedby="search-help">
                                    <button class="btn btn-outline-secondary search-clear" type="button" aria-label="Clear search" style="display: none;">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-primary search-submit" type="button">
                                        <i class="fas fa-search me-2" aria-hidden="true"></i>Search
                                    </button>
                                </div>
                                <div class="help-text" id="search-help">
                                    <i class="fas fa-lightbulb" aria-hidden="true"></i>Try searching for asset names, serial numbers, user names, or department codes
                                </div>
                                <div class="search-suggestions" id="searchSuggestions" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Filter Toggle -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button class="btn btn-outline-primary advanced-filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                                <i class="fas fa-sliders-h me-2" aria-hidden="true"></i>Advanced Filters
                                <i class="fas fa-chevron-down ms-2 transition-icon" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-outline-secondary ms-2 filter-reset" type="button">
                                <i class="fas fa-undo me-2" aria-hidden="true"></i>Reset All
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters Collapsible Section -->
                    <div class="collapse" id="advancedFilters">
                        <div class="advanced-filters-container p-3 mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label fw-semibold text-muted small text-uppercase">Status Filter</label>
                                    <select id="statusFilter" class="form-select modern-select" multiple aria-describedby="statusFilter-help">
                                        <option value="active">🟢 Active</option>
                                        <option value="inactive">🔴 Inactive</option>
                                        <option value="maintenance">🔧 Maintenance</option>
                                        <option value="disposed">🗑️ Disposed</option>
                                    </select>
                                    <div class="help-text" id="statusFilter-help">
                                        <i class="fas fa-info-circle" aria-hidden="true"></i>Hold Ctrl to select multiple
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="categoryFilter" class="form-label fw-semibold text-muted small text-uppercase">Category Filter</label>
                                    <select id="categoryFilter" class="form-select modern-select" multiple aria-describedby="categoryFilter-help">
                                        <option value="computer">💻 Computers</option>
                                        <option value="monitor">🖥️ Monitors</option>
                                        <option value="printer">🖨️ Printers</option>
                                        <option value="peripheral">🖱️ Peripherals</option>
                                    </select>
                                    <div class="help-text" id="categoryFilter-help">
                                        <i class="fas fa-info-circle" aria-hidden="true"></i>Filter by asset category
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="dateRangeFilter" class="form-label fw-semibold text-muted small text-uppercase">Date Range</label>
                                    <div class="input-group">
                                        <input type="date" id="dateFrom" class="form-control" aria-label="Date from">
                                        <span class="input-group-text">to</span>
                                        <input type="date" id="dateTo" class="form-control" aria-label="Date to">
                                    </div>
                                    <div class="help-text">
                                        <i class="fas fa-calendar" aria-hidden="true"></i>Filter by creation/update date
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="valueRangeFilter" class="form-label fw-semibold text-muted small text-uppercase">Value Range</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="valueMin" class="form-control" placeholder="Min" aria-label="Minimum value">
                                        <span class="input-group-text">to</span>
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="valueMax" class="form-control" placeholder="Max" aria-label="Maximum value">
                                    </div>
                                    <div class="help-text">
                                        <i class="fas fa-dollar-sign" aria-hidden="true"></i>Filter by asset value
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-primary apply-advanced-filters" type="button">
                                        <i class="fas fa-filter me-2" aria-hidden="true"></i>Apply Advanced Filters
                                    </button>
                                    <button class="btn btn-outline-secondary ms-2 clear-advanced-filters" type="button">
                                        <i class="fas fa-eraser me-2" aria-hidden="true"></i>Clear Advanced
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Filter Presets -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-2" id="quick-filters-label">Quick Filters</label>
                            <div class="btn-group" role="group" aria-labelledby="quick-filters-label" aria-describedby="quick-filters-help">
                                <button type="button" class="btn btn-outline-primary quick-filter" data-period="this-week" aria-pressed="false">
                                    <i class="fas fa-calendar-week me-2" aria-hidden="true"></i>This Week
                                </button>
                                <button type="button" class="btn btn-outline-primary quick-filter" data-period="this-month" aria-pressed="false">
                                    <i class="fas fa-calendar-alt me-2" aria-hidden="true"></i>This Month
                                </button>
                                <button type="button" class="btn btn-outline-primary quick-filter" data-period="this-year" aria-pressed="false">
                                    <i class="fas fa-calendar me-2" aria-hidden="true"></i>This Year
                                </button>
                                <button type="button" class="btn btn-outline-secondary quick-filter" data-period="all" aria-pressed="false">
                                    <i class="fas fa-globe me-2" aria-hidden="true"></i>All Time
                                </button>
                            </div>
                            <div class="help-text" id="quick-filters-help">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>Select a time period to filter the data
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Filters Display -->
                    <div class="row mb-3" id="activeFiltersRow" style="display: none;">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small text-uppercase mb-2">Active Filters</label>
                            <div class="active-filters d-flex flex-wrap align-items-center gap-2">
                                <!-- Filter tags will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="viewType" class="form-label fw-semibold text-muted small text-uppercase">View Type
                                <i class="fas fa-info-circle help-icon" data-bs-toggle="tooltip" title="Choose how to display the data" aria-label="Help: Choose how to display the data"></i>
                            </label>
                            <select id="viewType" class="form-select modern-select" aria-describedby="viewType-help">
                                <option value="weekly">📅 Weekly View</option>
                                <option value="monthly">📊 Monthly View</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="monthFilter" class="form-label fw-semibold text-muted small text-uppercase">Month
                                <i class="fas fa-info-circle help-icon" data-bs-toggle="tooltip" title="Filter by specific month" aria-label="Help: Filter by specific month"></i>
                            </label>
                            <select id="monthFilter" class="form-select modern-select" aria-describedby="monthFilter-help">
                                <option value="">🗓️ All Months</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="yearFilter" class="form-label fw-semibold text-muted small text-uppercase">Year</label>
                            <select id="yearFilter" class="form-select modern-select">
                                <option value="">📅 All Years</option>
                                @for($year = 2020; $year <= date('Y') + 1; $year++)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="applyFilter" class="btn btn-primary modern-btn w-100">
                                <i class="fas fa-filter me-2"></i>Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Enhanced Grid Layout for Weekly Breakdown -->
                <div id="weeklyGridView">
                    @if(!empty($weeklyBreakdown['months']))
                        @foreach($weeklyBreakdown['months'] as $monthName => $monthData)
                            <div class="month-section mb-4">
                                <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2"></i>{{ $monthName }}
                                    <span class="badge bg-primary ms-2">{{ array_sum(array_map('array_sum', array_values($monthData))) }} Total Assets</span>
                                </h6>
                                <div class="row g-3">
                                    @for($week = 1; $week <= 4; $week++)
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="dashboard-card h-100 week-card" data-month="{{ $monthName }}" data-week="{{ $week }}">
                                                <div class="card-header bg-gradient-primary text-white">
                                                    <h6 class="mb-0 fw-bold">
                                                        <i class="fas fa-calendar-week me-2"></i>Week {{ $week }}
                                                    </h6>
                                                    <small class="text-white-50">{{ array_sum($monthData["Week $week"] ?? []) }} assets</small>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-success-subtle border border-success rounded p-2 text-center drill-down" data-status="deployed" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view deployed assets">
                                                                <div class="text-success fw-bold small">✅ Deployed</div>
                                                                <div class="h5 mb-0 text-success fw-bold">{{ $monthData["Week $week"]['deployed'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-success" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['deployed'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-danger-subtle border border-danger rounded p-2 text-center drill-down" data-status="problematic" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view problematic assets">
                                                                <div class="text-danger fw-bold small">⚠️ Issues</div>
                                                                <div class="h5 mb-0 text-danger fw-bold">{{ $monthData["Week $week"]['problematic'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-danger" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['problematic'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-warning-subtle border border-warning rounded p-2 text-center drill-down" data-status="pending_confirm" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view pending assets">
                                                                <div class="text-warning fw-bold small">⏳ Pending</div>
                                                                <div class="h5 mb-0 text-warning fw-bold">{{ $monthData["Week $week"]['pending_confirm'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-warning" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['pending_confirm'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-info-subtle border border-info rounded p-2 text-center drill-down" data-status="returned" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view returned assets">
                                                                <div class="text-info fw-bold small">🔄 Returned</div>
                                                                <div class="h5 mb-0 text-info fw-bold">{{ $monthData["Week $week"]['returned'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-info" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['returned'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-secondary-subtle border border-secondary rounded p-2 text-center drill-down" data-status="disposed" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view disposed assets">
                                                                <div class="text-secondary fw-bold small">🗑️ Disposed</div>
                                                                <div class="h5 mb-0 text-secondary fw-bold">{{ $monthData["Week $week"]['disposed'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-secondary" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['disposed'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="status-metric-card bg-primary-subtle border border-primary rounded p-2 text-center drill-down" data-status="new_arrived" data-period="Week {{ $week }} ({{ $monthName }})" data-bs-toggle="tooltip" title="Click to view new arrivals">
                                                                <div class="text-primary fw-bold small">📦 New</div>
                                                                <div class="h5 mb-0 text-primary fw-bold">{{ $monthData["Week $week"]['new_arrived'] ?? 0 }}</div>
                                                                <div class="progress-mini mt-1">
                                                                    <div class="progress-bar bg-primary" style="width: {{ array_sum($monthData["Week $week"] ?? []) > 0 ? round(($monthData["Week $week"]['new_arrived'] ?? 0) / array_sum($monthData["Week $week"] ?? []) * 100) : 0 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No breakdown data available</h5>
                            <p class="text-muted">Weekly breakdown data will appear here once assets are tracked.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Monthly View (Hidden by default) -->
                <div id="monthlyGridView" style="display: none;">
                    @if(!empty($monthlyRollup['months']))
                        <div class="row g-4">
                            @foreach($monthlyRollup['months'] as $monthName => $monthData)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="dashboard-card h-100 month-card">
                                        <div class="card-header bg-gradient-info text-white">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="fas fa-calendar me-2"></i>{{ $monthName }}
                                            </h6>
                                            <small class="text-white-50">{{ array_sum(array_column($monthData, 'count')) }} total assets</small>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-success-subtle border border-success rounded p-2 text-center drill-down" data-status="deployed" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view deployed assets">
                                                        <div class="text-success fw-bold small">✅ Deployed</div>
                                                        <div class="h5 mb-0 text-success fw-bold">{{ $monthData['deployed']['count'] ?? 0 }}</div>
                                                        <small class="text-success">({{ $monthData['deployed']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-danger-subtle border border-danger rounded p-2 text-center drill-down" data-status="problematic" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view problematic assets">
                                                        <div class="text-danger fw-bold small">⚠️ Issues</div>
                                                        <div class="h5 mb-0 text-danger fw-bold">{{ $monthData['problematic']['count'] ?? 0 }}</div>
                                                        <small class="text-danger">({{ $monthData['problematic']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-warning-subtle border border-warning rounded p-2 text-center drill-down" data-status="pending_confirm" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view pending assets">
                                                        <div class="text-warning fw-bold small">⏳ Pending</div>
                                                        <div class="h5 mb-0 text-warning fw-bold">{{ $monthData['pending_confirm']['count'] ?? 0 }}</div>
                                                        <small class="text-warning">({{ $monthData['pending_confirm']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-info-subtle border border-info rounded p-2 text-center drill-down" data-status="returned" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view returned assets">
                                                        <div class="text-info fw-bold small">🔄 Returned</div>
                                                        <div class="h5 mb-0 text-info fw-bold">{{ $monthData['returned']['count'] ?? 0 }}</div>
                                                        <small class="text-info">({{ $monthData['returned']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-secondary-subtle border border-secondary rounded p-2 text-center drill-down" data-status="disposed" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view disposed assets">
                                                        <div class="text-secondary fw-bold small">🗑️ Disposed</div>
                                                        <div class="h5 mb-0 text-secondary fw-bold">{{ $monthData['disposed']['count'] ?? 0 }}</div>
                                                        <small class="text-secondary">({{ $monthData['disposed']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="status-metric-card bg-primary-subtle border border-primary rounded p-2 text-center drill-down" data-status="new_arrived" data-period="{{ $monthName }}" data-bs-toggle="tooltip" title="Click to view new arrivals">
                                                        <div class="text-primary fw-bold small">📦 New</div>
                                                        <div class="h5 mb-0 text-primary fw-bold">{{ $monthData['new_arrived']['count'] ?? 0 }}</div>
                                                        <small class="text-primary">({{ $monthData['new_arrived']['percentage'] ?? 0 }}%)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No monthly data available</h5>
                            <p class="text-muted">Monthly rollup data will appear here once assets are tracked.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>






</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Enhanced filter functionality with smooth animations
    $('#viewType').on('change', function() {
        const viewType = $(this).val();
        
        if (viewType === 'weekly') {
            $('.monthly-row').fadeOut(300);
            setTimeout(() => {
                $('.weekly-row').fadeIn(300);
            }, 300);
        } else {
            $('.weekly-row').fadeOut(300);
            setTimeout(() => {
                $('.monthly-row').fadeIn(300);
            }, 300);
        }
    });
    
    // Enhanced apply filter with loading state
    $('#applyFilter').on('click', function() {
        const button = $(this);
        const originalText = button.html();
        
        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        button.prop('disabled', true);
        
        const month = $('#monthFilter').val();
        const year = $('#yearFilter').val();
        
        let url = window.location.pathname + '?';
        const params = [];
        
        if (month) params.push('month=' + month);
        if (year) params.push('year=' + year);
        
        if (params.length > 0) {
            url += params.join('&');
        }
        
        // Simulate loading for better UX
        setTimeout(() => {
            window.location.href = url;
        }, 500);
    });
    
    // Quick filter functionality
    $('.quick-filter').on('click', function() {
        // Remove active class from all filters
        $('.quick-filter').removeClass('active');
        // Add active class to clicked filter
        $(this).addClass('active');
        
        const period = $(this).data('period');
        applyQuickFilter(period);
    });
    
    // Collapsible sections with chevron rotation
    $('.collapsible-header').on('click', function() {
        const chevron = $(this).find('.transition-icon');
        const target = $(this).attr('data-bs-target');
        const isExpanded = $(target).hasClass('show');
        
        if (chevron.length) {
            chevron.css({
                'transform': isExpanded ? 'rotate(0deg)' : 'rotate(180deg)',
                'transition': 'transform 0.3s ease'
            });
        }
    });
    
    // Drill-down functionality
    $('.drill-down').on('click', function() {
        const status = $(this).data('status');
        
        // Show loading state
        showLoadingState($(this).closest('.dashboard-card'));
        
        // Simulate API call delay
        setTimeout(() => {
            hideLoadingState($(this).closest('.dashboard-card'));
            showFeedback('Data loaded successfully!', 'success');
        }, 1500);
    });
    
    // Loading State Functions
    function showLoadingState(element) {
        if (!element.find('.loading-overlay').length) {
            const overlay = $('<div class="loading-overlay"><div class="loading-spinner"></div></div>');
            element.css('position', 'relative').append(overlay);
        }
    }
    
    function hideLoadingState(element) {
        element.find('.loading-overlay').remove();
    }
    
    // Skeleton Screen Functions
    function showSkeletonScreen(container) {
        const skeletonHTML = `
            <div class="skeleton-card skeleton"></div>
            <div class="skeleton-text large skeleton"></div>
            <div class="skeleton-text skeleton"></div>
            <div class="skeleton-text small skeleton"></div>
        `;
        container.html(skeletonHTML);
    }
    
    function hideSkeletonScreen(container, originalContent) {
        container.html(originalContent);
    }
    
    // Feedback Message Functions
    function showFeedback(message, type = 'info', duration = 3000) {
        const feedbackId = 'feedback-' + Date.now();
        const feedback = $(`
            <div id="${feedbackId}" class="feedback-message ${type}">
                <i class="fas fa-${getFeedbackIcon(type)} me-2"></i>
                ${message}
            </div>
        `);
        
        $('body').append(feedback);
        
        // Show with animation
        setTimeout(() => feedback.addClass('show'), 100);
        
        // Auto hide
        setTimeout(() => {
            feedback.removeClass('show');
            setTimeout(() => feedback.remove(), 300);
        }, duration);
    }
    
    function getFeedbackIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Progress Indicator Functions
    function showProgressIndicator() {
        if (!$('.progress-indicator').length) {
            const progress = $('<div class="progress-indicator"><div class="progress-bar"></div></div>');
            $('body').prepend(progress);
        }
    }
    
    function updateProgress(percentage) {
        $('.progress-indicator .progress-bar').css('width', percentage + '%');
    }
    
    function hideProgressIndicator() {
        $('.progress-indicator').remove();
    }
    
    // Enhanced Click Handlers with Loading States
    $('.clickable-card').on('click', function() {
        const card = $(this);
        showLoadingState(card);
        
        // Simulate navigation/data loading
        setTimeout(() => {
            hideLoadingState(card);
            showFeedback('Navigating to detailed view...', 'info');
        }, 800);
    });
    
    // Filter Apply with Loading
    $('#applyFilter').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show loading state
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Applying...')
           .prop('disabled', true);
        
        showProgressIndicator();
        
        // Simulate filter application
        let progress = 0;
        const interval = setInterval(() => {
            progress += 20;
            updateProgress(progress);
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    hideProgressIndicator();
                    btn.html(originalText).prop('disabled', false);
                    showFeedback('Filters applied successfully!', 'success');
                }, 500);
            }
        }, 200);
    });
    
    // Accessibility: Keyboard Navigation for Cards
    $('.clickable-card').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });
    
    // Quick Filter Accessibility
    $('.quick-filter').on('click', function() {
        // Update aria-pressed state
        $('.quick-filter').attr('aria-pressed', 'false');
        $(this).attr('aria-pressed', 'true');
    });
    
    // Advanced Search Functionality
    let searchTimeout;
    const searchInput = $('#globalSearch');
    const searchClear = $('.search-clear');
    const searchSuggestions = $('#searchSuggestions');
    
    // Search input handling
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        if (query.length > 0) {
            searchClear.show();
            clearTimeout(searchTimeout);
            
            // Debounce search suggestions
            searchTimeout = setTimeout(() => {
                showSearchSuggestions(query);
            }, 300);
        } else {
            searchClear.hide();
            searchSuggestions.hide();
        }
    });
    
    // Clear search
    searchClear.on('click', function() {
        searchInput.val('').focus();
        $(this).hide();
        searchSuggestions.hide();
        clearActiveFilters();
    });
    
    // Search submit
    $('.search-submit').on('click', function() {
        const query = searchInput.val().trim();
        if (query.length > 0) {
            performSearch(query);
        }
    });
    
    // Enter key search
    searchInput.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query.length > 0) {
                performSearch(query);
            }
        }
    });
    
    // Advanced Filter Toggle
    $('.advanced-filter-toggle').on('click', function() {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !isExpanded);
    });
    
    // Apply Advanced Filters
    $('.apply-advanced-filters').on('click', function() {
        const filters = collectAdvancedFilters();
        applyAdvancedFilters(filters);
    });
    
    // Clear Advanced Filters
    $('.clear-advanced-filters').on('click', function() {
        clearAdvancedFilters();
    });
    
    // Reset All Filters
    $('.filter-reset').on('click', function() {
        clearAllFilters();
    });
    
    // Search Suggestions Functions
    function showSearchSuggestions(query) {
        // Mock suggestions - in real app, this would be an API call
        const suggestions = [
            { type: 'asset', text: 'Dell Laptop #DL001', icon: 'laptop' },
            { type: 'user', text: 'John Doe', icon: 'user' },
            { type: 'department', text: 'IT Department', icon: 'building' },
            { type: 'serial', text: 'SN: ABC123456', icon: 'barcode' }
        ].filter(item => item.text.toLowerCase().includes(query.toLowerCase()));
        
        if (suggestions.length > 0) {
            let html = '';
            suggestions.forEach(suggestion => {
                html += `
                    <div class="suggestion-item" data-value="${suggestion.text}">
                        <i class="fas fa-${suggestion.icon} me-2 text-muted"></i>
                        ${suggestion.text}
                        <small class="text-muted ms-2">(${suggestion.type})</small>
                    </div>
                `;
            });
            searchSuggestions.html(html).show();
            
            // Handle suggestion clicks
            $('.suggestion-item').on('click', function() {
                const value = $(this).data('value');
                searchInput.val(value);
                searchSuggestions.hide();
                performSearch(value);
            });
        } else {
            searchSuggestions.hide();
        }
    }
    
    // Perform Search Function
    function performSearch(query) {
        showLoadingState($('.search-container'));
        showFeedback(`Searching for: "${query}"`, 'info');
        
        // Simulate search API call
        setTimeout(() => {
            hideLoadingState($('.search-container'));
            searchSuggestions.hide();
            
            // Add search filter tag
            addFilterTag('search', query, 'search');
            
            showFeedback(`Found results for: "${query}"`, 'success');
        }, 1000);
    }
    
    // Collect Advanced Filters
    function collectAdvancedFilters() {
        return {
            status: $('#statusFilter').val() || [],
            category: $('#categoryFilter').val() || [],
            dateFrom: $('#dateFrom').val(),
            dateTo: $('#dateTo').val(),
            valueMin: $('#valueMin').val(),
            valueMax: $('#valueMax').val()
        };
    }
    
    // Apply Advanced Filters
    function applyAdvancedFilters(filters) {
        showLoadingState($('.advanced-filters-container'));
        showFeedback('Applying advanced filters...', 'info');
        
        // Simulate filter application
        setTimeout(() => {
            hideLoadingState($('.advanced-filters-container'));
            
            // Add filter tags for active filters
            Object.keys(filters).forEach(key => {
                const value = filters[key];
                if (value && (Array.isArray(value) ? value.length > 0 : value.toString().trim())) {
                    if (Array.isArray(value)) {
                        value.forEach(v => addFilterTag(key, v, key));
                    } else {
                        addFilterTag(key, value, key);
                    }
                }
            });
            
            showFeedback('Advanced filters applied successfully!', 'success');
            
            // Collapse advanced filters
            $('#advancedFilters').collapse('hide');
        }, 1500);
    }
    
    // Clear Advanced Filters
    function clearAdvancedFilters() {
        $('#statusFilter, #categoryFilter').val([]);
        $('#dateFrom, #dateTo, #valueMin, #valueMax').val('');
        removeFilterTags(['status', 'category', 'dateFrom', 'dateTo', 'valueMin', 'valueMax']);
        showFeedback('Advanced filters cleared', 'info');
    }
    
    // Clear All Filters
     function clearAllFilters() {
         searchInput.val('');
         searchClear.hide();
         searchSuggestions.hide();
         clearAdvancedFilters();
         $('.active-filters').empty();
         $('#activeFiltersRow').hide();
         showFeedback('All filters cleared', 'info');
     }
    
    // Clear Active Filters
     function clearActiveFilters() {
         $('.active-filters').empty();
         $('#activeFiltersRow').hide();
     }
    
    // Add Filter Tag
     function addFilterTag(type, value, label) {
         const tagId = `filter-${type}-${Date.now()}`;
         const tag = $(`
             <span class="filter-tag" id="${tagId}" data-type="${type}" data-value="${value}">
                 ${label}: ${value}
                 <i class="fas fa-times remove-filter" data-tag-id="${tagId}"></i>
             </span>
         `);
         
         $('.active-filters').append(tag);
         $('#activeFiltersRow').show();
         
         // Handle tag removal
         tag.find('.remove-filter').on('click', function() {
             const tagId = $(this).data('tag-id');
             $(`#${tagId}`).fadeOut(200, function() {
                 $(this).remove();
                 // Hide active filters row if no tags remain
                 if ($('.active-filters .filter-tag').length === 0) {
                     $('#activeFiltersRow').hide();
                 }
             });
         });
     }
    
    // Remove Filter Tags
    function removeFilterTags(types) {
        types.forEach(type => {
            $(`.filter-tag[data-type="${type}"]`).fadeOut(200, function() {
                $(this).remove();
            });
        });
    }
        const period = $(this).data('period');
        
        // Show loading state
        $(this).css({
            'opacity': '0.7',
            'transform': 'scale(0.95)'
        });
        
        // Simulate drill-down navigation
        setTimeout(() => {
            alert(`Drilling down to ${status} assets for ${period}\n\nThis would navigate to a filtered asset list.`);
            $(this).css({
                'opacity': '1',
                'transform': 'scale(1)'
            });
        }, 300);
    });
    
    // Add hover effects to table rows
    $('.table-modern tbody tr').hover(
        function() {
            $(this).css({
                'background-color': '#f8f9fa',
                'transform': 'scale(1.01)',
                'transition': 'all 0.2s ease'
            });
        },
        function() {
            $(this).css({
                'background-color': '',
                'transform': 'scale(1)',
                'transition': 'all 0.2s ease'
            });
        }
    );
    
    // Initialize tooltips for status badges
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Add pulse animation to statistics cards
    $('.stat-card').each(function(index) {
        setTimeout(() => {
            $(this).addClass('animate__animated animate__fadeInUp');
        }, index * 100);
    });
    
    // Smooth filter animations
    const filterContainer = $('.filter-container');
    if (filterContainer.length) {
        filterContainer.css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        });
        
        setTimeout(() => {
            filterContainer.css({
                'transition': 'all 0.6s ease',
                'opacity': '1',
                'transform': 'translateY(0)'
            });
        }, 300);
    }
});

// Quick filter application function
function applyQuickFilter(filterType) {
    const currentDate = new Date();
    let startDate, endDate;
    
    switch(filterType) {
        case 'this-week':
            startDate = new Date(currentDate.setDate(currentDate.getDate() - 7));
            endDate = new Date();
            break;
        case 'this-month':
            startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            break;
        case 'this-year':
            startDate = new Date(currentDate.getFullYear(), 0, 1);
            endDate = new Date(currentDate.getFullYear(), 11, 31);
            break;
        case 'all':
            // Show all data
            console.log('Showing all data');
            return;
    }
    
    // Here you would typically update the form fields and submit
    console.log(`Filtering from ${startDate.toDateString()} to ${endDate.toDateString()}`);
    
    // Show visual feedback
    const filterContainer = $('.filter-container');
    if (filterContainer.length) {
        filterContainer.css('opacity', '0.7');
        setTimeout(() => {
            filterContainer.css('opacity', '1');
        }, 500);
    }
}

// Performance Optimizations and Progressive Enhancement

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Intersection Observer for lazy loading
const observerOptions = {
    root: null,
    rootMargin: '50px',
    threshold: 0.1
};

const intersectionObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            intersectionObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

// Lazy load images and content
function initLazyLoading() {
    const lazyElements = document.querySelectorAll('.lazy-load, .observe-visibility');
    lazyElements.forEach(el => {
        intersectionObserver.observe(el);
    });
}

// Memory efficient event delegation
function addDelegatedEventListener(parent, eventType, selector, handler) {
    parent.addEventListener(eventType, function(e) {
        if (e.target.matches(selector)) {
            handler.call(e.target, e);
        }
    });
}

// Progressive enhancement - Check for features
const supportsIntersectionObserver = 'IntersectionObserver' in window;
const supportsRequestAnimationFrame = 'requestAnimationFrame' in window;
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Optimized animation frame
function optimizedAnimate(callback) {
    if (supportsRequestAnimationFrame && !prefersReducedMotion) {
        requestAnimationFrame(callback);
    } else {
        setTimeout(callback, 16); // Fallback to 60fps
    }
}

// Preload critical resources
function preloadCriticalResources() {
    const criticalImages = document.querySelectorAll('img[data-critical]');
    criticalImages.forEach(img => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = img.src || img.dataset.src;
        document.head.appendChild(link);
    });
}

// Initialize performance optimizations
function initPerformanceOptimizations() {
    // Initialize lazy loading if supported
    if (supportsIntersectionObserver) {
        initLazyLoading();
    } else {
        // Fallback for older browsers
        document.querySelectorAll('.lazy-load, .observe-visibility').forEach(el => {
            el.classList.add('visible', 'loaded');
        });
    }
    
    // Preload critical resources
    preloadCriticalResources();
    
    // Add efficient event delegation for cards
    const dashboard = document.querySelector('.dashboard-container, .container-fluid');
    if (dashboard) {
        addDelegatedEventListener(dashboard, 'click', '.clickable-card, .stat-card', function(e) {
            optimizedAnimate(() => {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    }
    
    // Optimize search input with debouncing
    const searchInput = document.querySelector('#globalSearch');
    if (searchInput) {
        const debouncedSearch = debounce((value) => {
            if (value.length > 2) {
                // Perform search operation
                console.log('Optimized search for:', value);
            }
        }, 300);
        
        searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
    
    // Add scroll performance optimization
    const scrollableElements = document.querySelectorAll('.table-responsive, .scrollable-content');
    scrollableElements.forEach(element => {
        const throttledScroll = throttle(() => {
            // Handle scroll events efficiently
            element.style.willChange = 'scroll-position';
        }, 16);
        
        element.addEventListener('scroll', throttledScroll);
    });
}

// Initialize on DOM ready with performance considerations
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPerformanceOptimizations);
} else {
    // Use setTimeout to avoid blocking the main thread
    setTimeout(initPerformanceOptimizations, 0);
}

// Add visibility change optimization
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        // Pause expensive operations when tab is hidden
        console.log('Dashboard hidden - pausing operations');
    } else {
        // Resume operations when tab becomes visible
        console.log('Dashboard visible - resuming operations');
    }
});

</script>
@endpush



@section('scripts')
<script>
    // Add any dashboard-specific JavaScript here
    console.log('Dashboard loaded successfully');
    
    // View type toggle functionality for grid layout
    document.getElementById('viewType').addEventListener('change', function() {
        const viewType = this.value;
        const weeklyGridView = document.getElementById('weeklyGridView');
        const monthlyGridView = document.getElementById('monthlyGridView');
        const periodHeader = document.getElementById('periodHeader');
        
        if (viewType === 'weekly') {
            weeklyGridView.style.display = 'block';
            monthlyGridView.style.display = 'none';
            periodHeader.textContent = 'Week';
            
            // Add fade-in animation
            weeklyGridView.style.opacity = '0';
            setTimeout(() => {
                weeklyGridView.style.transition = 'opacity 0.3s ease';
                weeklyGridView.style.opacity = '1';
            }, 50);
        } else {
            weeklyGridView.style.display = 'none';
            monthlyGridView.style.display = 'block';
            periodHeader.textContent = 'Month';
            
            // Add fade-in animation
            monthlyGridView.style.opacity = '0';
            setTimeout(() => {
                monthlyGridView.style.transition = 'opacity 0.3s ease';
                monthlyGridView.style.opacity = '1';
            }, 50);
        }
    });
    
    // Filter functionality
     document.getElementById('applyFilter').addEventListener('click', function() {
         const month = document.getElementById('monthFilter').value;
         const year = document.getElementById('yearFilter').value;
         
         // Build URL with filter parameters
         let url = '{{ route("dashboard") }}';
         const params = new URLSearchParams();
         
         if (month && month !== 'all') {
             params.append('month', month);
         }
         if (year && year !== 'all') {
             params.append('year', year);
         }
         
         if (params.toString()) {
             url += '?' + params.toString();
         }
         
         // Redirect to filtered dashboard
         window.location.href = url;
     });
</script>
@endsection