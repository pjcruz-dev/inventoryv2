<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $action === 'confirmed' ? 'Asset Confirmation Successful' : 'Asset Assignment Declined' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset and accessibility improvements */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: #374151;
        }
        
        /* Skip to main content for accessibility */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: #000;
            color: #fff;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
        }
        
        .skip-link:focus {
            top: 6px;
        }
        
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
        }
        
        .card-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 0;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .card-header.declined {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }
        
        .success-icon {
            font-size: 4.5rem;
            margin-bottom: 20px;
            animation: bounce 1s ease-in-out;
            text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-15px);
            }
            60% {
                transform: translateY(-8px);
            }
        }
        
        /* Progress indicator */
        .progress-indicator {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            margin-bottom: 0;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
            position: relative;
        }
        
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 500;
            position: relative;
        }
        
        .progress-step.completed {
            color: white;
        }
        
        .progress-step .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .progress-step.completed .step-icon {
            background: rgba(255, 255, 255, 0.9);
            color: #10b981;
            border-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .card-header.declined .progress-step.completed .step-icon {
            color: #dc3545;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        /* Skip link for accessibility */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: #000;
            color: #fff;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            transition: top 0.3s;
        }
        
        .skip-link:focus {
            top: 6px;
        }
        
        /* Resend section styles */
        .resend-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #6c757d;
        }
        
        .resend-controls .btn {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .resend-controls .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .alert-sm {
            padding: 8px 12px;
            font-size: 0.875rem;
            border-radius: 8px;
        }
        /* Asset information styling */
        .asset-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 16px;
            padding: 28px;
            margin: 24px 0;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.1);
        }
        
        .asset-info h5 {
            color: #0c4a6e;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        
        .info-row .label {
            font-weight: 600;
            color: #0c4a6e;
            font-size: 14px;
        }
        
        .info-row .value {
            color: #374151;
            font-weight: 500;
            text-align: right;
        }
        
        /* Next steps styling */
        .next-steps {
            background: linear-gradient(135deg, #f0fff4 0%, #dcfce7 100%);
            border: 1px solid #22c55e;
            border-radius: 16px;
            padding: 28px;
            margin: 24px 0;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.1);
        }
        
        .next-steps h5 {
            color: #15803d;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .next-steps li {
            margin-bottom: 12px;
            color: #166534;
            line-height: 1.6;
        }
        
        .next-steps li::marker {
            color: #22c55e;
        }
        
        /* Icon styling */
        .icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        /* Alert improvements */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        /* Badge improvements */
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        
        .badge-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        
        /* Decline Details Styles */
        .decline-details {
            margin-top: 24px;
        }

        .decline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc3545;
        }

        .decline-header h4 {
            margin: 0;
            color: #dc3545;
            font-weight: 600;
        }

        .severity-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        .decline-asset-info {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
        }

        .decline-reason-section,
        .follow-up-section {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .decline-reason-section h5,
        .follow-up-section h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .decline-reason-section h5 i,
        .follow-up-section h5 i {
            margin-right: 8px;
            color: #ffc107;
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .info-row:hover {
            background: rgba(255,255,255,0.5);
            border-radius: 8px;
            padding: 12px 15px;
            margin: 0 -15px 15px -15px;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .info-row .label {
            font-weight: 700;
            color: #495057;
            min-width: 160px;
            margin-right: 15px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        .info-row .value {
            color: #212529;
            flex: 1;
            font-weight: 500;
            font-size: 1rem;
        }

        .follow-up-actions-list {
            margin: 0;
            padding-left: 25px;
        }
        .follow-up-actions-list li {
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
            line-height: 1.6;
        }
        .badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .badge-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        .badge-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }
        .badge-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .badge-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        .decline-details {
            margin-top: 20px;
        }
        .decline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #fff5f5, #fed7d7);
            border-radius: 15px;
            border-left: 5px solid #dc3545;
        }
        .decline-header h4 {
            margin: 0;
            color: #dc3545;
            font-weight: 700;
        }
        .decline-reason-section, .follow-up-section {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            border-left: 4px solid #4299e1;
        }
        .decline-reason-section h5, .follow-up-section h5 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .next-steps {
            background: linear-gradient(135deg, #f0fff4, #c6f6d5);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #38a169;
        }
        .next-steps h5 {
            color: #2f855a;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 25px;
        }
        .next-steps li {
            margin-bottom: 10px;
            color: #2d3748;
            font-weight: 500;
            line-height: 1.6;
        }
        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .alert-success {
            background: linear-gradient(135deg, #f0fff4, #c6f6d5);
            color: #2f855a;
            border-left: 4px solid #38a169;
        }
        .alert-info {
            background: linear-gradient(135deg, #ebf8ff, #bee3f8);
            color: #2c5282;
            border-left: 4px solid #4299e1;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .success-container {
                margin: 10px;
                padding: 0;
                max-width: 100%;
            }
            
            .card {
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            
            .card-header h1 {
                font-size: 1.8rem;
            }
            
            .success-icon i {
                font-size: 3rem;
            }
            
            .progress-steps {
                flex-direction: column;
                gap: 15px;
            }
            
            .progress-step:not(:last-child)::after {
                display: none;
            }
            
            .info-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }
            
            .resend-section {
                padding: 15px;
                margin: 15px 0;
            }
        }
        
        @media (max-width: 480px) {
            .card-header h1 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 15px !important;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .card {
                border: 2px solid #000;
            }
            
            .step-icon {
                border: 2px solid #000;
            }
            
            .alert {
                border: 2px solid currentColor;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .btn {
                transition: none;
            }
            
            .skip-link {
                transition: none;
            }
            
            .resend-controls .btn:hover {
                transform: none;
            }
        }
        
        /* Focus styles for better accessibility */
        .btn:focus,
        .alert:focus {
            outline: 3px solid #007bff;
            outline-offset: 2px;
        }
        
        /* Screen reader only content */
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
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <div class="success-container">
        <div class="card">
            <div class="card-header {{ $action === 'declined' ? 'declined' : '' }}">
                <!-- Progress Indicator -->
                <div class="progress-indicator">
                    <div class="progress-steps">
                        <div class="progress-step completed">
                            <div class="step-icon">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                            </div>
                            <span>Email Sent</span>
                        </div>
                        <div class="progress-step completed">
                            <div class="step-icon">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </div>
                            <span>Review Details</span>
                        </div>
                        <div class="progress-step completed">
                            <div class="step-icon">
                                <i class="fas fa-{{ $action === 'confirmed' ? 'check' : 'times' }}" aria-hidden="true"></i>
                            </div>
                            <span>{{ $action === 'confirmed' ? 'Confirmed' : 'Declined' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="success-icon" role="img" aria-label="{{ $action === 'confirmed' ? 'Success checkmark' : 'Declined X mark' }}">
                    <i class="fas fa-{{ $action === 'confirmed' ? 'check-circle' : 'times-circle' }}" aria-hidden="true"></i>
                </div>
                <h1 style="font-weight: 700; font-size: 2.2rem; margin-bottom: 10px; position: relative; z-index: 1;">
                    {{ $action === 'confirmed' ? 'Confirmation Successful!' : 'Assignment Declined' }}
                </h1>
                <p class="mb-0" style="font-size: 1.1rem; opacity: 0.95; position: relative; z-index: 1;">
                    {{ $action === 'confirmed' ? 'Thank you for confirming receipt of your asset' : 'Your decline has been recorded and processed' }}
                </p>
            </div>
            
            <main id="main-content" class="card-body p-4">
                <div class="alert alert-{{ $action === 'confirmed' ? 'success' : 'danger' }}" role="alert">
                    <i class="fas fa-{{ $action === 'confirmed' ? 'thumbs-up' : 'exclamation-triangle' }} me-2" aria-hidden="true"></i>
                    <strong>{{ $action === 'confirmed' ? 'Great!' : 'Noted!' }}</strong> 
                    {{ $action === 'confirmed' ? 'Your asset assignment has been successfully confirmed.' : 'Your asset assignment decline has been recorded.' }}
                </div>

                @if($action === 'confirmed')
                    <div class="asset-info" role="region" aria-labelledby="asset-info-heading">
                        <h5 id="asset-info-heading"><i class="fas fa-laptop" aria-hidden="true"></i>Asset Information</h5>
                        <div class="info-row">
                            <span class="label">Asset Tag:</span>
                            <span class="value" role="text" aria-label="Asset tag {{ $confirmation->asset->asset_tag }}">{{ $confirmation->asset->asset_tag }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Asset Name:</span>
                            <span class="value">{{ $confirmation->asset->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Category:</span>
                            <span class="value">{{ $confirmation->asset->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Brand:</span>
                            <span class="value">{{ $confirmation->asset->brand ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Model:</span>
                            <span class="value">{{ $confirmation->asset->model->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Serial Number:</span>
                            <span class="value">{{ $confirmation->asset->serial ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Status:</span>
                            <span class="value badge badge-success">Confirmed</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Confirmation Date:</span>
                            <span class="value">{{ $confirmation->confirmed_at ? $confirmation->confirmed_at->format('F j, Y \a\t g:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                @else
                    <!-- Declined Confirmation Details -->
                    <div class="decline-details">
                        <div class="decline-header">
                            <h4><i class="fas fa-times-circle text-danger"></i> Assignment Declined</h4>
                            <span class="badge badge-{{ $confirmation->getDeclineSeverityBadgeClass() }} severity-badge">
                                {{ ucfirst($confirmation->decline_severity ?? 'medium') }} Priority
                            </span>
                        </div>
                        
                        <div class="asset-info decline-asset-info">
                            <div class="info-row">
                                <span class="label">Asset Tag:</span>
                                <span class="value">{{ $confirmation->asset->asset_tag }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Asset Name:</span>
                                <span class="value">{{ $confirmation->asset->name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Status:</span>
                                <span class="value badge badge-danger">Declined</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Decline Date:</span>
                                <span class="value">{{ $confirmation->declined_at ? $confirmation->declined_at->format('F j, Y \a\t g:i A') : 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="decline-reason-section">
                            <h5><i class="fas fa-exclamation-triangle"></i> Decline Details</h5>
                            <div class="info-row">
                                <span class="label">Category:</span>
                                <span class="value">{{ ucwords(str_replace('_', ' ', $confirmation->decline_category ?? 'Other')) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Reason:</span>
                                <span class="value">{{ $confirmation->getFormattedDeclineReason() ?: 'No reason provided' }}</span>
                            </div>
                            @if($confirmation->decline_comments)
                                <div class="info-row">
                                    <span class="label">Additional Comments:</span>
                                    <span class="value">{{ $confirmation->decline_comments ?: 'No additional comments provided' }}</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="label">Contact Preference:</span>
                                <span class="value">{{ ucfirst($confirmation->contact_preference ?: 'Email') }}</span>
                            </div>
                        </div>

                        @if($confirmation->follow_up_required)
                            <div class="follow-up-section">
                                <h5><i class="fas fa-tasks"></i> Follow-up Actions Required</h5>
                                <div class="info-row">
                                    <span class="label">Follow-up Date:</span>
                                    <span class="value">
                                        {{ $confirmation->follow_up_date ? $confirmation->follow_up_date->format('F j, Y') : 'To be determined' }}
                                        @if($confirmation->isFollowUpOverdue())
                                            <span class="badge badge-warning ml-2">Overdue</span>
                                        @endif
                                    </span>
                                </div>
                                @if($confirmation->follow_up_actions)
                                    <div class="info-row">
                                        <span class="label">Planned Actions:</span>
                                        <div class="value">
                                            <ul class="follow-up-actions-list">
                                                @foreach($confirmation->getFormattedFollowUpActions() as $action)
                                                    <li>{{ $action }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                <div class="next-steps" role="region" aria-labelledby="next-steps-heading">
                    <h5 id="next-steps-heading"><i class="fas fa-arrow-right" aria-hidden="true"></i> What's Next?</h5>
                    @if($action === 'confirmed')
                        <ul role="list">
                            <li role="listitem">Your asset is now officially assigned to you</li>
                            <li role="listitem">Please take good care of the asset and follow company policies</li>
                            <li role="listitem">Contact IT support if you experience any issues with the asset</li>
                            <li role="listitem">Remember to return the asset when it's no longer needed</li>
                        </ul>
                    @else
                        <ul role="list">
                            <li role="listitem">Your decline has been recorded and the IT department has been notified</li>
                            <li role="listitem">The asset has been returned to available status for reassignment</li>
                            @if($confirmation->follow_up_required)
                                <li role="listitem">IT will follow up with you regarding the decline reason within {{ $confirmation->follow_up_date ? $confirmation->follow_up_date->diffForHumans() : '3 business days' }}</li>
                                <li role="listitem">Please be available via your preferred contact method: {{ ucfirst($confirmation->contact_preference ?? 'email') }}</li>
                            @else
                                <li role="listitem">No further action is required from you at this time</li>
                            @endif
                            <li role="listitem">If you need to request a different asset, please contact the IT department</li>
                        </ul>
                    @endif
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Need Help?</strong> If you have any questions about your asset or need technical support, 
                    please contact the IT department.
                </div>

                <div class="text-center" style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px;">
                    <small class="text-muted" style="font-size: 0.9rem; font-weight: 500;">
                        <i class="fas fa-clock me-2" style="color: #6c757d;"></i>
                        This confirmation was processed on {{ now()->format('F j, Y \\a\\t g:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>