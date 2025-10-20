<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset Maintenance Notification</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .asset-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-maintenance {
            background-color: #ffc107;
            color: #000;
        }
        .status-return {
            background-color: #17a2b8;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Asset Maintenance Notification</h1>
        <p>An asset has been sent to maintenance</p>
    </div>

    <div class="content">
        <h2>Asset Details</h2>
        <div class="asset-info">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 150px;">Asset Tag:</td>
                    <td style="padding: 8px 0;">{{ $asset->asset_tag }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Asset Name:</td>
                    <td style="padding: 8px 0;">{{ $asset->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Category:</td>
                    <td style="padding: 8px 0;">{{ $asset->category->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Current Status:</td>
                    <td style="padding: 8px 0;">
                        <span class="status-badge status-maintenance">Maintenance</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Movement:</td>
                    <td style="padding: 8px 0;">
                        <span class="status-badge status-return">Return</span>
                    </td>
                </tr>
                @if($assignedUser)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Assigned User:</td>
                    <td style="padding: 8px 0;">{{ $assignedUser->first_name }} {{ $assignedUser->last_name }} ({{ $assignedUser->employee_id }})</td>
                </tr>
                @else
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Assigned User:</td>
                    <td style="padding: 8px 0;">Unassigned</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Location:</td>
                    <td style="padding: 8px 0;">{{ $asset->location ?? 'No Location' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Processed By:</td>
                    <td style="padding: 8px 0;">{{ $processedBy->first_name }} {{ $processedBy->last_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Date Processed:</td>
                    <td style="padding: 8px 0;">{{ now()->format('F j, Y \a\t g:i A') }}</td>
                </tr>
            </table>
        </div>

        <div class="highlight">
            <h3>⚠️ Important Information</h3>
            <ul>
                <li><strong>Status Changed:</strong> The asset status has been updated to "Maintenance"</li>
                <li><strong>Movement Updated:</strong> The movement has been set to "Return"</li>
                @if($assignedUser)
                <li><strong>User Retained:</strong> The assigned user ({{ $assignedUser->first_name }} {{ $assignedUser->last_name }}) has been retained during maintenance</li>
                @endif
                <li><strong>Asset Hidden:</strong> This asset will no longer appear in the main asset list until maintenance is completed</li>
                <li><strong>Next Steps:</strong> Complete the maintenance process through the Maintenance module to restore the asset</li>
            </ul>
        </div>

        <p>This asset has been successfully sent to maintenance and is now ready for maintenance processing. Please complete the maintenance workflow to restore the asset to active status.</p>

        <p>If you have any questions or need assistance, please contact the IT department.</p>
    </div>

    <div class="footer">
        <p>This is an automated notification from the Asset Management System.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>



