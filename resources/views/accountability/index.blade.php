@extends('layouts.app')

@section('title', 'Accountability Forms')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Accountability Forms</h5>
                            <small class="text-white-50">{{ $assets->total() }} total assigned assets</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#bulkFormModal" style="color: #667eea;">
                                    <i class="fas fa-layer-group me-1"></i>
                                    Bulk Generate
                                </button>
                                <a href="{{ route('accountability.bulk-upload') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-upload me-1"></i>
                                    Bulk Upload
                                </a>
                                <a href="{{ route('accountability.bulk-email') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    Bulk Email
                                </a>
                                <button type="button" class="btn btn-light btn-sm" onclick="window.print()" style="color: #667eea;">
                                    <i class="fas fa-print me-1"></i>
                                    Print All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email Status Counts -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white" style="border: none;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="mb-1">{{ $assets->where('currentAssignment.signed_form_email_sent', true)->count() }}</h6>
                                                <small>Assets with Emails</small>
                                                <br>
                                                <small class="opacity-75">
                                                    {{ $assets->sum('currentAssignment.signed_form_email_count') }} total emails
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white" style="border: none;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="mb-1">{{ $assets->where('currentAssignment.signed_form_path', '!=', null)->where('currentAssignment.signed_form_email_sent', false)->count() }}</h6>
                                                <small>Pending Email</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white" style="border: none;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="mb-1">{{ $assets->where('currentAssignment.signed_form_path', '!=', null)->count() }}</h6>
                                                <small>Signed Forms</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-secondary text-white" style="border: none;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="mb-1">{{ $assets->where('currentAssignment.signed_form_path', null)->count() }}</h6>
                                                <small>No Signed Form</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('accountability.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search assigned assets..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Assets Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Assets ({{ $assets->total() }})</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllHeader">
                    <label class="form-check-label" for="selectAllHeader">
                        Select All
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>Asset Details</th>
                                <th>Assigned To</th>
                                <th>Assignment Date</th>
                                <th>Status</th>
                                <th>Print Status</th>
                                <th>Email Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input asset-checkbox" 
                                               value="{{ $asset->id }}" name="asset_ids[]">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($asset->category)
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-{{ $asset->category->name == 'Computer Hardware' ? 'desktop' : ($asset->category->name == 'Monitors' ? 'tv' : ($asset->category->name == 'Printers' ? 'print' : 'keyboard')) }} text-primary"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $asset->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $asset->asset_tag }} • {{ $asset->serial_number }}
                                                </small>
                                                @if($asset->category)
                                                    <br><span class="badge bg-secondary">{{ $asset->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($asset->assignedUser)
                                            <div>
                                                <strong>{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</strong>
                                                <br><small class="text-muted">{{ $asset->assignedUser->email }}</small>
                                                @if($asset->assignedUser->department)
                                                    <br><span class="badge bg-info">{{ $asset->assignedUser->department->name }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->assigned_date)
                                            <div>
                                                <strong>{{ $asset->assigned_date->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ $asset->assigned_date->format('g:i A') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'Active' => 'success',
                                                'Assigned' => 'primary',
                                                'Pending Confirmation' => 'warning',
                                                'Available' => 'secondary',
                                                'Inactive' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($asset->currentAssignment && $asset->currentAssignment->accountability_printed)
                                            <div>
                                                <span class="badge bg-success mb-1">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Printed
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $asset->currentAssignment->accountability_printed_at->format('M d, Y g:i A') }}
                                                </small>
                                                @if($asset->currentAssignment->accountabilityPrintedBy)
                                                    <br>
                                                    <small class="text-muted">
                                                        by {{ $asset->currentAssignment->accountabilityPrintedBy->first_name }} {{ $asset->currentAssignment->accountabilityPrintedBy->last_name }}
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Not Printed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->currentAssignment && $asset->currentAssignment->signed_form_path)
                                            @if($asset->currentAssignment->signed_form_email_sent)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Email Sent
                                                </span>
                                                @if($asset->currentAssignment->signed_form_email_count > 0)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-paper-plane me-1"></i>
                                                        {{ $asset->currentAssignment->signed_form_email_count }} time(s)
                                                    </small>
                                                @endif
                                                @if($asset->currentAssignment->signed_form_email_sent_at)
                                                    <br><small class="text-muted">{{ $asset->currentAssignment->signed_form_email_sent_at->format('M d, Y H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    Pending Email
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus me-1"></i>
                                                No Signed Form
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('accountability.generate', $asset->id) }}" 
                                               class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" target="_blank" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accountability.print', $asset->id) }}" 
                                               class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-print" 
                                               target="_blank"
                                               title="{{ ($asset->currentAssignment && $asset->currentAssignment->accountability_printed) ? 'Already printed - click to reprint' : 'Print accountability form' }}">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if(!$asset->currentAssignment || !$asset->currentAssignment->accountability_printed)
                                            <button type="button" 
                                                    class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-mark" 
                                                    data-asset-id="{{ $asset->id }}"
                                                    data-asset-tag="{{ $asset->asset_tag }}"
                                                    title="Mark as printed (for manual printing)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                            
                                            @if($asset->currentAssignment && $asset->currentAssignment->accountability_printed)
                                                @if($asset->currentAssignment->signed_form_path)
                                                    {{-- Show all buttons when signed form is uploaded --}}
                                                    <button type="button" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-upload" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#uploadSignedFormModal"
                                                            data-asset-id="{{ $asset->id }}"
                                                            data-asset-tag="{{ $asset->asset_tag }}"
                                                            title="Upload New Signed Form">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-preview" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#previewSignedFormModal"
                                                            data-asset-id="{{ $asset->id }}"
                                                            data-asset-tag="{{ $asset->asset_tag }}"
                                                            data-file-path="{{ $asset->currentAssignment->signed_form_path }}"
                                                            title="Preview & Download Signed Form">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-email" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#sendEmailModal"
                                                            data-asset-id="{{ $asset->id }}"
                                                            data-asset-tag="{{ $asset->asset_tag }}"
                                                            data-signed-form="{{ $asset->currentAssignment->signed_form_path ? 'true' : 'false' }}"
                                                            data-assigned-user-email="{{ $asset->currentAssignment->user->email }}"
                                                            title="Send Email with Signed Form">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                @else
                                                    {{-- Show only upload button when no signed form --}}
                                                    <button type="button" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-upload" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#uploadSignedFormModal"
                                                            data-asset-id="{{ $asset->id }}"
                                                            data-asset-tag="{{ $asset->asset_tag }}"
                                                            title="Upload Signed Form">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-muted">
                                Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }} of {{ $assets->total() }} results
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                {{ $assets->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No assigned assets found</h5>
                    <p class="text-muted">Try adjusting your search criteria or assign some assets first.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Form Modal -->
<div class="modal fade" id="bulkFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2"></i>
                    Generate Bulk Accountability Forms
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accountability.generate-bulk') }}" method="POST" id="bulkFormForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Select assets to generate bulk accountability forms:</p>
                    <div id="selectedAssetsList" class="mb-3">
                        <p class="text-muted">No assets selected</p>
                    </div>
                    <div id="hiddenInputs"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="generateBulkBtn" disabled>
                        <i class="fas fa-file-contract me-1"></i>
                        Generate Forms
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Action Button Styles */
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn-view {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    border-color: #4f46e5;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #3730a3 0%, #6d28d9 100%);
    color: white;
}

.action-btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-color: #f59e0b;
}

.action-btn-edit:hover {
    background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
    color: white;
}

.action-btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.action-btn-print {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-color: #10b981;
}

.action-btn-print:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
}

.action-btn-reminder {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border-color: #8b5cf6;
}

.action-btn-reminder:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.action-btn-mark {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
    border-color: #06b6d4;
}

.action-btn-mark:hover {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
}

.action-btn-upload {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border-color: #8b5cf6;
}

.action-btn-upload:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.action-btn-download {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    border-color: #059669;
}

.action-btn-download:hover {
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    color: white;
}

.action-btn-email {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    border-color: #dc2626;
}

        .action-btn-email:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            color: white;
        }
        .action-btn-preview {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border-color: #dc2626;
        }
        .action-btn-preview:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    color: white;
}

/* Loading state */
.action-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Pagination styling */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #667eea;
    border: 1px solid #e9ecef;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #764ba2;
    background-color: #f8f9fa;
    border-color: #667eea;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #e9ecef;
}

.pagination .page-item:first-child .page-link {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.pagination .page-item:last-child .page-link {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .card-footer .row {
        text-align: center;
    }
    
    .card-footer .col-md-6:first-child {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    
    // Select all checkbox change (table header)
    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        selectAllHeader.checked = this.checked;
        updateBulkForm();
    });
    
    // Select all checkbox change (card header)
    selectAllHeader.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        selectAllCheckbox.checked = this.checked;
        updateBulkForm();
    });
    
    // Individual checkbox change
    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkForm();
        });
    });
    
    function updateSelectAllState() {
        const allChecked = Array.from(assetCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(assetCheckboxes).some(cb => cb.checked);
        
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
        selectAllHeader.checked = allChecked;
        selectAllHeader.indeterminate = someChecked && !allChecked;
    }
    
    function updateBulkForm() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        const selectedAssetIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Clear existing hidden inputs
        const hiddenInputsContainer = document.getElementById('hiddenInputs');
        hiddenInputsContainer.innerHTML = '';
        
        // Create hidden inputs for each selected asset
        selectedAssetIds.forEach(assetId => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'asset_ids[]';
            hiddenInput.value = assetId;
            hiddenInputsContainer.appendChild(hiddenInput);
        });
        
        const generateBtn = document.getElementById('generateBulkBtn');
        const selectedAssetsList = document.getElementById('selectedAssetsList');
        
        if (selectedAssetIds.length > 0) {
            generateBtn.disabled = false;
            selectedAssetsList.innerHTML = `
                <div class="alert alert-info">
                    <strong>${selectedAssetIds.length} assets selected</strong>
                    <br><small>Click "Generate Forms" to create bulk accountability forms</small>
                </div>
            `;
        } else {
            generateBtn.disabled = true;
            selectedAssetsList.innerHTML = '<p class="text-muted">No assets selected</p>';
        }
    }
    
    // Initialize state
    updateSelectAllState();
    updateBulkForm();
    
    // Handle "Mark as Printed" button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mark-printed-btn')) {
            const button = e.target.closest('.mark-printed-btn');
            const assetId = button.dataset.assetId;
            const assetTag = button.dataset.assetTag;
            
            if (confirm(`Mark accountability form for asset ${assetTag} as printed?`)) {
                // Show loading state
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
                button.disabled = true;
                
                // Make AJAX request
                fetch(`/accountability/mark-printed/${assetId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showAlert('success', data.message);
                        
                        // Reload the page to update the UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('error', data.message);
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred. Please try again.');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }
    });
    
    // Make showAlert function globally available
    window.showAlert = function(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at the top of the page
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    };
});
</script>

<!-- Upload Signed Form Modal -->
<div class="modal fade" id="uploadSignedFormModal" tabindex="-1" aria-labelledby="uploadSignedFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSignedFormModalLabel">Upload Signed Accountability Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadSignedFormForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="signed_form" class="form-label">Signed Form File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="signed_form" name="signed_form" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</div>
                    </div>
                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Email Subject (for future emails)</label>
                        <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="Enter custom email subject...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Signed Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Email with Signed Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendEmailForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipients" class="form-label">Recipients <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="recipients" name="recipients" placeholder="Enter email addresses separated by commas" required>
                        <div class="form-text">Example: user1@example.com, user2@example.com</div>
                    </div>
                    <div class="mb-3">
                        <label for="email_description" class="form-label">Email Description</label>
                        <textarea class="form-control" id="email_description" name="description" rows="3" placeholder="Enter additional information for the email..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="email_subject_send" class="form-label">Email Subject</label>
                        <input type="text" class="form-control" id="email_subject_send" name="subject" placeholder="Enter email subject...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Signed Form Modal -->
<div class="modal fade" id="previewSignedFormModal" tabindex="-1" aria-labelledby="previewSignedFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewSignedFormModalLabel">Preview Signed Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="spinner-border text-primary" role="status" id="previewSpinner">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p id="previewLoadingText">Loading preview...</p>
                </div>
                <div id="previewContent" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 id="previewAssetTag" class="mb-0"></h6>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" id="openPreview">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Open Preview
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="downloadFromPreview">
                                <i class="fas fa-download me-1"></i>
                                Download
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                        </div>
                        <h5>PDF Document Ready</h5>
                        <p class="text-muted">Click "Open Preview" to view the signed form in a new tab, or "Download" to save it to your device.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> The PDF will open in a new browser tab for better viewing experience.
                        </div>
                    </div>
                </div>
                <div id="previewError" style="display: none;" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Unable to preview file</h5>
                    <p>This file format cannot be previewed in the browser. Please download the file to view it.</p>
                    <button type="button" class="btn btn-primary" id="downloadFromError">
                        <i class="fas fa-download me-1"></i>
                        Download File
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload Signed Form Modal
    const uploadModal = document.getElementById('uploadSignedFormModal');
    const uploadForm = document.getElementById('uploadSignedFormForm');
    
    uploadModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const assetTag = button.getAttribute('data-asset-tag');
        
        uploadForm.action = `/accountability/upload-signed-form/${assetId}`;
        document.getElementById('uploadSignedFormModalLabel').textContent = `Upload Signed Form - ${assetTag}`;
    });
    
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                // Success - show success message and reload
                showAlert('success', 'Signed form uploaded successfully!');
                // Use Bootstrap 5 modal hiding syntax
                const modalInstance = bootstrap.Modal.getInstance(uploadModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', 'Failed to upload signed form. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
    
    // Send Email Modal
    const emailModal = document.getElementById('sendEmailModal');
    const emailForm = document.getElementById('sendEmailForm');
    
    
    // Preview Modal
    const previewModal = document.getElementById('previewSignedFormModal');
    const previewSpinner = document.getElementById('previewSpinner');
    const previewLoadingText = document.getElementById('previewLoadingText');
    const previewContent = document.getElementById('previewContent');
    const previewError = document.getElementById('previewError');
    const previewAssetTag = document.getElementById('previewAssetTag');
    const openPreview = document.getElementById('openPreview');
    const downloadFromPreview = document.getElementById('downloadFromPreview');
    const downloadFromError = document.getElementById('downloadFromError');
    
    let currentAssetId = null;
    let currentPreviewUrl = null;
    
    // Preview Modal Event Handlers
    previewModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const assetTag = button.getAttribute('data-asset-tag');
        const filePath = button.getAttribute('data-file-path');
        
        currentAssetId = assetId;
        currentPreviewUrl = `/accountability/preview-signed-form/${assetId}`;
        previewAssetTag.textContent = assetTag;
        
        // Reset modal state
        previewSpinner.style.display = 'block';
        previewLoadingText.style.display = 'block';
        previewContent.style.display = 'none';
        previewError.style.display = 'none';
        
        // Check if file is PDF
        if (filePath && filePath.toLowerCase().endsWith('.pdf')) {
            // Simulate loading delay for better UX
            setTimeout(() => {
                previewSpinner.style.display = 'none';
                previewLoadingText.style.display = 'none';
                previewContent.style.display = 'block';
            }, 500);
        } else {
            // Show error for non-PDF files
            previewSpinner.style.display = 'none';
            previewLoadingText.style.display = 'none';
            previewError.style.display = 'block';
        }
    });
    
    // Open preview in new tab
    openPreview.addEventListener('click', function() {
        if (currentPreviewUrl) {
            window.open(currentPreviewUrl, '_blank');
        }
    });
    
    // Download from preview
    downloadFromPreview.addEventListener('click', function() {
        if (currentAssetId) {
            window.open(`/accountability/download-signed-form/${currentAssetId}`, '_blank');
        }
    });
    
    // Download from error
    downloadFromError.addEventListener('click', function() {
        if (currentAssetId) {
            window.open(`/accountability/download-signed-form/${currentAssetId}`, '_blank');
        }
    });
    
    emailModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const assetTag = button.getAttribute('data-asset-tag');
        const hasSignedForm = button.getAttribute('data-signed-form') === 'true';
        
        emailForm.action = `/accountability/send-signed-form-email/${assetId}`;
        document.getElementById('sendEmailModalLabel').textContent = `Send Email - ${assetTag}`;
        
        // Pre-populate with professional content if signed form exists
        if (hasSignedForm) {
            const professionalSubject = `Asset Accountability Form - ${assetTag} - Confirmed & Signed`;
            const professionalDescription = `Dear Employee,\n\n` +
                `This is to confirm that the signed accountability form for asset ${assetTag} has been successfully processed and is now available for your records.\n\n` +
                `Asset Details:\n` +
                `• Asset Tag: ${assetTag}\n` +
                `• Assigned Date: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}\n\n` +
                `The signed form is attached to this email. Please keep this document in a secure location for your records.\n\n` +
                `If you have any questions about this asset assignment, please contact your IT department.\n\n` +
                `Best regards,\n` +
                `IT Asset Management Team`;
            
            // Get the assigned user's email from the button data
            const assignedUserEmail = button.getAttribute('data-assigned-user-email');
            
            document.getElementById('email_subject_send').value = professionalSubject;
            document.getElementById('email_description').value = professionalDescription;
            
            // Pre-populate recipient if available
            if (assignedUserEmail) {
                document.getElementById('recipients').value = assignedUserEmail;
            }
        } else {
            // Clear fields if no signed form
            document.getElementById('email_subject_send').value = '';
            document.getElementById('email_description').value = '';
            document.getElementById('recipients').value = '';
        }
    });
    
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitButton.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            if (html.includes('Signed form email sent successfully')) {
                showAlert('success', 'Email sent successfully!');
                // Use Bootstrap 5 modal hiding syntax
                const modalInstance = bootstrap.Modal.getInstance(emailModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', 'Failed to send email. Please try again.');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred: ' + error.message);
        })
        .finally(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
    }
});
</script>
@endpush
