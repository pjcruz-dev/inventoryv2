@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Monitors</h4>
                    <div class="d-flex gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('import-export.template', 'monitors') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Template
                            </a>
                            <a href="{{ route('import-export.export', 'monitors') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-export me-1"></i>Export
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import me-1"></i>Import
                            </button>
                        </div>
                        <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Monitor
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('monitors.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by asset name, tag, size, or resolution..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="panel_type" class="form-control searchable-select">
                                        <option value="">All Panel Types</option>
                                        <option value="LCD" {{ request('panel_type') == 'LCD' ? 'selected' : '' }}>LCD</option>
                                        <option value="LED" {{ request('panel_type') == 'LED' ? 'selected' : '' }}>LED</option>
                                        <option value="OLED" {{ request('panel_type') == 'OLED' ? 'selected' : '' }}>OLED</option>
                                        <option value="CRT" {{ request('panel_type') == 'CRT' ? 'selected' : '' }}>CRT</option>
                                        <option value="Plasma" {{ request('panel_type') == 'Plasma' ? 'selected' : '' }}>Plasma</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Monitors Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">Asset Tag</th>
                                    <th class="fw-semibold">Asset Name</th>
                                    <th class="fw-semibold">Size</th>
                                    <th class="fw-semibold">Resolution</th>
                                    <th class="fw-semibold">Panel Type</th>
                                    <th class="fw-semibold">Status</th>
                                    <th class="fw-semibold">Movement</th>
                                    <th class="fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monitors as $monitor)
                                    <tr class="border-bottom">
                                        <td class="fw-bold text-primary">{{ $monitor->asset->asset_tag }}</td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $monitor->asset->name }}</div>
                                                @if($monitor->asset->assignedUser)
                                                    <small class="text-muted">Assigned to: {{ $monitor->asset->assignedUser->name }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tv text-info me-2"></i>
                                                <span class="fw-medium">{{ $monitor->size }}</span>
                                            </div>
                                        </td>
                                        <td class="fw-medium">{{ $monitor->resolution }}</td>
                                        <td>
                                            <span class="badge badge-enhanced bg-info">{{ $monitor->panel_type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-enhanced bg-{{ $monitor->asset->status == 'Available' ? 'success' : ($monitor->asset->status == 'In Use' ? 'primary' : 'warning') }}">
                                                {{ $monitor->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-enhanced bg-secondary">
                                                {{ $monitor->asset->movement === 'Deployed Tagged' ? 'Deployed' : $monitor->asset->movement }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('view_monitors')
                                                <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="View Monitor" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_monitors')
                                                <a href="{{ route('monitors.edit', $monitor) }}" class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center" title="Edit Monitor" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_monitors')
                                                <form action="{{ route('monitors.destroy', $monitor) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this monitor? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" title="Delete Monitor" style="width: 32px; height: 32px;">
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
                                            <i class="fas fa-tv fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No monitors found.</p>
                                            <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add First Monitor
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($monitors->hasPages())
                        <div class="pagination-wrapper mt-3">
                            {{ $monitors->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Monitors
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'monitors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'monitors') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status, size, resolution, panel_type, refresh_rate</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Monitors
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection