<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Asset Assignment Confirmation</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    
    <!--[if mso]>
    <style type="text/css">
        .btn-confirm {
            background-color: #10b981 !important;
            color: white !important;
            border: 2px solid #059669 !important;
        }
        .btn-decline {
            background-color: #ef4444 !important;
            color: white !important;
            border: 2px solid #dc2626 !important;
        }
    </style>
    <![endif]-->
    <style>
        /* Reset and base styles - Outlook compatible */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        /* Outlook-specific styles */
        .outlook-table {
            width: 100%;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        .outlook-table td {
            border-collapse: collapse;
            mso-line-height-rule: exactly;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .header {
            background-color: #4f46e5;
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .header .subtitle {
            font-size: 16px;
            margin-top: 8px;
            opacity: 0.9;
            font-weight: 400;
        }
        
        /* Progress indicator */
        .progress-indicator {
            background-color: #ffffff;
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .progress-step.active {
            color: #4f46e5;
            font-weight: 600;
        }
        
        .progress-step .icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        .progress-step.active .icon {
            background-color: #4f46e5;
            color: white;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }
        
        .intro-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 24px;
            line-height: 1.7;
        }
        
        /* Follow-up notice */
        .follow-up-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            position: relative;
        }
        
        .follow-up-notice::before {
            content: '⚠️';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        
        .follow-up-notice .content-text {
            margin-left: 35px;
            font-weight: 600;
            color: #92400e;
        }
        
        /* Asset details */
        .asset-details {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        
        .asset-details h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .asset-details h3::before {
            content: '💻';
            font-size: 24px;
        }
        
        .detail-grid {
            display: grid;
            gap: 12px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        
        .detail-label {
            font-weight: 600;
            color: #0c4a6e;
            font-size: 14px;
        }
        
        .detail-value {
            color: #374151;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }
        
        /* Confirmation section */
        .confirmation-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            margin: 32px 0;
            position: relative;
        }
        
        .confirmation-section::before {
            content: '✅';
            font-size: 32px;
            position: absolute;
            top: -16px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffffff;
            padding: 8px;
            border-radius: 50%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .confirmation-section h3 {
            font-size: 22px;
            font-weight: 700;
            color: #065f46;
            margin: 16px 0 12px 0;
        }
        
        .confirmation-section p {
            font-size: 16px;
            color: #047857;
            margin-bottom: 24px;
        }
        
        /* Buttons - Outlook compatible */
        .button-container {
            text-align: center;
            padding: 20px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            border: 2px solid;
            min-width: 280px;
            margin: 8px 0;
            mso-padding-alt: 0;
            mso-border-insideh: 0;
            mso-border-insidev: 0;
        }
        
        .btn-confirm {
            background-color: #10b981;
            color: white;
            border-color: #059669;
        }
        
        .btn-confirm:hover {
            background-color: #059669;
            color: white;
        }
        
        .btn-decline {
            background-color: #ef4444;
            color: white;
            border-color: #dc2626;
        }
        
        .btn-decline:hover {
            background-color: #dc2626;
            color: white;
        }
        
        /* Outlook button table */
        .btn-table {
            width: 100%;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        .btn-table td {
            text-align: center;
            padding: 8px 0;
        }
        
        /* Outlook-specific fixes */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .btn {
                display: inline-block !important;
            }
        }
        
        /* MSO (Microsoft Outlook) specific styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
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
        
        /* Important notice */
        .important-notice {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        
        .important-notice h4 {
            color: #991b1b;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .important-notice h4::before {
            content: '⏰';
            font-size: 18px;
        }
        
        .important-notice p {
            color: #7f1d1d;
            margin: 0;
        }
        
        /* Contact info */
        .contact-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        
        .contact-info h4 {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .contact-info h4::before {
            content: '📞';
            font-size: 18px;
        }
        
        /* Footer */
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .footer .signature {
            font-weight: 600;
            color: #374151;
            margin-top: 16px;
        }
        
        /* Responsive design */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 20px;
            }
            
            .progress-steps {
                flex-direction: column;
                gap: 12px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            
            .detail-value {
                text-align: left;
                max-width: 100%;
            }
            
            .btn {
                min-width: 100%;
                padding: 14px 24px;
            }
            
            .confirmation-section {
                padding: 24px 16px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f2937;
            }
            
            .content {
                background-color: #1f2937;
                color: #f9fafb;
            }
            
            .greeting {
                color: #f9fafb;
            }
            
            .intro-text {
                color: #d1d5db;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $isFollowUp ? 'Follow-up: ' : '' }}Asset Assignment Confirmation</h1>
            <div class="subtitle">Secure Asset Management System</div>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="progress-steps">
                <div class="progress-step">
                    <div class="icon">✓</div>
                    <span>Asset Assigned</span>
                </div>
                <div class="progress-step active">
                    <div class="icon">2</div>
                    <span>Awaiting Confirmation</span>
                </div>
                <div class="progress-step">
                    <div class="icon">3</div>
                    <span>Complete</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            @if($isFollowUp)
                <div class="follow-up-notice">
                    <div class="content-text">
                        <strong>Follow-up Notice:</strong> We have not yet received your confirmation for the asset assignment below. Please confirm your receipt of this asset to complete the assignment process.
                    </div>
                </div>
            @endif

            <div class="greeting">Dear {{ $user->name }},</div>
            
            <div class="intro-text">
                {{ $isFollowUp ? 'This is a follow-up reminder that an' : 'An' }} asset has been assigned to you. Please review the asset details below and confirm receipt by clicking the appropriate button.
            </div>

            <!-- Asset Details -->
            <div class="asset-details">
                <h3>Asset Details</h3>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-label">Asset Tag</span>
                        <span class="detail-value">{{ $asset->asset_tag }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Asset Name</span>
                        <span class="detail-value">{{ $asset->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">{{ $asset->category->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Brand</span>
                        <span class="detail-value">{{ $asset->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Model</span>
                        <span class="detail-value">{{ $asset->model ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Serial Number</span>
                        <span class="detail-value">{{ $asset->serial_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Assignment Date</span>
                        <span class="detail-value">{{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Device Specifications -->
            @if($asset->computer || $asset->monitor || $asset->printer || $asset->peripheral)
            <div class="asset-details">
                <h3>Device Specifications</h3>
                <div class="detail-grid">
                    @if($asset->computer)
                        <!-- Computer Specifications -->
                        @if($asset->computer->processor)
                        <div class="detail-row">
                            <span class="detail-label">Processor</span>
                            <span class="detail-value">{{ $asset->computer->processor }}</span>
                        </div>
                        @endif
                        @if($asset->computer->memory)
                        <div class="detail-row">
                            <span class="detail-label">Memory (RAM)</span>
                            <span class="detail-value">{{ $asset->computer->memory }}</span>
                        </div>
                        @endif
                        @if($asset->computer->storage)
                        <div class="detail-row">
                            <span class="detail-label">Storage</span>
                            <span class="detail-value">{{ $asset->computer->storage }}</span>
                        </div>
                        @endif
                        @if($asset->computer->operating_system)
                        <div class="detail-row">
                            <span class="detail-label">Operating System</span>
                            <span class="detail-value">{{ $asset->computer->operating_system }}</span>
                        </div>
                        @endif
                        @if($asset->computer->graphics_card)
                        <div class="detail-row">
                            <span class="detail-label">Graphics Card</span>
                            <span class="detail-value">{{ $asset->computer->graphics_card }}</span>
                        </div>
                        @endif
                        @if($asset->computer->computer_type)
                        <div class="detail-row">
                            <span class="detail-label">Computer Type</span>
                            <span class="detail-value">{{ ucfirst($asset->computer->computer_type) }}</span>
                        </div>
                        @endif
                    @endif

                    @if($asset->monitor)
                        <!-- Monitor Specifications -->
                        @if($asset->monitor->size)
                        <div class="detail-row">
                            <span class="detail-label">Screen Size</span>
                            <span class="detail-value">{{ $asset->monitor->size }}</span>
                        </div>
                        @endif
                        @if($asset->monitor->resolution)
                        <div class="detail-row">
                            <span class="detail-label">Resolution</span>
                            <span class="detail-value">{{ $asset->monitor->resolution }}</span>
                        </div>
                        @endif
                        @if($asset->monitor->panel_type)
                        <div class="detail-row">
                            <span class="detail-label">Panel Type</span>
                            <span class="detail-value">{{ $asset->monitor->panel_type }}</span>
                        </div>
                        @endif
                    @endif

                    @if($asset->printer)
                        <!-- Printer Specifications -->
                        @if($asset->printer->type)
                        <div class="detail-row">
                            <span class="detail-label">Printer Type</span>
                            <span class="detail-value">{{ $asset->printer->type }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Color Support</span>
                            <span class="detail-value">{{ $asset->printer->color_support ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Duplex Printing</span>
                            <span class="detail-value">{{ $asset->printer->duplex ? 'Yes' : 'No' }}</span>
                        </div>
                    @endif

                    @if($asset->peripheral)
                        <!-- Peripheral Specifications -->
                        @if($asset->peripheral->type)
                        <div class="detail-row">
                            <span class="detail-label">Peripheral Type</span>
                            <span class="detail-value">{{ $asset->peripheral->type }}</span>
                        </div>
                        @endif
                        @if($asset->peripheral->interface)
                        <div class="detail-row">
                            <span class="detail-label">Interface</span>
                            <span class="detail-value">{{ $asset->peripheral->interface }}</span>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            <!-- Confirmation Section -->
            <div class="confirmation-section">
                <h3>Please Confirm Receipt</h3>
                <p>Click one of the buttons below to confirm whether you have received this asset:</p>
                
                <!-- Outlook-compatible button layout -->
                <table class="btn-table">
                    <tr>
                        <td>
                            <a href="{{ url('/asset-confirmation/confirm/' . $confirmationToken) }}" 
                               class="btn btn-confirm" 
                               role="button" 
                               aria-label="Confirm that I have received this asset">
                                ✓ Yes, I have received this asset
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{ url('/asset-confirmation/decline/' . $confirmationToken) }}" 
                               class="btn btn-decline" 
                               role="button" 
                               aria-label="Report that I have not received this asset">
                                ✗ No, I have not received this asset
                            </a>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Important Notice -->
            <div class="important-notice">
                <h4>Important Deadline</h4>
                <p>Please confirm receipt within <strong>3 business days</strong>. If we don't receive your confirmation, automated follow-up reminders will be sent to ensure proper asset tracking.</p>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h4>Need Help?</h4>
                <p>If you have any questions about this asset assignment or need assistance, please contact our IT Asset Management Team. We're here to help ensure a smooth asset assignment process.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an automated message from our secure asset management system.</p>
            <p>Please do not reply directly to this email.</p>
            <p class="signature">
                <strong>IT Asset Management Team</strong>
            </p>
            <p>If you received this email in error, please contact the IT department.</p>
        </div>
    </div>
</body>
</html>