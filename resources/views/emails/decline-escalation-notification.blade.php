<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Escalation: Unresolved Declined Assets</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .urgent-badge {
            display: inline-block;
            background: #fff;
            color: #dc3545;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box h3 {
            margin-top: 0;
            color: #856404;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .decline-list {
            margin: 25px 0;
        }
        .decline-item {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .decline-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .asset-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .days-badge {
            background: #dc3545;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .decline-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            display: flex;
            align-items: flex-start;
        }
        .info-icon {
            color: #667eea;
            margin-right: 10px;
            font-size: 16px;
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 14px;
            color: #212529;
            font-weight: 500;
        }
        .decline-reason {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #dee2e6;
        }
        .decline-reason-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .decline-reason-text {
            color: #dc3545;
            font-weight: 600;
            font-size: 15px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            border: none;
            mso-padding-alt: 14px 30px;
            mso-border-insideh: 0;
            mso-border-insidev: 0;
        }
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        /* MSO (Microsoft Outlook) specific styles */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .btn {
                display: inline-block !important;
            }
        }
        
        /* Fix for Outlook 2016+ */
        [owa] .btn {
            display: inline-block !important;
        }
        
        /* Fallback for older Outlook versions */
        .btn[class="btn"] {
            display: inline-block !important;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .decline-info {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <span class="urgent-badge">🚨 URGENT ACTION REQUIRED</span>
            <h1>Unresolved Declined Asset Assignments</h1>
            <p style="margin: 0; font-size: 16px;">Management attention needed</p>
        </div>

        <div class="content">
            <p>Hello {{ $recipient->first_name }},</p>

            <div class="alert-box">
                <h3>⚠️ Escalation Alert</h3>
                <p><strong>{{ $unresolvedDeclines->count() }}</strong> high-severity declined asset assignment(s) have remained unresolved for more than <strong>{{ $daysThreshold }} days</strong>. These require immediate management attention and action.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Unresolved Declines</div>
                    <div class="stat-value">{{ $unresolvedDeclines->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Days Unresolved</div>
                    <div class="stat-value">{{ $daysThreshold }}+</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Severity Level</div>
                    <div class="stat-value">HIGH</div>
                </div>
            </div>

            <h3>📋 Unresolved Declined Assets</h3>
            <div class="decline-list">
                @foreach($unresolvedDeclines as $decline)
                    <div class="decline-item">
                        <div class="decline-item-header">
                            <span class="asset-tag">{{ $decline->asset->asset_tag }}</span>
                            <span class="days-badge">{{ $decline->declined_at->diffInDays(now()) }} days ago</span>
                        </div>

                        <div class="decline-info">
                            <div class="info-item">
                                <span class="info-icon">🖥️</span>
                                <div class="info-content">
                                    <div class="info-label">Asset</div>
                                    <div class="info-value">{{ $decline->asset->asset_name }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">👤</span>
                                <div class="info-content">
                                    <div class="info-label">Declined By</div>
                                    <div class="info-value">{{ $decline->user->first_name }} {{ $decline->user->last_name }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">🏢</span>
                                <div class="info-content">
                                    <div class="info-label">Department</div>
                                    <div class="info-value">{{ $decline->user->department->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-icon">📅</span>
                                <div class="info-content">
                                    <div class="info-label">Declined Date</div>
                                    <div class="info-value">{{ $decline->declined_at->format('M d, Y \a\t g:i A') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="decline-reason">
                            <div class="decline-reason-label">Decline Reason:</div>
                            <div class="decline-reason-text">{{ $decline->getFormattedDeclineReason() }}</div>
                            @if($decline->decline_comments)
                                <p style="margin-top: 10px; color: #495057; font-size: 14px;"><strong>Additional Comments:</strong> {{ $decline->decline_comments }}</p>
                            @endif
                        </div>

                        @if($decline->follow_up_actions)
                            <div style="margin-top: 12px; padding: 10px; background: #e7f3ff; border-radius: 4px;">
                                <strong style="color: #004085;">Follow-up Actions:</strong>
                                <p style="margin: 5px 0 0 0; color: #004085;">{{ str_replace('|', ', ', $decline->follow_up_actions) }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <h3>🎯 Recommended Actions</h3>
            <ul style="line-height: 2;">
                <li><strong>Review each decline</strong> reason and determine root cause</li>
                <li><strong>Contact the users</strong> to resolve their concerns</li>
                <li><strong>Inspect the assets</strong> if physical issues were reported</li>
                <li><strong>Reassign assets</strong> to other users if appropriate</li>
                <li><strong>Update asset records</strong> if corrections are needed</li>
                <li><strong>Document resolution</strong> steps for future reference</li>
            </ul>

            <div class="action-buttons">
                <!-- Outlook-compatible button layout -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: center; margin: 20px 0;">
                    <tr>
                        <td align="center" style="padding: 10px;">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" 
                                         href="{{ url('/asset-assignment-confirmations?status=declined&severity=high') }}" 
                                         style="height:48px;v-text-anchor:middle;width:250px;" 
                                         arcsize="10%" 
                                         stroke="f" 
                                         fillcolor="#667eea">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">📊 View All Declined Assets</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <a href="{{ url('/asset-assignment-confirmations?status=declined&severity=high') }}" class="btn btn-primary" style="display:inline-block;background-color:#667eea;color:#ffffff;text-decoration:none;padding:14px 30px;font-weight:bold;">
                                📊 View All Declined Assets
                            </a>
                            <!--<![endif]-->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 10px;">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" 
                                         href="{{ route('asset-assignments.export-declines', ['severity' => 'high', 'follow_up_required' => true]) }}" 
                                         style="height:48px;v-text-anchor:middle;width:250px;" 
                                         arcsize="10%" 
                                         stroke="f" 
                                         fillcolor="#6c757d">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">📥 Download Detailed Report</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <a href="{{ route('asset-assignments.export-declines', ['severity' => 'high', 'follow_up_required' => true]) }}" class="btn btn-secondary" style="display:inline-block;background-color:#6c757d;color:#ffffff;text-decoration:none;padding:14px 30px;font-weight:bold;">
                                📥 Download Detailed Report
                            </a>
                            <!--<![endif]-->
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #667eea;">
                <p style="margin: 0; color: #495057;"><strong>Note:</strong> This is an automated escalation notification. These declined assets have been unassigned for {{ $daysThreshold }}+ days and require immediate management attention to prevent inventory discrepancies and operational disruptions.</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>ICTAssetV2 Management System</strong></p>
            <p>Automatic Escalation System</p>
            <p>Generated on {{ now()->format('M d, Y \a\t g:i A T') }}</p>
        </div>
    </div>
</body>
</html>

