@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('import-export.template', 'departments') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Template
            </a>
            <a href="{{ route('import-export.export', 'departments') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Import
            </button>
        </div>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Department
        </a>
    </div>
@endsection

@section('content')
<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-info: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --gradient-warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --gradient-danger: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
    --soft-shadow: 0 2px 4px rgba(0,0,0,0.1);
    --soft-shadow-lg: 0 10px 25px rgba(0,0,0,0.15);
    --border-radius-sm: 6px;
    --border-radius-md: 8px;
    --border-radius-lg: 12px;
    --border-radius-xl: 16px;
}

.card-modern {
    border: none;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--soft-shadow);
    overflow: hidden;
}

.filter-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #dee2e6;
}

.table-modern {
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--soft-shadow);
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: var(--border-radius-sm);
    font-weight: 500;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-action {
    border-radius: var(--border-radius-sm);
    padding: 0.375rem 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
</style>

<div class="filter-container">
    <form method="GET" action="{{ route('departments.index') }}" class="d-flex align-items-center gap-3" id="searchForm">
        <div class="flex-grow-1">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" name="search" 
                       placeholder="Search departments by name, code, or description..." 
                       value="{{ request('search') }}" style="border-radius: 0 var(--border-radius-md) var(--border-radius-md) 0;">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="border-radius: var(--border-radius-md);">
                <i class="fas fa-search me-1"></i>Search
            </button>
            @if(request('search'))
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary" style="border-radius: var(--border-radius-md);">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card card-modern">
    
    <div class="card-body p-0">
        @if($departments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-modern">
                    <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <tr>
                            <th><i class="fas fa-building me-2 text-primary"></i>Name</th>
                            <th><i class="fas fa-align-left me-2 text-muted"></i>Description</th>
                            <th><i class="fas fa-user-tie me-2 text-success"></i>Manager</th>
                            <th><i class="fas fa-users me-2 text-info"></i>Members</th>
                            <th><i class="fas fa-box me-2 text-warning"></i>Assets</th>
                            <th><i class="fas fa-circle-check me-2 text-success"></i>Status</th>
                            <th><i class="fas fa-calendar me-2 text-muted"></i>Created</th>
                            <th width="120"><i class="fas fa-cogs me-2 text-secondary"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="background: var(--gradient-primary); width: 40px; height: 40px; font-weight: 600;">
                                            {{ strtoupper(substr($department->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $department->name }}</strong>
                                            @if($department->code)
                                                <br><span class="status-badge" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1976d2;">
                                                    <i class="fas fa-hashtag me-1"></i>{{ $department->code }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($department->description)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                              title="{{ $department->description }}">
                                            {{ $department->description }}
                                        </span>
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </td>
                                <td>
                                    @if($department->manager)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="background: var(--gradient-success); width: 28px; height: 28px; font-weight: 600;">
                                                {{ strtoupper(substr($department->manager->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <small class="fw-medium">{{ $department->manager->first_name }} {{ $department->manager->last_name }}</small>
                                                <br><small class="text-muted">{{ $department->manager->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="status-badge" style="background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%); color: #757575;">
                                            <i class="fas fa-user-slash me-1"></i>No manager
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); color: #2e7d32;">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $department->users->count() }} member{{ $department->users->count() !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1976d2;">
                                        <i class="fas fa-box me-1"></i>
                                        {{ $department->assets->count() }} asset{{ $department->assets->count() !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: {{ $department->status === 'active' ? 'var(--gradient-success)' : 'var(--gradient-danger)' }}; color: white;">
                                        <i class="fas fa-{{ $department->status === 'active' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ ucfirst($department->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $department->created_at->format('M d, Y') }}
                                        <br>{{ $department->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('departments.show', $department) }}" 
                                           class="btn btn-outline-primary btn-action" title="View Department Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}" 
                                           class="btn btn-outline-secondary btn-action" title="Edit Department">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('departments.destroy', $department) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-action" title="Delete Department"
                                                    {{ $department->users->count() > 0 || $department->assets->count() > 0 ? 'disabled' : '' }}>
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
            
            @if($departments->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} departments
                        </div>
                        {{ $departments->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-building fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
                    @if(request('search'))
                        <h5 class="text-muted mb-3">No departments match your search</h5>
                        <p class="text-muted mb-4">Try adjusting your search criteria or clear the filters.</p>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Clear Search
                        </a>
                    @else
                        <h5 class="text-muted mb-3">No departments found</h5>
                        <p class="text-muted mb-4">Get started by creating your first department.</p>
                    @endif
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Department
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Department Statistics -->
@if($departments->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card card-modern text-center" style="background: var(--gradient-primary); color: white;">
                <div class="card-body">
                    <i class="fas fa-building fa-2x mb-3" style="opacity: 0.9;"></i>
                    <h4 class="mb-1 fw-bold">{{ $departments->total() }}</h4>
                    <small style="opacity: 0.8;">Total Departments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-modern text-center" style="background: var(--gradient-success); color: white;">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-3" style="opacity: 0.9;"></i>
                    <h4 class="mb-1 fw-bold">{{ $departments->sum(function($dept) { return $dept->users->count(); }) }}</h4>
                    <small style="opacity: 0.8;">Total Members</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-modern text-center" style="background: var(--gradient-info); color: white;">
                <div class="card-body">
                    <i class="fas fa-box fa-2x mb-3" style="opacity: 0.9;"></i>
                    <h4 class="mb-1 fw-bold">{{ $departments->sum(function($dept) { return $dept->assets->count(); }) }}</h4>
                    <small style="opacity: 0.8;">Total Assets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-modern text-center" style="background: var(--gradient-warning); color: white;">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x mb-3" style="opacity: 0.9;"></i>
                    <h4 class="mb-1 fw-bold">{{ $departments->where('status', 'active')->count() }}</h4>
                    <small style="opacity: 0.8;">Active Departments</small>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}

.avatar-xs {
    width: 24px;
    height: 24px;
    font-size: 10px;
    font-weight: 600;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<script>
    // Auto-submit search form on input
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 2 || this.value.length === 0) {
                        document.getElementById('searchForm').submit();
                    }
                }, 500);
            });
        }
    });
</script>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: var(--border-radius-xl); border: none; box-shadow: var(--soft-shadow-lg);">
            <div class="modal-header" style="background: var(--gradient-primary); color: white; border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Departments
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'departments') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold"><i class="fas fa-file-csv me-1"></i>Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required 
                               style="border-radius: var(--border-radius-md); padding: 0.75rem;">
                        <div class="form-text mt-2">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'departments') }}" class="text-decoration-none fw-semibold">
                                <i class="fas fa-download me-1"></i>Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-radius: var(--border-radius-md);">
                        <h6 class="fw-semibold"><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: name, description, manager_id</li>
                            <li>Manager ID must exist in the users table</li>
                            <li>Department names must be unique</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: var(--border-radius-md);">Cancel</button>
                    <button type="submit" class="btn btn-warning" style="background: var(--gradient-warning); border: none; border-radius: var(--border-radius-md);">
                        <i class="fas fa-file-import me-2"></i>Import Departments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection