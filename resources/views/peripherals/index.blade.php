@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 text-white">All Peripherals</h5>
                            <small class="text-white-50">{{ $peripherals->total() }} total peripherals</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('peripherals.create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
                                </a>
                                <a href="{{ route('peripherals.bulk-create') }}" class="btn btn-light btn-sm" style="color: #667eea;">
                                    <i class="fas fa-layer-group me-1"></i>{{ __('Bulk Create') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('peripherals.index') }}" id="searchForm">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search peripherals..." value="{{ request('search') }}" style="border-radius: 6px 0 0 6px; border: 2px solid #e9ecef;">
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
                                            <div class="d-flex justify-content-center gap-2">
                                                @can('view_peripherals')
                                                <a href="{{ route('peripherals.show', $peripheral) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-view" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit_peripherals')
                                                <a href="{{ route('peripherals.edit', $peripheral) }}" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-edit" title="Edit Peripheral">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete_peripherals')
                                                <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this peripheral? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm d-flex align-items-center justify-content-center action-btn action-btn-delete" title="Delete Peripheral">
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
</style>
@endpush