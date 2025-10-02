<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Accountability Forms - Confirmed & Signed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .description {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            white-space: pre-line;
        }
        .assets-section {
            margin: 30px 0;
        }
        .assets-section h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .asset-list {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
        }
        .asset-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .asset-item:last-child {
            border-bottom: none;
        }
        .asset-info {
            flex: 1;
        }
        .asset-tag {
            font-weight: 600;
            color: #667eea;
            font-size: 16px;
        }
        .asset-name {
            color: #666;
            font-size: 14px;
            margin-top: 2px;
        }
        .asset-user {
            color: #333;
            font-size: 14px;
            margin-top: 2px;
        }
        .asset-status {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .attachments-note {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #1565c0;
        }
        .attachments-note strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Asset Accountability Forms</h1>
            <p>Confirmed & Signed</p>
        </div>
        
        <div class="content">
            @if($description)
            <div class="description">
                {!! nl2br(e($description)) !!}
            </div>
            @endif

            <div class="assets-section">
                <h3>Asset Details ({{ $assets->count() }} assets)</h3>
                <div class="asset-list">
                    @foreach($assets as $asset)
                    <div class="asset-item">
                        <div class="asset-info">
                            <div class="asset-tag">{{ $asset->asset_tag }}</div>
                            <div class="asset-name">{{ $asset->name }}</div>
                            <div class="asset-user">Assigned to: {{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</div>
                        </div>
                        <div class="asset-status">Ready</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="attachments-note">
                <strong>📎 Attachments</strong>
                {{ $assets->count() }} signed accountability form(s) for your assigned assets are attached to this email. Please keep these documents in a secure location for your records.
            </div>
        </div>
        
        <div class="footer">
            <p><strong>IT Asset Management Team</strong></p>
            <p>If you have any questions about these asset assignments, please contact your IT department.</p>
        </div>
    </div>
</body>
</html>
