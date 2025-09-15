<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Labels - Bulk Print</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            
            /* Optimize for small labels */
            @page {
                margin: {{ ($labelWidth ?? 320) < 150 ? '0.25in' : '0.5in' }};
            }
            
            body {
                padding: {{ ($labelWidth ?? 320) < 150 ? '5px' : '10px' }} !important;
            }
            
            .labels-container {
                gap: 10px !important;
                max-width: none !important;
            }
            
            .label {
                box-shadow: none !important;
                border-radius: {{ ($labelWidth ?? 320) < 150 ? '4px' : '8px' }} !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: {{ ($labelWidth ?? 320) < 150 ? '10px' : '20px' }};
            background-color: #f5f5f5;
        }
        
        .print-controls {
            margin-bottom: 20px;
            text-align: center;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .labels-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax({{ $labelWidth ?? 320 }}px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .label {
            border: 2px solid #000;
            padding: {{ ($labelWidth ?? 320) < 150 ? '8px' : '20px' }};
            background: white;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            break-inside: avoid;
            width: {{ $labelWidth ?? 320 }}px;
            height: {{ $labelHeight ?? 200 }}px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        
        .asset-tag {
            font-size: {{ ($labelWidth ?? 320) < 150 ? '14px' : (($labelWidth ?? 320) < 200 ? '18px' : '24px') }};
            font-weight: bold;
            margin-bottom: {{ ($labelHeight ?? 200) < 100 ? '4px' : '12px' }};
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: {{ ($labelHeight ?? 200) < 100 ? '2px' : '8px' }};
            word-wrap: break-word;
        }
        
        .asset-name {
            font-size: {{ ($labelWidth ?? 320) < 150 ? '10px' : (($labelWidth ?? 320) < 200 ? '12px' : '16px') }};
            margin-bottom: {{ ($labelHeight ?? 200) < 100 ? '4px' : '10px' }};
            color: #555;
            font-weight: 600;
            word-wrap: break-word;
        }
        
        .asset-details {
            font-size: {{ ($labelWidth ?? 320) < 150 ? '8px' : (($labelWidth ?? 320) < 200 ? '10px' : '12px') }};
            color: #666;
            line-height: 1.2;
            word-wrap: break-word;
        }
        
        .asset-details strong {
            color: #333;
        }
        
        .category-badge {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: {{ ($labelWidth ?? 320) < 150 ? '1px 4px' : '2px 8px' }};
            border-radius: 12px;
            font-size: {{ ($labelWidth ?? 320) < 150 ? '7px' : '10px' }};
            font-weight: bold;
            margin-top: {{ ($labelHeight ?? 200) < 100 ? '2px' : '8px' }};
            word-wrap: break-word;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 28px;
        }
        
        .header .subtitle {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }
        
        .summary {
            text-align: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        @media print {
            body {
                background-color: white;
                padding: 10px;
            }
            
            .labels-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .label {
                box-shadow: none;
                border-radius: 0;
                margin-bottom: 15px;
            }
            
            .header, .summary {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button class="btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Labels
        </button>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Assets
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>Asset Labels</h1>
        <div class="subtitle">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        <div class="subtitle">{{ config('app.name', 'Inventory Management System') }}</div>
    </div>

    <!-- Summary -->
    <div class="summary">
        <strong>{{ $assets->count() }}</strong> asset label(s) ready for printing
    </div>

    <!-- Labels Grid -->
    <div class="labels-container">
        @foreach($assets as $asset)
            <div class="label">
                <div class="asset-tag">{{ $asset->asset_tag }}</div>
                <div class="asset-name">{{ $asset->name }}</div>
                <div class="asset-details">
                    @if($asset->model)
                        <strong>Model:</strong> {{ $asset->model }}<br>
                    @endif
                    @if($asset->serial_number)
                        <strong>S/N:</strong> {{ $asset->serial_number }}<br>
                    @endif
                    @if($asset->location)
                        <strong>Location:</strong> {{ $asset->location }}<br>
                    @endif
                    @if($asset->purchase_date)
                        <strong>Purchase Date:</strong> {{ $asset->purchase_date->format('M d, Y') }}<br>
                    @endif
                    @if($asset->department)
                        <strong>Department:</strong> {{ $asset->department->name }}<br>
                    @endif
                    @if($asset->vendor)
                        <strong>Vendor:</strong> {{ $asset->vendor->name }}
                    @endif
                </div>
                @if($asset->category)
                    <div class="category-badge">{{ $asset->category->name }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        // Auto-focus for better print experience
        window.addEventListener('load', function() {
            // Optional: Auto-print when page loads (uncomment if desired)
            // window.print();
        });
    </script>
</body>
</html>