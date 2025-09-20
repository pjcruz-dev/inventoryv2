@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Peripherals') }}</span>
                    <div class="btn-group" role="group">
                        <a href="{{ route('peripherals.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
                        </a>
                        <a href="{{ route('peripherals.bulk-create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-layer-group me-1"></i>{{ __('Bulk Create') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">{{ __('Asset Tag') }}</th>
                                    <th class="fw-semibold">{{ __('Asset Name') }}</th>
                                    <th class="fw-semibold">{{ __('Type') }}</th>
                                    <th class="fw-semibold">{{ __('Connectivity') }}</th>
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
                                        <td>{{ $peripheral->asset->assignedUser ? $peripheral->asset->assignedUser->first_name . ' ' . $peripheral->asset->assignedUser->last_name : 'Unassigned' }}</td>
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

@push('styles')
<style>
/* Peripherals Index Styling */
.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Table improvements */
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

/* Badge styling */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Action buttons */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .card-header {
        padding: 1rem 0.75rem;
    }
}

/* Print styles */
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    
    .table {
        font-size: 0.75rem;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endpush