@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Maintenance Records</h5>
                            <small class="text-white-50">{{ $maintenances->total() }} total maintenance records</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                @can('create_maintenance')
                                <a href="{{ route('maintenance.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>Add New
                                </a>
                                <a href="{{ route('maintenance.bulk-create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-layer-group me-1"></i>Bulk Create
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('maintenance.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search by issue, action, or asset..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
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


                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            Showing {{ $maintenances->firstItem() ?? 0 }} to {{ $maintenances->lastItem() ?? 0 }} 
                            of {{ $maintenances->total() }} maintenance records
                        </span>
                    </div>

                    <!-- Maintenance Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Asset</th>
                                    <th>Issue Reported</th>
                                    <th>Vendor</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenances as $maintenance)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $maintenance->asset->name }}</div>
                                            <small class="text-muted">{{ $maintenance->asset->asset_tag }}</small>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $maintenance->issue_reported }}">
                                                {{ $maintenance->issue_reported }}
                                            </div>
                                        </td>
                                        <td>{{ $maintenance->vendor->name ?? 'N/A' }}</td>
                                        <td>{{ $maintenance->start_date->format('M d, Y') }}</td>
                                        <td>{{ $maintenance->end_date ? $maintenance->end_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Scheduled' => 'info',
                                                    'In Progress' => 'warning',
                                                    'Completed' => 'success',
                                                    'On Hold' => 'secondary',
                                                    'Cancelled' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }}">
                                                {{ $maintenance->status }}
                                            </span>
                                        </td>
                                        <td>
                            @if($maintenance->cost)
                                <span class="fw-bold text-primary">@currency($maintenance->cost)</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_maintenance')
                                                <a href="{{ route('maintenance.show', $maintenance) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('manage_maintenance')
                                                    <a href="{{ route('maintenance.edit', $maintenance) }}" 
                                                       class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('maintenance.destroy', $maintenance) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
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
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tools fa-3x mb-3"></i>
                                                <p class="mb-0">No maintenance records found.</p>
                                                @can('manage_maintenance')
                                                    <a href="{{ route('maintenance.create') }}" class="btn btn-primary btn-sm mt-2">
                                                        <i class="fas fa-plus me-1"></i>Add First Maintenance Record
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
                    @if($maintenances->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $maintenances->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
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

@push('scripts')
<script>
    // Auto-submit form on status change
    document.getElementById('status').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush