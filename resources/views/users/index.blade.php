@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('page-actions')
    <div class="btn-group me-2" role="group">
        <a href="{{ route('import-export.template', 'users') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-download me-1"></i>Download Template
        </a>
        <a href="{{ route('import-export.export', 'users') }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-file-export me-1"></i>Export Data
        </a>
        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import me-1"></i>Import Data
        </button>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New User
    </a>
@endsection

@section('content')
<style>
:root {
    --border-radius-xl: 1rem;
    --border-radius-lg: 0.75rem;
    --border-radius-md: 0.5rem;
    --soft-shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --soft-shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --soft-shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-info: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --gradient-warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
}

.card-modern {
    border: none;
    border-radius: var(--border-radius-xl);
    box-shadow: var(--soft-shadow-lg);
    overflow: hidden;
}

.filter-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--soft-shadow-sm);
}

.table-modern {
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--soft-shadow-sm);
}

.table-modern thead {
    background: var(--gradient-primary);
    color: white;
}

.table-modern thead th {
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateY(-1px);
    box-shadow: var(--soft-shadow-sm);
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.badge-primary { background: var(--gradient-primary); color: white; }
.badge-success { background: var(--gradient-success); color: white; }
.badge-info { background: var(--gradient-info); color: white; }
.badge-warning { background: var(--gradient-warning); color: white; }
.badge-danger { background: var(--gradient-danger); color: white; }
.badge-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; }

.btn-action {
    transition: all 0.2s ease;
    border-radius: var(--border-radius-md);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: var(--soft-shadow-md);
}
</style>

<div class="filter-container">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i>Search Users</label>
            <input type="text" name="search" class="form-control" 
                   placeholder="Search by name, email, employee ID..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold"><i class="fas fa-building me-1"></i>Department</label>
            <select name="department" class="form-select">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" 
                            {{ request('department') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold"><i class="fas fa-toggle-on me-1"></i>Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Search
                </button>
                @if(request()->hasAny(['search', 'department', 'status']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="card card-modern">
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-badge me-2"></i>Employee ID</th>
                            <th><i class="fas fa-user me-2"></i>Name</th>
                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                            <th><i class="fas fa-building me-2"></i>Department</th>
                            <th><i class="fas fa-briefcase me-2"></i>Job Title</th>
                            <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                            <th><i class="fas fa-laptop me-2"></i>Assets</th>
                            <th><i class="fas fa-tools me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    @if($user->employee_id)
                                        <span class="status-badge badge-secondary">
                                            <i class="fas fa-id-badge me-1"></i>{{ $user->employee_id }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="background: var(--gradient-primary); width: 40px; height: 40px; font-weight: 600;">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            @if($user->phone)
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $user->email }}</div>
                                </td>
                                <td>
                                    @if($user->department)
                                        <span class="status-badge badge-info">
                                            <i class="fas fa-building me-1"></i>{{ $user->department->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">No Department</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->job_title)
                                        <span class="fw-medium">{{ $user->job_title }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = $user->status === 'active' ? 'badge-success' : 'badge-danger';
                                        $statusIcon = $user->status === 'active' ? 'fas fa-check-circle' : 'fas fa-times-circle';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="{{ $statusIcon }} me-1"></i>{{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge badge-primary">
                                        <i class="fas fa-laptop me-1"></i>{{ $user->assignedAssets()->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-action btn-info" title="View User Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-action btn-warning" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-danger" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-users fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
                    @if(request()->hasAny(['search', 'department', 'status']))
                        <h5 class="text-muted mb-3">No users match your search</h5>
                        <p class="text-muted mb-4">Try adjusting your search criteria or clear the filters.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Clear Search
                        </a>
                    @else
                        <h5 class="text-muted mb-3">No users found</h5>
                        <p class="text-muted mb-4">Get started by adding your first user to the system.</p>
                    @endif
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add First User
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: var(--border-radius-xl); border: none; box-shadow: var(--soft-shadow-lg);">
            <div class="modal-header" style="background: var(--gradient-primary); color: white; border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Users
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'users') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold"><i class="fas fa-file-csv me-1"></i>Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required 
                               style="border-radius: var(--border-radius-md); padding: 0.75rem;">
                        <div class="form-text mt-2">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'users') }}" class="text-decoration-none fw-semibold">
                                <i class="fas fa-download me-1"></i>Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-radius: var(--border-radius-md);">
                        <h6 class="fw-semibold"><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV file must include headers: employee_no, employee_id, first_name, last_name, email, department_name, position, role_name, status</li>
                            <li>Department and role must exist in the system</li>
                            <li>Email addresses must be unique</li>
                            <li>Default password will be set to 'password123'</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: var(--border-radius-md);">Cancel</button>
                    <button type="submit" class="btn btn-warning" style="background: var(--gradient-warning); border: none; border-radius: var(--border-radius-md);">
                        <i class="fas fa-file-import me-2"></i>Import Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>

<script>
    // Auto-submit form when filters change
    document.querySelectorAll('select[name="department"], select[name="status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Handle search form submission on Enter
    document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });
</script>
@endsection