<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signed Accountability Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            margin-bottom: 30px;
        }
        .asset-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .description {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .attachment-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Signed Accountability Form</h1>
            <p>Asset Assignment Confirmation</p>
        </div>

        <div class="content">
            <div class="asset-info">
                <h3 style="margin-top: 0; color: #667eea;">Asset Information</h3>
                <div class="info-row">
                    <div class="info-label">Asset Name:</div>
                    <div class="info-value">{{ $assignment->asset->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Asset Tag:</div>
                    <div class="info-value">{{ $assignment->asset->asset_tag }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Serial Number:</div>
                    <div class="info-value">{{ $assignment->asset->serial_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Category:</div>
                    <div class="info-value">{{ $assignment->asset->category->name ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="asset-info">
                <h3 style="margin-top: 0; color: #667eea;">Assignment Details</h3>
                <div class="info-row">
                    <div class="info-label">Assigned To:</div>
                    <div class="info-value">{{ $assignment->user->first_name }} {{ $assignment->user->last_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $assignment->user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Department:</div>
                    <div class="info-value">{{ $assignment->user->department->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Assigned Date:</div>
                    <div class="info-value">{{ $assignment->assigned_date->format('F d, Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="badge {{ $assignment->status === 'confirmed' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($description)
            <div class="description">
                <h4 style="margin-top: 0; color: #2196f3;">📝 Additional Information</h4>
                <p>{{ $description }}</p>
            </div>
            @endif

            <div class="attachment-info">
                <h4 style="margin-top: 0; color: #4caf50;">📎 Attached Document</h4>
                <p>The signed accountability form has been attached to this email. Please review and keep a copy for your records.</p>
                <p><strong>Uploaded on:</strong> {{ $assignment->signed_form_uploaded_at->format('F d, Y \a\t g:i A') }}</p>
                @if($assignment->signedFormUploadedBy)
                <p><strong>Uploaded by:</strong> {{ $assignment->signedFormUploadedBy->first_name }} {{ $assignment->signedFormUploadedBy->last_name }}</p>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from the ICT Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
