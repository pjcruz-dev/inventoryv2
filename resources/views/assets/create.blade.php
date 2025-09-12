@extends('layouts.app')

@section('title', 'Create Asset')
@section('page-title', 'Create New Asset')

@section('page-actions')
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
                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_tag" class="form-label">Asset Tag <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                                           id="asset_tag" name="asset_tag" value="{{ old('asset_tag') }}" required
                                           placeholder="Will be auto-generated when category is selected">
                                    <button type="button" class="btn btn-outline-secondary" id="generateTagBtn" 
                                            title="Generate new asset tag" style="display: none;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Asset tag will be automatically generated when you select a category</small>
                                @error('asset_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <div class="dropdown">
                                    <input type="text" 
                                           class="form-control dropdown-toggle @error('category_id') is-invalid @enderror" 
                                           id="categorySearchCreate" 
                                           placeholder="Search and select category..." 
                                           data-bs-toggle="dropdown" 
                                           autocomplete="off"
                                           value="{{ $categories->where('id', old('category_id'))->first()->name ?? '' }}">
                                    <input type="hidden" name="category_id" id="categoryValueCreate" value="{{ old('category_id') }}">
                                    <ul class="dropdown-menu w-100" id="categoryDropdownCreate">
                                        <li><a class="dropdown-item" href="#" data-value="">Select Category</a></li>
                                        @foreach($categories as $category)
                                            <li><a class="dropdown-item {{ old('category_id') == $category->id ? 'active' : '' }}" 
                                                   href="#" 
                                                   data-value="{{ $category->id }}">{{ $category->name }}</a></li>
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
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Under Maintenance" {{ old('status') == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    <option value="Issue Reported" {{ old('status') == 'Issue Reported' ? 'selected' : '' }}>Issue Reported</option>
                                    <option value="Pending Confirmation" {{ old('status') == 'Pending Confirmation' ? 'selected' : '' }}>Pending Confirmation</option>
                                    <option value="Disposed" {{ old('status') == 'Disposed' ? 'selected' : '' }}>Disposed</option>
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
                                    <option value="New Arrival" {{ old('movement') == 'New Arrival' ? 'selected' : '' }}>New Arrival</option>
                                    <option value="Deployed" {{ old('movement') == 'Deployed' ? 'selected' : '' }}>Deployed</option>
                                    <option value="Returned" {{ old('movement') == 'Returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="Transferred" {{ old('movement') == 'Transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="Disposed" {{ old('movement') == 'Disposed' ? 'selected' : '' }}>Disposed</option>
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
                                       id="model" name="model" value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                       id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
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
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
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
                                           id="cost" name="cost" value="{{ old('cost') }}">
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }}
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
                               id="location" name="location" value="{{ old('location') }}" 
                               placeholder="e.g., Office 101, Warehouse A">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Additional information about this asset...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Asset tags should be unique identifiers</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Use descriptive names for easy identification</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Keep serial numbers for warranty tracking</small>
                    </li>
                    <li>
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        <small>Regular status updates help with maintenance</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Category search functionality
    const categorySearch = $('#categorySearchCreate');
    const categoryValue = $('#categoryValueCreate');
    const categoryDropdown = $('#categoryDropdownCreate');
    
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
        
        // Trigger asset tag generation
        if (value) {
            generateAssetTag(text);
        }
    });
    
    // Show all items when dropdown is opened
    categorySearch.on('focus', function() {
        categoryDropdown.find('li').show();
        categoryDropdown.addClass('show');
    });
    
    // Also show dropdown when clicking on the input
    categorySearch.on('click', function() {
        categoryDropdown.find('li').show();
        categoryDropdown.addClass('show');
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
    
    // Auto-generate asset tag based on category and current date
    function generateAssetTag(categoryText, forceGenerate = false) {
        const assetTagField = document.getElementById('asset_tag');
        const generateBtn = document.getElementById('generateTagBtn');
        
        // Only generate if field is empty or force generation is requested
        if (!assetTagField.value || forceGenerate) {
            const categoryPrefix = categoryText.substring(0, 3).toUpperCase();
            const date = new Date();
            const timestamp = date.getFullYear().toString().substr(-2) + 
                            String(date.getMonth() + 1).padStart(2, '0') + 
                            String(date.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            assetTagField.value = categoryPrefix + '-' + timestamp + '-' + random;
            
            // Add visual feedback
            assetTagField.classList.add('border-success');
            setTimeout(() => {
                assetTagField.classList.remove('border-success');
            }, 2000);
        }
        
        // Show generate button if category is selected and field has value
        if (categoryText && assetTagField.value) {
            generateBtn.style.display = 'block';
        }
    }
    
    // Manual asset tag generation
    $('#generateTagBtn').on('click', function() {
        const categorySearch = $('#categorySearchCreate');
        const categoryText = categorySearch.val();
        
        if (categoryText) {
            generateAssetTag(categoryText, true);
        } else {
            alert('Please select a category first.');
        }
    });
    
    // Show/hide generate button based on asset tag field content
    $('#asset_tag').on('input', function() {
        const generateBtn = document.getElementById('generateTagBtn');
        const categorySearch = $('#categorySearchCreate');
        
        if ($(this).val() && categorySearch.val()) {
            generateBtn.style.display = 'block';
        } else {
            generateBtn.style.display = 'none';
        }
    });
});
</script>
@endsection