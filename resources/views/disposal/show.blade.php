@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-trash-alt me-2"></i>Disposal Record #{{ $disposal->id }}
                        </h3>
                        <div>
                            <span class="badge badge-{{ $disposal->disposal_type == 'Sold' ? 'success' : ($disposal->disposal_type == 'Donated' ? 'info' : ($disposal->disposal_type == 'Recycled' ? 'warning' : 'danger')) }} fs-6">
                                {{ $disposal->disposal_type }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Asset Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-desktop me-2"></i>Asset Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($disposal->asset->image)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset('storage/' . $disposal->asset->image) }}" 
                                                 alt="{{ $disposal->asset->name }}" 
                                                 class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                        </div>
                                    @endif
                                    
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="35%">Asset Name:</td>
                                            <td>{{ $disposal->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Asset Tag:</td>
                                            <td><span class="badge bg-info">{{ $disposal->asset->asset_tag }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Model:</td>
                                            <td>{{ $disposal->asset->model->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Category:</td>
                                            <td>{{ $disposal->asset->model->category->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Serial Number:</td>
                                            <td>{{ $disposal->asset->serial ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Purchase Cost:</td>
                                            <td>
                                                @if($disposal->asset->purchase_cost)
                                                    ₱{{ number_format($disposal->asset->purchase_cost, 2) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Current Status:</td>
                                            <td>
                                                <span class="badge badge-{{ $disposal->asset->status_label->color ?? 'secondary' }}">
                                                    {{ $disposal->asset->status_label->name ?? $disposal->asset->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Disposal Details -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Disposal Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="35%">Disposal Date:</td>
                                            <td>{{ $disposal->disposal_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Disposal Type:</td>
                                            <td>
                                                <span class="badge badge-{{ $disposal->disposal_type == 'Sold' ? 'success' : ($disposal->disposal_type == 'Donated' ? 'info' : ($disposal->disposal_type == 'Recycled' ? 'warning' : 'danger')) }}">
                                                    {{ $disposal->disposal_type }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Disposal Value:</td>
                                            <td>
                                                @if($disposal->disposal_value)
                                                    <span class="text-success fw-bold">@currency($disposal->disposal_value)</span>
                                                @else
                                                    <span class="text-muted">No monetary value</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="fw-bold">Financial Impact:</td>
                                            <td>
                                                @php
                                                    $purchaseCost = $disposal->asset->purchase_cost ?? 0;
                                                    $disposalValue = $disposal->disposal_value ?? 0;
                                                    $loss = $purchaseCost - $disposalValue;
                                                @endphp
                                                
                                                @if($purchaseCost > 0)
                                                    <div>
                                                        <small class="text-muted">Purchase Cost: @currency($purchaseCost)</small><br>
                                                        <small class="text-muted">Disposal Value: @currency($disposalValue)</small><br>
                                                        <strong class="{{ $loss > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ $loss > 0 ? 'Loss' : 'Gain' }}: @currency(abs($loss))
                                                        </strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Cannot calculate (no purchase cost)</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remarks -->
                    @if($disposal->remarks)
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Remarks</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $disposal->remarks }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Asset Location & Assignment History -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location & Assignment</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="35%">Location:</td>
                                            <td>{{ $disposal->asset->location->name ?? 'Not assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Assigned To:</td>
                                            <td>{{ $disposal->asset->assignedTo->name ?? 'Not assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Department:</td>
                                            <td>{{ $disposal->asset->assignedTo->department->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Record Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="35%">Created:</td>
                                            <td>{{ $disposal->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Created By:</td>
                                            <td>{{ $disposal->createdBy->name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Last Updated:</td>
                                            <td>{{ $disposal->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Updated By:</td>
                                            <td>{{ $disposal->updatedBy->name ?? 'System' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Related Records -->
                    @if($disposal->asset && $disposal->asset->maintenance->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance History (Last 5 Records)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Issue</th>
                                                    <th>Status</th>
                                                    <th>Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($disposal->asset->maintenance->take(5) as $maintenance)
                                                    <tr>
                                                        <td>{{ $maintenance->start_date ? $maintenance->start_date->format('M d, Y') : 'N/A' }}</td>
                                                        <td>{{ Str::limit($maintenance->issue_reported, 50) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $maintenance->status == 'Completed' ? 'success' : ($maintenance->status == 'In Progress' ? 'warning' : 'info') }}">
                                                                {{ $maintenance->status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($maintenance->cost)
                                                                @currency($maintenance->cost)
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('disposal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <div>
                            @can('edit_disposal')
                                <a href="{{ route('disposal.edit', $disposal) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            @endcan
                            
                            @can('delete_disposal')
                                <form action="{{ route('disposal.destroy', $disposal) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this disposal record? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            @endcan
                            
                            <a href="{{ route('disposal.index', ['export' => 'pdf', 'id' => $disposal->id]) }}" 
                               class="btn btn-outline-primary ms-2">
                                <i class="fas fa-file-pdf me-1"></i>Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge {
    font-size: 0.875em;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-secondary {
    background-color: #6c757d;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.25rem;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.card .card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>
@endpush