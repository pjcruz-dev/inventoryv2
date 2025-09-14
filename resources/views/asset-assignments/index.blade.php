@extends('layouts.app')

@section('title', 'Asset Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-tag me-2"></i>Asset Assignments
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-assignments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>New Assignment
                        </a>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i>Import/Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('asset-assignments.export') }}">
                                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('asset-assignments.import-form') }}">
                                    <i class="fas fa-file-import me-2"></i>Import from Excel
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('asset-assignments.download-template') }}">
                                    <i class="fas fa-file-download me-2"></i>Download Template
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('asset-assignments.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search assignments..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('asset-assignments.index') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="asset_category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('asset_category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Assignments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset</th>
                                    <th>Assigned To</th>
                                    <th>Assigned By</th>
                                    <th>Assigned Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="asset-icon me-2">
                                                    <i class="fas fa-laptop text-primary"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $assignment->asset->name }}</strong><br>
                                                    <small class="text-muted">{{ $assignment->asset->asset_tag }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2">
                                                    <i class="fas fa-user-circle text-secondary"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $assignment->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $assignment->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $assignment->assignedBy->name ?? 'System' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $assignment->assigned_date ? $assignment->assigned_date->format('M d, Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($assignment->return_date)
                                                <span class="badge bg-success">
                                                    {{ $assignment->return_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not returned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($assignment->status)
                                                @case('assigned')
                                                    <span class="badge bg-primary">Assigned</span>
                                                    @break
                                                @case('returned')
                                                    <span class="badge bg-success">Returned</span>
                                                    @break
                                                @case('overdue')
                                                    <span class="badge bg-danger">Overdue</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($assignment->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('asset-assignments.show', $assignment) }}" 
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('asset-assignments.edit', $assignment) }}" 
                                                   class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete({{ $assignment->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-0">No asset assignments found.</p>
                                                @if(request()->hasAny(['search', 'status', 'asset_category']))
                                                    <a href="{{ route('asset-assignments.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                        Clear filters
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($assignments->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $assignments->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this asset assignment?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(assignmentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/asset-assignments/${assignmentId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush