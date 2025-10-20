@extends('layouts.app')

@section('title', 'Asset Category: ' . $assetCategory->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Category Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tag me-2"></i>{{ $assetCategory->name }}
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('asset-categories.edit', $assetCategory) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('asset-categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Categories
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Description</h5>
                            <p class="text-muted">
                                {{ $assetCategory->description ?: 'No description provided.' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-1">{{ $assetCategory->assets->count() }}</h4>
                                        <small class="text-muted">Total Assets</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-1">{{ $assetCategory->assets->where('status', 'Available')->count() }}</h4>
                                        <small class="text-muted">Available</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Created:</strong> {{ $assetCategory->created_at->format('M d, Y') }}<br>
                                    <strong>Updated:</strong> {{ $assetCategory->updated_at->format('M d, Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assets in this Category -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>Assets in this Category ({{ $assetCategory->assets->count() }})
                    </h5>
                    @if($assetCategory->assets->count() > 0)
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-status" href="#" data-status="all">All Assets</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Available">Available</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Assigned">Assigned</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Under Maintenance">Under Maintenance</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Retired">Retired</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Damaged">Damaged</a></li>
                                <li><a class="dropdown-item filter-status" href="#" data-status="Disposed">Disposed</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    @if($assetCategory->assets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="assetsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Name</th>
                                        <th>Vendor</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Purchase Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetCategory->assets as $asset)
                                        <tr data-status="{{ $asset->status }}">
                                            <td>
                                                <strong>{{ $asset->asset_tag }}</strong>
                                            </td>
                                            <td>
                                                {{ $asset->name }}
                                                @if($asset->serial_number)
                                                    <br><small class="text-muted">SN: {{ $asset->serial_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $asset->vendor->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'Available' => 'success',
                                                        'Assigned' => 'primary',
                                                        'Under Maintenance' => 'warning',
                                                        'Disposed' => 'danger',
                                                        'Issue Reported' => 'danger'
                                                    ];
                                                    $color = $statusColors[$asset->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $asset->status }}</span>
                                            </td>
                                            <td>
                                                @if($asset->assignedUser)
                                                    {{ $asset->assignedUser->first_name }} {{ $asset->assignedUser->last_name }}
                                                    <br><small class="text-muted">{{ $asset->assignedUser->department->name ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('assets.show', $asset) }}" 
                                                       class="btn btn-outline-info" title="View Asset">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('assets.edit', $asset) }}" 
                                                       class="btn btn-outline-warning" title="Edit Asset">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-boxes fa-3x mb-3"></i>
                                <h5>No Assets in this Category</h5>
                                <p>No assets have been assigned to this category yet.</p>
                                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add First Asset
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Category Statistics -->
            @if($assetCategory->assets->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Category Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $statusCounts = $assetCategory->assets->groupBy('status')->map->count();
                                $totalValue = $assetCategory->assets->sum('cost');
                                $avgValue = $assetCategory->assets->avg('cost');
                            @endphp
                            
                            <div class="col-md-6">
                                <h6>Status Distribution</h6>
                                <div class="row">
                                    @foreach($statusCounts as $status => $count)
                                        <div class="col-6 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $status }}:</span>
                                                <strong>{{ $count }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Financial Summary</h6>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Value:</span>
                                            <strong>₱{{ number_format($totalValue, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Average Value:</span>
                                            <strong>₱{{ number_format($avgValue, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Assets:</span>
                                            <strong>{{ $assetCategory->assets->count() }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter functionality
    document.querySelectorAll('.filter-status').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            const rows = document.querySelectorAll('#assetsTable tbody tr');
            
            rows.forEach(function(row) {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update button text
            const button = document.querySelector('.dropdown-toggle');
            button.innerHTML = '<i class="fas fa-filter me-1"></i>' + (status === 'all' ? 'Filter' : status);
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush