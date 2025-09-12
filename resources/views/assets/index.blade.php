@extends('layouts.app')

@section('title', 'Assets')
@section('page-title', 'Assets Management')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('import-export.template', 'assets') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Template
            </a>
            <a href="{{ route('import-export.export', 'assets') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Import
            </button>
        </div>
        <a href="{{ route('assets.print-employee-assets') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="fas fa-print me-1"></i>Employee Assets Report
        </a>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Asset
        </a>
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
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Assets</h5>
                <small class="text-muted">{{ $assets->total() }} total assets</small>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i>Filters
                    @if(request()->hasAny(['search', 'category', 'status', 'movement', 'assignment']))
                        <span class="badge bg-primary ms-1">{{ collect(request()->only(['search', 'category', 'status', 'movement', 'assignment']))->filter()->count() }}</span>
                    @endif
                </button>
            </div>
        </div>
        
        <!-- Advanced Filters -->
        <div class="collapse mt-3" id="filterCollapse">
            <div class="card card-body bg-light">
                <form method="GET" action="{{ route('assets.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search assets..." value="{{ request('search') }}">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Category</label>
                            <div class="dropdown">
                                <input type="text" 
                                       class="form-control dropdown-toggle" 
                                       id="categorySearch" 
                                       placeholder="Search categories..." 
                                       data-bs-toggle="dropdown" 
                                       autocomplete="off"
                                       value="{{ $categories->where('id', request('category'))->first()->name ?? '' }}">
                                <input type="hidden" name="category" id="categoryValue" value="{{ request('category') }}">
                                <ul class="dropdown-menu w-100" id="categoryDropdown">
                                    <li><a class="dropdown-item" href="#" data-value="">All Categories</a></li>
                                    @foreach($categories as $category)
                                        <li><a class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" 
                                               href="#" 
                                               data-value="{{ $category->id }}">{{ $category->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ str_replace('Deployed Tagged', 'Deployed', $status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Movement Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Movement</label>
                            <select name="movement" class="form-select">
                                <option value="">All Movements</option>
                                @foreach($movements as $movement)
                                    <option value="{{ $movement }}" {{ request('movement') == $movement ? 'selected' : '' }}>
                                        {{ str_replace('Deployed Tagged', 'Deployed', $movement) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Assignment Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Assignment</label>
                            <select name="assignment" class="form-select">
                                <option value="">All Assets</option>
                                <option value="assigned" {{ request('assignment') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="unassigned" {{ request('assignment') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            </select>
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($assets->count() > 0)
            <!-- Print All Assets Section -->
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

            <!-- Bulk Actions Toolbar -->
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
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="fw-semibold" style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
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
                        @foreach($assets as $asset)
                        <tr class="border-bottom">
                            <td>
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" class="form-check-input asset-checkbox">
                            </td>
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
                                        ($asset->status === 'Inactive' ? 'danger' : 
                                        ($asset->status === 'Under Maintenance' ? 'warning' : 
                                        ($asset->status === 'Issue Reported' ? 'danger' : 
                                        ($asset->status === 'Pending Confirmation' ? 'info' : 
                                        ($asset->status === 'Disposed' ? 'dark' : 'secondary')))))
                                    }} px-2 py-1">
                                        {{ $asset->status }}
                                    </span>
                                    <small class="text-muted">{{ str_replace('Deployed Tagged', 'Deployed', $asset->movement ?? 'New Arrival') }}</small>
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
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="View Asset" style="width: 32px; height: 32px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center" title="Edit Asset" style="width: 32px; height: 32px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" title="Delete Asset" style="width: 32px; height: 32px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($assets->hasPages())
                <div class="pagination-wrapper mt-3">
                    {{ $assets->appends(request()->query())->links('pagination.custom') }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-muted">No Assets Found</h5>
                <p class="text-muted">Get started by creating your first asset.</p>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Asset
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Assets
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'assets') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'assets') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Assets
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Category search functionality
    const categorySearch = $('#categorySearch');
    const categoryValue = $('#categoryValue');
    const categoryDropdown = $('#categoryDropdown');
    
    // Filter dropdown items based on search input
    categorySearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        categoryDropdown.find('li').each(function() {
            const text = $(this).find('a').text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Show dropdown if not already visible
        if (!categoryDropdown.hasClass('show')) {
            categoryDropdown.addClass('show');
        }
    });
    
    // Handle dropdown item selection
    categoryDropdown.on('click', 'a.dropdown-item', function(e) {
        e.preventDefault();
        
        const value = $(this).data('value');
        const text = $(this).text();
        
        categoryValue.val(value);
        categorySearch.val(value ? text : '');
        
        // Update active state
        categoryDropdown.find('a.dropdown-item').removeClass('active');
        $(this).addClass('active');
        
        // Hide dropdown
        categoryDropdown.removeClass('show');
        
        // Auto-submit form for immediate filtering
        $('#filterForm').submit();
    });
    
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
    
    // Show all items when dropdown is opened
    categorySearch.on('focus', function() {
        categoryDropdown.find('li').show();
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            categoryDropdown.removeClass('show');
        }
    });
    
    // Clear search when clicking the clear button
    categorySearch.on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            categoryValue.val('');
            categoryDropdown.find('a.dropdown-item').removeClass('active');
            categoryDropdown.find('a[data-value=""]').addClass('active');
            categoryDropdown.removeClass('show');
        }
    });
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

@endsection