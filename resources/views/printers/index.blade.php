@extends('layouts.app')

@section('title', 'Printers Management')

@section('content')
<style>
:root {
    --border-radius-sm: 0.375rem;
    --border-radius-md: 0.5rem;
    --border-radius-lg: 0.75rem;
    --border-radius-xl: 1rem;
    --soft-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --soft-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --soft-shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --soft-shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --gradient-info: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.page-title {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 2rem;
}

.page-actions .btn {
    border-radius: var(--border-radius-lg);
    box-shadow: var(--soft-shadow-md);
    transition: all 0.3s ease;
}

.filter-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius-xl);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--soft-shadow-md);
}

.card-modern {
    border: none;
    border-radius: var(--border-radius-xl);
    box-shadow: var(--soft-shadow-lg);
    overflow: hidden;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title mb-0">
                        <i class="fas fa-print me-3"></i>Printers Management
                    </h1>
                    <p class="text-muted mb-0">Manage and track all printer assets in your inventory</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('printers.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add New Printer
                    </a>
                </div>
            </div>

            <!-- Filter Container -->
            <div class="filter-container">
                <form method="GET" action="{{ route('printers.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="fas fa-search me-2"></i>Search
                            </label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by asset tag, name..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label fw-semibold">
                                <i class="fas fa-print me-2"></i>Type
                            </label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Inkjet" {{ request('type') == 'Inkjet' ? 'selected' : '' }}>Inkjet</option>
                                <option value="Laser" {{ request('type') == 'Laser' ? 'selected' : '' }}>Laser</option>
                                <option value="Dot Matrix" {{ request('type') == 'Dot Matrix' ? 'selected' : '' }}>Dot Matrix</option>
                                <option value="Thermal" {{ request('type') == 'Thermal' ? 'selected' : '' }}>Thermal</option>
                                <option value="3D" {{ request('type') == '3D' ? 'selected' : '' }}>3D</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="color_support" class="form-label fw-semibold">
                                <i class="fas fa-palette me-2"></i>Color Support
                            </label>
                            <select name="color_support" id="color_support" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('color_support') == '1' ? 'selected' : '' }}>Color</option>
                                <option value="0" {{ request('color_support') == '0' ? 'selected' : '' }}>Monochrome</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                    @if(request()->hasAny(['search', 'type', 'color_support']))
                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('printers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            </div>
                        </div>
                    @endif
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
                        <th><i class="fas fa-print me-2"></i>Name</th>
                        <th><i class="fas fa-cogs me-2"></i>Type</th>
                        <th><i class="fas fa-palette me-2"></i>Color Support</th>
                        <th><i class="fas fa-wifi me-2"></i>Network</th>
                        <th><i class="fas fa-copy me-2"></i>Duplex</th>
                        <th><i class="fas fa-signal me-2"></i>Status</th>
                        <th><i class="fas fa-tools me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($printers as $printer)
                        <tr>
                            <td>
                                <span class="status-badge badge-secondary">
                                    <i class="fas fa-tag me-1"></i>{{ $printer->asset->asset_tag }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $printer->asset->name }}</div>
                                @if($printer->asset->user)
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $printer->asset->user->name }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge badge-info">
                                    <i class="fas fa-print me-1"></i>{{ $printer->type }}
                                </span>
                            </td>
                            <td>
                                @if($printer->color_support)
                                    <span class="status-badge badge-success">
                                        <i class="fas fa-palette me-1"></i>Color
                                    </span>
                                @else
                                    <span class="status-badge badge-secondary">
                                        <i class="fas fa-circle me-1"></i>Monochrome
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($printer->network_enabled)
                                    <span class="status-badge badge-success">
                                        <i class="fas fa-wifi me-1"></i>Yes
                                    </span>
                                @else
                                    <span class="status-badge badge-secondary">
                                        <i class="fas fa-ethernet me-1"></i>No
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($printer->duplex_printing)
                                    <span class="status-badge badge-success">
                                        <i class="fas fa-copy me-1"></i>Yes
                                    </span>
                                @else
                                    <span class="status-badge badge-secondary">
                                        <i class="fas fa-ban me-1"></i>No
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match($printer->asset->status) {
                                        'Available' => 'badge-success',
                                        'In Use' => 'badge-primary',
                                        'Maintenance' => 'badge-warning',
                                        'Disposed' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    $statusIcon = match($printer->asset->status) {
                                        'Available' => 'fas fa-check-circle',
                                        'In Use' => 'fas fa-user',
                                        'Maintenance' => 'fas fa-tools',
                                        'Disposed' => 'fas fa-trash-alt',
                                        default => 'fas fa-question-circle'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="{{ $statusIcon }} me-1"></i>{{ $printer->asset->status }}
                                </span>
                            </td>
                                        <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('printers.show', $printer) }}" 
                                       class="btn btn-info btn-sm btn-action" title="View Printer Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('printers.edit', $printer) }}" 
                                       class="btn btn-warning btn-sm btn-action" title="Edit Printer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('printers.destroy', $printer) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this printer? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-action" title="Delete Printer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-print fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
                                    @if(request()->hasAny(['search', 'type', 'color_support']))
                                        <h5 class="text-muted mb-3">No printers match your search</h5>
                                        <p class="text-muted mb-4">Try adjusting your search criteria or clear the filters.</p>
                                        <a href="{{ route('printers.index') }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times me-1"></i>Clear Search
                                        </a>
                                    @else
                                        <h5 class="text-muted mb-3">No printers found</h5>
                                        <p class="text-muted mb-4">Get started by adding your first printer to the inventory.</p>
                                    @endif
                                    <a href="{{ route('printers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add First Printer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($printers->hasPages())
             <div class="d-flex justify-content-between align-items-center mt-4">
                 <div>
                     <p class="text-muted mb-0">
                         Showing {{ $printers->firstItem() }} to {{ $printers->lastItem() }} of {{ $printers->total() }} results
                     </p>
                 </div>
                 <div>
                     {{ $printers->appends(request()->query())->links() }}
                 </div>
             </div>
         @endif
     </div>
 </div>
</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#type, #color_support').change(function() {
        $(this).closest('form').submit();
    });
    
    // Focus search input
    $('#search').focus();
});
</script>
@endpush