<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            padding: 30px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            margin-bottom: 25px;
            font-size: 14px;
            color: #555;
        }
        .asset-list {
            margin: 20px 0;
        }
        .asset-card {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .asset-card h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 16px;
        }
        .asset-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 13px;
        }
        .detail-row {
            margin-bottom: 5px;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .specs-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        /* Outlook-compatible button styles */
        .confirm-button {
            display: inline-block;
            background-color: #667eea;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            border: none;
            mso-padding-alt: 12px 30px;
            mso-border-insideh: 0;
            mso-border-insidev: 0;
        }
        .confirm-button:hover {
            opacity: 0.9;
        }
        
        /* Button container for Outlook */
        .button-container {
            text-align: center;
            padding: 20px 0;
        }
        
        /* MSO (Microsoft Outlook) specific styles */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .confirm-button {
                display: inline-block !important;
            }
        }
        
        /* Fix for Outlook 2016+ */
        [owa] .confirm-button {
            display: inline-block !important;
        }
        
        /* Fallback for older Outlook versions */
        .confirm-button[class="confirm-button"] {
            display: inline-block !important;
        }
        .instructions {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
        .summary-box {
            background-color: #e7f3ff;
            border: 2px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .summary-box h2 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 20px;
        }
        @media only screen and (max-width: 600px) {
            .asset-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📦 {{ count($assetsData) >= 2 ? 'Multiple Asset Assignment Confirmation' : 'Asset Assignment Confirmation' }}</h1>
        </div>

        <div class="greeting">
            Hello {{ $user->first_name }} {{ $user->last_name }},
        </div>

        <div class="summary-box">
            <h2>{{ count($assetsData) }} Asset{{ count($assetsData) > 1 ? 's' : '' }} Assigned to You</h2>
            <p style="margin: 5px 0 0 0; color: #666;">Assignment Date: {{ \Carbon\Carbon::parse($assignedDate)->format('F d, Y') }}</p>
        </div>

        <div class="message">
            <p>You have been assigned <strong>{{ count($assetsData) }} asset{{ count($assetsData) > 1 ? 's' : '' }}</strong>. Please review the details below and confirm receipt of each asset.</p>
            
            @if($notes)
            <div class="instructions">
                <strong>📝 Assignment Notes:</strong><br>
                {{ $notes }}
            </div>
            @endif
        </div>

        <div class="asset-list">
            <h3 style="color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px;">Asset Details</h3>
            
            @foreach($assetsData as $index => $assetData)
                @php
                    $asset = $assetData['asset'];
                    $token = $assetData['confirmation_token'];
                @endphp
                
                <div class="asset-card">
                    <h3>{{ $index + 1 }}. {{ $asset->asset_tag }} - {{ $asset->name }}</h3>
                    
                    <div class="asset-details">
                        <div class="detail-row">
                            <span class="detail-label">Asset Tag:</span>
                            <span class="detail-value">{{ $asset->asset_tag }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Category:</span>
                            <span class="detail-value">{{ $asset->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Model:</span>
                            <span class="detail-value">{{ $asset->model ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Serial Number:</span>
                            <span class="detail-value">{{ $asset->serial_number ?? 'N/A' }}</span>
                        </div>
                        @if($asset->location)
                        <div class="detail-row">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value">{{ $asset->location }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Device Specifications --}}
                    @if($asset->computer)
                        <div class="specs-section">
                            <strong>💻 Computer Specifications:</strong>
                            <div class="asset-details" style="margin-top: 8px;">
                                <div class="detail-row">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value">{{ $asset->computer->type }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Processor:</span>
                                    <span class="detail-value">{{ $asset->computer->processor }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">RAM:</span>
                                    <span class="detail-value">{{ $asset->computer->ram }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Storage:</span>
                                    <span class="detail-value">{{ $asset->computer->storage }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($asset->monitor)
                        <div class="specs-section">
                            <strong>🖥️ Monitor Specifications:</strong>
                            <div class="asset-details" style="margin-top: 8px;">
                                <div class="detail-row">
                                    <span class="detail-label">Size:</span>
                                    <span class="detail-value">{{ $asset->monitor->size }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Resolution:</span>
                                    <span class="detail-value">{{ $asset->monitor->resolution }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Panel Type:</span>
                                    <span class="detail-value">{{ $asset->monitor->panel_type }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($asset->printer)
                        <div class="specs-section">
                            <strong>🖨️ Printer Specifications:</strong>
                            <div class="asset-details" style="margin-top: 8px;">
                                <div class="detail-row">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value">{{ $asset->printer->type }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Color Support:</span>
                                    <span class="detail-value">{{ $asset->printer->color_support ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($asset->peripheral)
                        <div class="specs-section">
                            <strong>🔌 Peripheral Specifications:</strong>
                            <div class="asset-details" style="margin-top: 8px;">
                                <div class="detail-row">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value">{{ $asset->peripheral->type }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Interface:</span>
                                    <span class="detail-value">{{ $asset->peripheral->interface }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Individual Confirm Button (Outlook-compatible) --}}
                    <div class="button-container">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: center;">
                            <tr>
                                <td align="center">
                                    <!--[if mso]>
                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" 
                                                 href="{{ route('asset-confirmation.show', $token) }}" 
                                                 style="height:44px;v-text-anchor:middle;width:300px;" 
                                                 arcsize="10%" 
                                                 stroke="f" 
                                                 fillcolor="#667eea">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;">✓ Confirm Receipt of {{ $asset->asset_tag }}</center>
                                    </v:roundrect>
                                    <![endif]-->
                                    <!--[if !mso]><!-->
                                    <a href="{{ route('asset-confirmation.show', $token) }}" class="confirm-button" style="display:inline-block;background-color:#667eea;color:#ffffff;text-decoration:none;padding:12px 30px;font-weight:bold;">
                                        ✓ Confirm Receipt of {{ $asset->asset_tag }}
                                    </a>
                                    <!--<![endif]-->
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="instructions">
            <strong>⚠️ Important Instructions:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Please confirm receipt of EACH asset by clicking the confirmation button for each one</li>
                <li>Inspect each asset for any damage or missing components</li>
                <li>If you find any issues, please report them immediately before confirming</li>
                <li>You are responsible for these assets once you confirm receipt</li>
                <li>Please keep these assets secure and in good condition</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions or concerns about these asset assignments, please contact the IT Department.</p>
            <p style="margin-top: 20px; color: #999;">
                This is an automated message from the Asset Management System. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>

