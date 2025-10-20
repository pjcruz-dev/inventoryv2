<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hybrid Asset Labels - Print & QR Codes</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            
            /* Optimize for hybrid labels */
            @page {
                margin: {{ ($labelWidth ?? 320) < 200 ? '0.25in' : '0.5in' }};
            }
            
            body {
                padding: {{ ($labelWidth ?? 320) < 200 ? '5px' : '10px' }} !important;
            }
            
            .labels-container {
                gap: 10px !important;
                max-width: none !important;
            }
            
            .hybrid-label {
                box-shadow: none !important;
                border-radius: {{ ($labelWidth ?? 320) < 200 ? '4px' : '8px' }} !important;
            }
            
            .qr-code {
                image-rendering: -webkit-optimize-contrast !important;
                image-rendering: crisp-edges !important;
                image-rendering: pixelated !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: {{ ($labelWidth ?? 320) < 200 ? '10px' : '20px' }};
            background: white;
            min-height: 100vh;
        }
        
        .main-container {
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
            margin: 0;
            max-width: none;
        }
        
        .print-controls {
            margin-bottom: 0;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .print-controls::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .print-controls h3 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        
        .print-controls p {
            margin: 0 0 20px 0;
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            margin: 0 8px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }
        
        .labels-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax({{ $labelWidth ?? 320 }}px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background: transparent;
        }
        
        .hybrid-label {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            page-break-inside: avoid;
            position: relative;
            min-height: {{ $labelHeight ?? 200 }}px;
            width: {{ $labelWidth ?? 320 }}px;
            max-width: {{ $labelWidth ?? 320 }}px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .hybrid-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-color: #667eea;
        }
        
        .hybrid-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .label-header {
            text-align: center;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 8px;
            margin-bottom: 12px;
            position: relative;
        }
        
        .label-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .asset-tag {
            font-size: {{ ($labelWidth ?? 320) < 200 ? '14px' : '18px' }};
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .asset-name {
            font-size: {{ ($labelWidth ?? 320) < 200 ? '10px' : '12px' }};
            color: #6c757d;
            margin: 2px 0;
            font-weight: 500;
        }
        
        .label-content {
            display: flex;
            flex: 1;
            gap: 12px;
        }
        
        .text-info {
            flex: 1;
            font-size: {{ ($labelWidth ?? 320) < 200 ? '8px' : '10px' }};
        }
        
        .qr-section {
            flex: 0 0 auto;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .qr-code {
            width: {{ ($labelWidth ?? 320) < 200 ? '95px' : '100px' }};
            height: {{ ($labelWidth ?? 320) < 200 ? '95px' : '100px' }};
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }
        
        .qr-code:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .no-qr {
            width: {{ ($labelWidth ?? 320) < 200 ? '95px' : '100px' }};
            height: {{ ($labelWidth ?? 320) < 200 ? '95px' : '100px' }};
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 2px dashed #dee2e6;
            color: #6c757d;
            font-size: {{ ($labelWidth ?? 320) < 200 ? '6px' : '8px' }};
            text-align: center;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            padding: 2px 0;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        
        .info-row:hover {
            background-color: transparent;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #6c757d;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: {{ ($labelWidth ?? 320) < 200 ? '7px' : '8px' }};
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-active { 
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .status-inactive { 
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        .status-pending { 
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); 
            color: #856404; 
            border: 1px solid #ffeaa7;
        }
        .status-maintenance { 
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); 
            color: #0c5460; 
            border: 1px solid #bee5eb;
        }
        
        .qr-label {
            font-size: {{ ($labelWidth ?? 320) < 200 ? '6px' : '8px' }};
            color: #6c757d;
            margin-top: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .company-logo {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: {{ ($labelWidth ?? 320) < 200 ? '8px' : '10px' }};
            color: #667eea;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Size Presets Styling */
        .size-presets {
            margin: 0;
            padding: 25px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 0;
            border: none;
            position: relative;
            z-index: 1;
        }
        
        .size-presets h4 {
            color: white;
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .preset-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .preset-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #495057;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 15px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 12px;
            line-height: 1.3;
            font-weight: 500;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .preset-btn:hover {
            background: rgba(255, 255, 255, 1);
            color: #667eea;
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .preset-btn.active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .preset-btn small {
            display: block;
            margin-top: 6px;
            opacity: 0.8;
            font-weight: 400;
        }
        
        .custom-size {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .custom-size h5 {
            color: white;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .size-inputs {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .size-inputs label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        
        .size-inputs input {
            width: 80px;
            padding: 8px 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .size-inputs input:focus {
            outline: none;
            border-color: #28a745;
            background: white;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
        }
        
        .action-buttons {
            margin-top: 25px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="print-controls no-print">
            <h3>Hybrid Asset Labels (Print + QR Codes)</h3>
            <p>Printing {{ count($assets) }} hybrid labels ({{ $labelWidth ?? 320 }}x{{ $labelHeight ?? 200 }}px each)</p>
            
            <!-- Label Size Presets -->
            <div class="size-presets">
                <h4>Label Size Presets</h4>
                <div class="preset-buttons">
                    <button class="btn preset-btn" onclick="setLabelSize(152, 102, 'Small')" data-width="152" data-height="102">
                        📱 Small (0.75" × 0.5")<br><small>Cellphones, USB</small>
                    </button>
                    <button class="btn preset-btn" onclick="setLabelSize(203, 102, 'Medium')" data-width="203" data-height="102">
                        💻 Medium (1" × 0.5")<br><small>Laptops, Tablets</small>
                    </button>
                    <button class="btn preset-btn" onclick="setLabelSize(304, 102, 'Large')" data-width="304" data-height="102">
                        🖥️ Large (1.5" × 0.5")<br><small>Monitors, Printers</small>
                    </button>
                    <button class="btn preset-btn active" onclick="setLabelSize(304, 203, 'QR Code')" data-width="304" data-height="203">
                        📊 QR Code (1.5" × 1")<br><small>QR Code Labels</small>
                    </button>
                    <button class="btn preset-btn" onclick="setLabelSize(406, 203, 'Extra Large')" data-width="406" data-height="203">
                        🖨️ Extra Large (2" × 1")<br><small>Large Equipment</small>
                    </button>
                </div>
                
                <!-- Custom Size Inputs -->
                <div class="custom-size">
                    <h5>Custom Size</h5>
                    <div class="size-inputs">
                        <label>Width: <input type="number" id="customWidth" value="{{ $labelWidth ?? 320 }}" min="50" max="800">px</label>
                        <label>Height: <input type="number" id="customHeight" value="{{ $labelHeight ?? 200 }}" min="50" max="400">px</label>
                        <button class="btn" onclick="applyCustomSize()">Apply Custom Size</button>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Labels
                </button>
                <button class="btn btn-secondary" onclick="window.close()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
        
        <div class="labels-container">
            @foreach($assets as $asset)
            <div class="hybrid-label">
                @if($asset->entity)
                <div class="company-logo">{{ $asset->entity }}</div>
                @elseif(request('company_name', 'INVENTORY') !== 'none')
                <div class="company-logo">{{ request('company_name', 'INVENTORY') }}</div>
                @endif
                
                <div class="label-header">
                    <div class="asset-tag">{{ $asset->asset_tag ?? 'N/A' }}</div>
                    <div class="asset-name">{{ $asset->name ?? 'Unknown Asset' }}</div>
                </div>
                
                <div class="label-content">
                    <div class="text-info">
                        <div class="info-row">
                            <span class="info-label">Category:</span>
                            <span class="info-value">{{ $asset->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $asset->status ?? 'unknown')) }}">
                                {{ $asset->status ?? 'Unknown' }}
                            </span>
                        </div>
                        @if(($labelWidth ?? 320) >= 200)
                        <div class="info-row">
                            <span class="info-label">Serial:</span>
                            <span class="info-value">{{ $asset->serial_number ?? 'N/A' }}</span>
                        </div>
                        @endif
                        @if($asset->assignedUser)
                        <div class="info-row">
                            <span class="info-label">Assigned:</span>
                            <span class="info-value">{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</span>
                        </div>
                        @endif
                        @if(($labelWidth ?? 320) >= 200)
                        <div class="info-row">
                            <span class="info-label">Location:</span>
                            <span class="info-value">{{ $asset->location ?? 'N/A' }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ $asset->created_at ? $asset->created_at->format('M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="qr-section">
                        @php
                            $qrCodeUrl = $asset->getQRCodeUrl();
                        @endphp
                        @if($qrCodeUrl)
                            <img src="{{ $qrCodeUrl }}" alt="QR Code for {{ $asset->asset_tag }}" class="qr-code" 
                                 onload="console.log('QR code loaded for {{ $asset->asset_tag }}')" 
                                 onerror="console.log('QR code failed to load for {{ $asset->asset_tag }}'); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="no-qr" style="display: none;">QR Code<br>Not Available</div>
                        @else
                            <div class="no-qr">QR Code<br>Not Available</div>
                        @endif
                        <div class="qr-label">Scan for details</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <script>
        // Current label dimensions
        let currentWidth = {{ $labelWidth ?? 320 }};
        let currentHeight = {{ $labelHeight ?? 200 }};
        
        // Set label size function
        function setLabelSize(width, height, presetName) {
            currentWidth = width;
            currentHeight = height;
            
            console.log(`Setting label size: ${width}x${height}px (${presetName})`);
            
            // Update UI
            document.getElementById('customWidth').value = width;
            document.getElementById('customHeight').value = height;
            
            // Update active preset button
            document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update labels container
            updateLabelSizes();
            
            // Update info text
            document.querySelector('.print-controls p').textContent = 
                `Printing {{ count($assets) }} hybrid labels (${width}x${height}px each) - ${presetName}`;
        }
        
        // Apply custom size
        function applyCustomSize() {
            const width = parseInt(document.getElementById('customWidth').value);
            const height = parseInt(document.getElementById('customHeight').value);
            
            if (width < 50 || width > 800 || height < 50 || height > 400) {
                alert('Please enter valid dimensions:\nWidth: 50-800px\nHeight: 50-400px');
                return;
            }
            
            currentWidth = width;
            currentHeight = height;
            
            // Remove active preset
            document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));
            
            // Update labels container
            updateLabelSizes();
            
            // Update info text
            document.querySelector('.print-controls p').textContent = 
                `Printing {{ count($assets) }} hybrid labels (${width}x${height}px each) - Custom Size`;
        }
        
        // Update label sizes dynamically
        function updateLabelSizes() {
            console.log(`Updating label sizes to: ${currentWidth}x${currentHeight}px`);
            
            const labelsContainer = document.querySelector('.labels-container');
            const labels = document.querySelectorAll('.hybrid-label');
            
            console.log(`Found ${labels.length} labels to update`);
            
            // Update container grid
            labelsContainer.style.gridTemplateColumns = `repeat(auto-fit, minmax(${currentWidth}px, 1fr))`;
            
            // Update each label
            labels.forEach((label, index) => {
                label.style.minHeight = `${currentHeight}px`;
                label.style.width = `${currentWidth}px`;
                label.style.maxWidth = `${currentWidth}px`;
                
                console.log(`Label ${index + 1}: Set to ${currentWidth}x${currentHeight}px`);
                
                // Update font sizes based on label size
                const isSmall = currentWidth < 200;
                
                // Update asset tag
                const assetTag = label.querySelector('.asset-tag');
                assetTag.style.fontSize = isSmall ? '14px' : '18px';
                
                // Update asset name
                const assetName = label.querySelector('.asset-name');
                assetName.style.fontSize = isSmall ? '10px' : '12px';
                
                // Update text info
                const textInfo = label.querySelector('.text-info');
                textInfo.style.fontSize = isSmall ? '8px' : '10px';
                
                // Show/hide fields based on label size
                const serialRow = label.querySelector('.info-row:nth-child(3)');
                const locationRow = label.querySelector('.info-row:nth-child(5)');
                
                if (serialRow) {
                    serialRow.style.display = isSmall ? 'none' : 'flex';
                }
                if (locationRow) {
                    locationRow.style.display = isSmall ? 'none' : 'flex';
                }
                
                // Update QR code
                const qrCode = label.querySelector('.qr-code');
                const qrSize = isSmall ? '95px' : '100px';
                if (qrCode) {
                    qrCode.style.width = qrSize;
                    qrCode.style.height = qrSize;
                }
                
                // Update no-qr placeholder
                const noQr = label.querySelector('.no-qr');
                if (noQr) {
                    noQr.style.width = qrSize;
                    noQr.style.height = qrSize;
                    noQr.style.fontSize = isSmall ? '6px' : '8px';
                }
                
                // Update QR label
                const qrLabel = label.querySelector('.qr-label');
                qrLabel.style.fontSize = isSmall ? '6px' : '8px';
                
                // Update company logo
                const companyLogo = label.querySelector('.company-logo');
                companyLogo.style.fontSize = isSmall ? '8px' : '10px';
                
                // Update status badge
                const statusBadge = label.querySelector('.status-badge');
                statusBadge.style.fontSize = isSmall ? '7px' : '8px';
                
                // Update border radius
                label.style.borderRadius = isSmall ? '4px' : '8px';
            });
            
            console.log('Label size update completed');
        }
        
        // Auto-print when page loads
        window.onload = function() {
            // Initialize labels with current size
            updateLabelSizes();
            
            // Wait a moment for images to load
            setTimeout(() => {
                window.print();
            }, 1000);
        };
        
        // Handle print completion
        window.onafterprint = function() {
            // Optional: Close window after printing
            // window.close();
        };
    </script>
</body>
</html>
