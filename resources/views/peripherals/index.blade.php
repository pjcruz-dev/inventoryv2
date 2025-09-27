@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Peripherals') }}</span>
                    <div class="btn-group" role="group">
                        <a href="{{ route('peripherals.bulk-create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle me-1"></i>{{ __('Bulk Create') }}
                        </a>
                        <a href="{{ route('peripherals.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter me-2"></i>Search & Filter
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('peripherals.index') }}" class="row g-3">
                                        <!-- Search Input -->
                                        <div class="col-md-4">
                                            <label for="search" class="form-label">Search</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="search" 
                                                   name="search" 
                                                   placeholder="Search by asset name, tag, serial, type, or interface..."
                                                   value="{{ request('search') }}">
                                        </div>
                                        
                                        <!-- Type Filter -->
                                        <div class="col-md-2">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="">All Types</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!-- Interface Filter -->
                                        <div class="col-md-2">
                                            <label for="interface" class="form-label">Interface</label>
                                            <select class="form-select" id="interface" name="interface">
                                                <option value="">All Interfaces</option>
                                                @foreach($interfaces as $interface)
                                                    <option value="{{ $interface }}" {{ request('interface') == $interface ? 'selected' : '' }}>
                                                        {{ $interface }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!-- Assignment Status Filter -->
                                        <div class="col-md-2">
                                            <label for="assignment_status" class="form-label">Assignment</label>
                                            <select class="form-select" id="assignment_status" name="assignment_status">
                                                <option value="">All</option>
                                                <option value="assigned" {{ request('assignment_status') == 'assigned' ? 'selected' : '' }}>
                                                    Assigned
                                                </option>
                                                <option value="unassigned" {{ request('assignment_status') == 'unassigned' ? 'selected' : '' }}>
                                                    Unassigned
                                                </option>
                                            </select>
                                        </div>
                                        
                                        <!-- Department Filter -->
                                        <div class="col-md-2">
                                            <label for="department" class="form-label">Department</label>
                                            <select class="form-select" id="department" name="department">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                                        {{ $department }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!-- Filter Buttons -->
                                        <div class="col-12">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search me-1"></i>Apply Filters
                                                </button>
                                                <a href="{{ route('peripherals.index') }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-times me-1"></i>Clear Filters
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Summary -->
                    @if(request()->hasAny(['search', 'type', 'interface', 'assignment_status', 'department']))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Showing filtered results. 
                            @if($peripherals->total() > 0)
                                Found {{ $peripherals->total() }} peripheral(s) matching your criteria.
                            @else
                                No peripherals found matching your criteria.
                            @endif
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">{{ __('Asset Tag') }}</th>
                                    <th class="fw-semibold">{{ __('Asset Name') }}</th>
                                    <th class="fw-semibold">{{ __('Type') }}</th>
                                    <th class="fw-semibold">{{ __('Interface') }}</th>
                                    <th class="fw-semibold">{{ __('Assigned To') }}</th>
                                    <th class="fw-semibold">{{ __('Department') }}</th>
                                    <th class="fw-semibold text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($peripherals as $peripheral)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $peripheral->asset->asset_tag }}</span></td>
                                        <td class="fw-medium">{{ $peripheral->asset->name }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $peripheral->type }}</span></td>
                                        <td><span class="badge bg-primary">{{ $peripheral->interface }}</span></td>
                                        <td>{{ $peripheral->asset->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>{{ $peripheral->asset->department->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('view_peripherals')
                                                <a href="{{ route('peripherals.show', $peripheral) }}" class="btn btn-outline-info btn-sm" style="width: 32px; height: 32px;" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_peripherals')
                                                <a href="{{ route('peripherals.edit', $peripheral) }}" class="btn btn-outline-warning btn-sm" style="width: 32px; height: 32px;" title="Edit Peripheral">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_peripherals')
                                                <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this peripheral? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" style="width: 32px; height: 32px;" title="Delete Peripheral">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No peripherals found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($peripherals->hasPages())
                        <div class="pagination-wrapper">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Showing {{ $peripherals->firstItem() }} to {{ $peripherals->lastItem() }} of {{ $peripherals->total() }} peripherals
                                </div>
                                <div>
                                    {{ $peripherals->appends(request()->query())->links() }}
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

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when search input changes (with delay)
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            // Only auto-submit if search has at least 3 characters or is empty
            const searchValue = $('#search').val();
            if (searchValue.length >= 3 || searchValue.length === 0) {
                $('#search').closest('form').submit();
            }
        }, 500);
    });
    
    // Auto-submit form when select filters change
    $('#type, #interface, #assignment_status, #department').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Clear search on Escape key
    $('#search').on('keydown', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            $(this).closest('form').submit();
        }
    });
    
    // Focus search input with Ctrl+F
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('#search').focus();
        }
    });
});
</script>
@endsection