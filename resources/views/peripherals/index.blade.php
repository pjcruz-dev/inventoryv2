@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Peripherals') }}</span>
                    <a href="{{ route('peripherals.create') }}" class="btn btn-primary btn-sm">
                        {{ __('Add New Peripheral') }}
                    </a>
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
                                        <td><span class="badge bg-primary">{{ $peripheral->connectivity }}</span></td>
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