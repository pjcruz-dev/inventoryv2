@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->first_name . ' ' . $user->last_name)

@section('page-actions')
    @can('view_users')
    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary me-2">
        <i class="fas fa-eye me-2"></i>View User
    </a>
    @endcan
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
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
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
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" 
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
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
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
                                    <option value="Philtower" {{ old('company', $user->company) == 'Philtower' ? 'selected' : '' }}>Philtower</option>
                                    <option value="MIDC" {{ old('company', $user->company) == 'MIDC' ? 'selected' : '' }}>MIDC</option>
                                    <option value="PRIMUS" {{ old('company', $user->company) == 'PRIMUS' ? 'selected' : '' }}>PRIMUS</option>
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
                                       id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}" 
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
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
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
                                    <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    <option value="2" {{ old('status', $user->status) == '2' ? 'selected' : '' }}>Suspended</option>
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
                                                {{ old('role_id', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
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
                    </div>"}]}}}
                    
                    <hr>
                    
                    <h6 class="mb-3">Change Password <small class="text-muted">(Leave blank to keep current password)</small></h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" minlength="8">
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
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" name="password_confirmation" minlength="8">
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
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- User Summary -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">User Summary</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                        <span class="fs-4 fw-bold">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </span>
                    </div>
                    <h6 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h6>
                    @php
                        $statusConfig = [
                            1 => ['label' => 'Active', 'class' => 'success'],
                            0 => ['label' => 'Inactive', 'class' => 'danger'],
                            2 => ['label' => 'Suspended', 'class' => 'warning']
                        ];
                        $config = $statusConfig[$user->status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
                    @endphp
                    <span class="badge bg-{{ $config['class'] }}">
                        {{ $config['label'] }}
                    </span>
                </div>
                
                <dl class="row small">
                    <dt class="col-5">Email:</dt>
                    <dd class="col-7">{{ $user->email }}</dd>
                    
                    @if($user->employee_id)
                        <dt class="col-5">Employee ID:</dt>
                        <dd class="col-7"><code>{{ $user->employee_id }}</code></dd>
                    @endif
                    
                    @if($user->department)
                        <dt class="col-5">Department:</dt>
                        <dd class="col-7">{{ $user->department->name }}</dd>
                    @endif
                    
                    @if($user->job_title)
                        <dt class="col-5">Job Title:</dt>
                        <dd class="col-7">{{ $user->job_title }}</dd>
                    @endif
                    
                    @if($user->phone)
                        <dt class="col-5">Phone:</dt>
                        <dd class="col-7">{{ $user->phone }}</dd>
                    @endif
                    
                    <dt class="col-5">Created:</dt>
                    <dd class="col-7">{{ $user->created_at->format('M d, Y') }}</dd>
                    
                    <dt class="col-5">Last Updated:</dt>
                    <dd class="col-7">{{ $user->updated_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
        
        <!-- Assigned Assets -->
        <div class="card mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Assigned Assets</h6>
                    <span class="badge bg-secondary">{{ $user->assignedAssets()->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($user->assignedAssets()->count() > 0)
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This user has {{ $user->assignedAssets()->count() }} assigned asset(s). 
                        Consider reassigning assets before deactivating the user.
                    </div>
                    <a href="{{ route('assets.index', ['assigned_to' => $user->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-2"></i>View Assigned Assets
                    </a>
                @else
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No assets currently assigned to this user.
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('edit_users')
                    <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                        <i class="fas fa-key me-2"></i>Generate New Password
                    </button>
                    @endcan
                    
                    @can('edit_users')
                    @if($user->status === 'active')
                        <button type="button" class="btn btn-outline-warning" onclick="toggleUserStatus('inactive')">
                            <i class="fas fa-user-slash me-2"></i>Deactivate User
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-success" onclick="toggleUserStatus('active')">
                            <i class="fas fa-user-check me-2"></i>Activate User
                        </button>
                    @endif
                    @endcan
                    
                    @can('delete_users')
                    <hr>
                    
                    <form method="POST" action="{{ route('users.destroy', $user) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                {{ $user->assignedAssets()->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                    @endcan
                    
                    @if($user->assignedAssets()->count() > 0)
                        <small class="text-muted">Cannot delete user with assigned assets</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-lg {
    width: 64px;
    height: 64px;
}
</style>

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
    
    // Toggle user status
    function toggleUserStatus(status) {
        const statusSelect = document.getElementById('status');
        statusSelect.value = status;
        
        const action = status === 'active' ? 'activate' : 'deactivate';
        if (confirm(`Are you sure you want to ${action} this user?`)) {
            document.querySelector('form').submit();
        }
    }
    
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        let strengthIndicator = document.getElementById('password-strength');
        
        if (!strengthIndicator && password) {
            strengthIndicator = document.createElement('div');
            strengthIndicator.id = 'password-strength';
            strengthIndicator.className = 'form-text';
            this.parentNode.parentNode.appendChild(strengthIndicator);
        }
        
        if (password) {
            const strength = calculatePasswordStrength(password);
            strengthIndicator.innerHTML = `Password strength: <span class="text-${strength.color}">${strength.text}</span>`;
        } else if (strengthIndicator) {
            strengthIndicator.remove();
        }
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