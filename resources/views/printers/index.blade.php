@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Printers</h5>
                            <small class="text-white-50">{{ $printers->total() }} total printers</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('printers.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>Add New Printer
                                </a>
                                <a href="{{ route('printers.bulk-create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-layer-group me-1"></i>Bulk Create
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('printers.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search printers..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
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

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Search Section -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('printers.index') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by asset tag, name..." 
                                               value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 2px solid #667eea;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Printers Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">Asset Tag</th>
                                    <th class="fw-semibold">Name</th>
                                    <th class="fw-semibold">Type</th>
                                    <th class="fw-semibold">Color Support</th>
                                    <th class="fw-semibold">Network Enabled</th>
                                    <th class="fw-semibold">Duplex Printing</th>
                                    <th class="fw-semibold">Status</th>
                                    <th class="fw-semibold">Movement</th>
                                    <th class="fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($printers as $printer)
                                    <tr class="border-bottom">
                                        <td>
                                            <span class="badge bg-secondary text-white fw-bold px-2 py-1">{{ $printer->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $printer->asset->name }}</div>
                                            @if($printer->asset->user)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-user me-1"></i>{{ $printer->asset->user->name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white px-2 py-1">{{ $printer->type }}</span>
                                        </td>
                                        <td>
                                            @if($printer->color_support)
                                                <span class="badge bg-success text-white px-2 py-1">
                                                    <i class="fas fa-palette me-1"></i>Color
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white px-2 py-1">
                                                    <i class="fas fa-circle me-1"></i>Monochrome
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($printer->network_enabled)
                                                <span class="badge bg-success text-white px-2 py-1">
                                                    <i class="fas fa-wifi me-1"></i>Yes
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white px-2 py-1">
                                                    <i class="fas fa-times me-1"></i>No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($printer->duplex_printing)
                                                <span class="badge bg-success text-white px-2 py-1">
                                                    <i class="fas fa-copy me-1"></i>Yes
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white px-2 py-1">
                                                    <i class="fas fa-times me-1"></i>No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $printer->asset->status == 'Available' ? 'success' : ($printer->asset->status == 'In Use' ? 'primary' : 'warning') }} px-2 py-1">
                                                {{ $printer->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white px-2 py-1">
                                                {{ str_replace('Deployed Tagged', 'Deployed', $printer->asset->movement) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('printers.show', $printer) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" 
                                                   title="View Printer Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('printers.edit', $printer) }}" 
                                                   class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" 
                                                   title="Edit Printer">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('printers.destroy', $printer) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to permanently delete this printer? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" 
                                                            title="Delete Printer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-print fa-3x mb-3"></i>
                                                <h5>No printers found</h5>
                                                <p>{{ request()->hasAny(['search', 'type', 'color_support']) ? 'Try adjusting your search criteria.' : 'Start by adding your first printer.' }}</p>
                                                @if(!request()->hasAny(['search', 'type', 'color_support']))
                                                    <a href="{{ route('printers.create') }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-plus"></i> Add First Printer
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
                    @if($printers->hasPages())
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        Showing {{ $printers->firstItem() }} to {{ $printers->lastItem() }} of {{ $printers->total() }} results
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        {{ $printers->links('pagination::bootstrap-5') }}
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
$(document).ready(function() {
    
    // Focus search input
    $('#search').focus();
});
</script>
@endpush