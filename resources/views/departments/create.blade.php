@extends('layouts.app')

@section('title', 'Add New Department')
@section('page-title', 'Add New Department')

@section('page-actions')
    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Departments
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Department Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('departments.store') }}" id="departmentForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., Information Technology" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter the full department name</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Department Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="e.g., IT" maxlength="10">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Short code for the department (optional)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Department</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" 
                                id="parent_id" name="parent_id">
                            <option value="">Select parent department (optional)</option>
                            @foreach(App\Models\Department::whereNull('parent_id')->orderBy('name')->get() as $parentDept)
                                <option value="{{ $parentDept->id }}" 
                                        {{ old('parent_id') == $parentDept->id ? 'selected' : '' }}>
                                    {{ $parentDept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Choose a parent department to create a sub-department</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Brief description of the department's role and responsibilities">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Describe the department's purpose and responsibilities</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manager_id" class="form-label">Department Manager</label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" 
                                        id="manager_id" name="manager_id">
                                    <option value="">Select a manager (optional)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Choose a user to manage this department</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Set the department status</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" 
                                       placeholder="e.g., Building A, Floor 3">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Physical location of the department</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="budget" class="form-label">Annual Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                           id="budget" name="budget" value="{{ old('budget') }}" 
                                           placeholder="0.00" step="0.01" min="0">
                                </div>
                                @error('budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Annual budget allocation (optional)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="e.g., +1 (555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Department contact number</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Department Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="e.g., it@company.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">General department email address</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Guidelines -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Guidelines</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Department Setup</h6>
                    <ul class="mb-0 small">
                        <li>Choose a clear, descriptive name</li>
                        <li>Add a short code for easy reference</li>
                        <li>Assign a manager to oversee the department</li>
                        <li>Set the status to active when ready</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                    <ul class="mb-0 small">
                        <li>Department names should be unique</li>
                        <li>Codes are optional but recommended</li>
                        <li>Managers can be assigned later</li>
                        <li>Budget information is for reference only</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="generateCode()">
                        <i class="fas fa-magic me-2"></i>Generate Code
                    </button>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                        <i class="fas fa-eraser me-2"></i>Clear Form
                    </button>
                    
                    <hr>
                    
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>View All Departments
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Form Validation Summary -->
        @if($errors->any())
            <div class="card mt-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Validation Errors
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li class="text-danger">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function generateCode() {
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');
        
        if (nameInput.value.trim()) {
            // Generate code from department name
            let code = nameInput.value
                .trim()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 10);
            
            codeInput.value = code;
            
            // Add visual feedback
            codeInput.classList.add('border-success');
            setTimeout(() => {
                codeInput.classList.remove('border-success');
            }, 2000);
        } else {
            alert('Please enter a department name first.');
            nameInput.focus();
        }
    }
    
    function clearForm() {
        if (confirm('Are you sure you want to clear all form data?')) {
            document.getElementById('departmentForm').reset();
            
            // Remove any validation classes
            document.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });
            
            // Focus on first input
            document.getElementById('name').focus();
        }
    }
    
    // Auto-generate code when name changes
    document.getElementById('name').addEventListener('input', function() {
        const codeInput = document.getElementById('code');
        
        // Only auto-generate if code field is empty
        if (!codeInput.value.trim() && this.value.trim()) {
            let code = this.value
                .trim()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase())
                .join('')
                .substring(0, 10);
            
            codeInput.value = code;
        }
    });
    
    // Form validation
    document.getElementById('departmentForm').addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        
        if (!nameInput.value.trim()) {
            e.preventDefault();
            nameInput.focus();
            alert('Please enter a department name.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        submitBtn.disabled = true;
        
        // Re-enable button after 5 seconds (in case of errors)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
    
    // Focus on first input when page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('name').focus();
    });
</script>
@endsection