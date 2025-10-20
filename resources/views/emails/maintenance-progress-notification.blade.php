<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance Progress Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
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
        .maintenance-info {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .asset-info {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-scheduled { background-color: #ffc107; color: #000; }
        .status-in-progress { background-color: #007bff; color: white; }
        .status-completed { background-color: #28a745; color: white; }
        .status-on-hold { background-color: #ff6b6b; color: white; }
        .status-cancelled { background-color: #6c757d; color: white; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .highlight {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .progress-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        td {
            padding: 8px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        .value {
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Maintenance Progress Notification</h1>
        <p>@if($action === 'created') New maintenance record has been created @else Maintenance record has been updated @endif</p>
    </div>

    <div class="content">
        <h2>Maintenance Details</h2>
        <div class="maintenance-info">
            <table>
                <tr>
                    <td class="label">Maintenance ID:</td>
                    <td class="value">#{{ $maintenance->id }}</td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td class="value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $maintenance->status)) }}">
                            {{ $maintenance->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Issue Reported:</td>
                    <td class="value">{{ $maintenance->issue_reported }}</td>
                </tr>
                @if($maintenance->repair_action)
                <tr>
                    <td class="label">Repair Action:</td>
                    <td class="value">{{ $maintenance->repair_action }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Start Date:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($maintenance->start_date)->format('F j, Y') }}</td>
                </tr>
                @if($maintenance->end_date)
                <tr>
                    <td class="label">End Date:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($maintenance->end_date)->format('F j, Y') }}</td>
                </tr>
                @endif
                @if($maintenance->cost)
                <tr>
                    <td class="label">Cost:</td>
                    <td class="value">₱{{ number_format($maintenance->cost, 2) }}</td>
                </tr>
                @endif
                @if($maintenance->vendor)
                <tr>
                    <td class="label">Vendor:</td>
                    <td class="value">{{ $maintenance->vendor->name }}</td>
                </tr>
                @endif
                @if($maintenance->remarks)
                <tr>
                    <td class="label">Remarks:</td>
                    <td class="value">{{ $maintenance->remarks }}</td>
                </tr>
                @endif
            </table>
        </div>

        <h2>Asset Information</h2>
        <div class="asset-info">
            <table>
                <tr>
                    <td class="label">Asset Tag:</td>
                    <td class="value">{{ $maintenance->asset->asset_tag }}</td>
                </tr>
                <tr>
                    <td class="label">Asset Name:</td>
                    <td class="value">{{ $maintenance->asset->name }}</td>
                </tr>
                <tr>
                    <td class="label">Category:</td>
                    <td class="value">{{ $maintenance->asset->category->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Current Status:</td>
                    <td class="value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $maintenance->asset->status)) }}">
                            {{ $maintenance->asset->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Movement:</td>
                    <td class="value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $maintenance->asset->movement)) }}">
                            {{ $maintenance->asset->movement }}
                        </span>
                    </td>
                </tr>
                @if($assignedUser)
                <tr>
                    <td class="label">Assigned User:</td>
                    <td class="value">{{ $assignedUser->first_name }} {{ $assignedUser->last_name }} ({{ $assignedUser->employee_id }})</td>
                </tr>
                @else
                <tr>
                    <td class="label">Assigned User:</td>
                    <td class="value">Unassigned</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Location:</td>
                    <td class="value">{{ $maintenance->asset->location ?? 'No Location' }}</td>
                </tr>
            </table>
        </div>

        @if($action === 'created')
        <div class="highlight">
            <h3>🎯 New Maintenance Record Created</h3>
            <p>A new maintenance record has been created for this asset. The maintenance process is now being tracked and you will receive updates on the progress.</p>
            <ul>
                <li><strong>Status:</strong> {{ $maintenance->status }}</li>
                <li><strong>Issue:</strong> {{ $maintenance->issue_reported }}</li>
                <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($maintenance->start_date)->format('F j, Y') }}</li>
                @if($maintenance->end_date)
                <li><strong>Expected Completion:</strong> {{ \Carbon\Carbon::parse($maintenance->end_date)->format('F j, Y') }}</li>
                @endif
            </ul>
        </div>
        @else
        <div class="progress-section">
            <h3>📈 Maintenance Progress Update</h3>
            <p>The maintenance record has been updated with new information. Here are the current details:</p>
            <ul>
                <li><strong>Current Status:</strong> {{ $maintenance->status }}</li>
                @if($maintenance->repair_action)
                <li><strong>Repair Action Taken:</strong> {{ $maintenance->repair_action }}</li>
                @endif
                @if($maintenance->remarks)
                <li><strong>Latest Remarks:</strong> {{ $maintenance->remarks }}</li>
                @endif
                @if($maintenance->cost)
                <li><strong>Cost Incurred:</strong> ₱{{ number_format($maintenance->cost, 2) }}</li>
                @endif
            </ul>
        </div>
        @endif

        <div class="highlight">
            <h3>📋 Next Steps</h3>
            @if($maintenance->status === 'Scheduled')
                <p>The maintenance is scheduled to begin on {{ \Carbon\Carbon::parse($maintenance->start_date)->format('F j, Y') }}. Please prepare the asset for maintenance.</p>
            @elseif($maintenance->status === 'In Progress')
                <p>The maintenance is currently in progress. Regular updates will be provided as work continues.</p>
            @elseif($maintenance->status === 'Completed')
                <p>The maintenance has been completed successfully. The asset should be ready for use.</p>
            @elseif($maintenance->status === 'On Hold')
                <p>The maintenance is currently on hold. Please contact the maintenance team for more information.</p>
            @elseif($maintenance->status === 'Cancelled')
                <p>The maintenance has been cancelled. Please contact the maintenance team for more information.</p>
            @endif
        </div>

        <p><strong>Processed by:</strong> {{ $processedBy->first_name }} {{ $processedBy->last_name }}</p>
        <p><strong>Date:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>

        <p>If you have any questions about this maintenance record, please contact the IT department or the maintenance team.</p>
    </div>

    <div class="footer">
        <p>This is an automated notification from the Asset Management System.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
