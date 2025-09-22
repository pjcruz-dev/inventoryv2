@extends('layouts.app')

@section('title', 'Asset Movements Detail')
@section('page-title', 'Asset Movements Detail')

@section('page-actions')
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        {{ $status }} Assets - {{ $week }} of {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                    </h5>
                    <span class="badge bg-primary fs-6">{{ $assets->count() }} Assets</span>
                </div>
                @if(config('app.debug'))
                <div class="mt-2">
                    <small class="text-muted">
                        Debug: Week {{ $week }}, Status: {{ $status }}, 
                        Date Range: {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}
                        <br>Found {{ $assets->count() }} assets matching the criteria
                    </small>
                </div>
                @endif
                <p class="text-muted mb-0 mt-2">
                    <i class="fas fa-calendar me-1"></i>
                    {{ $weekStart->format('M d, Y') }} - {{ $weekEnd->format('M d, Y') }}
                </p>
            </div>
            <div class="card-body">
                @if($assets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Vendor</th>
                                    <th>Assigned To</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $asset)
                                    <tr>
                                        <td>
                                            <code class="fs-6">{{ $asset->asset_tag }}</code>
                                        </td>
                                        <td>
                                            <strong>{{ $asset->name }}</strong>
                                            @if($asset->model)
                                                <br><small class="text-muted">{{ $asset->model }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asset->category)
                                                <span class="badge bg-info">{{ $asset->category->name }}</span>
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asset->vendor)
                                                {{ $asset->vendor->name }}
                                            @else
                                                <span class="text-muted">No Vendor</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asset->assignedUser)
                                                <div>
                                                    <strong>{{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}</strong>
                                                    @if($asset->assignedUser->employee_id)
                                                        <br><small class="text-muted">{{ $asset->assignedUser->employee_id }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asset->department)
                                                {{ $asset->department->name }}
                                            @elseif($asset->assignedUser && $asset->assignedUser->department)
                                                {{ $asset->assignedUser->department->name }}
                                            @else
                                                <span class="text-muted">No Department</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $asset->status === 'active' ? 'success' : 
                                                ($asset->status === 'deployed' ? 'primary' : 
                                                ($asset->status === 'inactive' ? 'danger' : 
                                                ($asset->status === 'problematic' ? 'danger' : 
                                                ($asset->status === 'disposed' ? 'dark' : 
                                                ($asset->status === 'maintenance' ? 'warning' : 
                                                ($asset->status === 'pending_confirm' ? 'info' : 
                                                ($asset->status === 'returned' ? 'secondary' : 
                                                ($asset->status === 'new_arrived' ? 'success' : 'warning'))))))))
                                            }} fs-6">
                                                {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $asset->created_at->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ $asset->created_at->format('g:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-primary" title="View Asset">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit_assets')
                                                <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-warning" title="Edit Asset">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No {{ strtolower($status) }} assets found</h5>
                        <p class="text-muted">No assets were {{ strtolower($status) }} during {{ $week }} of {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
