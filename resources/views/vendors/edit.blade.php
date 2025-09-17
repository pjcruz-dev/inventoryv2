@extends('layouts.app')

@section('title', 'Edit Vendor - ' . $vendor->name)
@section('page-title', 'Edit Vendor')

@section('page-actions')
    <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-info me-2">
        <i class="fas fa-eye me-2"></i>View Vendor
    </a>
    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Vendors
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Vendor Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendors.update', $vendor) }}" id="vendorForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $vendor->name) }}" 
                                       required 
                                       placeholder="Enter vendor company name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('contact_person') is-invalid @enderror" 
                                       id="contact_person" 
                                       name="contact_person" 
                                       value="{{ old('contact_person', $vendor->contact_person) }}" 
                                       required 
                                       placeholder="Enter primary contact name">
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $vendor->email) }}" 
                                       required 
                                       placeholder="vendor@company.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $vendor->phone) }}" 
                                       placeholder="+1-800-555-0123">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3" 
                                  placeholder="Enter complete business address">{{ old('address', $vendor->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Reset Changes
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="validateForm()">
                                <i class="fas fa-check-circle me-2"></i>Validate
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Vendor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Vendor Summary -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Vendor Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-circle me-3">
                        {{ strtoupper(substr($vendor->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $vendor->name }}</h6>
                        <small class="text-muted">{{ $vendor->contact_person }}</small>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-0 text-primary">{{ $vendor->assets->count() }}</h5>
                            <small class="text-muted">Assets</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-success">₱{{ number_format($vendor->assets->sum('cost'), 2) }}</h5>
                        <small class="text-muted">Total Value</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Created:</span>
                        <span>{{ $vendor->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Updated:</span>
                        <span>{{ $vendor->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Associated Assets -->
        @if($vendor->assets->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Associated Assets ({{ $vendor->assets->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This vendor has {{ $vendor->assets->count() }} associated asset(s). 
                    Deleting this vendor will affect these assets.
                </div>
                
                <div class="list-group list-group-flush">
                    @foreach($vendor->assets->take(5) as $asset)
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $asset->name }}</h6>
                                <small class="text-muted">{{ $asset->asset_tag }}</small>
                            </div>
                            <span class="badge bg-primary">₱{{ number_format($asset->cost, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($vendor->assets->count() > 5)
                    <div class="list-group-item px-0 py-2 text-center">
                        <a href="{{ route('vendors.show', $vendor) }}" class="text-decoration-none">
                            <small>View {{ $vendor->assets->count() - 5 }} more assets...</small>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i>Reset Changes
                    </button>
                    
                    <hr>
                    
                    @can('delete_vendors')
                    <button type="button" class="btn btn-outline-danger" onclick="deleteVendor()">
                        <i class="fas fa-trash me-2"></i>Delete Vendor
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        
        <!-- Form Validation Status -->
        <div class="card mt-3" id="validationCard" style="display: none;">
            <div class="card-header">
                <h6 class="mb-0">Form Validation</h6>
            </div>
            <div class="card-body">
                <div id="validationResults"></div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $vendor->name }}</strong>?</p>
                @if($vendor->assets->count() > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This vendor has {{ $vendor->assets->count() }} associated asset(s). 
                    You cannot delete this vendor until all assets are reassigned or removed.
                </div>
                @else
                <p class="text-muted">This action cannot be undone.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if($vendor->assets->count() == 0)
                @can('delete_vendors')
                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Vendor</button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.btn {
    border-radius: 0.375rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.validation-success {
    color: #198754;
}

.validation-error {
    color: #dc3545;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>

<script>
    // Store original form data
    const originalFormData = {
        name: '{{ $vendor->name }}',
        contact_person: '{{ $vendor->contact_person }}',
        email: '{{ $vendor->email }}',
        phone: '{{ $vendor->phone ?? '' }}',
        address: '{{ $vendor->address ?? '' }}'
    };
    
    function resetForm() {
        if (confirm('Are you sure you want to reset all changes?')) {
            Object.keys(originalFormData).forEach(key => {
                const field = document.getElementById(key);
                if (field) {
                    field.value = originalFormData[key];
                    field.classList.remove('is-valid', 'is-invalid');
                }
            });
            hideValidationCard();
        }
    }
    
    function validateForm() {
        const form = document.getElementById('vendorForm');
        const formData = new FormData(form);
        const validationResults = document.getElementById('validationResults');
        const validationCard = document.getElementById('validationCard');
        
        let isValid = true;
        let results = [];
        let hasChanges = false;
        
        // Check for changes
        Object.keys(originalFormData).forEach(key => {
            const currentValue = formData.get(key) || '';
            const originalValue = originalFormData[key] || '';
            if (currentValue !== originalValue) {
                hasChanges = true;
            }
        });
        
        if (!hasChanges) {
            results.push(`<div class="text-warning"><i class="fas fa-info-circle me-1"></i>No changes detected</div>`);
        }
        
        // Validate required fields
        const requiredFields = {
            'name': 'Vendor Name',
            'contact_person': 'Contact Person',
            'email': 'Email Address'
        };
        
        Object.keys(requiredFields).forEach(field => {
            const value = formData.get(field);
            if (!value || value.trim() === '') {
                results.push(`<div class="validation-error"><i class="fas fa-times-circle me-1"></i>${requiredFields[field]} is required</div>`);
                isValid = false;
            } else {
                results.push(`<div class="validation-success"><i class="fas fa-check-circle me-1"></i>${requiredFields[field]} is valid</div>`);
            }
        });
        
        // Validate email format
        const email = formData.get('email');
        if (email && email.trim() !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                results.push(`<div class="validation-error"><i class="fas fa-times-circle me-1"></i>Email format is invalid</div>`);
                isValid = false;
            }
        }
        
        // Display results
        validationResults.innerHTML = results.join('');
        validationCard.style.display = 'block';
        
        if (isValid && hasChanges) {
            validationResults.innerHTML += '<div class="alert alert-success mt-2 mb-0"><i class="fas fa-check-circle me-2"></i>Form is ready to submit!</div>';
        }
    }
    
    function hideValidationCard() {
        document.getElementById('validationCard').style.display = 'none';
    }
    
    function deleteVendor() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    // Real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('vendorForm');
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                // Remove any existing validation classes
                this.classList.remove('is-valid', 'is-invalid');
                
                // Add validation class based on value
                if (this.hasAttribute('required')) {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.add('is-valid');
                    }
                } else if (this.value.trim() !== '') {
                    this.classList.add('is-valid');
                }
                
                // Special validation for email
                if (this.type === 'email' && this.value.trim() !== '') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(this.value)) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                }
            });
        });
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.hasAttribute('required') && input.value.trim() === '') {
                    input.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endsection