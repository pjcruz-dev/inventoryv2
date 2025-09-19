<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asset {{ ucfirst($action) }} Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: {{ $action === 'confirmed' ? '#28a745' : '#dc3545' }};
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .section {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid {{ $action === 'confirmed' ? '#28a745' : '#dc3545' }};
        }
        .section h3 {
            margin-top: 0;
            color: {{ $action === 'confirmed' ? '#28a745' : '#dc3545' }};
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 0.9em;
        }
        .info-value {
            color: #212529;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status-declined {
            background: #f8d7da;
            color: #721c24;
        }
        .footer {
            background: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 0.9em;
        }
        .highlight {
            background: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
        }
        .decline-reason {
            background: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <i class="fas fa-{{ $action === 'confirmed' ? 'check-circle' : 'times-circle' }}"></i>
            Asset Assignment {{ ucfirst($action) }}
        </h1>
        <p>Asset: <strong>{{ $confirmation->asset->name }}</strong> ({{ $confirmation->asset->asset_tag }})</p>
    </div>

    <div class="content">
        <!-- Action Summary -->
        <div class="section">
            <h3>Action Summary</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Action Performed</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ $action }}">
                            {{ ucfirst($action) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Timestamp</div>
                    <div class="info-value">{{ now()->format('M d, Y \a\t g:i A') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Confirmation ID</div>
                    <div class="info-value">#{{ $confirmation->id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Token Used</div>
                    <div class="info-value">{{ substr($confirmation->confirmation_token, 0, 20) }}...</div>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="section">
            <h3>User Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Employee Name</div>
                    <div class="info-value">{{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $confirmation->user->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Employee Number</div>
                    <div class="info-value">{{ $confirmation->user->employee_no ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Position</div>
                    <div class="info-value">{{ $confirmation->user->position ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ $confirmation->user->department->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div class="info-value">{{ $confirmation->user->role->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Asset Information -->
        <div class="section">
            <h3>Asset Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Asset Name</div>
                    <div class="info-value">{{ $confirmation->asset->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Asset Tag</div>
                    <div class="info-value">{{ $confirmation->asset->asset_tag }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Serial Number</div>
                    <div class="info-value">{{ $confirmation->asset->serial_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Model</div>
                    <div class="info-value">{{ $confirmation->asset->model ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Category</div>
                    <div class="info-value">{{ $confirmation->asset->category->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Vendor</div>
                    <div class="info-value">{{ $confirmation->asset->vendor->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Current Status</div>
                    <div class="info-value">{{ $confirmation->asset->status }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Movement</div>
                    <div class="info-value">{{ $confirmation->asset->movement }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $confirmation->asset->location ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cost</div>
                    <div class="info-value">₱{{ number_format($confirmation->asset->cost, 2) }}</div>
                </div>
            </div>
        </div>

        @if($action === 'declined' && $confirmation->decline_reason)
        <!-- Decline Reason -->
        <div class="section">
            <h3>Decline Details</h3>
            <div class="decline-reason">
                <div class="info-label">Reason for Decline</div>
                <div class="info-value">{{ $confirmation->getFormattedDeclineReason() }}</div>
            </div>
            
            @if(isset($details['decline_category']))
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Decline Category</div>
                    <div class="info-value">{{ $details['decline_category'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Severity Level</div>
                    <div class="info-value">{{ $details['decline_severity'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Follow-up Required</div>
                    <div class="info-value">{{ $details['follow_up_required'] ? 'Yes' : 'No' }}</div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- System Information -->
        <div class="section">
            <h3>System Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">IP Address</div>
                    <div class="info-value">{{ request()->ip() ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">User Agent</div>
                    <div class="info-value">{{ request()->userAgent() ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Request Method</div>
                    <div class="info-value">{{ request()->method() ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Request URL</div>
                    <div class="info-value">{{ request()->fullUrl() ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="section">
            <h3>Next Steps</h3>
            @if($action === 'confirmed')
                <div class="highlight">
                    <strong>Asset Successfully Confirmed:</strong>
                    <ul>
                        <li>Asset status has been updated to <strong>Active</strong></li>
                        <li>Asset movement has been set to <strong>Deployed</strong></li>
                        <li>User is now officially assigned to this asset</li>
                        <li>Asset assignment confirmation has been marked as completed</li>
                    </ul>
                </div>
            @else
                <div class="highlight">
                    <strong>Asset Assignment Declined:</strong>
                    <ul>
                        <li>Asset status has been updated to <strong>Available</strong></li>
                        <li>Asset movement has been set to <strong>Returned</strong></li>
                        <li>Asset assignment has been marked as declined</li>
                        <li>Asset is now available for reassignment</li>
                        @if(isset($details['follow_up_required']) && $details['follow_up_required'])
                        <li><strong>Follow-up action required</strong> - Please review the decline reason</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p><strong>ICTAssetV2 Management System</strong></p>
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>Generated on {{ now()->format('M d, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>
