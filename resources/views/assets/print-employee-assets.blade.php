<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Asset Report</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header .subtitle {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .summary-item .label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .employee-section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .employee-header {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .employee-info {
            background-color: #ecf0f1;
            padding: 8px 15px;
            font-size: 11px;
            color: #2c3e50;
        }
        
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .assets-table th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        
        .assets-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        
        .assets-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .asset-tag {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #2ecc71; color: white; }
        .status-inactive { background-color: #e74c3c; color: white; }
        .status-maintenance { background-color: #f39c12; color: white; }
        .status-pending { background-color: #9b59b6; color: white; }
        
        .cost {
            text-align: right;
            font-weight: bold;
        }
        
        .print-controls {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn">
            <i class="fas fa-print"></i> Print Report
        </button>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assets
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>Employee Asset Report</h1>
        <div class="subtitle">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        <div class="subtitle">Inventory Management System</div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <div class="number">{{ $totalUsers }}</div>
            <div class="label">Employees with Assets</div>
        </div>
        <div class="summary-item">
            <div class="number">{{ $totalAssets }}</div>
            <div class="label">Total Assigned Assets</div>
        </div>
        <div class="summary-item">
            <div class="number">₱{{ number_format($totalValue, 2) }}</div>
            <div class="label">Total Asset Value</div>
        </div>
        <div class="summary-item">
            <div class="number">{{ $totalUsers > 0 ? number_format($totalAssets / $totalUsers, 1) : '0' }}</div>
            <div class="label">Avg Assets per Employee</div>
        </div>
    </div>

    <!-- Employee Asset Details -->
    @foreach($users as $user)
        <div class="employee-section">
            <div class="employee-header">
                {{ $user->first_name }} {{ $user->last_name }}
                <span style="float: right;">{{ $user->assignedAssets->count() }} Asset(s)</span>
            </div>
            
            <div class="employee-info">
                <strong>Employee ID:</strong> {{ $user->employee_id ?? 'N/A' }} |
                <strong>Email:</strong> {{ $user->email }} |
                <strong>Department:</strong> {{ $user->department->name ?? 'N/A' }} |
                <strong>Job Title:</strong> {{ $user->job_title ?? 'N/A' }}
            </div>
            
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Assigned Date</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->assignedAssets as $asset)
                        <tr>
                            <td class="asset-tag">{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->category->name ?? 'N/A' }}</td>
                            <td>{{ $asset->vendor->name ?? 'N/A' }}</td>
                            <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($asset->status) }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td>{{ $asset->assigned_date ? $asset->assigned_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="cost">{{ $asset->cost ? '₱' . number_format($asset->cost, 2) : 'N/A' }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #ecf0f1; font-weight: bold;">
                        <td colspan="7" style="text-align: right; padding-right: 10px;">Total Value:</td>
                        <td class="cost">₱{{ number_format($user->assignedAssets->sum('cost'), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(!$loop->last)
            <div style="margin: 20px 0;"></div>
        @endif
    @endforeach

    @if($users->isEmpty())
        <div style="text-align: center; padding: 50px; color: #7f8c8d;">
            <h3>No employees with assigned assets found.</h3>
            <p>There are currently no assets assigned to any employees in the system.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the Inventory Management System.</p>
        <p>Report contains {{ $totalUsers }} employee(s) with {{ $totalAssets }} assigned asset(s) worth ₱{{ number_format($totalValue, 2) }}.</p>
        <p>Generated by: {{ auth()->user()->first_name }} {{ auth()->user()->last_name }} ({{ auth()->user()->email }})</p>
    </div>

    <script>
        // Auto-focus for better print experience
        window.addEventListener('load', function() {
            // Optional: Auto-print when page loads (uncomment if desired)
            // window.print();
        });
        
        // Handle print button click
        function printReport() {
            window.print();
        }
    </script>
</body>
</html>