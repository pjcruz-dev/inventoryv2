@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Maintenance Management
                    </h3>
                    @can('manage_maintenance')
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Maintenance
                        </a>
                    @endcan
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('maintenance.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search by issue, action, or asset...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

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
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('maintenance.show', $maintenance) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('manage_maintenance')
                                                    <a href="{{ route('maintenance.edit', $maintenance) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('maintenance.destroy', $maintenance) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
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
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tools fa-3x mb-3"></i>
                                                <p class="mb-0">No maintenance records found.</p>
                                                @can('manage_maintenance')
                                                    <a href="{{ route('maintenance.create') }}" class="btn btn-primary mt-2">
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
                        <div class="d-flex justify-content-center mt-4">
                            {{ $maintenances->links() }}
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
    // Auto-submit form on status change
    document.getElementById('status').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush