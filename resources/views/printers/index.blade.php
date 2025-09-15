@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-print"></i> Printers Management
                    </h4>
                    <a href="{{ route('printers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Printer
                    </a>
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

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('printers.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search:</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Search by asset tag, name..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select name="type" id="type" class="form-control searchable-select">
                                        <option value="">All Types</option>
                                        <option value="Inkjet" {{ request('type') == 'Inkjet' ? 'selected' : '' }}>Inkjet</option>
                                        <option value="Laser" {{ request('type') == 'Laser' ? 'selected' : '' }}>Laser</option>
                                        <option value="Dot Matrix" {{ request('type') == 'Dot Matrix' ? 'selected' : '' }}>Dot Matrix</option>
                                        <option value="Thermal" {{ request('type') == 'Thermal' ? 'selected' : '' }}>Thermal</option>
                                        <option value="3D" {{ request('type') == '3D' ? 'selected' : '' }}>3D</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="color_support">Color Support:</label>
                                    <select name="color_support" id="color_support" class="form-control searchable-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('color_support') == '1' ? 'selected' : '' }}>Color</option>
                                        <option value="0" {{ request('color_support') == '0' ? 'selected' : '' }}>Monochrome</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(request()->hasAny(['search', 'type', 'color_support']))
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('printers.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>

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
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('printers.show', $printer) }}" 
                                                   class="btn btn-outline-info btn-sm d-flex align-items-center justify-content-center" 
                                                   style="width: 32px; height: 32px;" 
                                                   title="View Printer Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('printers.edit', $printer) }}" 
                                                   class="btn btn-outline-warning btn-sm d-flex align-items-center justify-content-center" 
                                                   style="width: 32px; height: 32px;" 
                                                   title="Edit Printer">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('printers.destroy', $printer) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to permanently delete this printer? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" 
                                                            style="width: 32px; height: 32px;" 
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
                                                    <a href="{{ route('printers.create') }}" class="btn btn-primary">
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
                        <div class="pagination-wrapper mt-3">
                            {{ $printers->appends(request()->query())->links('pagination.custom') }}
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