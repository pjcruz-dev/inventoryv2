<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Already Processed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .status-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 5vh;
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .status-icon.confirmed {
            color: #28a745;
        }
        
        .status-icon.declined {
            color: #dc3545;
        }
        
        .asset-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        
        .detail-value {
            color: #212529;
        }
        
        .status-badge {
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
        }
        
        .severity-indicator {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: normal;
            margin-left: 0.5rem;
        }
        
        .decline-details-section,
        .follow-up-details-section {
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            border-left: 4px solid #dc3545;
        }
        
        .follow-up-details-section {
            border-left-color: #0dcaf0;
        }
        
        .decline-details-section h5,
        .follow-up-details-section h5 {
            margin-bottom: 1rem;
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .decline-details-section h5 i {
            margin-right: 0.5rem;
        }
        
        .follow-up-details-section h5 i {
            margin-right: 0.5rem;
        }
        
        .follow-up-actions-list {
            margin: 0;
            padding-left: 1.25rem;
        }
        
        .follow-up-actions-list li {
            margin-bottom: 0.25rem;
            color: #495057;
        }
        
        .btn-home {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container text-center">
            @if($status === 'confirmed')
                <div class="status-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="text-success">Already Confirmed</h2>
            @else
                <div class="status-icon declined">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="text-danger">Already Declined</h2>
            @endif
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ $message }}
            </div>

            <div class="asset-details">
                <h5 class="mb-3"><i class="fas fa-desktop me-2"></i>Asset Information</h5>
                
                <div class="detail-row">
                    <span class="detail-label">Asset Tag:</span>
                    <span class="detail-value">{{ $confirmation->asset->asset_tag }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Asset Name:</span>
                    <span class="detail-value">{{ $confirmation->asset->asset_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Category:</span>
                    <span class="detail-value">{{ $confirmation->asset->assetCategory->category_name ?? 'N/A' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Assigned To:</span>
                    <span class="detail-value">{{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Assignment Date:</span>
                    <span class="detail-value">{{ $confirmation->assigned_at->format('F j, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        @if($status === 'confirmed')
                            <span class="badge bg-success status-badge">Confirmed</span>
                        @else
                            <span class="badge bg-danger status-badge">Declined</span>
                            @if($confirmation->decline_severity)
                                <span class="severity-indicator">({{ ucfirst($confirmation->decline_severity) }} Priority)</span>
                            @endif
                        @endif
                    </span>
                </div>
                
                @if($confirmation->confirmed_at)
                <div class="detail-row">
                    <span class="detail-label">Processed Date:</span>
                    <span class="detail-value">{{ $confirmation->confirmed_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                @endif
                
                @if($status === 'declined')
                    <!-- Enhanced Decline Details -->
                    @if($confirmation->decline_category || $confirmation->decline_reason)
                        <div class="decline-details-section">
                            <h5><i class="fas fa-exclamation-triangle text-warning"></i> Decline Information</h5>
                            
                            @if($confirmation->decline_category)
                                <div class="detail-row">
                                    <span class="detail-label">Category:</span>
                                    <span class="detail-value">{{ ucwords(str_replace('_', ' ', $confirmation->decline_category)) }}</span>
                                </div>
                            @endif
                            
                            @if($confirmation->decline_reason)
                                <div class="detail-row">
                                    <span class="detail-label">Reason:</span>
                                    <span class="detail-value">{{ $confirmation->getFormattedDeclineReason() }}</span>
                                </div>
                            @endif
                            
                            @if($confirmation->decline_comments)
                                <div class="detail-row">
                                    <span class="detail-label">Comments:</span>
                                    <span class="detail-value">{{ $confirmation->decline_comments }}</span>
                                </div>
                            @endif
                            
                            @if($confirmation->contact_preference)
                                <div class="detail-row">
                                    <span class="detail-label">Contact Preference:</span>
                                    <span class="detail-value">{{ ucfirst($confirmation->contact_preference) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($confirmation->follow_up_required)
                        <div class="follow-up-details-section">
                            <h5><i class="fas fa-tasks text-info"></i> Follow-up Information</h5>
                            
                            <div class="detail-row">
                                <span class="detail-label">Follow-up Required:</span>
                                <span class="detail-value">
                                    <span class="badge bg-info">Yes</span>
                                    @if($confirmation->isFollowUpOverdue())
                                        <span class="badge bg-warning ms-2">Overdue</span>
                                    @endif
                                </span>
                            </div>
                            
                            @if($confirmation->follow_up_date)
                                <div class="detail-row">
                                    <span class="detail-label">Follow-up Date:</span>
                                    <span class="detail-value">{{ $confirmation->follow_up_date->format('F j, Y') }}</span>
                                </div>
                            @endif
                            
                            @if($confirmation->follow_up_actions)
                                <div class="detail-row">
                                    <span class="detail-label">Planned Actions:</span>
                                    <div class="detail-value">
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
                @endif
                
                @if($confirmation->notes)
                <div class="detail-row">
                    <span class="detail-label">Legacy Notes:</span>
                    <span class="detail-value">{{ $confirmation->notes }}</span>
                </div>
                @endif
            </div>

            <div class="mt-4">
                <p class="text-muted mb-3">
                    <i class="fas fa-shield-alt me-1"></i>
                    This request has already been processed. No further action is required.
                </p>
                
                <a href="{{ url('/') }}" class="btn-home">
                    <i class="fas fa-home me-2"></i>Return to Home
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>