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
                        @endif
                    </span>
                </div>
                
                @if($confirmation->confirmed_at)
                <div class="detail-row">
                    <span class="detail-label">Processed Date:</span>
                    <span class="detail-value">{{ $confirmation->confirmed_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                @endif
                
                @if($confirmation->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes:</span>
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