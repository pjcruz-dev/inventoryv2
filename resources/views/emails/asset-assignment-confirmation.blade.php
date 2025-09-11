<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Assignment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .asset-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .asset-details h3 {
            margin-top: 0;
            color: #495057;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: bold;
            color: #6c757d;
        }
        .detail-value {
            color: #495057;
        }
        .confirmation-section {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .btn-confirm {
            background-color: #28a745;
            color: white;
        }
        .btn-decline {
            background-color: #dc3545;
            color: white;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .follow-up-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isFollowUp ? 'Follow-up: ' : '' }}Asset Assignment Confirmation</h1>
    </div>

    @if($isFollowUp)
    <div class="follow-up-notice">
        <strong>Follow-up Notice:</strong> We have not yet received your confirmation for the asset assignment below. Please confirm your receipt of this asset to complete the assignment process.
    </div>
    @endif

    <div class="content">
        <p>Dear {{ $user->name }},</p>
        
        <p>{{ $isFollowUp ? 'This is a follow-up reminder that an' : 'An' }} asset has been assigned to you. Please confirm that you have received this asset by clicking the confirmation button below.</p>

        <div class="asset-details">
            <h3>Asset Details</h3>
            <div class="detail-row">
                <span class="detail-label">Asset Tag:</span>
                <span class="detail-value">{{ $asset->asset_tag }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Asset Name:</span>
                <span class="detail-value">{{ $asset->asset_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Category:</span>
                <span class="detail-value">{{ $asset->assetCategory->category_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Brand:</span>
                <span class="detail-value">{{ $asset->brand ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Model:</span>
                <span class="detail-value">{{ $asset->model ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Serial Number:</span>
                <span class="detail-value">{{ $asset->serial_number ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Assignment Date:</span>
                <span class="detail-value">{{ now()->format('F j, Y') }}</span>
            </div>
        </div>

        <div class="confirmation-section">
            <h3>Please Confirm Receipt</h3>
            <p>Click one of the buttons below to confirm whether you have received this asset:</p>
            
            <a href="{{ url('/asset-confirmation/confirm/' . $confirmationToken) }}" class="btn btn-confirm">
                ✓ Yes, I have received this asset
            </a>
            
            <a href="{{ url('/asset-confirmation/decline/' . $confirmationToken) }}" class="btn btn-decline">
                ✗ No, I have not received this asset
            </a>
        </div>

        <p><strong>Important:</strong> Please confirm receipt within 3 days. If we don't receive your confirmation, we will send follow-up reminders.</p>
        
        <p>If you have any questions about this asset assignment, please contact the IT department.</p>
        
        <p>Thank you,<br>
        IT Asset Management Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you received this email in error, please contact the IT department.</p>
    </div>
</body>
</html>