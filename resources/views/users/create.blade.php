@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('page-actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Users
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">User Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id') }}" 
                                       placeholder="e.g., EMP001">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional unique identifier for the employee</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select class="form-select searchable-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company" class="form-label">Company</label>
                                <select class="form-select searchable-select @error('company') is-invalid @enderror" 
                                        id="company" name="company">
                                    <option value="">Select Company</option>
                                    <option value="Philtower" {{ old('company') == 'Philtower' ? 'selected' : '' }}>Philtower</option>
                                    <option value="MIDC" {{ old('company') == 'MIDC' ? 'selected' : '' }}>MIDC</option>
                                </select>
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="job_title" class="form-label">Job Title</label>
                                <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                                       id="job_title" name="job_title" value="{{ old('job_title') }}" 
                                       placeholder="e.g., Software Developer">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="e.g., +1 (555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select searchable-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Suspended</option>
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
                                <label for="role_id" class="form-label">Role</label>
                                <select class="form-select searchable-select @error('role_id') is-invalid @enderror" 
                                        id="role_id" name="role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" 
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Assign a role to define user permissions</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Account Security</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 8 characters required</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" name="password_confirmation" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Guidelines</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>User Creation Tips</h6>
                    <ul class="mb-0 small">
                        <li>Email addresses must be unique in the system</li>
                        <li>Employee IDs are optional but must be unique if provided</li>
                        <li>Users will receive login credentials via email</li>
                        <li>Department assignment helps with asset management</li>
                        <li>Inactive users cannot log in to the system</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Security Requirements</h6>
                    <ul class="mb-0 small">
                        <li>Passwords must be at least 8 characters long</li>
                        <li>Use strong passwords with mixed characters</li>
                        <li>Users should change default passwords on first login</li>
                        <li>Consider enabling two-factor authentication</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="generateEmployeeId()">
                        <i class="fas fa-magic me-2"></i>Generate Employee ID
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                        <i class="fas fa-key me-2"></i>Generate Strong Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Generate Employee ID
    function generateEmployeeId() {
        const prefix = 'EMP';
        const randomNum = Math.floor(Math.random() * 9000) + 1000; // 4-digit number
        const employeeId = prefix + randomNum;
        
        document.getElementById('employee_id').value = employeeId;
    }
    
    // Generate strong password
    function generatePassword() {
        const length = 12;
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        
        document.getElementById('password').value = password;
        document.getElementById('password_confirmation').value = password;
        
        // Show password temporarily
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');
        const originalType = passwordField.type;
        
        passwordField.type = 'text';
        confirmField.type = 'text';
        
        setTimeout(() => {
            passwordField.type = originalType;
            confirmField.type = originalType;
        }, 3000);
        
        alert('Password generated and will be hidden in 3 seconds. Make sure to save it!');
    }
    
    // Auto-generate employee ID based on name
    document.getElementById('first_name').addEventListener('blur', function() {
        const firstName = this.value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const employeeIdField = document.getElementById('employee_id');
        
        if (firstName && lastName && !employeeIdField.value) {
            const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
            const randomNum = Math.floor(Math.random() * 900) + 100; // 3-digit number
            employeeIdField.value = initials + randomNum;
        }
    });
    
    document.getElementById('last_name').addEventListener('blur', function() {
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = this.value.trim();
        const employeeIdField = document.getElementById('employee_id');
        
        if (firstName && lastName && !employeeIdField.value) {
            const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
            const randomNum = Math.floor(Math.random() * 900) + 100; // 3-digit number
            employeeIdField.value = initials + randomNum;
        }
    });
    
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthIndicator = document.getElementById('password-strength');
        
        if (!strengthIndicator) {
            const indicator = document.createElement('div');
            indicator.id = 'password-strength';
            indicator.className = 'form-text';
            this.parentNode.parentNode.appendChild(indicator);
        }
        
        const strength = calculatePasswordStrength(password);
        const indicator = document.getElementById('password-strength');
        
        indicator.innerHTML = `Password strength: <span class="text-${strength.color}">${strength.text}</span>`;
    });
    
    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        if (score < 3) return { text: 'Weak', color: 'danger' };
        if (score < 5) return { text: 'Medium', color: 'warning' };
        return { text: 'Strong', color: 'success' };
    }
</script>
@endsection