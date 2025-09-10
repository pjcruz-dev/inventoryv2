@extends('layouts.app')

@section('title', 'Computers')
@section('page-title', 'Computers Management')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('import-export.template', 'computers') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Template
            </a>
            <a href="{{ route('import-export.export', 'computers') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Import
            </button>
        </div>
        <a href="{{ route('computers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Computer
        </a>
    </div>
@endsection

@section('content')
<style>
/* Enhanced Computers Module Styling */
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
</style>

<div class="filter-container">
    <form method="GET" action="{{ route('computers.index') }}">
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold"><i class="fas fa-search me-2"></i>Search Computers</label>
                <input type="text" name="search" class="form-control search-input" 
                       placeholder="Search by asset name, tag, processor, or memory..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="fas fa-microchip me-2"></i>Processor</label>
                <select name="processor" class="form-control filter-select">
                    <option value="">All Processors</option>
                    <option value="Intel" {{ request('processor') == 'Intel' ? 'selected' : '' }}>Intel</option>
                    <option value="AMD" {{ request('processor') == 'AMD' ? 'selected' : '' }}>AMD</option>
                    <option value="Apple" {{ request('processor') == 'Apple' ? 'selected' : '' }}>Apple</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="background: var(--gradient-primary); border: none; box-shadow: var(--soft-shadow-md);">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('computers.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th><i class="fas fa-desktop me-2"></i>Asset Name</th>
                        <th><i class="fas fa-microchip me-2"></i>Processor</th>
                        <th><i class="fas fa-memory me-2"></i>Memory</th>
                        <th><i class="fas fa-hdd me-2"></i>Storage</th>
                        <th><i class="fas fa-signal me-2"></i>Status</th>
                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($computers as $computer)
                        <tr>
                            <td>
                                <span class="status-badge badge-secondary">
                                    <i class="fas fa-tag me-1"></i>{{ $computer->asset->asset_tag }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $computer->asset->name }}</div>
                                @if($computer->asset->user)
                                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $computer->asset->user->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="spec-chip">
                                    <i class="fas fa-microchip me-1"></i>{{ $computer->processor }}
                                </span>
                            </td>
                            <td>
                                <span class="spec-chip">
                                    <i class="fas fa-memory me-1"></i>{{ $computer->memory }}
                                </span>
                            </td>
                            <td>
                                <span class="spec-chip">
                                    <i class="fas fa-hdd me-1"></i>{{ $computer->storage }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge badge-{{ $computer->asset->status == 'Available' ? 'success' : ($computer->asset->status == 'In Use' ? 'primary' : 'warning') }}">
                                    @if($computer->asset->status == 'Available')
                                        <i class="fas fa-check-circle me-1"></i>
                                    @elseif($computer->asset->status == 'In Use')
                                        <i class="fas fa-user-check me-1"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                    @endif
                                    {{ $computer->asset->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('computers.show', $computer) }}" class="btn btn-sm btn-action btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('computers.edit', $computer) }}" class="btn btn-sm btn-action btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('computers.destroy', $computer) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this computer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-desktop"></i>
                                    </div>
                                    <h5 class="text-muted mb-3">No Computers Found</h5>
                                    <p class="text-muted mb-4">Get started by adding your first computer to the inventory.</p>
                                    <a href="{{ route('computers.create') }}" class="btn btn-primary" style="background: var(--gradient-primary); border: none; box-shadow: var(--soft-shadow-md);">
                                        <i class="fas fa-plus me-2"></i>Add First Computer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $computers->appends(request()->query())->links() }}
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: var(--border-radius-2xl); border: 1px solid var(--border-color-light); box-shadow: var(--soft-shadow-xl);">
            <div class="modal-header" style="background: var(--gradient-primary); color: white; border-radius: var(--border-radius-2xl) var(--border-radius-2xl) 0 0; border-bottom: none;">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Computers
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'computers') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold">
                            <i class="fas fa-file-csv me-2"></i>Select CSV File
                        </label>
                        <input type="file" class="form-control search-input" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text mt-2">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'computers') }}" class="text-decoration-none fw-semibold">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info" style="border-radius: var(--border-radius-xl); border: none; background: var(--gradient-info); color: white; box-shadow: var(--soft-shadow-md);">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: asset_tag, category_name, vendor_name, name, description, serial_number, purchase_date, warranty_end, cost, status, processor, ram, storage, os</li>
                            <li>Category and vendor must exist in the system</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                        </ul>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('import-export.template', 'computers') }}" class="btn btn-outline-success" style="border-radius: var(--border-radius-xl); box-shadow: var(--soft-shadow-sm);">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color-light); background: var(--surface-color); border-radius: 0 0 var(--border-radius-2xl) var(--border-radius-2xl); padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: var(--border-radius-xl);">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" style="background: var(--gradient-warning); border: none; border-radius: var(--border-radius-xl); box-shadow: var(--soft-shadow-md);">
                        <i class="fas fa-upload me-2"></i>Import Computers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection