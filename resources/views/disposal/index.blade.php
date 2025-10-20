@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Asset Disposals</h5>
                            <small class="text-white-50">{{ $disposals->total() }} total disposals</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                @can('create_disposal')
                                <a href="{{ route('disposal.bulk-create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus-circle me-1"></i>Bulk Dispose
                                </a>
                                <a href="{{ route('disposal.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>Add New
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filter Section -->
                    <div class="mt-3">
                        <form method="GET" action="{{ route('disposal.index') }}" id="filterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search asset name, tag, or disposal type..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="disposal_type" id="disposal_type" class="form-select" style="border: 2px solid #e9ecef; border-radius: 6px;">
                                        <option value="">All Disposal Types</option>
                                        <option value="Damaged" {{ request('disposal_type') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                        <option value="Recycled" {{ request('disposal_type') == 'Recycled' ? 'selected' : '' }}>Recycled</option>
                                        <option value="Sold" {{ request('disposal_type') == 'Sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="Donated" {{ request('disposal_type') == 'Donated' ? 'selected' : '' }}>Donated</option>
                                        <option value="Lost" {{ request('disposal_type') == 'Lost' ? 'selected' : '' }}>Lost</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="disposal_date" class="form-control" placeholder="Disposal Date" value="{{ request('disposal_date') }}" style="border: 2px solid #e9ecef; border-radius: 6px;">
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 6px;">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('disposal.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 6px;">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- Results Summary -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                Showing {{ $disposals->firstItem() ?? 0 }} to {{ $disposals->lastItem() ?? 0 }} 
                                of {{ $disposals->total() }} disposal records
                            </p>
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
                                            <span class="badge {{ $disposal->disposal_type == 'Sold' ? 'bg-success' : ($disposal->disposal_type == 'Donated' ? 'bg-info' : ($disposal->disposal_type == 'Recycled' ? 'bg-warning' : ($disposal->disposal_type == 'Damaged' ? 'bg-danger' : ($disposal->disposal_type == 'Lost' ? 'bg-secondary' : 'bg-primary')))) }} text-white" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; font-weight: 500;">
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
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_disposal')
                                                    <a href="{{ route('disposal.show', $disposal) }}" 
                                                       class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('edit_disposal')
                                                    <a href="{{ route('disposal.edit', $disposal) }}" 
                                                       class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete_disposal')
                                                    <form action="{{ route('disposal.destroy', $disposal) }}" method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this disposal record?')">
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
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-0">No disposal records found.</p>
                                                @can('create_disposal')
                                                    <a href="{{ route('disposal.create') }}" class="btn btn-primary btn-sm mt-2">
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
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        Showing {{ $disposals->firstItem() }} to {{ $disposals->lastItem() }} of {{ $disposals->total() }} results
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        {{ $disposals->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
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

@push('scripts')
<script>
    // Auto-submit form when disposal type changes
    const disposalTypeElement = document.getElementById('disposal_type');
    if (disposalTypeElement) {
        disposalTypeElement.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Auto-submit form when disposal date changes
    const disposalDateElement = document.querySelector('input[name="disposal_date"]');
    if (disposalDateElement) {
        disposalDateElement.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Auto-submit form when search input changes (with debounce)
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500); // 500ms delay
        });
    }
    
    // Handle form submission
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Remove empty values to clean up URL
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            // Update URL without page reload
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.pushState({}, '', newUrl);
        });
    }
</script>
@endpush

@push('styles')
<style>
/* Custom badge styling for disposal types */
.badge {
    font-size: 0.75rem !important;
    padding: 0.25rem 0.5rem !important;
    font-weight: 500 !important;
    border-radius: 4px !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

.badge.bg-dark {
    background-color: #343a40 !important;
}

.badge.bg-primary {
    background-color: #007bff !important;
}

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