<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Asset Report - {{ $user->first_name }} {{ $user->last_name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            margin: 0.4in;
            size: A4;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page-break { page-break-before: always; }
            .print-controls { display: none !important; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .report-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }
        
        .print-controls {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            margin: 0 5px;
        }
        
        .btn:hover {
            background: #34495e;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .company-name {
            color: #2c3e50;
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }
        
        .report-title {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 3px;
        }
        
        .report-date {
            color: #7f8c8d;
            font-size: 11px;
            margin-top: 2px;
        }
        
        .content {
            padding: 0;
        }
        
        .employee-info {
            background: #f8f9fa;
            padding: 6px;
            border-radius: 3px;
            margin-bottom: 12px;
            border-left: 3px solid #3498db;
        }
        
        .employee-info h3 {
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 13px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .employee-info h3 i {
            margin-right: 8px;
            color: #3498db;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .info-item {
            border: 2px solid #000;
            padding: 4px;
            border-radius: 2px;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .info-value {
            color: #333;
            font-size: 12px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            border: 2px solid #000;
            padding: 4px;
            text-align: center;
            border-radius: 2px;
        }
        
        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
            display: block;
        }
        
        .stat-label {
            font-size: 10px;
            color: #2c3e50;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .section-title {
            background: #34495e;
            color: white;
            padding: 4px 8px;
            margin: 0 0 8px 0;
            font-size: 13px;
            font-weight: bold;
            border-radius: 2px;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        .assets-table-container {
            background: white;
            overflow: hidden;
        }
        
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        
        .assets-table th,
        .assets-table td {
            border: 2px solid #000;
            padding: 3px 4px;
            text-align: left;
            font-size: 10px;
        }
        
        .assets-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .assets-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-maintenance {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-disposed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-available {
            background: #cce5ff;
            color: #004085;
        }
        
        .no-assets {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .no-assets i {
            font-size: 24px;
            color: #ddd;
            margin-bottom: 8px;
        }
        
        .no-assets h3 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #999;
        }
        
        .no-assets p {
            font-size: 12px;
            color: #aaa;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .footer p {
            margin-bottom: 3px;
        }
        
        .computer-info {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .computer-info small {
            color: #666;
            font-style: italic;
        }
        
        .print-only {
            display: block;
        }
        
        .no-print {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            body {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    
    <div class="report-container">
        <div class="header">
            <div class="company-name">{{ config('app.name', 'Inventory Management System') }}</div>
            <div class="report-title">Employee Asset Report</div>
            <div class="report-date">Generated on {{ now()->format('F j, Y \\a\\t g:i A') }}</div>
        </div>
        
        <div class="content">
            <div class="employee-info">
                <h3><i class="fas fa-user"></i> Employee Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $user->first_name }} {{ $user->last_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Employee ID</div>
                        <div class="info-value">{{ $user->employee_no ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $user->department->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Position</div>
                        <div class="info-value">{{ $user->position ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $user->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalAssets }}</div>
                    <div class="stat-label">Total Assets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">@currency($totalValue)</div>
                    <div class="stat-label">Total Value</div>
                </div>
                @foreach($assetsByCategory as $category => $count)
                <div class="stat-card">
                    <div class="stat-number">{{ $count }}</div>
                    <div class="stat-label">{{ $category }}</div>
                </div>
                @endforeach
            </div>
            
            @if($user->assignedAssets->count() > 0)
                <h3 class="section-title"><i class="fas fa-laptop"></i> Assigned Assets</h3>
                <div class="assets-table-container">
                    <table class="assets-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> Asset Tag</th>
                                <th><i class="fas fa-cube"></i> Asset Name</th>
                                <th><i class="fas fa-folder"></i> Category</th>
                                <th><i class="fas fa-building"></i> Vendor</th>
                                <th><i class="fas fa-barcode"></i> Serial Number</th>
                                <th><i class="fas fa-desktop"></i> Computer Info</th>
                                <th><i class="fas fa-calendar"></i> Assigned Date</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->assignedAssets as $asset)
                            <tr>
                                <td><strong>{{ $asset->asset_tag }}</strong></td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                                <td>{{ $asset->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                                <td class="computer-info">
                                    @if($asset->computer)
                                        {{ $asset->computer->brand }} {{ $asset->computer->model }}
                                        @if($asset->computer->serial_number)
                                            <br><small>SN: {{ $asset->computer->serial_number }}</small>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $asset->assigned_date ? $asset->assigned_date->format('M j, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $asset->status }}">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-assets">
                    <i class="fas fa-inbox"></i>
                    <h3>No Assets Assigned</h3>
                    <p>This employee currently has no assets assigned to them.</p>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p><i class="fas fa-info-circle"></i> This report was generated automatically by the Inventory Management System.</p>
            <p><i class="fas fa-envelope"></i> For questions or concerns, please contact the IT department.</p>
        </div>
    </div>
    
    <script>
        // Print functionality
        function printReport() {
            window.print();
        }
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printReport();
            }
            if (e.key === 'Escape') {
                window.close();
            }
        });
        
        // Add click-to-copy functionality for asset tags
        document.addEventListener('DOMContentLoaded', function() {
            const assetTags = document.querySelectorAll('td:first-child strong');
            assetTags.forEach(tag => {
                tag.style.cursor = 'pointer';
                tag.title = 'Click to copy asset tag';
                
                tag.addEventListener('click', function() {
                    const text = this.textContent;
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.style.color = '#28a745';
                        
                        setTimeout(() => {
                            this.textContent = originalText;
                            this.style.color = '';
                        }, 1000);
                    }).catch(err => {
                        console.log('Failed to copy: ', err);
                    });
                });
            });
        });
    </script>
</body>
</html>