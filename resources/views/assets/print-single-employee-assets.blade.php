<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Asset Report - {{ $user->first_name }} {{ $user->last_name }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-date {
            font-size: 12px;
            color: #888;
        }
        
        .employee-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .employee-info h3 {
            margin-top: 0;
            color: #333;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            color: #333;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .assets-table th,
        .assets-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        
        .assets-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .assets-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-assets {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
        
        .print-controls {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn" onclick="window.print()">Print Report</button>
        <button class="btn" onclick="window.close()" style="background-color: #6c757d; margin-left: 10px;">Close</button>
    </div>
    
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Inventory Management System') }}</div>
        <div class="report-title">Employee Asset Report</div>
        <div class="report-date">Generated on {{ now()->format('F j, Y \\a\\t g:i A') }}</div>
    </div>
    
    <div class="employee-info">
        <h3>Employee Information</h3>
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
                <div class="info-label">Email</div>
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
        </div>
    </div>
    
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $totalAssets }}</div>
            <div class="stat-label">Total Assets</div>
        </div>
        @foreach($assetsByCategory as $category => $count)
        <div class="stat-card">
            <div class="stat-number">{{ $count }}</div>
            <div class="stat-label">{{ $category }}</div>
        </div>
        @endforeach
    </div>
    
    @if($user->assignedAssets->count() > 0)
        <h3>Assigned Assets</h3>
        <table class="assets-table">
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Asset Name</th>
                    <th>Category</th>
                    <th>Vendor</th>
                    <th>Serial Number</th>
                    <th>Computer Info</th>
                    <th>Assigned Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->assignedAssets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? 'N/A' }}</td>
                    <td>{{ $asset->vendor->name ?? 'N/A' }}</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                    <td>
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
                        <span style="
                            padding: 2px 6px;
                            border-radius: 3px;
                            font-size: 10px;
                            font-weight: bold;
                            @if($asset->status === 'active') background-color: #d4edda; color: #155724;
                            @elseif($asset->status === 'maintenance') background-color: #fff3cd; color: #856404;
                            @elseif($asset->status === 'disposed') background-color: #f8d7da; color: #721c24;
                            @else background-color: #e2e3e5; color: #383d41; @endif
                        ">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-assets">
            <p>No assets are currently assigned to this employee.</p>
        </div>
    @endif
    
    <div class="footer">
        <p>This report was generated automatically by the Inventory Management System.</p>
        <p>For questions or concerns, please contact the IT department.</p>
    </div>
    
    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Print function
        function printReport() {
            window.print();
        }
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>