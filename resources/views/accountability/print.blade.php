<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Accountability Form - {{ $formData['asset']->asset_tag }}</title>
    <style>
        @page {
            margin: 0.4in;
            size: A4;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 3px;
        }
        
        .form-meta {
            background: #f8f9fa;
            padding: 6px;
            border-radius: 3px;
            margin-bottom: 12px;
            border-left: 3px solid #3498db;
        }
        
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
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
        
        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        
        .timeline-table th,
        .timeline-table td {
            border: 2px solid #000;
            padding: 3px 4px;
            text-align: left;
            font-size: 10px;
        }
        
        .timeline-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .timeline-table tr:nth-child(even) {
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
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-declined {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-assigned {
            background: #cce5ff;
            color: #004085;
        }
        
        .signature-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        
        .signature-line {
            border-bottom: 2px solid #000;
            width: 150px;
            margin: 10px 0 3px 0;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
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
            
            .section {
                page-break-inside: avoid;
            }
        }
        
        .highlight {
            background: #fff3cd;
            padding: 8px;
            border-radius: 3px;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 8px;
            border-radius: 3px;
            border-left: 4px solid #17a2b8;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ASSET ACCOUNTABILITY FORM</h1>
        <div class="subtitle">ICT Asset Management System - Complete Audit Trail</div>
    </div>

    <!-- Form Metadata -->
    <div class="form-meta">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Form ID</div>
                <div class="info-value">{{ $formData['form_id'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Generated Date</div>
                <div class="info-value">{{ $formData['generated_at']->format('F d, Y \a\t g:i A') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Generated By</div>
                <div class="info-value">{{ $formData['generated_by']->first_name }} {{ $formData['generated_by']->last_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Generated Time</div>
                <div class="info-value">{{ $formData['generated_at']->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Asset Information -->
    <div class="section">
        <div class="section-title">Asset Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Asset Name</div>
                <div class="info-value">{{ $formData['asset']->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Asset Tag</div>
                <div class="info-value">{{ $formData['asset']->asset_tag }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Serial Number</div>
                <div class="info-value">{{ $formData['asset']->serial_number }}</div>
            </div>
            @if($formData['asset']->category && strtolower($formData['asset']->category->name) == 'mobile devices' && $formData['asset']->mobile_number)
            <div class="info-item">
                <div class="info-label">Mobile Number</div>
                <div class="info-value">{{ $formData['asset']->mobile_number }}</div>
            </div>
            @endif
            <div class="info-item">
                <div class="info-label">Model</div>
                <div class="info-value">{{ $formData['asset']->model ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Category</div>
                <div class="info-value">{{ $formData['asset']->category->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Vendor</div>
                <div class="info-value">{{ $formData['asset']->vendor->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Purchase Date</div>
                <div class="info-value">{{ $formData['asset']->purchase_date ? $formData['asset']->purchase_date->format('M d, Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Cost</div>
                <div class="info-value">₱{{ number_format($formData['asset']->cost, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Current Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $formData['asset']->status)) }}">
                        {{ $formData['asset']->status }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $formData['asset']->location ?? 'N/A' }}</div>
            </div>
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">Notes</div>
                <div class="info-value">{{ $formData['asset']->notes ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Current Assignment -->
    <div class="section">
        <div class="section-title">Current Assignment</div>
        @if($formData['asset']->assignedUser)
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Assigned To</div>
                    <div class="info-value">
                        {{ $formData['asset']->assignedUser->first_name }} {{ $formData['asset']->assignedUser->last_name }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $formData['asset']->assignedUser->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Employee Number</div>
                    <div class="info-value">{{ $formData['asset']->assignedUser->employee_no ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Position</div>
                    <div class="info-value">{{ $formData['asset']->assignedUser->position ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ $formData['asset']->assignedUser->department->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Assignment Date</div>
                    <div class="info-value">
                        {{ $formData['asset']->assigned_date ? $formData['asset']->assigned_date->format('M d, Y \a\t g:i A') : 'N/A' }}
                    </div>
                </div>
            </div>
        @else
            <div class="alert-info">
                <strong>No Current Assignment:</strong> This asset is not currently assigned to any user.
            </div>
        @endif
    </div>

    <!-- Assignment History -->
    <!-- <div class="section">
        <div class="section-title">Assignment History</div>
        @if($formData['assignments']->count() > 0)
            <table class="timeline-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Assigned To</th>
                        <th>Assigned By</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formData['assignments']->take(3) as $assignment)
                        <tr>
                            <td>{{ $assignment->assigned_date->format('M d, Y g:i A') }}</td>
                            <td>{{ $assignment->user->first_name }} {{ $assignment->user->last_name }}</td>
                            <td>{{ $assignment->assignedBy->first_name ?? 'System' }} {{ $assignment->assignedBy->last_name ?? '' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $assignment->status)) }}">
                                    {{ $assignment->status }}
                                </span>
                            </td>
                            <td>{{ $assignment->notes ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($formData['assignments']->count() > 3)
                <div style="font-size: 10px; color: #666; margin-top: 5px; text-align: center;">
                    Showing 3 of {{ $formData['assignments']->count() }} assignment records
                </div>
            @endif
        @else
            <div class="alert-info">No assignment history found for this asset.</div>
        @endif
    </div> -->

    <!-- Confirmation History -->
    <!-- <div class="section">
        <div class="section-title">Confirmation History</div>
        @if($formData['confirmations']->count() > 0)
            <table class="timeline-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Response Time</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formData['confirmations']->take(3) as $confirmation)
                        <tr>
                            <td>{{ $confirmation->created_at->format('M d, Y g:i A') }}</td>
                            <td>{{ $confirmation->user->first_name }} {{ $confirmation->user->last_name }}</td>
                            <td>
                                <span class="status-badge status-{{ $confirmation->status }}">
                                    {{ ucfirst($confirmation->status) }}
                                </span>
                            </td>
                            <td>
                                @if($confirmation->confirmed_at || $confirmation->declined_at)
                                    {{ $confirmation->created_at->diffForHumans($confirmation->confirmed_at ?? $confirmation->declined_at) }}
                                @else
                                    Pending
                                @endif
                            </td>
                            <td>
                                @if($confirmation->decline_reason)
                                    Reason: {{ $confirmation->getFormattedDeclineReason() }}
                                @else
                                    Confirmed via email
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($formData['confirmations']->count() > 3)
                <div style="font-size: 10px; color: #666; margin-top: 5px; text-align: center;">
                    Showing 3 of {{ $formData['confirmations']->count() }} confirmation records
                </div>
            @endif
        @else
            <div class="alert-info">No confirmation history found for this asset.</div>
        @endif
    </div> -->

    <!-- Activity Timeline -->
    <div class="section">
        <div class="section-title">Activity Timeline</div>
        @if($formData['timeline']->count() > 0)
            <table class="timeline-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Action</th>
                        <th>From User</th>
                        <th>To User</th>
                        <th>Performed By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formData['timeline']->take(5) as $entry)
                        <tr>
                            <td>{{ $entry->performed_at->format('M d, Y g:i A') }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $entry->action)) }}">
                                    {{ ucfirst($entry->action) }}
                                </span>
                            </td>
                            <td>{{ $entry->fromUser->first_name ?? 'N/A' }} {{ $entry->fromUser->last_name ?? '' }}</td>
                            <td>{{ $entry->toUser->first_name ?? 'N/A' }} {{ $entry->toUser->last_name ?? '' }}</td>
                            <td>{{ $entry->performedBy->first_name ?? 'System' }} {{ $entry->performedBy->last_name ?? '' }}</td>
                            <td>{{ $entry->notes ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($formData['timeline']->count() > 5)
                <div style="font-size: 12px; color: #666; margin-top: 5px; text-align: center;">
                    Showing 5 of {{ $formData['timeline']->count() }} timeline entries
                </div>
            @endif
        @else
            <div class="alert-info">No timeline entries found for this asset.</div>
        @endif
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="section-title">Signatures & Approvals</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
            <div>
                <div class="info-label">Asset Custodian</div>
                <div class="signature-line"></div>
                <div style="text-align: center; font-size: 10px; margin-top: 3px;">
                    {{ $formData['asset']->assignedUser->first_name ?? 'N/A' }} {{ $formData['asset']->assignedUser->last_name ?? '' }}
                </div>
                <div style="text-align: center; font-size: 8px; color: #666;">
                    Signature & Date
                </div>
            </div>
            <div>
                <div class="info-label">IT Administrator</div>
                <div class="signature-line"></div>
                <div style="text-align: center; font-size: 10px; margin-top: 3px;">
                    {{ $formData['generated_by']->first_name }} {{ $formData['generated_by']->last_name }}
                </div>
                <div style="text-align: center; font-size: 8px; color: #666;">
                    Signature & Date
                </div>
            </div>
            <div>
                <div class="info-label">ICT Director</div>
                <div class="signature-line"></div>
                <div style="text-align: center; font-size: 10px; margin-top: 3px;">
                    Rex David De Chavez                         <!-- Change to the actual ICT Director -->  
                </div>
                <div style="text-align: center; font-size: 8px; color: #666;">
                    Signature & Date
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>ICT Asset Management System</strong></p>
        <p>This form was generated on {{ $formData['generated_at']->format('F d, Y \a\t g:i A T') }}</p>
        <p>Form ID: {{ $formData['form_id'] }} | Generated by: {{ $formData['generated_by']->first_name }} {{ $formData['generated_by']->last_name }}</p>
        <p class="no-print">This is a system-generated document. For questions, contact the IT Department.</p>
    </div>

    <!-- Print Button (Hidden in print) -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-1"></i>
            Print Form
        </button>
        <button onclick="window.close()" class="btn btn-secondary ms-2">
            <i class="fas fa-times me-1"></i>
            Close
        </button>
    </div>
</body>
</html>
 