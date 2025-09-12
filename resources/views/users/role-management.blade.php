@extends('layouts.app')

@section('title', 'User Role Management')
@section('page-title', 'User Role Management')

@section('page-actions')
    <div class="btn-group me-2" role="group">
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
            <i class="fas fa-users me-1"></i>Bulk Assign Roles
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Users
        </a>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-primary">
        <i class="fas fa-user-tag me-2"></i>Manage Roles
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0">User Role Assignments</h6>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search users..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <select name="role" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Roles</option>
                        @foreach($allRoles as $role)
                            <option value="{{ $role->id }}" 
                                    {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="department" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                    {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    @if(request()->hasAny(['search', 'role', 'department']))
                        <a href="{{ route('users.role-management') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>User</th>
                            <th>Department</th>
                            <th>Current Role</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input user-checkbox" 
                                           value="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            @if($user->employee_id)
                                                <br><small class="text-muted">ID: {{ $user->employee_id }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $user->department->name ?? 'No Department' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            @php
                                                $badgeClass = match($role->name) {
                                                    'Super Admin' => 'bg-danger',
                                                    'Admin' => 'bg-warning text-dark',
                                                    'Manager' => 'bg-info',
                                                    'IT Support' => 'bg-success',
                                                    default => 'bg-primary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} me-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">No Role</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            1 => ['label' => 'Active', 'class' => 'success'],
                                            0 => ['label' => 'Inactive', 'class' => 'danger'],
                                            2 => ['label' => 'Suspended', 'class' => 'warning']
                                        ];
                                        $config = $statusConfig[$user->status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }}">{{ $config['label'] }}</span>
                                </td>
                                <td>
                                    @php
                                        $canManage = \App\Http\Middleware\RoleHierarchy::canManageUser(auth()->user(), $user);
                                    @endphp
                                    
                                    @if($canManage)
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="showAssignRoleModal({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            @if($user->roles->count() > 0)
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="removeUserRole({{ $user->id }}, '{{ $user->name }}')">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            <i class="fas fa-lock" title="Insufficient privileges"></i>
                                            No access
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $users->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">Try adjusting your search criteria.</p>
            </div>
        @endif
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignRoleForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User:</label>
                        <div id="selectedUserName" class="fw-medium"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="roleSelect" name="role_id" required>
                            <option value="">Choose a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-description="{{ $role->description }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="roleDescription" class="form-text"></div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Assigning a new role will replace any existing roles for this user.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Assign Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAssignModalLabel">Bulk Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkAssignForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selected Users:</label>
                        <div id="selectedUsersCount" class="fw-medium text-primary">No users selected</div>
                        <div id="selectedUsersList" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulkRoleSelect" class="form-label">Select Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulkRoleSelect" name="role_id" required>
                            <option value="">Choose a role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-description="{{ $role->description }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="bulkRoleDescription" class="form-text"></div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will replace existing roles for all selected users.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Assign to Selected Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedUserId = null;
let selectedUsers = [];

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedUsers();
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        updateSelectedUsers();
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.user-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (checkedCheckboxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes.length === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
});

function updateSelectedUsers() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    selectedUsers = Array.from(checkedBoxes).map(cb => ({
        id: cb.value,
        name: cb.dataset.userName
    }));
    
    const countElement = document.getElementById('selectedUsersCount');
    const listElement = document.getElementById('selectedUsersList');
    
    if (selectedUsers.length === 0) {
        countElement.textContent = 'No users selected';
        countElement.className = 'fw-medium text-muted';
        listElement.innerHTML = '';
    } else {
        countElement.textContent = `${selectedUsers.length} user(s) selected`;
        countElement.className = 'fw-medium text-primary';
        
        const userBadges = selectedUsers.map(user => 
            `<span class="badge bg-light text-dark me-1 mb-1">${user.name}</span>`
        ).join('');
        listElement.innerHTML = userBadges;
    }
}

function showAssignRoleModal(userId, userName) {
    selectedUserId = userId;
    document.getElementById('selectedUserName').textContent = userName;
    document.getElementById('roleSelect').value = '';
    document.getElementById('roleDescription').textContent = '';
    
    const modal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
    modal.show();
}

// Role description update
document.getElementById('roleSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const description = selectedOption.dataset.description || '';
    document.getElementById('roleDescription').textContent = description;
});

document.getElementById('bulkRoleSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const description = selectedOption.dataset.description || '';
    document.getElementById('bulkRoleDescription').textContent = description;
});

// Assign role form submission
document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const roleId = document.getElementById('roleSelect').value;
    if (!roleId) {
        alert('Please select a role');
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Assigning...';
    submitBtn.disabled = true;
    
    fetch(`/users/${selectedUserId}/assign-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ role_id: roleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to assign role'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the role');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Bulk assign form submission
document.getElementById('bulkAssignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedUsers.length === 0) {
        alert('Please select at least one user');
        return;
    }
    
    const roleId = document.getElementById('bulkRoleSelect').value;
    if (!roleId) {
        alert('Please select a role');
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Assigning...';
    submitBtn.disabled = true;
    
    fetch('/users/bulk-assign-roles', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            user_ids: selectedUsers.map(u => u.id),
            role_id: roleId 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to assign roles'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning roles');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function removeUserRole(userId, userName) {
    if (!confirm(`Are you sure you want to remove all roles from ${userName}?`)) {
        return;
    }
    
    fetch(`/users/${userId}/remove-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to remove role'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the role');
    });
}

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
}

.pagination-info {
    font-size: 0.875rem;
    color: #6c757d;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

.badge {
    font-size: 0.75rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush