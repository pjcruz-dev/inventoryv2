@extends('layouts.app')

@section('title', 'Vendors')
@section('page-title', 'Vendors')

@section('page-actions')
    <div class="d-flex gap-2">
        <div class="btn-group" role="group">
            <a href="{{ route('import-export.template', 'vendors') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Template
            </a>
            <a href="{{ route('import-export.export', 'vendors') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Import
            </button>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Vendor
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
    --soft-shadow: 0 2px 15px rgba(0,0,0,0.08);
    --soft-shadow-lg: 0 10px 40px rgba(0,0,0,0.1);
    --border-radius-sm: 0.5rem;
    --border-radius-md: 0.75rem;
    --border-radius-lg: 1rem;
    --border-radius-xl: 1.5rem;
}

.card-modern {
    border: none;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--soft-shadow);
    transition: all 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--soft-shadow-lg);
}

.filter-container {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow: var(--soft-shadow);
}

.table-modern {
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--soft-shadow);
}

.table-modern thead th {
    background: var(--gradient-primary);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem 0.75rem;
    font-size: 0.875rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-action {
    border-radius: var(--border-radius-sm);
    padding: 0.375rem 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<!-- Vendor Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-modern text-white" style="background: var(--gradient-primary);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $vendors->total() }}</h4>
                        <small class="opacity-90">Total Vendors</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern text-white" style="background: var(--gradient-success);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $vendors->sum(function($vendor) { return $vendor->assets->count(); }) }}</h4>
                        <small class="opacity-90">Total Assets</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-boxes fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern text-white" style="background: var(--gradient-info);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $vendors->filter(function($vendor) { return $vendor->assets->count() > 0; })->count() }}</h4>
                        <small class="opacity-90">Active Vendors</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-handshake fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern text-white" style="background: var(--gradient-warning);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">₱{{ number_format($vendors->sum(function($vendor) { return $vendor->assets->sum('cost'); }), 0) }}</h4>
                        <small class="opacity-90">Total Value</small>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="filter-container mb-4">
    <form method="GET" action="{{ route('vendors.index') }}" id="searchForm">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-primary"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search vendors by name, contact person, email, phone, or address..."
                           style="border-radius: 0 var(--border-radius-md) var(--border-radius-md) 0;">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="background: var(--gradient-primary); border: none; border-radius: var(--border-radius-md);">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: var(--border-radius-md);">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Vendors Table -->
<div class="card card-modern">
    <div class="card-header" style="background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%); border-bottom: 1px solid #e9ecef;">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="fas fa-building me-2 text-primary"></i>All Vendors</h6>
            <span class="status-badge" style="background: var(--gradient-primary); color: white;">{{ $vendors->total() }} total</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($vendors->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-building me-2"></i>Vendor Name</th>
                            <th><i class="fas fa-user me-2"></i>Contact Person</th>
                            <th><i class="fas fa-address-book me-2"></i>Contact Info</th>
                            <th><i class="fas fa-map-marker-alt me-2"></i>Location</th>
                            <th><i class="fas fa-boxes me-2"></i>Assets</th>
                            <th><i class="fas fa-dollar-sign me-2"></i>Total Value</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="background: var(--gradient-primary); width: 45px; height: 45px; font-weight: 600;">
                                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">
                                                <a href="{{ route('vendors.show', $vendor) }}" class="text-decoration-none text-dark">
                                                    {{ $vendor->name }}
                                                </a>
                                            </h6>
                                            <small class="status-badge" style="background: #e9ecef; color: #6c757d;">ID: {{ $vendor->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $vendor->contact_person }}</div>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($vendor->email)
                                            <div class="mb-1">
                                                <span class="status-badge" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1976d2;">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">
                                                        {{ Str::limit($vendor->email, 20) }}
                                                    </a>
                                                </span>
                                            </div>
                                        @endif
                                        @if($vendor->phone)
                                            <div>
                                                <span class="status-badge" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); color: #388e3c;">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">
                                                        {{ $vendor->phone }}
                                                    </a>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($vendor->address)
                                        <span class="status-badge" style="background: linear-gradient(135deg, #fff3e0 0%, #ffcc02 30%); color: #f57c00;">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($vendor->address, 25) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-badge" style="background: var(--gradient-info); color: white;">
                                            <i class="fas fa-boxes me-1"></i>{{ $vendor->assets->count() }}
                                        </span>
                                        @if($vendor->assets->count() > 0)
                                            <a href="{{ route('assets.index', ['vendor' => $vendor->id]) }}" 
                                               class="btn btn-action btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($vendor->assets->sum('cost') > 0)
                                        <span class="status-badge" style="background: var(--gradient-success); color: white;">
                                            <i class="fas fa-peso-sign me-1"></i>{{ number_format($vendor->assets->sum('cost'), 0) }}
                                        </span>
                                    @else
                                        <span class="text-muted">₱0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vendors.show', $vendor) }}" 
                                           class="btn btn-action btn-outline-primary btn-sm" 
                                           title="View Vendor Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', $vendor) }}" 
                                           class="btn btn-action btn-outline-warning btn-sm" 
                                           title="Edit Vendor Information">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('vendors.destroy', $vendor) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to permanently delete this vendor? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-action btn-outline-danger btn-sm" 
                                                    title="Delete Vendor"
                                                    {{ $vendor->assets->count() > 0 ? 'disabled' : '' }}>
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
            
            <!-- Pagination -->
            @if($vendors->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
                        </div>
                        {{ $vendors->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                         style="width: 80px; height: 80px; background: var(--gradient-primary);">
                        <i class="fas fa-building fa-2x text-white"></i>
                    </div>
                </div>
                <h5 class="text-muted mb-3">No Vendors Found</h5>
                @if(request('search'))
                    <p class="text-muted mb-4">No vendors match your search criteria "<strong>{{ request('search') }}</strong>".</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Search
                        </a>
                        <a href="{{ route('vendors.create') }}" class="btn btn-primary" style="background: var(--gradient-primary); border: none;">
                            <i class="fas fa-plus me-2"></i>Add New Vendor
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-4">Get started by adding your first vendor to the system.</p>
                    <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-lg" style="background: var(--gradient-primary); border: none; border-radius: var(--border-radius-md);">
                        <i class="fas fa-plus me-2"></i>Add First Vendor
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
    border-color: #f1f3f4;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.opacity-75 {
    opacity: 0.75;
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
                searchTimeout = setTimeout(function() {
                    if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
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
                    <i class="fas fa-file-import me-2"></i>Import Vendors
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'vendors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold"><i class="fas fa-file-csv me-1"></i>Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required 
                               style="border-radius: var(--border-radius-md); padding: 0.75rem;">
                        <div class="form-text mt-2">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'vendors') }}" class="text-decoration-none fw-semibold">
                                <i class="fas fa-download me-1"></i>Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-radius: var(--border-radius-md);">
                        <h6 class="fw-semibold"><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: name, contact_person, email, phone, address, website</li>
                            <li>Vendor names must be unique</li>
                            <li>Email format must be valid</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: var(--border-radius-md);">Cancel</button>
                    <button type="submit" class="btn btn-warning" style="background: var(--gradient-warning); border: none; border-radius: var(--border-radius-md);">
                        <i class="fas fa-file-import me-2"></i>Import Vendors
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection