@extends('layouts.app')

@section('title', 'Vendors')
@section('page-title', 'Vendors')

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

<!-- Vendors Table -->
<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 text-white">All Vendors</h5>
                <small class="text-white-50">{{ $vendors->total() }} total vendors</small>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('vendors.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                        <i class="fas fa-plus me-1"></i>Add New Vendor
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="mt-3">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('vendors.index') }}" id="searchForm">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search vendors..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
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
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('vendors.show', $vendor) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vendors.edit', $vendor) }}" 
                                           class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" 
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
                                                    class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" 
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
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-muted">
                                Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} results
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                {{ $vendors->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
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
                <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
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

/* Pagination styling */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: #667eea;
    border: 1px solid #e9ecef;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #764ba2;
    background-color: #f8f9fa;
    border-color: #667eea;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #e9ecef;
}

.pagination .page-item:first-child .page-link {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.pagination .page-item:last-child .page-link {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .card-footer .row {
        text-align: center;
    }
    
    .card-footer .col-md-6:first-child {
        margin-bottom: 1rem;
    }
}
</style>
@endpush