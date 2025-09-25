@extends('layouts.app')

@section('title', 'Accountability Forms')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        Accountability Forms
                    </h2>
                    <p class="text-muted mb-0">Generate and print asset accountability forms with complete audit trails</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#bulkFormModal">
                        <i class="fas fa-layer-group me-1"></i>
                        Bulk Generate
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Print All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filters & Search
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('accountability.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Asset tag, name, serial, or user...">
                </div>
                <div class="col-md-2">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Assigned" {{ request('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="Pending Confirmation" {{ request('status') == 'Pending Confirmation' ? 'selected' : '' }}>Pending Confirmation</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="print_status" class="form-label">Print Status</label>
                    <select class="form-select" id="print_status" name="print_status">
                        <option value="">All</option>
                        <option value="printed" {{ request('print_status') == 'printed' ? 'selected' : '' }}>Printed</option>
                        <option value="not_printed" {{ request('print_status') == 'not_printed' ? 'selected' : '' }}>Not Printed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('accountability.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assets Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Assets ({{ $assets->total() }})</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('accountability.generate', $asset->id) }}" 
                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye me-1"></i>
                                                View
                                            </a>
                                            <a href="{{ route('accountability.print', $asset->id) }}" 
                                               class="btn btn-sm {{ ($asset->currentAssignment && $asset->currentAssignment->accountability_printed) ? 'btn-success' : 'btn-primary' }}" 
                                               target="_blank"
                                               title="{{ ($asset->currentAssignment && $asset->currentAssignment->accountability_printed) ? 'Already printed - click to reprint' : 'Print accountability form' }}">
                                                <i class="fas fa-print me-1"></i>
                                                {{ ($asset->currentAssignment && $asset->currentAssignment->accountability_printed) ? 'Reprint' : 'Print' }}
                                            </a>
                                            @if(!$asset->currentAssignment || !$asset->currentAssignment->accountability_printed)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success mark-printed-btn" 
                                                    data-asset-id="{{ $asset->id }}"
                                                    data-asset-tag="{{ $asset->asset_tag }}"
                                                    title="Mark as printed (for manual printing)">
                                                <i class="fas fa-check me-1"></i>
                                                Mark Printed
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    
    // Select all checkbox change
    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
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
