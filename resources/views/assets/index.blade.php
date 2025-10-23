@extends('layouts.app')

@section('title', 'Assets')
@section('page-title', auth()->user()->hasRole('User') && !auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']) ? 'My Assets' : 'Assets Management')

@if(session('asset_updated'))
    <div class="alert alert-info">
        Debug: Asset update session detected. Success message: {{ session('success') }}
    </div>
@endif

@section('page-actions')
    <div class="d-flex gap-2">
        <!-- Buttons moved to header for better alignment -->
    </div>
<script>
// Function to set label dimensions for bulk print modal
function setBulkLabelDimensions() {
    const preset = document.getElementById('bulk_label_preset');
    const selectedOption = preset.options[preset.selectedIndex];
    const widthInput = document.getElementById('bulk_label_width');
    const heightInput = document.getElementById('bulk_label_height');
    
    if (selectedOption.value !== 'custom') {
        const width = selectedOption.getAttribute('data-width');
        const height = selectedOption.getAttribute('data-height');
        
        if (width && height) {
            widthInput.value = width;
            heightInput.value = height;
            widthInput.readOnly = true;
            heightInput.readOnly = true;
        }
    } else {
        widthInput.readOnly = false;
        heightInput.readOnly = false;
    }
}

// Function to set label dimensions for print all modal
function setLabelDimensions() {
    const preset = document.getElementById('label_preset');
    const selectedOption = preset.options[preset.selectedIndex];
    const widthInput = document.getElementById('label_width');
    const heightInput = document.getElementById('label_height');
    
    if (selectedOption.value !== 'custom') {
        const width = selectedOption.getAttribute('data-width');
        const height = selectedOption.getAttribute('data-height');
        
        if (width && height) {
            widthInput.value = width;
            heightInput.value = height;
            widthInput.readOnly = true;
            heightInput.readOnly = true;
        }
    } else {
        widthInput.readOnly = false;
        heightInput.readOnly = false;
    }
}

// Initialize preset dimensions on page load
document.addEventListener('DOMContentLoaded', function() {
    setBulkLabelDimensions();
    setLabelDimensions();
});
</script>

@endsection

@section('content')
<script>
// Global functions for asset maintenance and disposal
let currentAssetId = null;

function sendToMaintenance(assetId, assetTag, assetName, assignedUser, status, movement) {
    console.log('sendToMaintenance called with:', assetId, assetTag, assetName);
    currentAssetId = assetId;
    
    // Check if modal elements exist
    const maintenanceDetails = document.getElementById('maintenanceAssetDetails');
    const maintenanceModal = document.getElementById('maintenanceModal');
    
    if (!maintenanceDetails) {
        console.error('maintenanceAssetDetails element not found');
        alert('Modal elements not found. Please refresh the page.');
        return;
    }
    
    if (!maintenanceModal) {
        console.error('maintenanceModal element not found');
        alert('Modal elements not found. Please refresh the page.');
        return;
    }
    
    // Populate asset details in modal
    maintenanceDetails.innerHTML = `
        <div class="row">
            <div class="col-6"><strong>Asset Tag:</strong> ${assetTag}</div>
            <div class="col-6"><strong>Name:</strong> ${assetName}</div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Assigned User:</strong> ${assignedUser || 'None'}</div>
            <div class="col-6"><strong>Current Status:</strong> <span class="badge bg-primary">${status}</span></div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Current Movement:</strong> <span class="badge bg-info">${movement}</span></div>
        </div>
    `;
    
    // Show the modal
    try {
        const modal = new bootstrap.Modal(maintenanceModal);
        modal.show();
        console.log('Maintenance modal shown successfully');
    } catch (error) {
        console.error('Error showing maintenance modal:', error);
        alert('Error opening modal: ' + error.message);
    }
}

function sendToDisposal(assetId, assetTag, assetName, assignedUser, status, movement) {
    console.log('sendToDisposal called with:', assetId, assetTag, assetName);
    currentAssetId = assetId;
    
    // Check if modal elements exist
    const disposalDetails = document.getElementById('disposalAssetDetails');
    const disposalModal = document.getElementById('disposalModal');
    
    if (!disposalDetails) {
        console.error('disposalAssetDetails element not found');
        alert('Modal elements not found. Please refresh the page.');
        return;
    }
    
    if (!disposalModal) {
        console.error('disposalModal element not found');
        alert('Modal elements not found. Please refresh the page.');
        return;
    }
    
    // Populate asset details in modal
    disposalDetails.innerHTML = `
        <div class="row">
            <div class="col-6"><strong>Asset Tag:</strong> ${assetTag}</div>
            <div class="col-6"><strong>Name:</strong> ${assetName}</div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Assigned User:</strong> ${assignedUser || 'None'}</div>
            <div class="col-6"><strong>Current Status:</strong> <span class="badge bg-primary">${status}</span></div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Current Movement:</strong> <span class="badge bg-info">${movement}</span></div>
        </div>
    `;
    
    // Show the modal
    try {
        const modal = new bootstrap.Modal(disposalModal);
        modal.show();
        console.log('Disposal modal shown successfully');
    } catch (error) {
        console.error('Error showing disposal modal:', error);
        alert('Error opening modal: ' + error.message);
    }
}

// Make functions globally accessible
window.sendToMaintenance = sendToMaintenance;
window.sendToDisposal = sendToDisposal;

console.log('Functions defined:', typeof sendToMaintenance, typeof sendToDisposal);

// Handle maintenance and disposal confirmations
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    const confirmMaintenanceBtn = document.getElementById('confirmMaintenance');
    const confirmDisposalBtn = document.getElementById('confirmDisposal');
    
    console.log('confirmMaintenanceBtn found:', !!confirmMaintenanceBtn);
    console.log('confirmDisposalBtn found:', !!confirmDisposalBtn);
    
    if (confirmMaintenanceBtn) {
        confirmMaintenanceBtn.addEventListener('click', function() {
            console.log('Maintenance confirmation clicked, currentAssetId:', currentAssetId);
            if (currentAssetId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', csrfToken);
                
                fetch(`/assets/${currentAssetId}/maintenance`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Maintenance response:', data);
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('maintenanceModal'));
                        modal.hide();
                        // Reload page
                        location.reload();
                    } else {
                        alert('Error sending to maintenance: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Maintenance error:', error);
                    alert('Error sending to maintenance');
                });
            }
        });
    }

    if (confirmDisposalBtn) {
        confirmDisposalBtn.addEventListener('click', function() {
            console.log('Disposal confirmation clicked, currentAssetId:', currentAssetId);
            if (currentAssetId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', csrfToken);
                
                fetch(`/assets/${currentAssetId}/disposal`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Disposal response:', data);
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('disposalModal'));
                        modal.hide();
                        // Reload page
                        location.reload();
                    } else {
                        alert('Error sending to disposal: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Disposal error:', error);
                    alert('Error sending to disposal');
                });
            }
        });
    }
});
</script>

<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 text-white">All Assets</h5>
                <small class="text-white-50">{{ $assets->total() }} total assets</small>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                        <a href="{{ route('assets.print-employee-assets') }}" class="btn btn-light btn-sm" target="_blank" style="color: #667eea;">
                            <i class="fas fa-print me-1"></i>Employee Assets Report
                        </a>
                        <a href="{{ route('assets.export.comprehensive', request()->query()) }}" class="btn btn-light btn-sm" style="color: #667eea;">
                            <i class="fas fa-file-excel me-1"></i>Export Assets with Users & Vendors
                        </a>
                        <a href="{{ route('assets.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                            <i class="fas fa-plus me-1"></i>Add New Asset
                        </a>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="mt-3">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" action="{{ route('assets.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search assets by name, tag, serial, model, location..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                            <button class="btn btn-primary" type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn {{ request()->hasAny(['category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max']) ? 'btn-primary' : 'btn-outline-primary' }}" id="toggleAdvancedSearch" style="border-radius: 0 6px 6px 0; {{ !request()->hasAny(['category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max']) ? 'color: white; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;' : '' }}">
                                <i class="fas fa-filter"></i> {{ request()->hasAny(['category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max']) ? 'Hide Filters' : 'Advanced' }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    @php
                        $activeFilters = 0;
                        $filterParams = ['category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max'];
                        foreach($filterParams as $param) {
                            if(request()->filled($param)) $activeFilters++;
                        }
                    @endphp
                    @if($activeFilters > 0 || request()->filled('search'))
                        <span class="badge bg-primary me-2" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                            <i class="fas fa-filter me-1"></i>{{ $activeFilters + (request()->filled('search') ? 1 : 0) }} Active Filter{{ ($activeFilters + (request()->filled('search') ? 1 : 0)) > 1 ? 's' : '' }}
                        </span>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times"></i> Clear All
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Advanced Search Panel -->
            <div id="advancedSearchPanel" class="mt-3" style="display: {{ request()->hasAny(['category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max']) ? 'block' : 'none' }};">
                <div class="card" style="border: 2px solid #667eea; border-radius: 12px;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px 10px 0 0;">
                        <h6 class="mb-0 text-white"><i class="fas fa-sliders-h me-2"></i>Advanced Search Filters</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('assets.index') }}" id="advancedSearchForm">
                            <!-- Keep simple search value if it exists -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            
                            <div class="row g-3">
                                <!-- Category Filter -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Status Filter -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Movement Filter -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Movement</label>
                                    <select name="movement" class="form-select">
                                        <option value="">All Movements</option>
                                        @foreach($movements as $movement)
                                            <option value="{{ $movement }}" {{ request('movement') == $movement ? 'selected' : '' }}>
                                                {{ $movement }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Vendor Filter -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Vendor</label>
                                    <select name="vendor_id" class="form-select">
                                        <option value="">All Vendors</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Assigned To Filter -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Assigned To</label>
                                    <select name="assigned_to" class="form-select">
                                        <option value="">All Users</option>
                                        <option value="assigned" {{ request('assigned_to') == 'assigned' ? 'selected' : '' }}>Any Assigned User</option>
                                        <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                        <optgroup label="Specific Users">
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->employee_id }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <!-- Location Filter -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Location</label>
                                    <input type="text" name="location" class="form-control" list="locationList" placeholder="Enter or select location" value="{{ request('location') }}">
                                    <datalist id="locationList">
                                        @foreach($locations as $location)
                                            <option value="{{ $location }}">
                                        @endforeach
                                    </datalist>
                                </div>
                                
                                <!-- Sort By -->
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Sort By</label>
                                    <select name="sort_by" class="form-select">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                        <option value="asset_tag" {{ request('sort_by') == 'asset_tag' ? 'selected' : '' }}>Asset Tag</option>
                                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                                        <option value="cost" {{ request('sort_by') == 'cost' ? 'selected' : '' }}>Cost</option>
                                        <option value="purchase_date" {{ request('sort_by') == 'purchase_date' ? 'selected' : '' }}>Purchase Date</option>
                                    </select>
                                </div>
                                
                                <!-- Sort Order -->
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Order</label>
                                    <select name="sort_order" class="form-select">
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                        <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Date and Cost Range Filters -->
                            <div class="row g-3 mt-2">
                                <div class="col-md-12">
                                    <h6 class="text-muted mb-2"><i class="fas fa-calendar-alt me-2"></i>Date & Cost Ranges</h6>
                                </div>
                                
                                <!-- Purchase Date Range -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Purchase From</label>
                                    <input type="date" name="purchase_date_from" class="form-control" value="{{ request('purchase_date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Purchase To</label>
                                    <input type="date" name="purchase_date_to" class="form-control" value="{{ request('purchase_date_to') }}">
                                </div>
                                
                                <!-- Cost Range -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Min Cost (₱)</label>
                                    <input type="number" name="cost_min" class="form-control" placeholder="0.00" step="0.01" value="{{ request('cost_min') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Max Cost (₱)</label>
                                    <input type="number" name="cost_max" class="form-control" placeholder="999999.99" step="0.01" value="{{ request('cost_max') }}">
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('advancedSearchForm').reset(); window.location.href='{{ route('assets.index') }}';">
                                        <i class="fas fa-redo me-1"></i>Reset All
                                    </button>
                                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <i class="fas fa-search me-1"></i>Apply Filters
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <div class="card-body">
        <!-- Active Filters Summary -->
        @if(request()->hasAny(['search', 'category_id', 'status', 'movement', 'vendor_id', 'assigned_to', 'location', 'purchase_date_from', 'purchase_date_to', 'cost_min', 'cost_max']))
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); border: 2px solid #3b82f6; border-radius: 12px;">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>Active Filters</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @if(request('search'))
                        <span class="badge bg-primary" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-search me-1"></i>Search: "{{ request('search') }}"
                        </span>
                        @endif
                        
                        @if(request('category_id'))
                        <span class="badge bg-info" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-folder me-1"></i>Category: {{ $categories->find(request('category_id'))->name ?? 'Unknown' }}
                        </span>
                        @endif
                        
                        @if(request('status'))
                        <span class="badge bg-success" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-toggle-on me-1"></i>Status: {{ request('status') }}
                        </span>
                        @endif
                        
                        @if(request('movement'))
                        <span class="badge bg-warning text-dark" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-arrows-alt me-1"></i>Movement: {{ request('movement') }}
                        </span>
                        @endif
                        
                        @if(request('vendor_id'))
                        <span class="badge bg-secondary" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-store me-1"></i>Vendor: {{ $vendors->find(request('vendor_id'))->name ?? 'Unknown' }}
                        </span>
                        @endif
                        
                        @if(request('assigned_to'))
                        <span class="badge bg-purple" style="padding: 0.5rem 0.75rem; background-color: #8b5cf6;">
                            <i class="fas fa-user me-1"></i>Assigned: 
                            @if(request('assigned_to') == 'assigned')
                                Any Assigned User
                            @elseif(request('assigned_to') == 'unassigned')
                                Unassigned
                            @else
                                {{ $users->find(request('assigned_to'))->first_name ?? '' }} {{ $users->find(request('assigned_to'))->last_name ?? '' }}
                            @endif
                        </span>
                        @endif
                        
                        @if(request('location'))
                        <span class="badge bg-dark" style="padding: 0.5rem 0.75rem;">
                            <i class="fas fa-map-marker-alt me-1"></i>Location: {{ request('location') }}
                        </span>
                        @endif
                        
                        @if(request('purchase_date_from') || request('purchase_date_to'))
                        <span class="badge bg-teal" style="padding: 0.5rem 0.75rem; background-color: #14b8a6;">
                            <i class="fas fa-calendar me-1"></i>Purchase: 
                            {{ request('purchase_date_from') ? date('M d, Y', strtotime(request('purchase_date_from'))) : 'Any' }}
                            to 
                            {{ request('purchase_date_to') ? date('M d, Y', strtotime(request('purchase_date_to'))) : 'Any' }}
                        </span>
                        @endif
                        
                        @if(request('cost_min') || request('cost_max'))
                        <span class="badge bg-orange" style="padding: 0.5rem 0.75rem; background-color: #f97316;">
                            <i class="fas fa-dollar-sign me-1"></i>Cost: 
                            ₱{{ request('cost_min') ? number_format(request('cost_min'), 2) : '0.00' }}
                            to 
                            ₱{{ request('cost_max') ? number_format(request('cost_max'), 2) : '∞' }}
                        </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-danger ms-3" title="Clear all filters">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
        @endif
        
        @if($assets->count() > 0)
            <!-- Export and Print Section - Only for non-User roles -->
            @if(!auth()->user()->hasRole('User') || auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']))
            <div class="mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('assets.export.comprehensive', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i>Export Assets with Users & Vendors ({{ $assets->total() }} assets)
                    </a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#printAllModal">
                        <i class="fas fa-print me-1"></i>Print All Assets ({{ $assets->total() }})
                    </button>
                </div>
                @if($assets->total() < 200)
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i>
                        Filters are active. <a href="{{ route('assets.index') }}" class="text-decoration-none">Clear filters</a> to export/print all assets.
                    </small>
                @endif
            </div>
            @endif

            <!-- Bulk Actions Toolbar - Only for non-User roles -->
            @if(!auth()->user()->hasRole('User') || auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']))
            <div id="bulkActionsToolbar" class="alert alert-info d-none mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="selectedCount">0</span> asset(s) selected
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm me-2" onclick="openBulkAssignModal()">
                            <i class="fas fa-user-plus me-1"></i>Bulk Assign
                        </button>
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="openBulkPrintModal()">
                            <i class="fas fa-print me-1"></i>Print Labels
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                            <i class="fas fa-times me-1"></i>Clear Selection
                        </button>
                    </div>
                </div>
            </div>
            @endif
            <!-- Skeleton Loading State -->
            <div id="skeleton-loading" class="d-none">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="fw-semibold" style="width: 50px;">
                                    <div class="skeleton skeleton-text short" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                </th>
                                <th class="fw-semibold">Asset Tag</th>
                                <th class="fw-semibold">Name</th>
                                <th class="fw-semibold">Category</th>
                                <th class="fw-semibold">Status</th>
                                <th class="fw-semibold">Assigned To</th>
                                <th class="fw-semibold">Location</th>
                                <th class="fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 5; $i++)
                            <tr class="border-bottom">
                                <td>
                                    <div class="skeleton skeleton-text short" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text medium"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text medium"></div>
                                </td>
                                <td>
                                    <div class="skeleton skeleton-text short"></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                        <div class="skeleton skeleton-button" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-responsive" id="main-table">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            @if(!auth()->user()->hasRole('User') || auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']))
                            <th class="fw-semibold" style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            @endif
                            <th class="fw-semibold">Asset Tag</th>
                            <th class="fw-semibold">Name</th>
                            <th class="fw-semibold">Category</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold">Assigned To</th>
                            <th class="fw-semibold">Location</th>
                            <th class="fw-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assets-table-body">
                        @foreach($assets as $asset)
                        <tr class="border-bottom">
                            @if(!auth()->user()->hasRole('User') || auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']))
                            <td>
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                            </td>
                            @endif
                            <td class="fw-bold text-primary">{{ $asset->asset_tag }}</td>
                            <td>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $asset->name }}</div>
                                    @if($asset->model)
                                        <small class="text-muted">{{ $asset->model }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($asset->category)
                                    <span class="badge badge-enhanced bg-info px-2 py-1">{{ $asset->category->name }}</span>
                                @else
                                    <span class="text-muted fst-italic">No Category</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge badge-enhanced bg-{{ 
                                        $asset->status === 'Active' ? 'success' : 
                                        ($asset->status === 'Available' ? 'info' : 
                                        ($asset->status === 'Maintenance' ? 'warning' : 
                                        ($asset->status === 'Pending Confirmation' ? 'primary' : 
                                        ($asset->status === 'For Disposal' ? 'danger' : 'secondary'))))
                                    }} px-2 py-1">
                                        {{ $asset->status }}
                                    </span>
                                    <small class="text-muted">{{ $asset->movement ?? 'New Arrival' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($asset->assigned_to)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $asset->assignedUser->first_name ?? 'Unknown' }} {{ $asset->assignedUser->last_name ?? '' }}</div>
                                            <small class="text-muted">{{ $asset->assignedUser->employee_id ?? '' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->location)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                                        <span class="fw-medium">{{ $asset->location }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">No Location</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @can('view_assets')
                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Asset Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('edit_assets')
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit Asset">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($asset->status !== 'Maintenance' && $asset->status !== 'For Disposal')
                                        @can('maintenance', $asset)
                                        <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-maintenance" title="Send to Maintenance" onclick="sendToMaintenance({{ $asset->id }}, '{{ $asset->asset_tag }}', '{{ $asset->name }}', '{{ $asset->assignedUser ? $asset->assignedUser->first_name . ' ' . $asset->assignedUser->last_name : 'None' }}', '{{ $asset->status }}', '{{ $asset->movement }}')">
                                            <i class="fas fa-wrench"></i>
                                        </button>
                                        @endcan
                                        @can('dispose', $asset)
                                        <button type="button" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-disposal" title="Send to Disposal" onclick="sendToDisposal({{ $asset->id }}, '{{ $asset->asset_tag }}', '{{ $asset->name }}', '{{ $asset->assignedUser ? $asset->assignedUser->first_name . ' ' . $asset->assignedUser->last_name : 'None' }}', '{{ $asset->status }}', '{{ $asset->movement }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endcan
                                    @endif
                                    @can('delete_assets')
                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" title="Delete Asset">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($assets->hasPages())
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
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-muted">No Assets Found</h5>
                <p class="text-muted">Get started by creating your first asset.</p>
                <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Add New Asset
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
    <script>
        // Enhanced Loading States and Progress Indicators
        class AssetLoadingManager {
            constructor() {
                this.init();
            }
            
            init() {
                this.setupSearchLoading();
                this.setupBulkOperations();
                this.setupFormLoading();
            }
            
            setupSearchLoading() {
                // Removed event listener that was interfering with form submission
                // Form now submits normally without JavaScript interference
            }
            
            setupBulkOperations() {
                const bulkPrintBtn = document.querySelector('[data-bs-target="#printAllModal"]');
                if (bulkPrintBtn) {
                    bulkPrintBtn.addEventListener('click', () => {
                        this.showProgressIndicator('Preparing print data...', 0);
                        setTimeout(() => {
                            this.hideProgressIndicator();
                        }, 1000);
                    });
                }
            }
            
        setupFormLoading() {
            // Form loading is now handled by setupSearchLoading()
            // This function is kept for compatibility but does nothing
        }
            
            showSkeletonLoading() {
                const skeleton = document.getElementById('skeleton-loading');
                const mainTable = document.getElementById('main-table');
                if (skeleton && mainTable) {
                    skeleton.classList.remove('d-none');
                    mainTable.style.display = 'none';
                }
            }
            
            hideSkeletonLoading() {
                const skeleton = document.getElementById('skeleton-loading');
                const mainTable = document.getElementById('main-table');
                if (skeleton && mainTable) {
                    skeleton.classList.add('d-none');
                    mainTable.style.display = 'block';
                }
            }
            
            showProgressIndicator(message, progress = 0) {
                if (window.loadingManager) {
                    window.loadingManager.show(message);
                }
                
                // Create progress bar if it doesn't exist
                let progressBar = document.getElementById('bulk-progress-bar');
                if (!progressBar) {
                    progressBar = document.createElement('div');
                    progressBar.id = 'bulk-progress-bar';
                    progressBar.className = 'progress mb-3';
                    progressBar.innerHTML = `
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: ${progress}%">
                            ${progress}%
                        </div>
                    `;
                    document.querySelector('.card-body').insertBefore(progressBar, document.querySelector('.table-responsive'));
                }
                
                const progressBarInner = progressBar.querySelector('.progress-bar');
                progressBarInner.style.width = `${progress}%`;
                progressBarInner.textContent = `${progress}%`;
            }
            
            hideProgressIndicator() {
                if (window.loadingManager) {
                    window.loadingManager.hide();
                }
                
                const progressBar = document.getElementById('bulk-progress-bar');
                if (progressBar) {
                    progressBar.remove();
                }
            }
            
        showButtonLoading(button) {
            // Store original content if not already stored
            if (!button.dataset.originalContent) {
                button.dataset.originalContent = button.innerHTML;
            }
            
            console.log('Showing loading state for button:', button);
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            button.disabled = true;
            button.classList.add('loading');
        }
        
        hideButtonLoading(button) {
            if (button.dataset.originalContent) {
                console.log('Hiding loading state for button:', button);
                button.innerHTML = button.dataset.originalContent;
                button.disabled = false;
                button.classList.remove('loading');
            }
        }
            
            simulateBulkOperation(operation, totalItems) {
                this.showProgressIndicator(`Starting ${operation}...`, 0);
                
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(interval);
                        this.hideProgressIndicator();
                        if (window.toastManager) {
                            window.toastManager.show(`${operation} completed successfully!`, 'success');
                        }
                    }
                    this.showProgressIndicator(`${operation} in progress...`, Math.round(progress));
                }, 200);
            }
        }
        
        $(document).ready(function() {
            // Toggle Advanced Search Panel
            $('#toggleAdvancedSearch').on('click', function() {
                const panel = $('#advancedSearchPanel');
                const button = $(this);
                
                panel.slideToggle(300, function() {
                    if (panel.is(':visible')) {
                        button.html('<i class="fas fa-filter"></i> Hide Filters');
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                        button.css({
                            'color': 'white',
                            'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'border': '2px solid #667eea'
                        });
                    } else {
                        button.html('<i class="fas fa-filter"></i> Advanced');
                        button.removeClass('btn-primary').addClass('btn-outline-primary');
                        button.css({
                            'color': 'white',
                            'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'border': '2px solid #667eea'
                        });
                    }
                });
            });
            
            // Initialize loading manager
            window.assetLoadingManager = new AssetLoadingManager();
            
            // Reset any stuck loading states
            const loadingButtons = document.querySelectorAll('.btn.loading');
            loadingButtons.forEach(button => {
                if (button.dataset.originalContent) {
                    button.innerHTML = button.dataset.originalContent;
                    button.disabled = false;
                    button.classList.remove('loading');
                }
            });
            
            // Add global reset function for debugging
            window.resetAllLoadingStates = function() {
                console.log('Resetting all loading states...');
                const allLoadingButtons = document.querySelectorAll('.btn.loading');
                allLoadingButtons.forEach(button => {
                    if (button.dataset.originalContent) {
                        button.innerHTML = button.dataset.originalContent;
                        button.disabled = false;
                        button.classList.remove('loading');
                        console.log('Reset button:', button);
                    }
                });
                console.log('All loading states reset!');
            };
    
    // Bulk selection functionality
    function initializeBulkSelection() {
        const selectAllCheckbox = $('#selectAll');
        const assetCheckboxes = $('.asset-checkbox');
        const bulkActionsToolbar = $('#bulkActionsToolbar');
        const selectedCountSpan = $('#selectedCount');
        const selectedAssetIdsDiv = $('#selectedAssetIds');
        
        // Handle "Select All" checkbox
        selectAllCheckbox.on('change', function() {
            const isChecked = $(this).is(':checked');
            assetCheckboxes.prop('checked', isChecked);
            updateBulkActions();
        });
        
        // Handle individual asset checkboxes
        assetCheckboxes.on('change', function() {
            updateSelectAllState();
            updateBulkActions();
        });
        
        function updateSelectAllState() {
            const totalCheckboxes = assetCheckboxes.length;
            const checkedCheckboxes = assetCheckboxes.filter(':checked').length;
            
            if (checkedCheckboxes === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
                selectAllCheckbox.prop('checked', false);
            }
        }
        
        function updateBulkActions() {
            const checkedCheckboxes = assetCheckboxes.filter(':checked');
            const selectedCount = checkedCheckboxes.length;
            
            if (selectedCount > 0) {
                bulkActionsToolbar.removeClass('d-none');
                selectedCountSpan.text(selectedCount);
                
                // Update hidden inputs for selected asset IDs
                selectedAssetIdsDiv.empty();
                checkedCheckboxes.each(function() {
                    selectedAssetIdsDiv.append(
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'asset_ids[]',
                            value: $(this).val()
                        })
                    );
                });
            } else {
                bulkActionsToolbar.addClass('d-none');
            }
        }
    }
    
    // Initialize bulk selection after DOM is ready
    initializeBulkSelection();
    
});

// Clear selection function (called from toolbar button)
function clearSelection() {
    $('.asset-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false).prop('indeterminate', false);
    $('#bulkActionsToolbar').addClass('d-none');
}

// Open bulk print modal with selected assets
function openBulkPrintModal() {
    const selectedAssets = $('.asset-checkbox:checked');
    if (selectedAssets.length === 0) {
        alert('Please select at least one asset to print.');
        return;
    }
    
    // Clear previous asset IDs
    $('#bulkPrintAssetIds').empty();
    
    // Add selected asset IDs to the form
    selectedAssets.each(function() {
        $('#bulkPrintAssetIds').append(
            '<input type="hidden" name="asset_ids[]" value="' + $(this).val() + '">'
        );
    });
    
    // Show the modal
    $('#bulkPrintModal').modal('show');
}

// Open bulk assign modal with selected assets
function openBulkAssignModal() {
    const selectedAssets = $('.asset-checkbox:checked');
    if (selectedAssets.length === 0) {
        alert('Please select at least one asset to assign.');
        return;
    }
    
    const selectedCount = selectedAssets.length;
    
    // Update count message
    $('#bulkAssignCount').text(`You will assign ${selectedCount} asset${selectedCount > 1 ? 's' : ''} to a user.`);
    
    // Clear previous asset IDs and list
    $('#bulkAssignAssetIds').empty();
    $('#selectedAssetsList').empty();
    
    // Build asset list display
    let assetListHtml = '<div class="list-group">';
    
    selectedAssets.each(function() {
        const assetId = $(this).val();
        const row = $(this).closest('tr');
        const assetTag = row.find('td:eq(1)').text().trim();
        const assetName = row.find('td:eq(2) .fw-semibold').text().trim();
        const status = row.find('td:eq(4) .badge').first().text().trim();
        
        // Add hidden input
        $('#bulkAssignAssetIds').append(
            '<input type="hidden" name="asset_ids[]" value="' + assetId + '">'
        );
        
        // Add to visual list
        assetListHtml += `
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold text-primary">${assetTag}</div>
                    <small class="text-muted">${assetName}</small>
                </div>
                <span class="badge bg-info rounded-pill">${status}</span>
            </div>
        `;
    });
    
    assetListHtml += '</div>';
    $('#selectedAssetsList').html(assetListHtml);
    
    // Reset form fields
    $('#bulk_assigned_to').val('');
    $('#bulk_assigned_date').val('{{ date("Y-m-d") }}');
    $('#bulk_notes').val('');
    
    // Show the modal
    $('#bulkAssignModal').modal('show');
}
</script>

<!-- Bulk Print Selected Assets Modal -->
<div class="modal fade" id="bulkPrintModal" tabindex="-1" aria-labelledby="bulkPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkPrintModalLabel">Print Selected Assets Labels</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('assets.bulk-print-labels') }}" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="bulkPrintCount">This will print labels for the selected assets.</span>
                    </div>
                    
                    <div id="bulkPrintAssetIds"></div>
                    
                    <!-- Label Size Presets -->
                    <div class="mb-3">
                        <label for="bulk_label_preset" class="form-label">Label Size Preset</label>
                        <select class="form-select" id="bulk_label_preset" onchange="setBulkLabelDimensions()">
                            <option value="custom">Custom Size</option>
                            <option value="small" data-width="72" data-height="144">0.75" × 1.5" (Small items - laptops, tools)</option>
                            <option value="medium1" data-width="192" data-height="72">2" × 0.75" (Equipment with text)</option>
                            <option value="medium2" data-width="192" data-height="96">2" × 1" (Equipment with logos)</option>
                            <option value="default" data-width="320" data-height="200" selected>Default (3.33" × 2.08")</option>
                        </select>
                        <div class="form-text">Choose a preset size or select "Custom Size" for manual input</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="bulk_label_width" class="form-label">Label Width (px)</label>
                            <input type="number" class="form-control" id="bulk_label_width" name="label_width" value="320" min="50" max="800">
                            <div class="form-text">Width in pixels (96 DPI)</div>
                        </div>
                        <div class="col-md-6">
                            <label for="bulk_label_height" class="form-label">Label Height (px)</label>
                            <input type="number" class="form-control" id="bulk_label_height" name="label_height" value="200" min="50" max="400">
                            <div class="form-text">Height in pixels (96 DPI)</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i>Print Selected Labels
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Print All Assets Modal -->
<div class="modal fade" id="printAllModal" tabindex="-1" aria-labelledby="printAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printAllModalLabel">Print All Assets Labels</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('assets.print-all-labels') }}" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will print labels for {{ $assets->total() }} assets matching your current filters.
                        @if($assets->total() < 200)
                            <br><strong>Note:</strong> You have filters active. <a href="{{ route('assets.index') }}" target="_blank" class="text-decoration-none">Clear all filters</a> to print all 200 assets.
                        @endif
                    </div>
                    
                    <!-- Pass current filters -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('movement'))
                        <input type="hidden" name="movement" value="{{ request('movement') }}">
                    @endif
                    @if(request('assignment'))
                        <input type="hidden" name="assignment" value="{{ request('assignment') }}">
                    @endif
                    
                    <!-- Label Size Presets -->
                    <div class="mb-3">
                        <label for="label_preset" class="form-label">Label Size Preset</label>
                        <select class="form-select" id="label_preset" onchange="setLabelDimensions()">
                            <option value="custom">Custom Size</option>
                            <option value="small" data-width="72" data-height="144">0.75" × 1.5" (Small items - laptops, tools)</option>
                            <option value="medium1" data-width="192" data-height="72">2" × 0.75" (Equipment with text)</option>
                            <option value="medium2" data-width="192" data-height="96">2" × 1" (Equipment with logos)</option>
                            <option value="default" data-width="320" data-height="200" selected>Default (3.33" × 2.08")</option>
                        </select>
                        <div class="form-text">Choose a preset size or select "Custom Size" for manual input</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="label_width" class="form-label">Label Width (px)</label>
                            <input type="number" class="form-control" id="label_width" name="label_width" value="320" min="50" max="800">
                            <div class="form-text">Width in pixels (96 DPI)</div>
                        </div>
                        <div class="col-md-6">
                            <label for="label_height" class="form-label">Label Height (px)</label>
                            <input type="number" class="form-control" id="label_height" name="label_height" value="200" min="50" max="400">
                            <div class="form-text">Height in pixels (96 DPI)</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-print me-1"></i>Print All Labels
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Maintenance Confirmation Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel">
                    <i class="fas fa-wrench text-warning me-2"></i>Send to Maintenance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action will change the asset status and movement.
                </div>
                <p>Are you sure you want to send this asset to maintenance?</p>
                <div class="bg-light p-3 rounded">
                    <h6 class="mb-2">This will:</h6>
                    <ul class="mb-0">
                        <li>Set status to <span class="badge bg-warning">Maintenance</span></li>
                        <li>Set movement to <span class="badge bg-info">Return</span></li>
                        <li>Retain the assigned user (if any)</li>
                        <li>Hide the asset from the main asset list</li>
                    </ul>
                </div>
                <div class="mt-3">
                    <strong>Asset Details:</strong>
                    <div id="maintenanceAssetDetails" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-warning" id="confirmMaintenance">
                    <i class="fas fa-wrench me-2"></i>Send to Maintenance
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assignment Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white" id="bulkAssignModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Bulk Assign Assets
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('assets.bulk-assign') }}" id="bulkAssignForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="bulkAssignCount">You will assign selected assets to a user.</span>
                    </div>
                    
                    <!-- Hidden inputs for selected asset IDs -->
                    <div id="bulkAssignAssetIds"></div>
                    
                    <!-- Selected Assets Preview -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-boxes me-2"></i>Selected Assets
                        </label>
                        <div id="selectedAssetsList" class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                            <!-- Asset list will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Assign To User -->
                    <div class="mb-3">
                        <label for="bulk_assigned_to" class="form-label fw-bold">
                            <i class="fas fa-user me-2"></i>Assign To <span class="text-danger">*</span>
                        </label>
                        <select name="assigned_to" id="bulk_assigned_to" class="form-select" required>
                            <option value="">Select User</option>
                            @php
                                $activeUsers = \App\Models\User::where('status', 1)->orderBy('first_name')->get();
                            @endphp
                            @foreach($activeUsers as $activeUser)
                                <option value="{{ $activeUser->id }}">
                                    {{ $activeUser->first_name }} {{ $activeUser->last_name }} ({{ $activeUser->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Assignment Date -->
                    <div class="mb-3">
                        <label for="bulk_assigned_date" class="form-label fw-bold">
                            <i class="fas fa-calendar me-2"></i>Assignment Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               name="assigned_date" 
                               id="bulk_assigned_date" 
                               class="form-control" 
                               value="{{ date('Y-m-d') }}" 
                               required>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="bulk_notes" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-2"></i>Notes (Optional)
                        </label>
                        <textarea name="notes" 
                                  id="bulk_notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Add any notes about this bulk assignment..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> All selected assets will be assigned to the same user. Assignment confirmation emails will be sent for each asset.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i>Assign Assets
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Disposal Confirmation Modal -->
<div class="modal fade" id="disposalModal" tabindex="-1" aria-labelledby="disposalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disposalModalLabel">
                    <i class="fas fa-trash-alt text-danger me-2"></i>Send to Disposal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action will change the asset status and movement.
                </div>
                <p>Are you sure you want to send this asset to disposal?</p>
                <div class="bg-light p-3 rounded">
                    <h6 class="mb-2">This will:</h6>
                    <ul class="mb-0">
                        <li>Set status to <span class="badge bg-secondary">For Disposal</span></li>
                        <li>Set movement to <span class="badge bg-info">Return</span></li>
                        <li>Hide the asset from the main asset list</li>
                    </ul>
                </div>
                <div class="mt-3">
                    <strong>Asset Details:</strong>
                    <div id="disposalAssetDetails" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDisposal">
                    <i class="fas fa-trash-alt me-2"></i>Send to Disposal
                </button>
            </div>
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

.action-btn-maintenance {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-color: #f59e0b;
}

.action-btn-maintenance:hover {
    background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
    color: white;
}

.action-btn-disposal {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    border-color: #6b7280;
}

.action-btn-disposal:hover {
    background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
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

<script>
// JavaScript functions for Maintenance and Disposal actions
let currentAssetId = null;

function sendToMaintenance(assetId) {
    currentAssetId = assetId;
    
    // Get asset details from the table row
    const row = document.querySelector(`button[onclick="sendToMaintenance(${assetId})"]`).closest('tr');
    const assetTag = row.cells[1].textContent.trim();
    const assetName = row.cells[2].textContent.trim();
    const assignedUser = row.cells[4].textContent.trim();
    const status = row.cells[5].textContent.trim();
    const movement = row.cells[6].textContent.trim();
    
    // Populate asset details in modal
    document.getElementById('maintenanceAssetDetails').innerHTML = `
        <div class="row">
            <div class="col-6"><strong>Asset Tag:</strong> ${assetTag}</div>
            <div class="col-6"><strong>Name:</strong> ${assetName}</div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Assigned User:</strong> ${assignedUser || 'None'}</div>
            <div class="col-6"><strong>Current Status:</strong> <span class="badge bg-primary">${status}</span></div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Current Movement:</strong> <span class="badge bg-info">${movement}</span></div>
        </div>
    `;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
    modal.show();
}

function sendToDisposal(assetId) {
    currentAssetId = assetId;
    
    // Get asset details from the table row
    const row = document.querySelector(`button[onclick="sendToDisposal(${assetId})"]`).closest('tr');
    const assetTag = row.cells[1].textContent.trim();
    const assetName = row.cells[2].textContent.trim();
    const assignedUser = row.cells[4].textContent.trim();
    const status = row.cells[5].textContent.trim();
    const movement = row.cells[6].textContent.trim();
    
    // Populate asset details in modal
    document.getElementById('disposalAssetDetails').innerHTML = `
        <div class="row">
            <div class="col-6"><strong>Asset Tag:</strong> ${assetTag}</div>
            <div class="col-6"><strong>Name:</strong> ${assetName}</div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Assigned User:</strong> ${assignedUser || 'None'}</div>
            <div class="col-6"><strong>Current Status:</strong> <span class="badge bg-primary">${status}</span></div>
        </div>
        <div class="row mt-2">
            <div class="col-6"><strong>Current Movement:</strong> <span class="badge bg-info">${movement}</span></div>
        </div>
    `;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('disposalModal'));
    modal.show();
}

// Prevent double submission of bulk assign form
document.getElementById('bulkAssignForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn.disabled) {
        e.preventDefault();
        return false;
    }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Assigning...';
});

@endpush