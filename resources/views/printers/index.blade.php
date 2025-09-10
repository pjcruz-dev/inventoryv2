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
                                    <select name="type" id="type" class="form-control">
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
                                    <select name="color_support" id="color_support" class="form-control">
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
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Color Support</th>
                                    <th>Network Enabled</th>
                                    <th>Duplex Printing</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($printers as $printer)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">{{ $printer->asset->asset_tag }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $printer->asset->name }}</strong>
                                            @if($printer->asset->user)
                                                <br><small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $printer->asset->user->name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $printer->type }}</span>
                                        </td>
                                        <td>
                                            @if($printer->color_support)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-palette"></i> Color
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-circle"></i> Monochrome
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($printer->network_enabled)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-wifi"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($printer->duplex_printing)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-copy"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $printer->asset->status == 'Available' ? 'success' : ($printer->asset->status == 'In Use' ? 'primary' : 'warning') }}">
                                                {{ $printer->asset->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('printers.show', $printer) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('printers.edit', $printer) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('printers.destroy', $printer) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this printer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
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