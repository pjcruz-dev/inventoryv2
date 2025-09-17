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
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0">User Management</h6>
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
                    
                    <select name="department" class="form-select form-select-sm searchable-select" style="width: 150px;">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                    {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="status" class="form-select form-select-sm searchable-select" style="width: 120px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    
                    <select name="entity" class="form-select form-select-sm searchable-select" style="width: 120px;">
                        <option value="">All Entities</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                        @endforeach
                    </select>
                    
                    @if(request()->hasAny(['search', 'department', 'status', 'entity']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Assets</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    @if($user->employee_id)
                                        <code class="fs-6">{{ $user->employee_id }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            @if($user->phone)
                                                <small class="text-muted">{{ $user->phone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->department)
                                        <span class="badge bg-info">{{ $user->department->name }}</span>
                                    @else
                                        <span class="text-muted">No Department</span>
                                    @endif
                                </td>
                                <td>{{ $user->job_title ?? '-' }}</td>
                                <td>
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
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->assignedAssets()->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view_users')
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @if($user->assignedAssets()->count() > 0)
                                        @can('view_assets')
                                            <a href="{{ route('assets.print-single-employee-assets', $user) }}" 
                                               class="btn btn-outline-info" title="Print Assets" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endcan
                                        @endif
                                        @can('edit_users')
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete_users')
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $users->appends(request()->query())->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Users Found</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'department', 'status']))
                        No users match your current filters.
                    @else
                        Get started by adding your first user to the system.
                    @endif
                </p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First User
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Users
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'users') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'users') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV file must include headers: employee_no, employee_id, first_name, last_name, email, department_name, position, role_name, status</li>
                            <li>Department and role must exist in the system</li>
                            <li>Email addresses must be unique</li>
                            <li>Default password will be set to 'password123'</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
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