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
                                <div id="asset-tag-feedback" class="mt-1"></div>
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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Creation Mode</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="single_creation" name="creation_mode" value="single" {{ old('creation_mode', 'single') == 'single' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="single_creation">
                                        Single Asset Creation
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="bulk_creation" name="creation_mode" value="bulk" {{ old('creation_mode') == 'bulk' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bulk_creation">
                                        Bulk Creation (without serial numbers)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="bulk_serial_creation" name="creation_mode" value="bulk_serial" {{ old('creation_mode') == 'bulk_serial' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bulk_serial_creation">
                                        Bulk Creation (with manual serial numbers)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="bulk_options" style="display: {{ in_array(old('creation_mode'), ['bulk', 'bulk_serial']) ? 'block' : 'none' }};">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="20">
                                <small class="form-text text-muted">Number of identical assets to create (max 20)</small>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Serial Numbers Input Section for Bulk Creation with Serial Numbers -->
                    <div class="row" id="serial_numbers_section" style="display: {{ old('creation_mode') == 'bulk_serial' ? 'block' : 'none' }};">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Serial Numbers <span class="text-danger">*</span></label>
                                <small class="form-text text-muted mb-2">Enter a unique serial number for each asset</small>
                                <div id="serial_numbers_container">
                                    @php
                                        $quantity = old('quantity', 1);
                                        $serialNumbers = old('serial_numbers', []);
                                    @endphp
                                    @for($i = 1; $i <= $quantity; $i++)
                                        <div class="input-group mb-2 serial-input-group">
                                            <span class="input-group-text">Asset {{ $i }}</span>
                                            <input type="text" class="form-control @error('serial_numbers.' . ($i-1)) is-invalid @enderror" 
                                                   name="serial_numbers[]" 
                                                   placeholder="Enter serial number for asset {{ $i }}" 
                                                   value="{{ $serialNumbers[$i-1] ?? '' }}">
                                            @error('serial_numbers.' . ($i-1))
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endfor
                                </div>
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
                                    <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                                    <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="Pending Confirmation" {{ old('status') == 'Pending Confirmation' ? 'selected' : '' }}>Pending Confirmation</option>
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="For Disposal" {{ old('status') == 'For Disposal' ? 'selected' : '' }}>For Disposal</option>
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
                                <select class="form-select @error('movement') is-invalid @enderror" id="movement" name="movement" readonly>
                                    <option value="Return" {{ old('movement') == 'Return' ? 'selected' : '' }}>Return</option>
                                    <option value="New Arrival" {{ old('movement', 'New Arrival') == 'New Arrival' ? 'selected' : '' }}>New Arrival</option>
                                    <option value="Deployed" {{ old('movement') == 'Deployed' ? 'selected' : '' }}>Deployed</option>
                                </select>
                                <small class="form-text text-muted">Automatically set to "New Arrival" for new assets</small>
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
                        <div class="col-md-6" id="single_serial_field" style="display: {{ old('creation_mode', 'single') == 'single' ? 'block' : 'none' }};">
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
                    
                    <!-- Mobile Number Field - Only for Mobile Devices category -->
                    <div class="row" id="mobile_number_section" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" 
                                       id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}"
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
                                <label for="po_number" class="form-label">PO Number</label>
                                <input type="text" class="form-control @error('po_number') is-invalid @enderror" 
                                       id="po_number" name="po_number" value="{{ old('po_number') }}">
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
                                    <option value="MIDC" {{ old('entity') == 'MIDC' ? 'selected' : '' }}>MIDC</option>
                                    <option value="PHILTOWER" {{ old('entity') == 'PHILTOWER' ? 'selected' : '' }}>PHILTOWER</option>
                                    <option value="PRIMUS" {{ old('entity') == 'PRIMUS' ? 'selected' : '' }}>PRIMUS</option>
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
                                       id="lifespan" name="lifespan" value="{{ old('lifespan') }}" placeholder="e.g., 5">
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
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }} - {{ $user->department->name ?? 'No Department' }}
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
    // Set movement to "New Arrival" automatically for new assets
    $('#movement').val('New Arrival');
    
    // Add styling to show it's read-only but still submittable
    $('#movement').css({
        'background-color': '#f8f9fa',
        'cursor': 'not-allowed'
    });
    
    // Prevent user from changing the value but allow form submission
    $('#movement').on('mousedown', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Prevent keyboard changes but allow form submission
    $('#movement').on('keydown', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Ensure the value is set before form submission
    $('form').on('submit', function() {
        $('#movement').val('New Arrival');
    });
    
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
        
        // Show/hide mobile number field based on category
        toggleMobileNumberField(text);
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
    
    // Auto-generate asset tag based on category using backend API
    function generateAssetTag(categoryText, forceGenerate = false) {
        const assetTagField = document.getElementById('asset_tag');
        const generateBtn = document.getElementById('generateTagBtn');
        
        // Only generate if field is empty or force generation is requested
        if (!assetTagField.value || forceGenerate) {
            // Show loading state
            assetTagField.value = 'Generating...';
            assetTagField.disabled = true;
            
            // Call backend API to generate unique tag
            fetch('{{ route("assets.generate-tag") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    category_name: categoryText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    assetTagField.value = data.asset_tag;
                    
                    // Add visual feedback
                    assetTagField.classList.add('border-success');
                    setTimeout(() => {
                        assetTagField.classList.remove('border-success');
                    }, 2000);
                } else {
                    assetTagField.value = '';
                    alert('Error generating asset tag: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                assetTagField.value = '';
                alert('Error generating asset tag. Please try again.');
            })
            .finally(() => {
                assetTagField.disabled = false;
            });
        }
        
        // Show generate button if category is selected and field has value
        if (categoryText && assetTagField.value && assetTagField.value !== 'Generating...') {
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
             'Pending Confirmation': 'New Arrival',
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
    
    // Handle creation mode changes
    $('input[name="creation_mode"]').change(function() {
        var selectedMode = $(this).val();
        var bulkOptions = $('#bulk_options');
        var serialNumbersSection = $('#serial_numbers_section');
        var singleSerialField = $('#single_serial_field');
        
        // Hide all sections first
        bulkOptions.hide();
        serialNumbersSection.hide();
        singleSerialField.hide();
        
        // Show appropriate sections based on selected mode
        if (selectedMode === 'single') {
            singleSerialField.show();
            $('#serial_number').prop('disabled', false);
        } else if (selectedMode === 'bulk') {
            bulkOptions.show();
            $('#serial_number').prop('disabled', true).val('');
        } else if (selectedMode === 'bulk_serial') {
            bulkOptions.show();
            serialNumbersSection.show();
            $('#serial_number').prop('disabled', true).val('');
            generateSerialInputs();
        }
    });
    
    // Handle quantity changes for bulk creation with serial numbers
    $('#quantity').on('input', function() {
        if ($('#bulk_serial_creation').is(':checked')) {
            generateSerialInputs();
        }
    });
    
    // Function to generate serial number input fields
    function generateSerialInputs() {
        var quantity = parseInt($('#quantity').val()) || 1;
        var container = $('#serial_numbers_container');
        
        // Get existing values before clearing
        var existingValues = [];
        container.find('input[name="serial_numbers[]"]').each(function() {
            existingValues.push($(this).val());
        });
        
        // Clear existing inputs
        container.empty();
        
        // Generate new inputs based on quantity
        for (var i = 1; i <= quantity; i++) {
            var inputGroup = $('<div class="input-group mb-2 serial-input-group">');
            var label = $('<span class="input-group-text">Asset ' + i + '</span>');
            var input = $('<input type="text" class="form-control" name="serial_numbers[]" placeholder="Enter serial number for asset ' + i + '" required>');
            
            // Preserve existing value if available
            if (existingValues[i-1]) {
                input.val(existingValues[i-1]);
            }
            
            inputGroup.append(label).append(input);
            container.append(inputGroup);
        }
    }
    
    // Initialize creation mode state on page load
    var initialMode = $('input[name="creation_mode"]:checked').val() || 'single';
    $('input[name="creation_mode"][value="' + initialMode + '"]').trigger('change');
    
    // Initialize mobile number field visibility on page load
    var initialCategory = $('#categorySearchCreate').val();
    if (initialCategory) {
        toggleMobileNumberField(initialCategory);
    }
    
    // Real-time asset tag validation
    let assetTagTimeout;
    $('#asset_tag').on('input', function() {
        const assetTag = $(this).val().trim();
        const feedback = $('#asset-tag-feedback');
        
        // Clear previous timeout
        clearTimeout(assetTagTimeout);
        
        if (assetTag.length === 0) {
            feedback.removeClass('text-success text-danger').text('');
            return;
        }
        
        // Show checking status
        feedback.removeClass('text-success text-danger').addClass('text-info').text('Checking availability...');
        
        // Debounce the API call
        assetTagTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("assets.check-tag-uniqueness") }}',
                method: 'POST',
                data: {
                    asset_tag: assetTag,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.available) {
                        feedback.removeClass('text-info text-danger').addClass('text-success')
                               .html('<i class="fas fa-check-circle"></i> Asset tag is available');
                    } else {
                        feedback.removeClass('text-info text-success').addClass('text-danger')
                               .html('<i class="fas fa-times-circle"></i> Asset tag already exists');
                    }
                },
                error: function() {
                    feedback.removeClass('text-info text-success').addClass('text-danger')
                           .html('<i class="fas fa-exclamation-triangle"></i> Error checking availability');
                }
            });
        }, 500); // 500ms delay
    });
});
</script>
@endsection