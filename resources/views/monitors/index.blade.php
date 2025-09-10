@extends('layouts.app')

@section('title', 'Monitors')
@section('page-title', 'Monitors Management')

@section('page-actions')
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
            <i class="fas fa-plus me-2"></i>Add New Monitor
        </a>
    </div>
@endsection

@section('content')
<style>
/* Enhanced Monitors Module Styling */
.card-modern {
    border: 1px solid var(--border-color-light);
    border-radius: var(--border-radius-2xl);
    box-shadow: var(--soft-shadow-lg);
    backdrop-filter: blur(10px);
    background: var(--card-bg-gradient);
    transition: all 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--soft-shadow-xl), var(--soft-glow-primary);
}

.filter-container {
    background: var(--card-bg-gradient);
    border-radius: var(--border-radius-2xl);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--soft-shadow-lg);
    border: 1px solid var(--border-color-light);
}

.search-input, .filter-select {
    border-radius: var(--border-radius-xl);
    border: 1px solid var(--border-color-light);
    padding: 12px 16px;
    background: var(--input-bg-gradient);
    box-shadow: var(--soft-shadow-sm);
    transition: all 0.3s ease;
}

.search-input:focus, .filter-select:focus {
    border-color: var(--primary-500);
    box-shadow: var(--soft-shadow-md), var(--soft-glow-primary);
    background: var(--surface-color);
}

.table-modern {
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    box-shadow: var(--soft-shadow-md);
}

.table-modern thead {
    background: var(--gradient-primary);
    color: white;
}

.table-modern th {
    border: none;
    padding: 16px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table-modern td {
    border: none;
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color-light);
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background: var(--hover-bg-gradient);
    transform: scale(1.01);
}

.status-badge {
    border-radius: var(--border-radius-3xl);
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: var(--soft-shadow-sm);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.status-badge:hover::before {
    left: 100%;
}

.badge-success {
    background: var(--gradient-success);
    color: white;
    box-shadow: var(--soft-shadow-sm), 0 0 20px rgba(34, 197, 94, 0.3);
}

.badge-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--soft-shadow-sm), 0 0 20px rgba(59, 130, 246, 0.3);
}

.badge-warning {
    background: var(--gradient-warning);
    color: white;
    box-shadow: var(--soft-shadow-sm), 0 0 20px rgba(245, 158, 11, 0.3);
}

.badge-secondary {
    background: var(--gradient-secondary);
    color: white;
    box-shadow: var(--soft-shadow-sm), 0 0 20px rgba(107, 114, 128, 0.3);
}

.badge-info {
    background: var(--gradient-info);
    color: white;
    box-shadow: var(--soft-shadow-sm), 0 0 20px rgba(14, 165, 233, 0.3);
}

.btn-action {
    border-radius: var(--border-radius-xl);
    padding: 8px 12px;
    border: 1px solid var(--border-color-light);
    background: var(--surface-color);
    box-shadow: var(--soft-shadow-sm);
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: var(--soft-shadow-md);
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
    background: var(--card-bg-gradient);
    border-radius: var(--border-radius-2xl);
    border: 2px dashed var(--border-color-light);
}

.empty-state-icon {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1.5rem;
    opacity: 0.6;
}

.spec-chip {
    display: inline-block;
    background: var(--gradient-info);
    color: white;
    padding: 4px 8px;
    border-radius: var(--border-radius-lg);
    font-size: 0.75rem;
    font-weight: 500;
    margin: 2px;
    box-shadow: var(--soft-shadow-sm);
}

.monitor-size {
    display: inline-flex;
    align-items: center;
    background: var(--gradient-secondary);
    color: white;
    padding: 6px 10px;
    border-radius: var(--border-radius-lg);
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: var(--soft-shadow-sm);
}
</style>

<div class="filter-container">
    <form method="GET" action="{{ route('monitors.index') }}">
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold"><i class="fas fa-search me-2"></i>Search Monitors</label>
                <input type="text" name="search" class="form-control search-input" 
                       placeholder="Search by asset name, tag, size, or resolution..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fas fa-tv me-2"></i>Panel Type</label>
                <select name="panel_type" class="form-control filter-select">
                    <option value="">All Panel Types</option>
                    <option value="LCD" {{ request('panel_type') == 'LCD' ? 'selected' : '' }}>LCD</option>
                    <option value="LED" {{ request('panel_type') == 'LED' ? 'selected' : '' }}>LED</option>
                    <option value="OLED" {{ request('panel_type') == 'OLED' ? 'selected' : '' }}>OLED</option>
                    <option value="CRT" {{ request('panel_type') == 'CRT' ? 'selected' : '' }}>CRT</option>
                    <option value="Plasma" {{ request('panel_type') == 'Plasma' ? 'selected' : '' }}>Plasma</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="background: var(--gradient-primary); border: none; box-shadow: var(--soft-shadow-md);">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('monitors.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card card-modern">

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius-xl); border: none; box-shadow: var(--soft-shadow-md);">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th><i class="fas fa-tag me-2"></i>Asset Tag</th>
                        <th><i class="fas fa-tv me-2"></i>Asset Name</th>
                        <th><i class="fas fa-expand-arrows-alt me-2"></i>Size</th>
                        <th><i class="fas fa-desktop me-2"></i>Resolution</th>
                        <th><i class="fas fa-layer-group me-2"></i>Panel Type</th>
                        <th><i class="fas fa-signal me-2"></i>Status</th>
                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitors as $monitor)
                        <tr>
                            <td>
                                <span class="status-badge badge-secondary">
                                    <i class="fas fa-tag me-1"></i>{{ $monitor->asset->asset_tag }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $monitor->asset->name }}</div>
                                @if($monitor->asset->assignedUser)
                                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $monitor->asset->assignedUser->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="monitor-size">
                                    <i class="fas fa-tv me-1"></i>{{ $monitor->size }}
                                </span>
                            </td>
                            <td>
                                <span class="spec-chip">
                                    <i class="fas fa-desktop me-1"></i>{{ $monitor->resolution }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge badge-info">
                                    <i class="fas fa-layer-group me-1"></i>{{ $monitor->panel_type }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($monitor->asset->status) {
                                        'Available' => 'badge-success',
                                        'In Use' => 'badge-primary',
                                        'Maintenance' => 'badge-warning',
                                        'Disposed' => 'badge-secondary',
                                        default => 'badge-secondary'
                                    };
                                    $statusIcon = match($monitor->asset->status) {
                                        'Available' => 'fas fa-check-circle',
                                        'In Use' => 'fas fa-user',
                                        'Maintenance' => 'fas fa-tools',
                                        'Disposed' => 'fas fa-trash-alt',
                                        default => 'fas fa-question-circle'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="{{ $statusIcon }} me-1"></i>{{ $monitor->asset->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('monitors.show', $monitor) }}" class="btn btn-sm btn-action btn-outline-info" title="View Monitor Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('monitors.edit', $monitor) }}" class="btn btn-sm btn-action btn-outline-warning" title="Edit Monitor Information">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('monitors.destroy', $monitor) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to permanently delete this monitor from the inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action btn-outline-danger" title="Delete Monitor">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state text-center py-5">
                                    @if(request('search') || request('panel_type'))
                                        <i class="fas fa-search empty-state-icon"></i>
                                        <h5 class="text-muted">No monitors match your search</h5>
                                        <p class="text-muted mb-3">Try adjusting your search criteria or filters.</p>
                                        <a href="{{ route('monitors.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times"></i> Clear Search
                                        </a>
                                        <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add Monitor
                                        </a>
                                    @else
                                        <i class="fas fa-tv empty-state-icon"></i>
                                        <h5 class="text-muted">No monitors found</h5>
                                        <p class="text-muted mb-3">Get started by adding your first monitor to the inventory.</p>
                                        <a href="{{ route('monitors.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add First Monitor
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
                    <div class="d-flex justify-content-center">
                        {{ $monitors->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: var(--border-radius-xl); border: none; box-shadow: var(--soft-shadow-xl);">
            <div class="modal-header" style="background: var(--gradient-primary); color: white; border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Monitors
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'monitors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold">
                            <i class="fas fa-file-csv me-2 text-primary"></i>Select CSV File
                        </label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required 
                               style="border-radius: var(--border-radius-lg); border: 2px dashed var(--bs-border-color); padding: 12px;">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'monitors') }}" class="text-decoration-none text-primary fw-semibold">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info" style="border-radius: var(--border-radius-lg); border: none; background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);">
                        <h6 class="fw-semibold mb-3">
                            <i class="fas fa-info-circle me-2"></i>Import Guidelines:
                        </h6>
                        <ul class="mb-0 small">
                            <li class="mb-1"><strong>Required fields:</strong> asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status, size, resolution, panel_type, refresh_rate</li>
                            <li class="mb-1"><strong>Prerequisites:</strong> Category and vendor must exist in the system</li>
                            <li class="mb-1"><strong>Uniqueness:</strong> Asset tags must be unique across all assets</li>
                            <li class="mb-1"><strong>Date format:</strong> Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--bs-border-color); background: var(--bs-light); border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" style="background: var(--gradient-warning); border: none; box-shadow: var(--soft-shadow-md);">
                        <i class="fas fa-file-import me-2"></i>Import Monitors
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection