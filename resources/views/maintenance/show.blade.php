@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>Maintenance Record #{{ $maintenance->id }}
                        </h3>
                        <div>
                            <span class="badge badge-{{ $maintenance->status == 'Completed' ? 'success' : ($maintenance->status == 'In Progress' ? 'warning' : ($maintenance->status == 'Cancelled' ? 'danger' : 'info')) }} fs-6">
                                {{ $maintenance->status }}
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
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="30%">Asset Name:</td>
                                            <td>{{ $maintenance->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Asset Tag:</td>
                                            <td>{{ $maintenance->asset->asset_tag }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Model:</td>
                                            <td>{{ $maintenance->asset->model->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Category:</td>
                                            <td>{{ $maintenance->asset->model->category->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td>
                                                <span class="badge badge-{{ $maintenance->asset->status_label->color ?? 'secondary' }}">
                                                    {{ $maintenance->asset->status_label->name ?? $maintenance->asset->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Maintenance Details -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Maintenance Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold" width="30%">Start Date:</td>
                                            <td>{{ $maintenance->start_date ? $maintenance->start_date->format('M d, Y H:i') : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">End Date:</td>
                                            <td>{{ $maintenance->end_date ? $maintenance->end_date->format('M d, Y H:i') : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Duration:</td>
                                            <td>
                                                @if($maintenance->start_date && $maintenance->end_date)
                                                    {{ $maintenance->start_date->diffForHumans($maintenance->end_date, true) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cost:</td>
                                            <td>
                                                @if($maintenance->cost)
                                                    <span class="fw-bold text-primary">@currency($maintenance->cost)</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Vendor:</td>
                                            <td>{{ $maintenance->vendor->name ?? 'Internal' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Issue and Repair Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Issue Reported</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $maintenance->issue_reported ?: 'No issue description provided.' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-wrench me-2"></i>Repair Action</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $maintenance->repair_action ?: 'No repair action documented yet.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remarks -->
                    @if($maintenance->remarks)
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Remarks</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $maintenance->remarks }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Audit Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Record Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless mb-0">
                                                <tr>
                                                    <td class="fw-bold" width="30%">Created:</td>
                                                    <td>{{ $maintenance->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Created By:</td>
                                                    <td>{{ $maintenance->createdBy->name ?? 'System' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless mb-0">
                                                <tr>
                                                    <td class="fw-bold" width="30%">Last Updated:</td>
                                                    <td>{{ $maintenance->updated_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Updated By:</td>
                                                    <td>{{ $maintenance->updatedBy->name ?? 'System' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <div>
                            @can('edit_maintenance')
                                <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            @endcan
                            
                            @can('delete_maintenance')
                                <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this maintenance record? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            @endcan
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
</style>
@endpush