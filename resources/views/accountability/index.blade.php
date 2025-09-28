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
                                <button type="button" class="btn btn-light btn-sm" onclick="window.print()" style="color: #667eea;">
                                    <i class="fas fa-print me-1"></i>
                                    Print All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
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
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $assets->links() }}
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
    
    function showAlert(type, message) {
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
    }
});
</script>
@endpush
