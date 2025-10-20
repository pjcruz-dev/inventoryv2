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
                        <a href="{{ route('assets.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                            <i class="fas fa-plus me-1"></i>Add New Asset
                        </a>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="mt-3">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('assets.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search assets..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
    <div class="card-body">
        @if($assets->count() > 0)
            <!-- Print All Assets Section - Only for non-User roles -->
            @if(!auth()->user()->hasRole('User') || auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support']))
            <div class="mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#printAllModal">
                    <i class="fas fa-print me-1"></i>Print All Assets ({{ $assets->total() }})
                </button>
                @if($assets->total() < 200)
                    <small class="text-muted ms-2">
                        <i class="fas fa-info-circle"></i>
                        Filters are active. <a href="{{ route('assets.index') }}" class="text-decoration-none">Clear filters</a> to print all 200 assets.
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
                const searchForm = document.querySelector('form[method="GET"]');
                const searchInput = document.querySelector('input[name="search"]');
                const searchBtn = document.querySelector('button[type="submit"]');
                
                if (searchForm && searchInput && searchBtn) {
                    searchForm.addEventListener('submit', (e) => {
                        this.showSkeletonLoading();
                        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
                        searchBtn.disabled = true;
                    });
                }
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
            // Only target main search form, not action button forms
            const searchForm = document.querySelector('form[method="GET"]');
            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    const submitBtn = searchForm.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        this.showButtonLoading(submitBtn);
                        
                        // Reset button state after search completes
                        setTimeout(() => {
                            this.hideButtonLoading(submitBtn);
                        }, 1000);
                    }
                });
            }
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

@endpush