@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit search form on Enter key
    $('#filterForm input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).closest('form').submit();
        }
    });
    
    // Clear search input when clear button is clicked
    $('.btn-clear-search').on('click', function(e) {
        e.preventDefault();
        $('#filterForm input[name="search"]').val('');
        $('#filterForm').submit();
    });
    
    // Highlight search terms in results
    var searchTerm = '{{ request("search") }}';
    if (searchTerm) {
        highlightSearchTerms(searchTerm);
    }
    
    function highlightSearchTerms(term) {
        if (!term) return;
        
        var regex = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        
        $('.table tbody td').each(function() {
            var $this = $(this);
            var html = $this.html();
            
            // Skip if this cell contains HTML elements we don't want to modify
            if ($this.find('button, a, form').length > 0) return;
            
            var newHtml = html.replace(regex, '<mark class="bg-warning">$1</mark>');
            if (newHtml !== html) {
                $this.html(newHtml);
            }
        });
    }
    
    // Add loading state to form submission
    $('#filterForm').on('submit', function() {
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i>');
    });
});
</script>
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <h3 class="card-title mb-0 text-white">
            <i class="fas fa-building me-2"></i>All Departments
        </h3>
        <div class="btn-group">
            <a href="{{ route('departments.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                <i class="fas fa-plus me-1"></i>Add New Department
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search Section -->
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('departments.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search departments..." 
                                   value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                <i class="fas fa-search"></i>
                            </button>
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
                            <th>Parent Department</th>
                            <th>Description</th>
                            <th>Manager</th>
                            <th>Members</th>
                            <th>Assets</th>
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
                                            @if($department->children->count() > 0)
                                                <br><small class="text-info"><i class="fas fa-sitemap me-1"></i>{{ $department->children->count() }} sub-department{{ $department->children->count() > 1 ? 's' : '' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($department->parent)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-level-up-alt text-muted me-1"></i>
                                            <a href="{{ route('departments.show', $department->parent) }}" class="text-decoration-none">
                                                {{ $department->parent->name }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted"><i class="fas fa-building me-1"></i>Main Department</span>
                                    @endif
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
                                    <small class="text-muted">
                                        {{ $department->created_at->format('M d, Y') }}
                                        <br>{{ $department->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('view_departments')
                                        <a href="{{ route('departments.show', $department) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('edit_departments')
                                        <a href="{{ route('departments.edit', $department) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete_departments')
                                        <form method="POST" action="{{ route('departments.destroy', $department) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this department?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" title="Delete"
                                                    {{ $department->users->count() > 0 || $department->assets->count() > 0 ? 'disabled' : '' }}>
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
            
            @if($departments->hasPages())
                <div class="card-footer">
                    <div class="pagination-wrapper">
                        {{ $departments->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Departments Found</h5>
                @if(request('search'))
                    <p class="text-muted mb-3">
                        No departments match your search.
                        @if(request('search'))
                            <br>Search term: <strong>"{{ request('search') }}"</strong>
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Search
                        </a>
                        <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add New Department
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-3">Get started by creating your first department.</p>
                    <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add New Department
                    </a>
                @endif
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
                    <h4 class="mb-0">{{ $departments->count() }}</h4>
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