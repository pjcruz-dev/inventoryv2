<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Assignment Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        .asset-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
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
            color: #6c757d;
            text-align: right;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-confirm {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .btn-decline {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-decline:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }
        .user-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .icon {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="card">
            <div class="card-header text-center">
                <h2><i class="fas fa-clipboard-check me-2"></i>Asset Assignment Confirmation</h2>
                <p class="mb-0">Please confirm receipt of your assigned asset</p>
            </div>
            <div class="card-body p-4">
                <div class="user-info">
                    <h5><i class="fas fa-user icon"></i>Hello, {{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</h5>
                    <p class="mb-0"><i class="fas fa-envelope icon"></i>{{ $confirmation->user->email }}</p>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    An asset has been assigned to you. Please review the details below and confirm whether you have received this asset.
                </div>

                <div class="asset-details">
                    <h5 class="mb-3"><i class="fas fa-laptop icon"></i>Asset Details</h5>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-tag icon"></i>Asset Tag:</span>
                        <span class="detail-value"><strong>{{ $confirmation->asset->asset_tag }}</strong></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-desktop icon"></i>Asset Name:</span>
                        <span class="detail-value">{{ $confirmation->asset->asset_name }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-layer-group icon"></i>Category:</span>
                        <span class="detail-value">{{ $confirmation->asset->assetCategory->category_name ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-industry icon"></i>Brand:</span>
                        <span class="detail-value">{{ $confirmation->asset->brand ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-cogs icon"></i>Model:</span>
                        <span class="detail-value">{{ $confirmation->asset->model ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-barcode icon"></i>Serial Number:</span>
                        <span class="detail-value">{{ $confirmation->asset->serial_number ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar icon"></i>Assignment Date:</span>
                        <span class="detail-value">{{ $confirmation->assigned_at->format('F j, Y') }}</span>
                    </div>
                    
                    @if($confirmation->notes)
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-sticky-note icon"></i>Notes:</span>
                        <span class="detail-value">{{ $confirmation->notes }}</span>
                    </div>
                    @endif
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> Please confirm receipt within 3 days. If you don't confirm, you will receive follow-up reminders every 3 days.
                </div>

                <div class="action-buttons">
                    <a href="{{ url('/asset-confirmation/confirm/' . $confirmation->confirmation_token) }}" 
                       class="btn btn-confirm btn-lg"
                       id="confirmBtn"
                       onclick="return handleConfirmClick(this)">
                        <i class="fas fa-check me-2"></i>Yes, I have received this asset
                    </a>
                    
                    <a href="{{ url('/asset-confirmation/decline-form/' . $confirmation->confirmation_token) }}" 
                       class="btn btn-decline btn-lg"
                       id="declineBtn"
                       onclick="return handleDeclineClick(this)">
                        <i class="fas fa-times me-2"></i>No, I have not received this asset
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

                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        This is a secure confirmation link. If you received this email in error, please contact the IT department.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .processing-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
    
    <script>
        let isProcessing = false;
        
        function handleConfirmClick(button) {
            if (isProcessing) {
                return false;
            }
            
            if (!confirm('Are you sure you have received this asset?')) {
                return false;
            }
            
            disableButtons();
            showProcessingOverlay();
            isProcessing = true;
            
            // Allow the navigation to proceed
            return true;
        }
        
        function handleDeclineClick(button) {
            if (isProcessing) {
                return false;
            }
            
            disableButtons();
            showProcessingOverlay();
            isProcessing = true;
            
            // Allow the navigation to proceed
            return true;
        }
        
        function disableButtons() {
            const confirmBtn = document.getElementById('confirmBtn');
            const declineBtn = document.getElementById('declineBtn');
            
            if (confirmBtn) {
                confirmBtn.style.pointerEvents = 'none';
                confirmBtn.style.opacity = '0.6';
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
            
            if (declineBtn) {
                declineBtn.style.pointerEvents = 'none';
                declineBtn.style.opacity = '0.6';
                declineBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
        }
        
        function showProcessingOverlay() {
            const overlay = document.getElementById('processingOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }
        
        // Prevent back button after processing starts
        window.addEventListener('beforeunload', function(e) {
            if (isProcessing) {
                e.preventDefault();
                e.returnValue = 'Your request is being processed. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
        
        // Re-enable buttons if user stays on page (shouldn't happen in normal flow)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                isProcessing = false;
                const overlay = document.getElementById('processingOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>