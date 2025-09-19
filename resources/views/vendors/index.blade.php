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
<!-- Vendor Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $vendors->total() }}</h4>
                        <small>Total Vendors</small>
                    </div>
                    <i class="fas fa-building fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $vendors->sum(function($vendor) { return $vendor->assets->count(); }) }}</h4>
                        <small>Total Assets</small>
                    </div>
                    <i class="fas fa-boxes fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $vendors->filter(function($vendor) { return $vendor->assets->count() > 0; })->count() }}</h4>
                        <small>Active Vendors</small>
                    </div>
                    <i class="fas fa-handshake fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">₱{{ number_format($vendors->sum(function($vendor) { return $vendor->assets->sum('cost'); }), 0) }}</h4>
                        <small>Total Value</small>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('vendors.index') }}" id="searchForm">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search vendors by name, contact person, email, phone, or address...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Vendors Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">All Vendors</h6>
            <span class="badge bg-secondary">{{ $vendors->total() }} total</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($vendors->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vendor Name</th>
                            <th>Contact Person</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Assets</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                <a href="{{ route('vendors.show', $vendor) }}" class="text-decoration-none">
                                                    {{ $vendor->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">ID: {{ $vendor->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $vendor->contact_person }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($vendor->email)
                                            <div class="mb-1">
                                                <i class="fas fa-envelope text-primary me-1"></i>
                                                <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">
                                                    {{ $vendor->email }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($vendor->phone)
                                            <div>
                                                <i class="fas fa-phone text-success me-1"></i>
                                                <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">
                                                    {{ $vendor->phone }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($vendor->address)
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($vendor->address, 30) }}
                                        </small>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">{{ $vendor->assets->count() }}</span>
                                        @if($vendor->assets->count() > 0)
                                            <a href="{{ route('assets.index', ['vendor' => $vendor->id]) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($vendor->assets->sum('cost') > 0)
                                        <strong class="text-success">
                                            ₱{{ number_format($vendor->assets->sum('cost'), 2) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">₱0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vendors.show', $vendor) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', $vendor) }}" 
                                           class="btn btn-outline-warning btn-sm" 
                                           title="Edit Vendor">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('vendors.destroy', $vendor) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this vendor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
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
                    <div class="pagination-wrapper">
                        {{ $vendors->appends(request()->query())->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Vendors Found</h5>
                @if(request('search'))
                    <p class="text-muted mb-3">No vendors match your search criteria.</p>
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-times me-2"></i>Clear Search
                    </a>
                @else
                    <p class="text-muted mb-3">Get started by adding your first vendor to the system.</p>
                @endif
                <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Vendor
                </a>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Vendors
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import-export.import', 'vendors') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Please upload a CSV file with the correct format. 
                            <a href="{{ route('import-export.template', 'vendors') }}" class="text-decoration-none">
                                Download template
                            </a> if you need the correct format.
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Import Guidelines:</h6>
                        <ul class="mb-0">
                            <li>CSV must include: name, contact_person, email, phone, address, website</li>
                            <li>Vendor names must be unique</li>
                            <li>Email format must be valid</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import me-2"></i>Import Vendors
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection