<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $options['title'] ?? 'Assets Export' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .summary-row strong {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-issue {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-maintenance {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $options['title'] ?? 'Assets Export' }}</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Total Assets: {{ $assets->count() }}</p>
    </div>
    
    @if($assets->count() > 0)
    <div class="summary">
        <h3>Export Summary</h3>
        <div class="summary-row">
            <span><strong>Total Assets:</strong></span>
            <span>{{ $assets->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Active Assets:</strong></span>
            <span>{{ $assets->where('status', 'Active')->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Under Maintenance:</strong></span>
            <span>{{ $assets->where('status', 'Under Maintenance')->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Issue Reported:</strong></span>
            <span>{{ $assets->where('status', 'Issue Reported')->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Assigned Assets:</strong></span>
            <span>{{ $assets->whereNotNull('assigned_user_id')->count() }}</span>
        </div>
        <div class="summary-row">
            <span><strong>Unassigned Assets:</strong></span>
            <span>{{ $assets->whereNull('assigned_user_id')->count() }}</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Tag</th>
                <th style="width: 20%;">Name</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Assigned To</th>
                <th style="width: 15%;">Location</th>
                <th style="width: 15%;">Serial Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
                @if($index > 0 && $index % 25 == 0)
                    <tr class="page-break"></tr>
                @endif
                <tr>
                    <td><strong>{{ $asset->tag }}</strong></td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $asset->status)) }}">
                            {{ $asset->status }}
                        </span>
                    </td>
                    <td>{{ $asset->assignedUser->name ?? 'Unassigned' }}</td>
                    <td>{{ $asset->location ?? 'N/A' }}</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($options['include_images'] ?? false)
    <div class="page-break"></div>
    <h3>Asset Images</h3>
    <p><em>Note: Images are not included in this PDF export. Please use Excel export for image data.</em></p>
    @endif
    
    @if($options['include_qr_codes'] ?? false)
    <div class="page-break"></div>
    <h3>QR Codes</h3>
    <p><em>Note: QR codes are not included in this PDF export. Please use Excel export for QR code data.</em></p>
    @endif
    
    @else
    <div class="no-data">
        <h3>No Assets Found</h3>
        <p>There are no assets to export based on the current filters.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>This report was generated automatically by the Inventory Management System.</p>
        <p>For questions or concerns, please contact the IT department.</p>
        <p>Page {{ $PAGE_NUM ?? 1 }} of {{ $PAGE_COUNT ?? 1 }}</p>
    </div>
</body>
</html>
