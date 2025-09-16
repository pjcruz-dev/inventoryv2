@extends('layouts.app')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department: ' . $department->name)

@section('page-actions')
    <a href="{{ route('departments.show', $department) }}" class="btn btn-info me-2">
        <i class="fas fa-eye me-2"></i>View Department
    </a>
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
                <form method="POST" action="{{ route('departments.update', $department) }}" id="departmentForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $department->name) }}" 
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
                                       id="code" name="code" value="{{ old('code', $department->code) }}" 
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
                            @foreach(App\Models\Department::whereNull('parent_id')->where('id', '!=', $department->id)->orderBy('name')->get() as $parentDept)
                                <option value="{{ $parentDept->id }}" 
                                        {{ old('parent_id', $department->parent_id) == $parentDept->id ? 'selected' : '' }}>
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
                                  placeholder="Brief description of the department's role and responsibilities">{{ old('description', $department->description) }}</textarea>
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
                                                {{ old('manager_id', $department->manager_id) == $user->id ? 'selected' : '' }}>
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
                                    <option value="active" {{ old('status', $department->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $department->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                       id="location" name="location" value="{{ old('location', $department->location) }}" 
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
                                           id="budget" name="budget" value="{{ old('budget', $department->budget) }}" 
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
                                       id="phone" name="phone" value="{{ old('phone', $department->phone) }}" 
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
                                       id="email" name="email" value="{{ old('email', $department->email) }}" 
                                       placeholder="e.g., it@company.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">General department email address</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Department Summary -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Department Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        {{ strtoupper(substr($department->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $department->name }}</h6>
                        @if($department->code)
                            <small class="text-muted">Code: {{ $department->code }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary mb-0">{{ $department->users->count() }}</h5>
                            <small class="text-muted">Members</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success mb-0">{{ $department->assets->count() }}</h5>
                        <small class="text-muted">Assets</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-{{ $department->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($department->status) }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Created:</span>
                    <span>{{ $department->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Last Updated:</span>
                    <span>{{ $department->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Current Members -->
        @if($department->users->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Current Members</h6>
                        <span class="badge bg-secondary">{{ $department->users->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($department->users->take(5) as $user)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <small class="text-muted">{{ $user->job_title ?? 'No title' }}</small>
                            </div>
                            @if($user->id === $department->manager_id)
                                <span class="badge bg-warning text-dark">Manager</span>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($department->users->count() > 5)
                        <div class="text-center mt-2">
                            <small class="text-muted">and {{ $department->users->count() - 5 }} more...</small>
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('users.index', ['department' => $department->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-users me-1"></i>View All Members
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="generateCode()">
                        <i class="fas fa-magic me-2"></i>Regenerate Code
                    </button>
                    
                    @if($department->status === 'active')
                        <button type="button" class="btn btn-outline-warning" onclick="toggleStatus('inactive')">
                            <i class="fas fa-pause me-2"></i>Deactivate Department
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-success" onclick="toggleStatus('active')">
                            <i class="fas fa-play me-2"></i>Activate Department
                        </button>
                    @endif
                    
                    <hr>
                    
                    <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>View Department
                    </a>
                    
                    <form method="POST" action="{{ route('departments.destroy', $department) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                {{ $department->users->count() > 0 || $department->assets->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash me-2"></i>Delete Department
                        </button>
                    </form>
                    
                    @if($department->users->count() > 0 || $department->assets->count() > 0)
                        <small class="text-muted text-center d-block mt-1">
                            Cannot delete department with members or assets
                        </small>
                    @endif
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
<style>
.avatar-lg {
    width: 48px;
    height: 48px;
    font-size: 16px;
    font-weight: 600;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}
</style>

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
    
    function toggleStatus(status) {
        const action = status === 'active' ? 'activate' : 'deactivate';
        
        if (confirm(`Are you sure you want to ${action} this department?`)) {
            // Update the status field and submit the form
            document.getElementById('status').value = status;
            document.getElementById('departmentForm').submit();
        }
    }
    
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;
        
        // Re-enable button after 5 seconds (in case of errors)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
</script>
@endsection