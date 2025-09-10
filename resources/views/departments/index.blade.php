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
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="card-title mb-0">All Departments</h6>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('departments.index') }}" class="d-flex" id="searchForm">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search departments..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($departments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Manager</th>
                            <th>Members</th>
                            <th>Assets</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($department->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $department->name }}</strong>
                                            @if($department->code)
                                                <br><small class="text-muted">Code: {{ $department->code }}</small>
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
                                            <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($department->manager->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <small class="fw-medium">{{ $department->manager->first_name }} {{ $department->manager->last_name }}</small>
                                                <br><small class="text-muted">{{ $department->manager->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No manager assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        <span class="fw-medium">{{ $department->users->count() }}</span>
                                        @if($department->users->count() > 0)
                                            <small class="text-muted ms-1">member{{ $department->users->count() > 1 ? 's' : '' }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box text-info me-1"></i>
                                        <span class="fw-medium">{{ $department->assets->count() }}</span>
                                        @if($department->assets->count() > 0)
                                            <small class="text-muted ms-1">asset{{ $department->assets->count() > 1 ? 's' : '' }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $department->status === 'active' ? 'success' : 'danger' }}">
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
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('departments.destroy', $department) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this department?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete"
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
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Departments Found</h5>
                @if(request('search'))
                    <p class="text-muted mb-3">No departments match your search criteria.</p>
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-times me-2"></i>Clear Search
                    </a>
                @else
                    <p class="text-muted mb-3">Get started by creating your first department.</p>
                @endif
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Department
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Department Statistics -->
@if($departments->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-building fa-2x text-primary mb-2"></i>
                    <h4 class="mb-0">{{ $departments->total() }}</h4>
                    <small class="text-muted">Total Departments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h4 class="mb-0">{{ $departments->sum(function($dept) { return $dept->users->count(); }) }}</h4>
                    <small class="text-muted">Total Members</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-box fa-2x text-info mb-2"></i>
                    <h4 class="mb-0">{{ $departments->sum(function($dept) { return $dept->assets->count(); }) }}</h4>
                    <small class="text-muted">Total Assets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-warning mb-2"></i>
                    <h4 class="mb-0">{{ $departments->where('status', 'active')->count() }}</h4>
                    <small class="text-muted">Active Departments</small>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Departments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'departments') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'departments') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: name, description, manager_id</li>
                            <li>Manager ID must exist in the users table</li>
                            <li>Department names must be unique</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Departments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection