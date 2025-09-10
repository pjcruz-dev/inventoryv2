@extends('layouts.app')

@section('title', 'Add New Vendor')
@section('page-title', 'Add New Vendor')

@section('page-actions')
    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Vendors
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Vendor Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendors.store') }}" id="vendorForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('contact_person') }}" 
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
                                       value="{{ old('email') }}" 
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
                                       value="{{ old('phone') }}" 
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
                                  placeholder="Enter complete business address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="clearForm()">
                                <i class="fas fa-eraser me-2"></i>Clear Form
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="validateForm()">
                                <i class="fas fa-check-circle me-2"></i>Validate
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Vendor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Guidelines -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Guidelines</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Required Information</h6>
                    <ul class="mb-0 small">
                        <li>Vendor name must be unique</li>
                        <li>Contact person is required</li>
                        <li>Email address must be valid and unique</li>
                        <li>Phone number is optional but recommended</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Best Practices</h6>
                    <ul class="mb-0 small">
                        <li>Use the official company name</li>
                        <li>Provide primary contact person</li>
                        <li>Include complete address for shipping</li>
                        <li>Verify contact information accuracy</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-info" onclick="generateSampleData()">
                        <i class="fas fa-magic me-2"></i>Fill Sample Data
                    </button>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                        <i class="fas fa-eraser me-2"></i>Clear All Fields
                    </button>
                    
                    <hr>
                    
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>View All Vendors
                    </a>
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

.alert {
    border: none;
    border-radius: 0.5rem;
}

.alert-heading {
    font-size: 0.875rem;
    font-weight: 600;
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
</style>

<script>
    function clearForm() {
        if (confirm('Are you sure you want to clear all form fields?')) {
            document.getElementById('vendorForm').reset();
            hideValidationCard();
        }
    }
    
    function generateSampleData() {
        const sampleData = {
            name: 'TechCorp Solutions',
            contact_person: 'John Smith',
            email: 'john.smith@techcorp.com',
            phone: '+1-555-123-4567',
            address: '123 Technology Drive\nSuite 100\nSan Francisco, CA 94105'
        };
        
        Object.keys(sampleData).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                field.value = sampleData[key];
            }
        });
        
        // Trigger validation
        validateForm();
    }
    
    function validateForm() {
        const form = document.getElementById('vendorForm');
        const formData = new FormData(form);
        const validationResults = document.getElementById('validationResults');
        const validationCard = document.getElementById('validationCard');
        
        let isValid = true;
        let results = [];
        
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
        
        if (isValid) {
            validationResults.innerHTML += '<div class="alert alert-success mt-2 mb-0"><i class="fas fa-check-circle me-2"></i>Form is ready to submit!</div>';
        }
    }
    
    function hideValidationCard() {
        document.getElementById('validationCard').style.display = 'none';
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