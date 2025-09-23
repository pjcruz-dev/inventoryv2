@extends('layouts.app')

@section('title', 'Edit Asset')
{{-- Page title removed - using breadcrumbs instead --}}

@section('page-actions')
    @can('view_assets')
    <a href="{{ route('assets.show', $asset) }}" class="btn btn-info me-2">
        <i class="fas fa-eye me-2"></i>View Asset
    </a>
    @endcan
    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Assets
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Asset Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.update', $asset) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_tag" class="form-label">Asset Tag <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-warning text-dark">
                                        <i class="fas fa-lock" title="Asset tag should remain unchanged"></i>
                                    </span>
                                    <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                                           id="asset_tag" name="asset_tag" value="{{ old('asset_tag', $asset->asset_tag) }}" required readonly>
                                </div>
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Important:</strong> Asset tag should remain unchanged to maintain asset history and tracking.
                                </small>
                                @error('asset_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $asset->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <div class="dropdown">
                                    <input type="text" 
                                           class="form-control @error('category_id') is-invalid @enderror" 
                                           id="categorySearchEdit" 
                                           placeholder="Search categories..." 
                                           value="{{ old('category_id', $asset->category_id) ? $categories->find(old('category_id', $asset->category_id))->name ?? '' : '' }}" 
                                           autocomplete="off">
                                    <input type="hidden" 
                                           name="category_id" 
                                           id="categoryValueEdit" 
                                           value="{{ old('category_id', $asset->category_id) }}">
                                    <ul class="dropdown-menu w-100" id="categoryDropdownEdit">
                                        <li><a class="dropdown-item {{ !old('category_id', $asset->category_id) ? 'active' : '' }}" href="#" data-value="">Select Category</a></li>
                                        @foreach($categories as $category)
                                            <li><a class="dropdown-item {{ old('category_id', $asset->category_id) == $category->id ? 'active' : '' }}" href="#" data-value="{{ $category->id }}">{{ $category->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Available" {{ old('status', $asset->status) == 'Available' ? 'selected' : '' }}>Available</option>
                                    <option value="Assigned" {{ old('status', $asset->status) == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="Active" {{ old('status', $asset->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $asset->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Under Maintenance" {{ old('status', $asset->status) == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    <option value="Issue Reported" {{ old('status', $asset->status) == 'Issue Reported' ? 'selected' : '' }}>Issue Reported</option>
                                    <option value="Pending Confirmation" {{ old('status', $asset->status) == 'Pending Confirmation' ? 'selected' : '' }}>Pending Confirmation</option>
                                    <option value="Retired" {{ old('status', $asset->status) == 'Retired' ? 'selected' : '' }}>Retired</option>
                                    <option value="Damaged" {{ old('status', $asset->status) == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                    <option value="Disposed" {{ old('status', $asset->status) == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="movement" class="form-label">Movement</label>
                                <select class="form-select @error('movement') is-invalid @enderror" id="movement" name="movement">
                                    <option value="New Arrival" {{ old('movement', $asset->movement) == 'New Arrival' ? 'selected' : '' }}>New Arrival</option>
                                    <option value="Deployed" {{ old('movement', $asset->movement) == 'Deployed' ? 'selected' : '' }}>Deployed</option>
                                    <option value="Returned" {{ old('movement', $asset->movement) == 'Returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="Transferred" {{ old('movement', $asset->movement) == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="Disposed" {{ old('movement', $asset->movement) == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                                </select>
                                @error('movement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $asset->model) }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                       id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Number Field - Only for Mobile Devices category -->
                    <div class="row" id="mobile_number_section" style="display: {{ $asset->category && strtolower($asset->category->name) == 'mobile devices' ? 'block' : 'none' }};">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" 
                                       id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $asset->mobile_number) }}"
                                       placeholder="e.g., +63 912 345 6789">
                                <small class="form-text text-muted">Phone number associated with this mobile device</small>
                                @error('mobile_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select class="form-select searchable-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id', $asset->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">Purchase Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" 
                                           id="cost" name="cost" value="{{ old('cost', $asset->cost) }}">
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="po_number" class="form-label">PO Number</label>
                                <input type="text" class="form-control @error('po_number') is-invalid @enderror" 
                                       id="po_number" name="po_number" value="{{ old('po_number', $asset->po_number) }}">
                                @error('po_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entity" class="form-label">Entity</label>
                                <select class="form-select @error('entity') is-invalid @enderror" id="entity" name="entity">
                                    <option value="">Select Entity</option>
                                    <option value="MIDC" {{ old('entity', $asset->entity) == 'MIDC' ? 'selected' : '' }}>MIDC</option>
                                    <option value="PHILTOWER" {{ old('entity', $asset->entity) == 'PHILTOWER' ? 'selected' : '' }}>PHILTOWER</option>
                                    <option value="PRIMUS" {{ old('entity', $asset->entity) == 'PRIMUS' ? 'selected' : '' }}>PRIMUS</option>
                                </select>
                                @error('entity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lifespan" class="form-label">Lifespan (Years)</label>
                                <input type="number" min="1" max="50" class="form-control @error('lifespan') is-invalid @enderror" 
                                       id="lifespan" name="lifespan" value="{{ old('lifespan', $asset->lifespan) }}" placeholder="e.g., 5">
                                <small class="form-text text-muted">Recommended lifespan for laptops and other equipment</small>
                                @error('lifespan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $asset->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }} - {{ $user->department ? $user->department->name : 'No Department' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location', $asset->location) }}" 
                               placeholder="e.g., Office 101, Warehouse A">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Additional information about this asset...">{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Asset Details</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Created:</dt>
                    <dd class="col-sm-7">{{ $asset->created_at->format('M d, Y') }}</dd>
                    
                    <dt class="col-sm-5">Last Updated:</dt>
                    <dd class="col-sm-7">{{ $asset->updated_at->format('M d, Y') }}</dd>
                    
                    <dt class="col-sm-5">Current Status:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $asset->status === 'active' ? 'success' : ($asset->status === 'inactive' ? 'danger' : 'warning') }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('view_assets')
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('timeline.show', $asset) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-history me-2"></i>View Full Timeline
                    </a>
                    @endcan
                    @can('delete_assets')
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this asset? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-trash me-2"></i>Delete Asset
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        
        <!-- Asset Timeline -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Timeline</h6>
                @can('view_assets')
                <a href="{{ route('timeline.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($asset->timeline && $asset->timeline->count() > 0)
                    <div class="timeline">
                        @foreach($asset->timeline->take(3) as $entry)
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="timeline-badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : ($entry->action == 'unassigned' ? 'danger' : ($entry->action == 'updated' ? 'info' : 'secondary')))) }} me-3">
                                        <i class="fas fa-{{ $entry->action == 'created' ? 'plus' : ($entry->action == 'assigned' ? 'user-plus' : ($entry->action == 'transferred' ? 'exchange-alt' : ($entry->action == 'unassigned' ? 'user-minus' : ($entry->action == 'updated' ? 'edit' : 'cog')))) }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <span class="badge bg-{{ $entry->action == 'created' ? 'success' : ($entry->action == 'assigned' ? 'primary' : ($entry->action == 'transferred' ? 'warning' : ($entry->action == 'unassigned' ? 'danger' : ($entry->action == 'updated' ? 'info' : 'secondary')))) }}">
                                                        {{ ucfirst($entry->action) }}
                                                    </span>
                                                </h6>
                                                @if($entry->notes)
                                                    <p class="text-muted mb-1 small">{{ $entry->notes }}</p>
                                                @endif
                                                @if($entry->fromUser || $entry->toUser)
                                                    <div class="small text-muted">
                                                        @if($entry->fromUser)
                                                            <span><strong>From:</strong> {{ $entry->fromUser->name }}</span>
                                                        @endif
                                                        @if($entry->toUser)
                                                            @if($entry->fromUser) | @endif
                                                            <span><strong>To:</strong> {{ $entry->toUser->name }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $entry->performed_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <p class="mb-0">No timeline entries found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline-badge {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 17px;
    top: 45px;
    width: 2px;
    height: calc(100% - 35px);
    background-color: #dee2e6;
    z-index: 0;
}

.timeline-badge {
    position: relative;
    z-index: 1;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Category search functionality
    const categorySearch = $('#categorySearchEdit');
    const categoryValue = $('#categoryValueEdit');
    const categoryDropdown = $('#categoryDropdownEdit');
    
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
        
        // Show/hide mobile number field based on category
        toggleMobileNumberField(text);
    });
    
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
    
    // Clear search when pressing Escape
    categorySearch.on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            categoryValue.val('');
            categoryDropdown.find('a.dropdown-item').removeClass('active');
            categoryDropdown.find('a[data-value=""]').addClass('active');
            categoryDropdown.removeClass('show');
        }
    });
    
    // Toggle mobile number field based on category selection
    function toggleMobileNumberField(categoryText) {
        const mobileNumberSection = $('#mobile_number_section');
        const mobileNumberField = $('#mobile_number');
        
        if (categoryText && categoryText.toLowerCase().includes('mobile')) {
            mobileNumberSection.show();
            mobileNumberField.prop('disabled', false);
        } else {
            mobileNumberSection.hide();
            mobileNumberField.prop('disabled', true).val('');
        }
    }
    
    // Asset tag protection - warn user if they try to modify it
    const originalAssetTag = $('#asset_tag').val();
    let assetTagWarningShown = false;
    
    $('#asset_tag').on('input', function() {
        const currentValue = $(this).val();
        
        if (currentValue !== originalAssetTag && !assetTagWarningShown) {
            assetTagWarningShown = true;
            
            const confirmChange = confirm(
                'WARNING: Changing the asset tag may affect asset tracking and history.\n\n' +
                'Asset tags are typically permanent identifiers that should remain unchanged.\n\n' +
                'Are you sure you want to modify this asset tag?'
            );
            
            if (!confirmChange) {
                $(this).val(originalAssetTag);
                assetTagWarningShown = false;
            }
        }
        
        // Reset warning flag if user reverts to original value
        if (currentValue === originalAssetTag) {
            assetTagWarningShown = false;
        }
    });
    
    // Additional warning on form submission
    $('form').on('submit', function(e) {
        const currentAssetTag = $('#asset_tag').val();
        
        if (currentAssetTag !== originalAssetTag) {
            const confirmSubmit = confirm(
                'FINAL WARNING: You are about to change the asset tag from "' + originalAssetTag + '" to "' + currentAssetTag + '".\n\n' +
                'This change may affect asset tracking, reports, and historical data.\n\n' +
                'Do you want to proceed with this change?'
            );
            
            if (!confirmSubmit) {
                e.preventDefault();
                $('#asset_tag').focus();
                return false;
            }
        }
    });
    
    // Initialize Select2 for assigned_to dropdown
    $('#assigned_to').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search and select a user...',
        allowClear: true,
        width: '100%'
    });
    
    // Auto-populate movement based on status selection
    document.getElementById('status').addEventListener('change', function() {
        const status = this.value;
        const movementSelect = document.getElementById('movement');
        
        // Define status to movement mapping
         const statusMovementMap = {
             'Active': 'Deployed',
             'Inactive': 'Returned',
             'Under Maintenance': 'Deployed',
             'Issue Reported': 'Deployed',
             'Pending Confirmation': 'Deployed',
             'Retired': 'Returned',
             'Damaged': 'Returned',
             'Disposed': 'Disposed'
         };
        
        // Auto-populate movement if mapping exists
        if (statusMovementMap[status]) {
            movementSelect.value = statusMovementMap[status];
            
            // Trigger change event for any dependent functionality
            movementSelect.dispatchEvent(new Event('change'));
            
            // Optional: Show brief notification
            console.log(`Movement auto-populated: ${statusMovementMap[status]} based on status: ${status}`);
        }
    });
    
    // Initialize mobile number field visibility on page load
    var initialCategory = $('#categorySearchEdit').val();
    if (initialCategory) {
        toggleMobileNumberField(initialCategory);
    }
});
</script>
@endsection