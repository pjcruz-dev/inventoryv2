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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Asset Tag') }}</th>
                                    <th>{{ __('Asset Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Connectivity') }}</th>
                                    <th>{{ __('Assigned To') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($peripherals as $peripheral)
                                    <tr>
                                        <td>{{ $peripheral->asset->tag }}</td>
                                        <td>{{ $peripheral->asset->name }}</td>
                                        <td>{{ $peripheral->type }}</td>
                                        <td>{{ $peripheral->connectivity }}</td>
                                        <td>{{ $peripheral->asset->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>{{ $peripheral->asset->department->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('peripherals.show', $peripheral) }}" class="btn btn-info btn-sm">
                                                    {{ __('View') }}
                                                </a>
                                                <a href="{{ route('peripherals.edit', $peripheral) }}" class="btn btn-warning btn-sm">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form action="{{ route('peripherals.destroy', $peripheral) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this peripheral?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
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

                    <div class="d-flex justify-content-center">
                        {{ $peripherals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection