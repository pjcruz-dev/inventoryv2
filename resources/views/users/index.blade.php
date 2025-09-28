@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 text-white">All Users</h5>
                <small class="text-white-50">{{ $users->total() }} total users</small>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                        <i class="fas fa-plus me-1"></i>Add New User
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="mt-3">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('users.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body">
    
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
                            <th>Role</th>
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
                                    @if($user->role)
                                        <span class="badge bg-primary">{{ $user->role->name }}</span>
                                    @else
                                        <span class="text-muted">No Role</span>
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
                                    <span class="badge bg-{{ $config['class'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->assignedAssets()->count() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('view_users')
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @if($user->assignedAssets()->count() > 0)
                                        @can('view_assets')
                                            <a href="{{ route('assets.print-single-employee-assets', $user) }}" 
                                               class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-print" title="Print Assets" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endcan
                                        @endif
                                        @can('edit_users')
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete_users')
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" title="Delete">
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
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
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

@push('styles')
<style>
/* Action Button Styles */
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-btn-view {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    border-color: #4f46e5;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #3730a3 0%, #6d28d9 100%);
    color: white;
}

.action-btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    border-color: #f59e0b;
}

.action-btn-edit:hover {
    background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);
    color: white;
}

.action-btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.action-btn-print {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-color: #10b981;
}

.action-btn-print:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
}

.action-btn-reminder {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border-color: #8b5cf6;
}

.action-btn-reminder:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: white;
}

.action-btn-mark {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
    border-color: #06b6d4;
}

.action-btn-mark:hover {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
}

/* Loading state */
.action-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush