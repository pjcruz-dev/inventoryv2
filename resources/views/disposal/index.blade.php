@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-trash-alt me-2"></i>Asset Disposals
                        </h3>
                        <div class="btn-group" role="group">
                            @can('create_disposal')
                                <a href="{{ route('disposal.bulk-create') }}" class="btn btn-danger">
                                    <i class="fas fa-plus-circle me-1"></i>Bulk Dispose
                                </a>
                                <a href="{{ route('disposal.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add New
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('disposal.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Asset name, tag, or disposal type...">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="disposal_type" class="form-label">Disposal Type</label>
                                <select class="form-select" id="disposal_type" name="disposal_type">
                                    <option value="">All Types</option>
                                    <option value="Sold" {{ request('disposal_type') == 'Sold' ? 'selected' : '' }}>Sold</option>
                                    <option value="Donated" {{ request('disposal_type') == 'Donated' ? 'selected' : '' }}>Donated</option>
                                    <option value="Recycled" {{ request('disposal_type') == 'Recycled' ? 'selected' : '' }}>Recycled</option>
                                    <option value="Destroyed" {{ request('disposal_type') == 'Destroyed' ? 'selected' : '' }}>Destroyed</option>
                                    <option value="Lost" {{ request('disposal_type') == 'Lost' ? 'selected' : '' }}>Lost</option>
                                    <option value="Stolen" {{ request('disposal_type') == 'Stolen' ? 'selected' : '' }}>Stolen</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                            

                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @if(request()->hasAny(['search', 'disposal_type', 'date_from', 'date_to']))
                            <div class="mt-2">
                                <a href="{{ route('disposal.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                    
                    <!-- Results Summary -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                Showing {{ $disposals->firstItem() ?? 0 }} to {{ $disposals->lastItem() ?? 0 }} 
                                of {{ $disposals->total() }} disposal records
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('disposal.index', array_merge(request()->all(), ['export' => 'csv'])) }}" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-file-csv me-1"></i>Export CSV
                                </a>
                                <a href="{{ route('disposal.index', array_merge(request()->all(), ['export' => 'pdf'])) }}" 
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Disposals Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Asset</th>
                                    <th>Asset Tag</th>
                                    <th>Disposal Type</th>
                                    <th>Disposal Date</th>
                                    <th>Value</th>

                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disposals as $disposal)
                                    <tr>
                                        <td>{{ $disposal->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($disposal->asset->image)
                                                    <img src="{{ asset('storage/' . $disposal->asset->image) }}" 
                                                         alt="{{ $disposal->asset->name }}" 
                                                         class="rounded me-2" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-desktop text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $disposal->asset->name }}</div>
                                                    <small class="text-muted">{{ $disposal->asset->model->name ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $disposal->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $disposal->disposal_type == 'Sold' ? 'success' : ($disposal->disposal_type == 'Donated' ? 'info' : ($disposal->disposal_type == 'Recycled' ? 'warning' : 'danger')) }}">
                                                {{ $disposal->disposal_type }}
                                            </span>
                                        </td>
                                        <td>{{ $disposal->disposal_date->format('M d, Y') }}</td>
                                        <td>
                            @if($disposal->disposal_value)
                                <span class="fw-bold text-success">@currency($disposal->disposal_value)</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>

                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view_disposal')
                                                    <a href="{{ route('disposal.show', $disposal) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('edit_disposal')
                                                    <a href="{{ route('disposal.edit', $disposal) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete_disposal')
                                                    <form action="{{ route('disposal.destroy', $disposal) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this disposal record?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-0">No disposal records found.</p>
                                                @can('create_disposal')
                                                    <a href="{{ route('disposal.create') }}" class="btn btn-primary mt-2">
                                                        <i class="fas fa-plus me-1"></i>Add First Disposal
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($disposals->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $disposals->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form when disposal type changes
    const disposalTypeElement = document.getElementById('disposal_type');
    if (disposalTypeElement) {
        disposalTypeElement.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Auto-submit form when approved_by changes (if element exists)
    const approvedByElement = document.getElementById('approved_by');
    if (approvedByElement) {
        approvedByElement.addEventListener('change', function() {
            this.form.submit();
        });
    }
</script>
@endpush

@push('styles')
<style>
.badge-success {
    background-color: #28a745;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}
</style>
@endpush