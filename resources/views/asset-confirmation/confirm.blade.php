<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Assignment Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --success-color: #10b981;
            --success-dark: #059669;
            --danger-color: #ef4444;
            --danger-dark: #dc2626;
            --warning-color: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, #e0e7ff 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .confirmation-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0;
        }

        .main-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.1;
        }

        .card-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            position: relative;
            z-index: 1;
        }

        .card-header p {
            font-size: 1.125rem;
            margin: 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 2rem;
        }

        .progress-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--gray-100);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        .progress-step.active {
            background: var(--primary-color);
            color: white;
        }

        .user-welcome {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #93c5fd;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .user-welcome h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0 0 0.5rem 0;
        }

        .user-welcome p {
            color: var(--gray-600);
            margin: 0;
            font-size: 1rem;
        }

        .instruction-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--warning-color);
        }

        .instruction-card .icon {
            color: var(--warning-color);
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }

        .asset-details {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .asset-details h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }

        .detail-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-800);
            word-break: break-word;
        }

        .action-section {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .action-section h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 1rem 0;
        }

        .action-section p {
            color: var(--gray-600);
            margin: 0 0 2rem 0;
            font-size: 1.125rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--success-color) 0%, var(--success-dark) 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
            text-decoration: none;
        }

        .btn-primary-action:focus {
            outline: 2px solid var(--success-color);
            outline-offset: 2px;
        }

        .btn-secondary-action {
            background: white;
            border: 2px solid var(--danger-color);
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            color: var(--danger-color);
            font-weight: 600;
            font-size: 1.125rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-secondary-action:hover {
            background: var(--danger-color);
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary-action:focus {
            outline: 2px solid var(--danger-color);
            outline-offset: 2px;
        }

        .security-notice {
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            margin-top: 2rem;
        }

        .security-notice p {
            color: var(--gray-600);
            margin: 0;
            font-size: 0.875rem;
        }

        .resend-section {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn-resend {
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: var(--gray-700);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-resend:hover {
            background: var(--gray-200);
            color: var(--gray-800);
            text-decoration: none;
        }

        .btn-resend:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus indicators */
        *:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .main-card {
                border: 2px solid var(--gray-800);
            }
            
            .detail-item {
                border: 2px solid var(--gray-600);
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Mobile responsive design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .card-header {
                padding: 1.5rem 1rem;
            }
            
            .card-header h1 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 1.5rem 1rem;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                gap: 0.75rem;
            }
            
            .btn-primary-action,
            .btn-secondary-action {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .progress-indicator {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .progress-step {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="progress-step active">
                <i class="fas fa-envelope" aria-hidden="true"></i>
                <span>Email Received</span>
            </div>
            <div class="progress-step active">
                <i class="fas fa-eye" aria-hidden="true"></i>
                <span>Review Details</span>
            </div>
            <div class="progress-step">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <span>Confirm Receipt</span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="main-card">
            <div class="card-header">
                <h1>
                    <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                    Asset Assignment Confirmation
                </h1>
                <p>Please review and confirm your asset assignment</p>
            </div>
            
            <div class="card-body">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> 
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i> 
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- User Welcome Section -->
                <div class="user-welcome">
                    <h2>
                        <i class="fas fa-user" aria-hidden="true"></i>
                        Hello, {{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}!
                    </h2>
                    <p>An asset has been assigned to you. Please review the details below and confirm receipt.</p>
                </div>

                <!-- Instruction Card -->
                <div class="instruction-card">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle icon" aria-hidden="true"></i>
                        <div>
                            <strong>What you need to do:</strong>
                            <p class="mb-0 mt-1">Review the asset details below and click the appropriate button to confirm whether you have received this asset.</p>
                        </div>
                    </div>
                </div>

                <!-- Asset Details Section -->
                <div class="asset-details">
                    <h3>
                        <i class="fas fa-laptop" aria-hidden="true"></i>
                        Asset Details
                    </h3>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-tag" aria-hidden="true"></i>
                                Asset Tag
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->asset_tag }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-desktop" aria-hidden="true"></i>
                                Asset Name
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->asset_name }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-layer-group" aria-hidden="true"></i>
                                Category
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->assetCategory->category_name ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-industry" aria-hidden="true"></i>
                                Brand
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->brand ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-cogs" aria-hidden="true"></i>
                                Model
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->model ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-barcode" aria-hidden="true"></i>
                                Serial Number
                            </div>
                            <div class="detail-value">{{ $confirmation->asset->serial_number ?? 'N/A' }}</div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                Assignment Date
                            </div>
                            <div class="detail-value">{{ $confirmation->assigned_at->format('F j, Y') }}</div>
                        </div>
                        
                        @if($confirmation->notes)
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-sticky-note" aria-hidden="true"></i>
                                Notes
                            </div>
                            <div class="detail-value">{{ $confirmation->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Section -->
                <div class="action-section">
                    <h3>
                        <i class="fas fa-question-circle" aria-hidden="true"></i>
                        Confirm Asset Receipt
                    </h3>
                    <p>Please confirm whether you have received this asset by selecting one of the options below.</p>
                    
                    <div class="action-buttons">
                        <a href="{{ url('/asset-confirmation/confirm/' . $confirmation->confirmation_token) }}" 
                           class="btn-primary-action" 
                           id="confirmBtn"
                           onclick="return handleConfirmClick(this)"
                           role="button"
                           aria-describedby="confirm-help">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            <span>Yes, I have received this asset</span>
                        </a>
                        
                        <a href="{{ url('/asset-confirmation/decline-form/' . $confirmation->confirmation_token) }}" 
                           class="btn-secondary-action" 
                           id="declineBtn"
                           onclick="return handleDeclineClick(this)"
                           role="button"
                           aria-describedby="decline-help">
                            <i class="fas fa-times-circle" aria-hidden="true"></i>
                            <span>No, I have not received this asset</span>
                        </a>
                    </div>
                    
                    <div id="confirm-help" class="sr-only">
                        Click this button if you have physically received the asset and it matches the details shown above.
                    </div>
                    <div id="decline-help" class="sr-only">
                        Click this button if you have not received the asset or if there are discrepancies with the details shown.
                    </div>
                </div>

                <!-- Resend Section -->
                <div class="resend-section">
                    <p class="mb-3">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        Didn't receive the confirmation email or need to resend it?
                    </p>
                    <a href="#" class="btn-resend" onclick="resendConfirmation(event)">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        Resend Confirmation Email
                    </a>
                </div>

                <!-- Processing overlay -->
                <div id="processingOverlay" class="processing-overlay" style="display: none;">
                    <div class="processing-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <p class="mt-3">Processing your request...</p>
                        <p class="text-muted">Please do not refresh or close this page.</p>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-3" aria-hidden="true" style="margin-top: 2px;"></i>
                        <div>
                            <strong>Important Notice:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Please confirm receipt within <strong>3 days</strong> of receiving this email</li>
                                <li>If you don't receive your asset, we will send follow-up reminders</li>
                                <li>Contact the IT Asset Management Team if you have any questions</li>
                            </ul>
                            <div class="mt-3">
                                <strong>Thank you,</strong><br>
                                IT Asset Management Team
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="security-notice">
            <p>
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                This is a secure link that expires after use. Do not share this link with others.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Processing Overlay -->
    <div class="processing-overlay" id="processingOverlay" role="dialog" aria-labelledby="processing-title" aria-describedby="processing-desc">
        <div class="processing-content">
            <div class="spinner" aria-hidden="true"></div>
            <h3 id="processing-title">Processing Your Request</h3>
            <p id="processing-desc">Please wait while we process your confirmation...</p>
        </div>
    </div>
    
    <style>
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }
        
        .processing-content {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-xl);
            max-width: 400px;
            margin: 1rem;
        }
        
        .processing-content h3 {
            color: var(--gray-800);
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .processing-content p {
            color: var(--gray-600);
            margin: 0;
            font-size: 1rem;
        }
        
        .spinner {
            border: 4px solid var(--gray-200);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        /* Loading state for buttons */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Toast notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }
        
        .toast {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 1rem;
            margin-bottom: 0.5rem;
            min-width: 300px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .toast.success {
            border-left: 4px solid var(--success-color);
        }
        
        .toast.error {
            border-left: 4px solid var(--danger-color);
        }
        
        .toast.info {
            border-left: 4px solid var(--primary-color);
        }
    </style>
    
    <script>
        // Toast notification system
        function showToast(message, type = 'info', duration = 5000) {
            const container = document.querySelector('.toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${getToastIcon(type)} me-2" aria-hidden="true"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close ms-auto" onclick="hideToast(this)" aria-label="Close"></button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Auto hide
            setTimeout(() => hideToast(toast.querySelector('.btn-close')), duration);
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }
        
        function getToastIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-triangle',
                info: 'info-circle',
                warning: 'exclamation-triangle'
            };
            return icons[type] || 'info-circle';
        }
        
        function hideToast(button) {
            const toast = button.closest('.toast');
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
        
        // Enhanced processing overlay
        function showProcessing(message = 'Processing Your Request', description = 'Please wait while we process your confirmation...') {
            const overlay = document.getElementById('processingOverlay');
            const title = document.getElementById('processing-title');
            const desc = document.getElementById('processing-desc');
            
            title.textContent = message;
            desc.textContent = description;
            overlay.style.display = 'flex';
            
            // Disable all interactive elements
            disableInteractiveElements();
            
            // Prevent back button during processing
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };
        }
        
        function hideProcessing() {
            document.getElementById('processingOverlay').style.display = 'none';
            enableInteractiveElements();
        }
        
        function disableInteractiveElements() {
            const buttons = document.querySelectorAll('a[role="button"], button');
            buttons.forEach(btn => {
                btn.classList.add('btn-disabled');
                btn.setAttribute('aria-disabled', 'true');
            });
        }
        
        function enableInteractiveElements() {
            const buttons = document.querySelectorAll('a[role="button"], button');
            buttons.forEach(btn => {
                btn.classList.remove('btn-disabled', 'btn-loading');
                btn.removeAttribute('aria-disabled');
            });
        }
        
        function handleConfirmClick(button) {
            button.classList.add('btn-loading');
            showProcessing('Confirming Receipt', 'Please wait while we confirm your asset receipt...');
            
            // Add a small delay to show the loading state
            setTimeout(() => {
                window.location.href = button.href;
            }, 500);
            
            return false; // Prevent immediate navigation
        }
        
        function handleDeclineClick(button) {
            button.classList.add('btn-loading');
            showProcessing('Processing Decline', 'Please wait while we process your response...');
            
            // Add a small delay to show the loading state
            setTimeout(() => {
                window.location.href = button.href;
            }, 500);
            
            return false; // Prevent immediate navigation
        }
        
        // Resend confirmation functionality
        async function resendConfirmation(event) {
            event.preventDefault();
            const button = event.target.closest('.btn-resend');
            
            // Add loading state
            button.classList.add('btn-loading');
            button.setAttribute('aria-disabled', 'true');
            
            try {
                // Simulate API call (replace with actual endpoint)
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                showToast('Confirmation email has been resent successfully!', 'success');
                
                // Disable resend button for 60 seconds
                let countdown = 60;
                const originalText = button.innerHTML;
                const updateButton = () => {
                    if (countdown > 0) {
                        button.innerHTML = `<i class="fas fa-clock" aria-hidden="true"></i> Resend in ${countdown}s`;
                        countdown--;
                        setTimeout(updateButton, 1000);
                    } else {
                        button.innerHTML = originalText;
                        button.classList.remove('btn-loading');
                        button.removeAttribute('aria-disabled');
                    }
                };
                updateButton();
                
            } catch (error) {
                showToast('Failed to resend confirmation email. Please try again.', 'error');
                button.classList.remove('btn-loading');
                button.removeAttribute('aria-disabled');
            }
        }
        
        // Re-enable elements when page is shown (back button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideProcessing();
            }
        });
        
        // Keyboard navigation improvements
        document.addEventListener('keydown', function(event) {
            // Close processing overlay with Escape key
            if (event.key === 'Escape') {
                const overlay = document.getElementById('processingOverlay');
                if (overlay.style.display === 'flex') {
                    hideProcessing();
                }
            }
            
            // Quick confirm with Ctrl+Enter
            if (event.ctrlKey && event.key === 'Enter') {
                const confirmBtn = document.getElementById('confirmBtn');
                if (confirmBtn && !confirmBtn.classList.contains('btn-disabled')) {
                    confirmBtn.click();
                }
            }
        });
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Focus management for accessibility
            const firstFocusableElement = document.querySelector('a[role="button"]:not([aria-disabled="true"])');
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
            
            // Add keyboard shortcuts info
            console.log('Keyboard shortcuts: Ctrl+Enter to confirm, Escape to close overlays');
        });
    </script>
</body>
</html>